<?php

namespace app\commands;

use yii\console\Controller;
use yii\db\Query;
use app\models\Product;
use app\models\Service;

/**
 * This command executing dranyk products via sql file.
 * It must be run every day in the morning to keep foods and prices up-to-date
 */
class DranykImportController extends Controller
{
    const SQL_FILEPATH = '/commands/';
    const SQL_FILENAME = 'dranyk_products.sql';
    const TABLE_NAME = 'product';
    const TABLE_COLUMN_NAME = 'serv_id';
    const SERVICE_ID = 3; //  serv_id = 3 -> Dranyk products

    public function actionIndex()
    {
        $products = $this->getProducts();

        if (empty($products)) {
            $this->insertProducts();
            $this->importFile();

        } else {
            $this->clearProducts();
            $this->insertProducts();
            $this->importFile();
        }
    }

    private function importItem($item, $categoryName) {

        $product = new Product();

        $product->product_name = $item['product_name'];
        $product->price = $item["price"];
        $product->photo_url = $item["photo_url"];
        $product->date_add = date("Y:m:d");
        $product->category = $categoryName;
        $product->sub_category = $item["subcategory"];
        $service = ((new Query())->select('id')
            ->from('service')
            ->where(['link'=>'http://dranyk.km.ua'])
            ->one());
        if (empty($service["id"])){
            $serviceinsert = new Service();
            $serviceinsert->id = self::SERVICE_ID;
            $serviceinsert->name = "Dranyk";
            $serviceinsert->link = "http://dranyk.km.ua";
            $serviceinsert->save();
        }
        $product->serv_id = self::SERVICE_ID;
        $product->link = $item['link'];
        $product->save();

    }

    private function importSet($set, $categoryName) {

        if (is_array($set)) {
            foreach ($set as $category) {
                $this->importItem($category, $categoryName);
            }
        }
    }

    private function importFile(){
        // Start the parser here
        $commandPath = \Yii::getAlias('@app') . "/commands/";
        $filepath=\Yii::getAlias('@app') ."/runtime/";
        $output = array();
        $return_var = false;
        echo exec("php " . $commandPath . "dranykparser.php", $output, $return_var);
        if ($return_var == 0) {
            $fileJSON = fopen("$filepath". "dranyk_output.json", 'r');
            $contents = fread($fileJSON, filesize($filepath.'dranyk_output.json'));
            $sets_json = json_decode($contents, JSON_UNESCAPED_UNICODE);
            if ($fileJSON && $contents) {
                if (!empty($sets_json)) {

                    foreach ($sets_json as $category => $set) {
                        if (isset($set)) {

                            $this->importSet($set, $category);
                        }
                    }

                }
            }
        }
    }

    private function insertProducts()
    {
        // Read SQL file
        $filepath = \Yii::getAlias('@app') . self::SQL_FILEPATH;
        $sqlFile = file_get_contents($filepath . self::SQL_FILENAME);

        //Execute SQL file
        \Yii::$app->db->createCommand($sqlFile)->execute();
    }

    private function clearProducts()
    {
        // Clear all Dranyk products
        (new Query)
            ->createCommand()
            ->delete(self::TABLE_NAME, [self::TABLE_COLUMN_NAME => self::SERVICE_ID])
            ->execute();
    }

    private function getProducts()
    {
        return Product::find()->where([self::TABLE_COLUMN_NAME => self::SERVICE_ID]);
    }
}