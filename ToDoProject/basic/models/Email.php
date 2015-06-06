<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "email".
 *
 * @property string $emailid
 * @property integer $todoid
 */
class Email extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'email';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['emailid', 'required'],
            ['emailid','email'],
            [['emailid'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'emailid' => 'Enter email Id',
            ];
    }
}
