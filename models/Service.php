<?php

namespace app\models;

use yii\db\ActiveRecord;

class Service extends ActiveRecord
{

    public static function tableName()
    {
        return 'service';
    }

    public function getProduct()
    {
        return $this->hasMany(Product::className(), ['serv_id' => 'id']);
    }

    public function rules()
    {
        return [
            // name and link are both required
            [['name', 'link'], 'required'],

        ];
    }

    public function isCookDrive() {
        if( $this->id == 1 || $this->name == 'CookDrive' || $this->link == 'http://cookdrive.com.ua' ) {
            return true;
        } else {
            return false;
        }
    }
}
