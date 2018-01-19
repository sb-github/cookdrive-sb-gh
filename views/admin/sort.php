<?php
/**
 * Created by PhpStorm.
 * User: Gladiator
 * Date: 11.01.2018
 * Time: 19:46
 */

echo $this->render('blocs/_from', ['model'=> $model]);

echo \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        [
            'label' => 'Ім\'я користувача',
            'content' => function($data){
                return $data->profile->name;
            }
        ],
        'product_name',
        'product_price'
    ]
]);
