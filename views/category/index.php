<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

$this->title = 'My food';
?>

    <div class="row">
        <div class="col-lg-12">
            <ul class="categories_wrap">
            <?= app\components\CategoryWidget::widget()?>
                
            </ul>

        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="categori_content_wrap">
<?php if(!empty($new_arr)):?>
                <div class="categori_title">
                    <h1><?php echo $category?></h1>
                </div>
<?php foreach ($new_arr as $key => $value):?>
                <div class="subcategori_content_wrap">
                    <div class="subcategori_title">
                        <h2><?php echo $key?></h2>
                    </div>
                    <div class="categori_content">
                        <ul>
    <?php foreach ($value as $key => $value):?>
                            <li>
                                <div class="catalog_product_wrap">
                                    <a href="<?= \yii\helpers\Url::to(['product/view', 'id'=>$value[id]])?>">
                                        <div class="catalog_product_img">
                                            <img src="<?php echo $value[photo_url] ?>" alt="<?php echo $value[product_name]?>">
                                            <div class="hidden_info">
                                                <p><?php echo $value[description]?></p>
                                            </div>
                                        </div>
                                        <div class="catalog_product_info_wrap">
                                            <p class="catalog_product_subcategori_title"><?php echo $value[sub_category]?></p>
                                            <h3><?php echo $value[product_name]?></h3>
                                            <p class="catalog_product_info"><?php echo $value[weight]?></p>
                                        </div>
                                        <div class="catalog_product_price">
                                            <p><?php echo $value[price]?><span> грн.</span></p>
                                        </div>
                                    </a>
                                    <div class="catalog_product_footer clearfix">
                                        <a class='add_to_cart' data-id='<?php echo $value[id]?>' href="<?= \yii\helpers\Url::to(['cart/index', 'id'=>$value[id]])?>">Замовити</a>
                                        <div class="catalog_product_quantity">
                                            <input class='qty' type="text" value="1" maxlength="3">
                                            <span class="plus"></span>
                                            <span class="minus"></span>
                                            <span class="unit">шт.</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
<?php endforeach;?>
                        </ul>
                    </div>
                </div>
<?php endforeach;?>
<?php endif;?>
            </div>
        </div>
    </div>