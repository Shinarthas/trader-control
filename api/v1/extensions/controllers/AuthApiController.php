<?php

namespace api\v1\extensions\controllers;

use api\v1\renders\ResponseRender;
use Yii;

class AuthApiController extends ApiController
{
    public function beforeAction($action)
	{
		if (parent::beforeAction($action)) {
		
			if($_POST['key']==md5('xc;nj;235[xznhc09[3,v62398mnp:IUNPOnuh023v%*#JVM%8mj2342610_N)*(Hsdnh'.date("Y-m-d",time())))
				return true;
				
			return false;
        }

        return false;
    }
}