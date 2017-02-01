<?php

namespace AppBundle\ORM\Query\AST\Functions\MySql;

use CrEOF\Spatial\ORM\Query\AST\Functions\AbstractSpatialDQLFunction;

/**
 * STDistance DQL function
 *
 * @author  luca capra <luca.capra@create-net.org>
 * @license http://dlambert.mit-license.org MIT
 */
class STDistanceSphere extends AbstractSpatialDQLFunction {

    protected $platforms = array('mysql');
    protected $functionName = 'ST_Distance_Sphere';
    protected $minGeomExpr = 2;
    protected $maxGeomExpr = 2;

}
