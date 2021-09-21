<?php
class Mailer
{
  public function __construct(
    $to = '',
    $headers = [],
    $body = '',
    $subject = '',
    $language = '',
    $encoding = ''
  )
  {
    $this->to = $to;
    $this->subject = $subject;
    $this->body = $body;
    $this->headers = $headers;
    $this->language = $language;
    $this->encoding = $encoding;
  }

  /**
   * mb_send_mail()でメールを送信するメソッドを呼び出し、
   * 成否の結果をrespond()メソッドに引き渡す。
   * @return boolean
   */
  public function send()
  {
    $this->response = $this->sendMail();

    if ($this->response) {
      $this->respond([
        'status' => 'success',
      ]);
      return true;
    } else {
      $this->respond([
        'status' => 'failure',
      ]);
      return false;
    }
  }

  /**
   * 自動返信メールを送信し、プログラムを終了する。
   */
  public function reply()
  {
    $this->sendMail();
    exit;
  }

  /**
   * メールを送信し、成否の結果を返す。
   * @return boolean
   */
  public function sendMail()
  {
    mb_language($this->language);
    mb_internal_encoding($this->encoding);
    return mb_send_mail(
      $this->to, $this->subject, $this->body, $this->headers
    );
  }

  /**
  * メールの送信が成功したかどうかをJSONとして出力する。
  * @param array $result
  */
  public function respond($result)
  {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result);
  }

  /**
   * $this->headersに基づいてメールヘッダ用の文字列を生成して返す。
   * @return string
   */
  public function getHeaderString()
  {
    $header_string = '';
    array_walk(
      $this->headers,
      function($value, $key) use (&$header_string)
      {
        $header_string .= sprintf("%s: %s\r\n", trim($key), trim($value));
      }
    );
    return trim($header_string);
  }
}
