<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class InsertToDo extends ActiveRecord
{
	public static function tableName()
    {
        return 'insert_to_do';
    }
	
	public function rules()
	{
		return [
		[['Date','Description'],'required'],
		];
	}
}
