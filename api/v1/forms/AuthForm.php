<?php

namespace api\v1\forms;

use common\models\User;
use yii\base\Model;

class AuthForm extends Model
{
    public $password;
    public $username;

    protected $_user = null;

    public function rules()
    {
        return [
            [['password', 'username'], 'string']
        ];
    }

    /**
     * @return bool
     */
    public function auth()
    {
        if (!$this->validate())
            return false;

			if(!$user = User::find()->where(['username'=>$this->username])->one())
			{
				$this->addError('username', "User not found");
				return false;
			}
			if(!$user->validatePassword($this->password))
			{
				$this->addError('password', "Password is wrong");
				return false;
			}
			
			$this->_user = $user;

        return true;
    }
	


    protected function createUser()
    {
        $user = new User();

        $user->setPassword($this->password);

        if (!$user->save())
            return false;

        $this->_user = $user;

        return $user;
    }


    /**
     * @return null|User
     */
    public function getUser()
    {
        return $this->_user;
    }


}