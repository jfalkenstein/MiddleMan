<?php
/**
 * This is the central config file, used to set valuses used throughout the
 * middleman application.
 */
return [
    'authenticator' => [
        'passphrase'=>'',
        'salter' => '',
        'returnSalt' => '',
        'bypass' => function(){
            $env = getenv('APPLICATION_ENV');
            $method = filter_input(INPUT_SERVER,"REQUEST_METHOD",FILTER_SANITIZE_STRING);
            return ($env === 'development' || $method === "POST");
        },
        'authenticationStringKey' => 'token',
    ],
    'requestFactory' => [
        'returnValueKey' => 'returnVals',
        'customHeaderPrefix' => 'MM-'
    ],
    'serializer' => [
        'jsonp' => [
            'callbackKey' => 'callback',
            'defaultCallbackName' => 'callback',
            'prettyPrint' => true,
            'errorKey' => 'error',
            'finallyKey' => 'finalCall'
        ],
        'json' => [
            'prettyPrint' => function(){
                $env = getenv('APPLICATION_ENV');
                return $env === 'development';
            }
        ]
    ],
];