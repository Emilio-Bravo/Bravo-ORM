<?php

return [
    
    'rules' => [
        'required' => '/.+/',
        'email' => '/[\w\_.@%]+@[\w\d_.@%]+\.[\w\d]/',
        'string' => '/[A-z]+[0-9]*/',
        'number' => '/[0-9]/',
        'digit' => '/\d/',
        'word' => '/\w/',
        'url' => '/^(http|https)+:\/\/+([\w\S\d])+\.([\w\d])+?([a-z])+$/',
        'letters' => '/[A-z]/'
    ],

    //Do not modify this 
    'counting_rules' => [
        'min' => 'min:[0-9]+',
        'max' => 'max:[0-9]+',
        'fix' => 'fix:[0-9]+'
    ],

    'error_msgs' => [
        'required' => ':input is required',
        'email' => 'Enter a valid email address',
        'string' => ':input can just have letters with numbers',
        'number' => ':input can just have numbers',
        'digit' => ':input can just have digits',
        'word' => ':input can just have words',
        'url' => 'Enter a valid URL',
        'max' => ':input musn´t have more than :amount characters',
        'min' => ':input must have more than :amount characters',
        'fix' => ':input must have :amount characters'
    ]

];
