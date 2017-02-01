<?php
namespace AppBundle\Entity\Auth;

use OAuth2\OAuth2;
use OAuth2\IOAuth2GrantUser;
use OAuth2\OAuth2ServerException;
use App\CoreBundle\Entity\User;
use OAuth2\Model\IOAuth2Client;
use OAuth2\Model\IOAuth2AuthCode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppOAuth2 extends OAuth2 {
	/**
	 * @var string
	 */
	protected $network;

	public function grantAccessToken(Request $request = NULL, $network = NULL) {
		$this->network = $network;

		$filters = array("grant_type" => array("filter" => FILTER_VALIDATE_REGEXP, "options" => array("regexp" => self::GRANT_TYPE_REGEXP), "flags" => FILTER_REQUIRE_SCALAR), "scope" => array("flags" => FILTER_REQUIRE_SCALAR), "code" => array("flags" => FILTER_REQUIRE_SCALAR), "redirect_uri" => array("filter" => FILTER_SANITIZE_URL), "username" => array("flags" => FILTER_REQUIRE_SCALAR), "password" => array("flags" => FILTER_REQUIRE_SCALAR), "refresh_token" => array("flags" => FILTER_REQUIRE_SCALAR), );

		if ($request === NULL) {
			$request = Request::createFromGlobals();
		}

		// Input data by default can be either POST or GET
		if ($request->getMethod() === 'POST') {
			$inputData = $request->request->all();
		} else {
			$inputData = $request->query->all();
		}

		// Basic authorization header
		$authHeaders = $this->getAuthorizationHeader($request);

		// Filter input data
		$input = filter_var_array($inputData, $filters);

		// Grant Type must be specified.
		if (!$input["grant_type"]) {
			throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_REQUEST, 'Invalid grant_type parameter or parameter missing');
		}

		// Authorize the client
		$clientCreds = $this->getClientCredentials($inputData, $authHeaders);

		$client = $this->storage->getClient($clientCreds[0]);

		if (!$client) {
			throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_CLIENT, 'The client credentials are invalid');
		}

		if ($this->storage->checkClientCredentials($client, $clientCreds[1]) === FALSE) {
			throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_CLIENT, 'The client credentials are invalid');
		}

		if (!$this->storage->checkRestrictedGrantType($client, $input["grant_type"])) {
			throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_UNAUTHORIZED_CLIENT, 'The grant type is unauthorized for this client_id');
		}

		// Do the granting
		switch ($input["grant_type"]) {
			case self::GRANT_TYPE_AUTH_CODE :
				$stored = $this->grantAccessTokenAuthCode($client, $input);
				// returns array('data' => data, 'scope' => scope)
				break;
			case self::GRANT_TYPE_USER_CREDENTIALS :
				$stored = $this->grantAccessTokenUserCredentials($client, $input);
				// returns: true || array('scope' => scope)
				break;
			case self::GRANT_TYPE_CLIENT_CREDENTIALS :
				$stored = $this->grantAccessTokenClientCredentials($client, $input, $clientCreds);
				// returns: true || array('scope' => scope)
				break;
			case self::GRANT_TYPE_REFRESH_TOKEN :
				$stored = $this->grantAccessTokenRefreshToken($client, $input);
				// returns array('data' => data, 'scope' => scope)
				break;
			default :
				if (filter_var($input["grant_type"], FILTER_VALIDATE_URL)) {
					$stored = $this->grantAccessTokenExtension($client, $inputData, $authHeaders);
					// returns: true || array('scope' => scope)
				} else {
					throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_REQUEST, 'Invalid grant_type parameter or parameter missing');
				}
		}

		if (!is_array($stored)) {
			$stored = array();
		}

		// if no scope provided to check against $input['scope'] then application defaults are set
		// if no data is provided than null is set
		$stored += array('scope' => $this->getVariable(self::CONFIG_SUPPORTED_SCOPES, null), 'data' => null);

		// Check scope, if provided
		if ($input["scope"] && (!isset($stored["scope"]) || !$this->checkScope($input["scope"], $stored["scope"]))) {
			throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_SCOPE, 'An unsupported scope was requested.');
		}

		$token = $this->createAccessToken($client, $stored['data'], $stored['scope']);

		return new Response(json_encode($token), 200, $this->getJsonHeaders());
	}

	protected function grantAccessTokenUserCredentials(IOAuth2Client $client, array $input) {
		if (!($this->storage instanceof IOAuth2GrantUser)) {
			throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_UNSUPPORTED_GRANT_TYPE);
		}

		if (!$input["username"] || !$input["password"]) {
			throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_REQUEST, 'Missing parameters. "username" and "password" required');
		}

		if ($this->network == NULL)
			$stored = $this->storage->checkUserCredentials($client, $input["username"], $input["password"]);
		else
			$stored = $this->storage->checkSocialCredentials($client, $input["username"], $input["password"], $this->network);

		if ($stored === FALSE) {
			throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_GRANT);
		}

		return $stored;
	}

	private function getJsonHeaders() {
		return array('Content-Type' => 'application/json', 'Cache-Control' => 'no-store', 'Pragma' => 'no-cache', );
	}

}
