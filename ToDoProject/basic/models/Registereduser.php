<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "registereduser".
 *
 * @property string $emailid
 * @property string $password
 */
class Registereduser extends \yii\db\ActiveRecord
{
    
	public $password_repeat;
	/**
     * @inheritdoc
     */
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
            [['password_repeat', 'password'], 'required'],
			[['password_repeat'], 'compare', 'compareAttribute' => 'password'],     
        // validates if the value of “password” attribute equals to that of “password_repeat”
        ['password', 'compare'],
            [['password_repeat'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 50],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			 'emailid' => 'Email',
            'password_repeat' => 'Confirm',
            'password' => 'Password',
        ];
    }
}
