<?php
/**
 * Created by PhpStorm.
 * User: MyBe
 * Date: 20.01.2017
 * Time: 0:27
 */

namespace app\models;

use yii\db\ActiveRecord;

class History extends ActiveRecord
{
    public function rules()
    {
        return [
            // operation, summa, users_id are both required
            [['operation', 'summa', 'users_id'], 'required'],
            // summa is int TODO: summ can be int ?
            ['summa', 'integer']

        ];
    }

    public static function myBalance($user_id)
    {
        $data = History::find()->where(['users_id' => $user_id])->sum('summa');
        if (!$data) {
            return 0;
        }
        return $data;
    }

}