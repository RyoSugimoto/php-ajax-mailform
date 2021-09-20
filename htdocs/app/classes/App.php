<?php
class App
{
  public function __construct($settings)
  {
    // オートローダーの設定
    spl_autoload_register(function($class)
    {
      require_once($class . '.php');
    });

    // メーラー実行
    $mailer = new Controller($settings);
    $mailer->send();
  }
}
