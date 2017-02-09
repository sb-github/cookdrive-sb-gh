<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Profile;
use app\models\Service;
use yii\helpers\Url;
use app\models\Product;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Замовлення на ' . date("Y:m:d");
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_menu') ?>

<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        //TODO: потрібно адміну створювати замовлення ?
        //echo Html::a('Create Order', ['order-create'], ['class' => 'btn btn-success']);
        //debug($users);
        ?>
    </p>
    <div class="row">
        <div class="col-lg-12">
            <div class="admin_order_wrap">
                <div class="clearfix">
                    <div class="all_user_order_block_slide active">
                        <span>Згорнути всі</span>
                    </div>
                </div>
                <ul>
                    <?php if(isset($orders_per_user)) { ?>
                    <?php foreach($orders_per_user as $keys => $values) { ?>
                    <li class="admin_order_one active">
                        <div class="user_order_block_up">
                            <div class="user_name">
                                <?= Html::img(Profile::findOne($keys)->getAvatarUrl(24), [
                                    'class' => 'img-rounded',
                                    'alt' => Profile::findOne($keys)->name,
                                ]) ?>
                                <?php echo Profile::findOne($keys)->name ; ?>
                            </div>
                        </div>
                        <div class="table-responsive">
                <?php Pjax::begin(['id' => 'items', 'enablePushState' => false]) ?>
                        <table class="table table-hover user_order_block_dn">
                            <thead>
                                <tr>
                                    <th>Назва продукту</th>
                                    <th>Ціна</th>
                                    <th>Кількість</th>
                                    <th>Загальна вартість</th>
                                    <th>Сервіс</th>
                                    <th>Операції</th>
                                </tr>
                            </thead>
                        <?php
                        $summ_all = 0;
                        foreach ($values as $key => $value) { ?>
                            <?php $product = Product::findOne($value['product_id']); ?>
                            <tr>
                                <td><?= $product->product_name ?></td>
                                <td><?= $product->price ?> грн.</td>
                                <td><?= $value['quantity'] ?> шт.</td>
                                <td><?= $product->price*$value['quantity'] ?> грн.</td>
                                <td><?= Html::a(Service::findOne($product->serv_id)->name, Url::to(Service::findOne($value['serv_id'])->link, true), ['target' => '_blank']); ?></td>
                                <td>
                                    <?= Html::a('<span class="glyphicon glyphicon-remove"></span>', ['/user/admin/order-delete', 'id' => $value['id']], [
                                        'title' => 'Видалити',
                                        'data-confirm' => 'А ви впевнені що хочете видалити замовлення?',
                                        'data-method' => 'POST',
                                    ]) ?>
                                    <?= Html::a('<span class="glyphicon glyphicon-retweet replace"></span>', ['/user/admin/order-update', 'id' => $value['id']], [
                                        'title' => 'Редагування замовлення',
                                        //'class' => 'replace',
                                        'data-order-id' => $value['id']
                                    ]) ?>
                                </td>
                            </tr>


                        <?php
                                $summ_all +=$product->price*$value['quantity'];
                            }
                        ?>
                            <tfooter>
                                <tr>
                                    <th colspan="6">Все замовлення користувача на суму: <?=$summ_all?> грн. </th>
                                </tr>
                            </tfooter>
                        </table>
                    <?php Pjax::end(); ?>

                        </div>
                    </li>
                    <?php } ?>
                    <?php } else if(isset($orders)) { ?>
                    <li class="admin_order_one active">
                        <div class="user_order_block_up">
                            <div class="user_name">
                                Загальний чек замовлення
                            </div>
                        </div>
                            <div class="table-responsive">
                                <table class="table table-hover user_order_block_dn">
                                    <thead>
                                    <tr>
                                        <th>Назва товару</th>
                                        <th>Кількість</th>
                                        <th>Ціна</th>
                                        <th>Всього</th>
                                        <th>Сервіс</th>
                                    </tr>
                                    </thead>
                                    <?php
                                        $summ_all = 0;
                                        foreach ($orders as $key => $value) { ?>
                                            <?php $product = Product::findOne($value['product_id']); ?>
                                            <tr>
                                                <td><?= $product->product_name ?></td>
                                                <td><?= $value['quantity_sum'] ?> шт.</td>
                                                <td><?= $product->price ?> грн.</td>
                                                <td><?= $product->price*$value['quantity_sum'] ?> грн.</td>
                                                <td><?= Html::a(Service::findOne($product->serv_id)->name, Url::to(Service::findOne($product->serv_id)->link, true), ['target' => '_blank']); ?></td>
                                            </tr>
                                            <?php $summ_all += $product->price*$value['quantity_sum']; ?>

                                        <?php } ?>
                                    <tfooter>
                                        <tr>
                                            <th colspan="6">Загальна сума: <?=$summ_all?> грн. </th>
                                        </tr>
                                    </tfooter>
                                </table>
                        </div>
                    </li>

                    <?php } else {?>
                        <h1>Замовлення відсутні</h1>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php yii\bootstrap\Modal::begin(['id'=>'pModal']); ?>

<?= isset($orders_per_user)?$this->render('_replace_form'):'' ?>


<?php yii\bootstrap\Modal::end();?>

<?php
$this->registerJs("$(document).on('ready', function() {  // 'pjax:success' use if you have used pjax
           var order_id = 0;
            $('.replace').click(function(e) {
               e.preventDefault();
               $('#pModal').modal('show').find('.modal-content').load($(this).attr('href'));
               var order_id = $(this).closest('a').attr('data-order-id');
                $('#pModal').attr('data-order-id', order_id);

            });
            $('#pModal').on('replaceconfirm', function (e, obj) {
                $.ajax({
                    url:'/user/admin/order-update?id=' + obj.orderId + '&itemId=' + obj.itemId + '&qty=' + obj.qty,
                    success: function(result) {
                        $.pjax.reload({container:'[id=items]'}); 
                    },
                    error: function() {
                    }
                });
            });
        });
    ");

?>