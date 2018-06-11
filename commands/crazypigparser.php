<?php
/**
 * Developed by @antydemant.
 * User: antydemant
 * Date: 2/26/18
 */

require(__DIR__ . '/../vendor/autoload.php');
use Sunra\PhpSimple\HtmlDomParser;

// json output location
define('FILENAME','runtime/crazypig_output.json');
//init service link
define('SERVICE','http://www.crazypig.km.ua/full-menu-page/');



//create or open file if exists
$file = fopen(FILENAME, 'w');

//open service's page
$html = HtmlDomParser::file_get_html(SERVICE) or die("Wrong service's URL or crazypig.km.ua is down!");

$products = [];
$menuItems = $html->find("h1.menu-title");

$products = [];

foreach ($menuItems as $menuItem) {
    $caregoryName = $menuItem->find('a', 0)->plaintext;
    $categoryLink = $menuItem->find('a', 0)->href;


    $category = HtmlDomParser::file_get_html($categoryLink) or die("Wrong category URL or crazypig.km.ua is down!");

    $subCategories = $category->find('.cat-title');

    foreach ($subCategories as $subCategory) {
        $subCategoryName = $subCategory->find('a', 0)->plaintext;
        $subCategoryLink = $subCategory->find('a', 0)->href;

        $subCategoryProducts = HtmlDomParser::file_get_html($subCategoryLink) or die("Wrong subcategory URL or crazypig.km.ua is down!");

        $subCategoryProducts = $subCategoryProducts->find('.product_wrapper');

        foreach ($subCategoryProducts as $subCategoryProduct)
        {
            $productLink = $subCategoryProduct->find('a', 0)->href;
            $product = HtmlDomParser::file_get_html($productLink) or die("Wrong product URL or crazypig.km.ua is down!");

            $productName = trim($product->find('h1.product_title', 0)->plaintext);
            $productDescription = trim($product->find('div.woocommerce-product-details__short-description', 0)->plaintext);
            $productPrice = (double)trim($product->find('p.price', 0)->plaintext);
            $productImg = trim($product->find('.single-product-main-image img', 0)->src);

            $products[$caregoryName][] = [
                'product_name' => $productName,
                'subcategory' => $subCategoryName,
                'description' => $productDescription,
                'price' => $productPrice,
                'link' => $productLink,
                'photo_url' => $productImg,

            ];
        }

    }
}
fwrite($file, json_encode($products, JSON_UNESCAPED_UNICODE));
fclose($file);
?>