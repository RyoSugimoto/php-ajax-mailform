<?php
class Validator
{
  /**
   * メールアドレスに不正がないかをテストする。
   */
  public static function isEmail($string)
  {
    $pattern = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";
    return preg_match($pattern, $string);
  }
}
