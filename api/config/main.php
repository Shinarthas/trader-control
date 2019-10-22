<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'api\v1\Module',
        ],
    ],
    'components' => [
		'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'request' => [
            'enableCookieValidation' => false,
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'response' => [
            'format' => 'json',
            'on beforeSend' => function ($event) {
                $response = $event->sender;

                if (!$response->isSuccessful && (
                        !isset($response->data['status']) || $response->data['status'] !== false
                    )
                ) {
                    $response->data = [
                        'status' => false,
                        'error_code' => 'INTERNAL_SERVER_ERROR',
                        'data' => $response->data
                    ];
                }
            },
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', YII_DEBUG ? 'info' : 0],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
				'/v1/<controller>/<action>' => '/v1/<controller>/<action>',
				'/' => '/v1/site/index',
				'/auth' => '/v1/auth/index',
				'/empty' => '/v1/project/empty',
				'/tasks' => '/v1/project/tasks',
				'/task/start/<id:\d+>' => '/v1/project/task-start',
				'/task/pause/<id:\d+>' => '/v1/project/task-pause',
				'/start-track' => '/v1/system/start-track',
            ]
        ],
    ],
    'params' => $params,
];
