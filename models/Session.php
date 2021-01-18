<?php

namespace app\models;

use yii\db\ActiveRecord;

class Session extends ActiveRecord
{
    
    const CREATE_SESSION = 'CREATE_SESSION';

    public static function tableName()
    {
        return 'session';
    }

    public function rules()
    {
        return [
            [['name','description','duration','start'],'safe'],
            [['name','description','start','duration'],'required','on' => $this::CREATE_SESSION]
        ];
    }

   
}