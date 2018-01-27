<?php
require(__DIR__ . '/../vendor/autoload.php');
Dotenv::load(__DIR__ . '/..');
Dotenv::required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'YII_DEBUG']);

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', getenv('YII_DEBUG') != 'false');
defined('YII_ENV') or define('YII_ENV', getenv('YII_ENV'));

require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');
require_once __DIR__ . '/../function.php';

(new yii\web\Application($config))->run();
