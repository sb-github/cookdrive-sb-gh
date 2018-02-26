<?php
/**
 * Developed by @antydemant.
 * User: antydemant
 * Date: 2/26/18
 */

require(__DIR__ . '/../vendor/autoload.php');
use Sunra\PhpSimple\HtmlDomParser;
// json output location
define('FILENAME','runtime/dranyk_output.json');
//init service link
define('SERVICE','http://dranyk.km.ua');



//create or open file if exists
$file = fopen(FILENAME, 'w');

//open service's page
$html = HtmlDomParser::file_get_html(SERVICE) or die("Wrong service's URL or Dranyk.km.ua is down!");

$products = [];
$variations = $html->find('a.image-product');
$newProducts = [];

foreach ($variations as $variation)
{
    //link to product variations
    $variationHref = trim($variation->href);
    $variationImage = rtrim(ltrim($variation->style, 'background-image: url(*'),')');
    $variationTitle = trim($variation->find('span',0)->plaintext);

    $productVariation = HtmlDomParser::file_get_html($variationHref) or die("Wrong URL or You shall not pass!");

    $categoryVariation = trim($productVariation->find('h1.product_title', 0)->plaintext);
    $productPrice = trim($productVariation->find('span.woocommerce-Price-amount', 0)->plaintext);

    $productAttributes = $productVariation->find('table.shop_attributes tbody td');

    foreach ($productAttributes as $productAttribute)
    {
        $products[$categoryVariation][$variationTitle]["price"] = (int)$productPrice;
        $products[$categoryVariation][$variationTitle]["link"] = $variationHref;
        $products[$categoryVariation][$variationTitle]["photo"] = $variationImage;
        $products[$categoryVariation][$variationTitle][] = explode(', ' ,trim($productAttribute->plaintext));

    }
//    TODO: Parse products category withour variations ...
//    $productAttributes = $productVariation->find('a.woocommerce-LoopProduct-link woocommerce-loop-product__link');
//
//    foreach ($productAttributes as $productAttribute)
//    {
//        $products[$categoryVariation][$variationTitle]["price"] = (int)$productPrice;
//        $products[$categoryVariation][$variationTitle]["link"] = $variationHref;
//        $products[$categoryVariation][$variationTitle]["photo"] = $variationImage;
//        $products[$categoryVariation][$variationTitle][] = explode(', ' ,trim($productAttribute->plaintext));
//
//    }

}

function returnProduct($price, $link, $photo, $subcategory, $firstOrGarnish, $garnishOrMain, $mainOrSalad, $salad = null)
{
    return [
        "product_name" => $firstOrGarnish . ', ' . $garnishOrMain
                                          . ', ' . $mainOrSalad .
                                  ($salad ? ', ' . $salad : '' ),
        "subcategory" => $subcategory,
        "price" => $price,
        "link" => $link,
        "photo_url" => $photo,

    ];
}

// TODO: Oh, god, please, no, noooo ...
foreach ($products as $productsCategory => $productsArrayPerTitle)
{
    foreach ($productsArrayPerTitle as $productsTitle => $productsValue) {

        foreach ($productsValue[0] as $firstOrGarnish) {

            foreach ($productsValue[1] as $garnishOrMain) {

                foreach ($productsValue[2] as $mainOrSalad) {

                    if ($productsValue[3]) { // Salad exist in Lanch Menu  ?

                        foreach ($productsValue[3] as $salad) {

                            $newProducts[$productsCategory][] = returnProduct($productsValue["price"], $productsValue["link"], $productsValue["photo"], $productsTitle, $firstOrGarnish, $garnishOrMain, $mainOrSalad, $salad);
                        }
                    } else { // "delicious lunch" menu

                        $newProducts[$productsCategory][] = returnProduct($productsValue["price"], $productsValue["link"], $productsValue["photo"], $productsTitle, $firstOrGarnish, $garnishOrMain, $mainOrSalad);
                    }
                }
            }
        }
    }
}

fwrite($file, json_encode($newProducts, JSON_UNESCAPED_UNICODE));
fclose($file);
?>