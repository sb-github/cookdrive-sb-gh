<?php

namespace app\controllers;

use dektrium\user\controllers\AdminController as BaseAdminController;
use Yii;
use app\models\Order;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\History;
use app\models\Product;

class AdminController extends BaseAdminController
{
    public function actionOrders()
    {
        $orders = Order::find()->select('id, product_id, SUM(quantity) AS quantity_sum')->groupBy(['id', 'product_id'])->asArray()->where(['date' => date("Y:m:d")])->all();

        return $this->render('orderindex', ['orders' => $orders]);
    }

    public function actionUserOrders()
    {
        /*$dataProvider = new ActiveDataProvider([
            'query' => Order::find()->where(['date' => date("Y:m:d")]),
        ]);*/
        $orders = Order::find()->asArray()->where(['date' => date("Y:m:d")])->all();
        $orders_per_user = [];
        foreach ($orders as $key => $value) {
            $orders_per_user[$value['user_id']][]=$value;
        }

        return $this->render('orderindex', [
            'orders_per_user' => $orders_per_user,
            'orders' => $orders,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionOrderView($id)
    {
        return $this->render('orderview', [
            'model' => $this->findOrderModel($id),
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionOrderCreate()
    {
        $model = new Order();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['order-view', 'id' => $model->id]);
        } else {
            return $this->render('ordercreate', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $itemId
     * @param integer $qty
     * @return mixed
     */
    public function actionOrderUpdate($id, $itemId, $qty)
    {
        if(Yii::$app->request->isAjax) {
            $model = $this->findOrderModel($id);

            //if ($model->load(Yii::$app->request->post())) {

                $history_order = History::find()->where(['orders_id' => $id])->asArray()->one();

                $history = new History();
                $history->orders_id = $model->id;
                $history->summa = $history_order['summa'] * (-1);
                $history->operation = 2;  // операція возврата поповнення
                $history->users_id = $history_order['users_id'];
                $history->date = date("Y:m:d");
                $history->save();

                $new_product = Product::find()->where(['id' => $itemId])->asArray()->one();

                $history = new History();
                $history->orders_id = $model->id;
                $history->summa = -($new_product['price'] * $qty);
                $history->operation = 1;  // операция зняття грошей
                $history->users_id = $history_order['users_id'];
                $history->date = date("Y:m:d");
                $history->save();

                $model->product_id = $itemId;
                $model->quantity = $qty;
                $model->date = date("Y:m:d");
                $model->save();






                //return $this->redirect(['order-view', 'id' => $model->id]);
           // } else {
                //return $this->render('orderupdate', [
                //    'model' => $model,
                //]);
            //}
        }
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionOrderDelete($id)
    {
        //History::findAll(['orders_id' => $id])->deleteAll();
        History::deleteAll("orders_id = " . $id );
        $this->findOrderModel($id)->delete();

        return $this->redirect(['user-orders']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findOrderModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}