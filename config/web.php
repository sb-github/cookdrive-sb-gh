<?php
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'service/index',
    'language'=>'en',
    'components' => [
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@dektrium/user/views' => '@app/views/',
                ],
            ],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'google' => [
                    'class'        => 'dektrium\user\clients\Google',
                    'clientId'     => getenv('OAUTH_GOOGLE_AUTH_CLIENT_ID'),
                    'clientSecret' => getenv('OAUTH_GOOGLE_AUTH_CLIENT_SECRET'),
                ],
            ],
        ],

        'urlManager' => [
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'rules' => [
                'defaultRoute' => '/site/index',
            ],
        ],
        'request' => [
            // !!! insert a secret key in the .env file (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY'),
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
            'htmlLayout' => '@app/mail/layouts/main-html',
            'textLayout' => '@app/mail/layouts/main-text',
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => [
                    getenv('EMAIL_FROM_EMAIL') => getenv('EMAIL_FROM_NAME')
                ],
            ],
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => getenv('EMAIL_SMTP_HOST'),
                'username' => getenv('EMAIL_SMTP_USERNAME'),
                'password' => getenv('EMAIL_SMTP_PASSWORD'),
                'port' => getenv('EMAIL_SMTP_PORT'),
                'encryption' => getenv('EMAIL_SMTP_ENCRYPTION'),
                ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'modules' => [
        'redactor' => 'yii\redactor\RedactorModule',
        'class' => 'yii\redactor\RedactorModule',
        'uploadDir' => '@webroot/uploads',
        'uploadUrl' => '/hello/uploads',
        'rbac' => 'dektrium\rbac\RbacWebModule',
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableUnconfirmedLogin' => TRUE,
            'confirmWithin' => 21600,
            'cost' => 12,
            'modelMap' => [
                'Profile' => 'app\models\Profile',
                'User' => 'app\models\User',
            ],
            'controllerMap' => [
                'registration' => 'app\controllers\RegistrationController',
                'admin' => 'app\controllers\AdminController',
                'settings' => 'app\controllers\SettingsController',
            ],
        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
