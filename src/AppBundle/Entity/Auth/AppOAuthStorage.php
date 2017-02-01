<?php
namespace AppBundle\Entity\Auth;

use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;

use OAuth2\Model\IOAuth2Client;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;

use FOS\OAuthServerBundle\Storage\OAuthStorage;
use FOS\OAuthServerBundle\Model\ClientInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use FOS\OAuthServerBundle\Model\AccessTokenManagerInterface;
use FOS\OAuthServerBundle\Model\RefreshTokenManagerInterface;
use FOS\OAuthServerBundle\Model\AuthCodeManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class AppOAuthStorage extends OAuthStorage {
	
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $container;

	/**
	 * @var array
	 */
	protected $socialDetails;

	/**
	 * @param \FOS\OAuthServerBundle\Model\ClientManagerInterface $clientManager
	 * @param \FOS\OAuthServerBundle\Model\AccessTokenManagerInterface $accessTokenManager
	 * @param \FOS\OAuthServerBundle\Model\RefreshTokenManagerInterface $refreshTokenManager
	 * @param \FOS\OAuthServerBundle\Model\AuthCodeManagerInterface $authCodeManager
	 * @param null|\Symfony\Component\Security\Core\User\UserProviderInterface $userProvider
	 * @param null|\Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 */
	public function __construct(ClientManagerInterface $clientManager, AccessTokenManagerInterface $accessTokenManager, RefreshTokenManagerInterface $refreshTokenManager, AuthCodeManagerInterface $authCodeManager, UserProviderInterface $userProvider = null, EncoderFactoryInterface $encoderFactory = null, EntityManager $entityManager, Container $container) {
		$this->clientManager = $clientManager;
		$this->accessTokenManager = $accessTokenManager;
		$this->refreshTokenManager = $refreshTokenManager;
		$this->authCodeManager = $authCodeManager;
		$this->userProvider = $userProvider;
		$this->encoderFactory = $encoderFactory;
		$this->em = $entityManager;
		$this->container = $container;

		$this->grantExtensions = array();
	}

	public function checkSocialCredentials(IOAuth2Client $client, $socialId, $socialToken, $network) {
		
		if (!$client instanceof ClientInterface) {
			throw new \InvalidArgumentException('Client has to implement the ClientInterface');
		}

		try {
			$user = $this->em->getRepository('AppBundle:User')->findOneBy(array(strtolower($network) . 'Id' => $socialId));
		} catch(AuthenticationException $e) {
			throw new \InvalidArgumentException('Invalid network');
		}
		
		if (null !== $user) {
			$encoder = $this->encoderFactory->getEncoder($user);
			if ($this->checkSocialAccessToken($socialId, $socialToken, $network)) {
				$this->updateSocialToken($user, $socialId, $socialToken, $network);
				return array('data' => $user, );
			}
		} else {
			if ($this->checkSocialAccessToken($socialId, $socialToken, $network)) {
				$user = $this->createProfileFromSocialDetails($socialId, $socialToken, $network);
				if (null !== $user) {
					$encoder = $this->encoderFactory->getEncoder($user);
					return array('data' => $user, );
				}
			}
		}

		return false;
	}

	private function checkSocialAccessToken($socialId, $socialToken, $network) {
		$function = 'check' . ucfirst($network) . 'AccessToken';
		return $this->$function($socialId, $socialToken);
	}

	private function checkFacebookAccessToken($socialId, $socialToken) {
		
		$facebook_client_id = $this->container->getParameter('facebook_client_id');
		$facebook_client_secret = $this->container->getParameter('facebook_client_secret');
		
		$facebook = new Facebook( array('app_id' => $facebook_client_id, 'app_secret' => $facebook_client_secret, ));
		$facebook->setDefaultAccessToken($socialToken);

		try {
			$result = $facebook->get('/me?fields=id,email,name',$socialToken)->getDecodedBody();
			$this->socialDetails = $result;
			
			if (!array_key_exists('id', $result) || ($result['id'] != $socialId))
				return false;

			return true;
		} catch (FacebookSDKException $e) {
			return false;
		}
	}

	private function checkTwitterAccessToken($socialId, $socialToken) {
		// to be implemented

		return false;
	}

	private function checkGoogleAccessToken($socialId, $socialToken) {
		// to be implemented
		return false;
	}

	private function checkUserExistWithEmail($email) {
		return $this->em->getRepository('AppBundle:User')->findOneBy(array('email' => $email));
	}

	private function createProfileFromSocialDetails($socialId, $socialToken, $network) {
		if (array_key_exists('email', $this->socialDetails)) {
			$user = $this->em->getRepository('AppBundle:User')->findOneBy(array('email' => $this->socialDetails['email']));

			if (null !== $user) {
				$this->updateSocialToken($user, $socialId, $socialToken, $network);
				return $user;
			}
		}

		$user = new User();
		$user->setUsername($this->socialDetails['id']);
		$user->setPassword($this->socialDetails['id']);
		$user->setEnabled(true);
		
		if (array_key_exists('email', $this->socialDetails))
			$user->setEmail($this->socialDetails['email']);
		else
			$user->setEmail($this->socialDetails['id']);

		$this->updateSocialToken($user, $socialId, $socialToken, $network);

		$this->em->persist($user);
		$this->em->flush();

		return $user;
		
	}

	private function updateSocialToken($user, $socialId, $socialToken, $network) {
		if (null == $user)
			return;

		$socialIdSetter = 'set' . ucfirst($network) . 'Id';
		$socialAccessTokenSetter = 'set' . ucfirst($network) . 'AccessToken';

		$user->$socialIdSetter($socialId);
		$user->$socialAccessTokenSetter($socialToken);

		$this->em->persist($user);
		$this->em->flush();
	}

}
