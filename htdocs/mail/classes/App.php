<?php
class App
{
  public function __construct($setting_name, $base_url)
  {
    // メーラー実行
    $mailer = new Controller($setting_name, $base_url);
    $mailer->send();
  }
}
