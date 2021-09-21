<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
// オートローダーの設定
spl_autoload_register(function($class)
{
  require_once(__DIR__ . '/classes/' . $class . '.php');
});

$formName = Data::getDataFromPost()['setting-name'] ? : '';
new App($formName, __DIR__);
