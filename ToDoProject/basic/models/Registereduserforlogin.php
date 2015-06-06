<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "registereduser".
 *
 * @property string $emailid
 * @property string $password
 */
class Registereduserforlogin extends \yii\db\ActiveRecord
{
    
	/**
     * @inheritdoc
     */
	 public $emailid;
	 public $password;
	 
	public static function tableName()
    {
        return 'registereduser';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['emailid', 'password'], 'required'],
			['emailid','email'],
			['password', 'validateUsernamePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			 'emailid' => 'Email',
             'password' => 'Password',
        ];
    }
	
	 public function validateUsernamePassword($attribute)
    {
        $validator = Registereduserforlogin::findBySql('select * from registereduser where emailid ="'.$this->emailid.'" AND password ="'.$this->password.'"')->count();
		if($validator > 0)
		{
			return true;
		}
		else
		{	
			$this->addError($attribute, 'Incorrect username or password.');
			return false;
		}
    }
	
	
}
