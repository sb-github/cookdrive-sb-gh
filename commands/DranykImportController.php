<?php
namespace app\commands;

use yii\console\Controller;
use yii\db\Query;
use app\models\Product;

/**
 * This command executing dranyk products via sql file.
 * It must be run every day in the morning to keep foods and prices up-to-date
 */

class DranykImportController extends Controller
{
    const SQL_FILEPATH      = '/commands/';
    const SQL_FILENAME      = 'dranyk_products.sql';
    const TABLE_NAME        = 'product';
    const TABLE_COLUMN_NAME = 'serv_id';
    const SERVICE_ID        = 3; //  serv_id = 3 -> Dranyk products

    public function actionIndex()
    {
        $products = $this->getProducts();

        if (empty($products)) {
            $this->insertProducts();
        } else {
            $this->clearProducts();
            $this->insertProducts();
        }
    }

    private function insertProducts()
    {
        // Read SQL file
        $filepath = \Yii::getAlias('@app') . self::SQL_FILEPATH;
        $sqlFile = file_get_contents($filepath . self::SQL_FILENAME );

        //Execute SQL file
        \Yii::$app->db->createCommand($sqlFile)->execute();
    }

    private function clearProducts()
    {
        // Clear all Dranyk products
        (new Query)
            ->createCommand()
            ->delete(self::TABLE_NAME, [ self::TABLE_COLUMN_NAME => self::SERVICE_ID ])
            ->execute();
    }

    private function getProducts()
    {
        return Product::find()->where([ self::TABLE_COLUMN_NAME => self::SERVICE_ID ]);
    }
}