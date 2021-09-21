<?php
class Data
{
  /**
  * 送信されたデータを返す。
  * 取得に失敗した場合はfalseを返す。
  */
  public static function getDataFromPost()
  {
    if (empty($_POST)) {
      return false;
    }
    return $_POST;
  }

  /**
   * 送信されたデータ（JSON）を連想配列にして返す。
   * 変換に失敗した場合はfalseを返す。
   */
  public static function getDataFromJson()
  {
    $json = file_get_contents('php://input');
    // JSONを連想配列に変換。
    $data = json_decode($json, true);
    if (!is_array($data)) {
      // データが正常に処理できなかった場合
      return false;
    }
    return $data;
  }
}
