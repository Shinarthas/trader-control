<?php

namespace api\v1\renders;

use common\models\User;

class AuthRender extends BaseRender
{
    /**
     * @param $entity User
     * @return array
     */
    public static function one($entity)
    {
        return [
            'user' => [
                'id' => $entity->id,
            ]
        ];
    }

    public static function oneWithToken($user)
    {
		$data = \Yii::$app->cache->get('user_'.$user->id);
		if(!isset($data['token']))
		{
			$data['token'] = $user->id.':'.rand(0,100000000);
			$data['username'] =  $user->username;
			\Yii::$app->cache->set('user_'.$user->id, $data);
		}
		
        return [
            'token' => base64_encode ( $data['token'] ),
            'user' => [
                'id' => $user->id,
                'username' => (string) $user->username ?: null
            ]
        ];
    }
}