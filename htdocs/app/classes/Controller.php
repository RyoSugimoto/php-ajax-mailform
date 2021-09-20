<?php
class Controller
{
  public $settings = [];
  public $post = [];
  public $mailer = null;
  public $mailer_reply = null;

  public function __construct($settings)
  {
    $settingObject = new Settings($settings);
    $this->settings = $settingObject->get();

    if (
      !empty($this->settings['domains'])
      && !$this->checkDomain($this->settings['domains'])
    ) {
      // 'domains'が1つでも設定されている
      // かつ、設定済みのドメインからのアクセス〈ではない〉場合
      exit;
    }

    // 送信データが正常に取得できなければプログラムを終了する。
    $this->post = $this->getData() ? : exit;

    // ハニーポットとしたフィールドに値があればプログラムを終了する。
    if ($this->honeypotHasValue()) exit;

    // 送信データと設定に基づいた件名を用意する。
    $mail_subject = Formatter::getSubject($this->post, $this->settings['options']['subject']);

    // 送信データとテンプレートから本文を用意する。
    $template_string = file_get_contents($this->settings['options']['template']);
    $mail_body = Formatter::getMailBody($this->post, $template_string);

    if (!Validator::isEmail($this->settings['to'])) exit;

    // メーラーインスタンスを生成する。
    $this->mailer = new Mailer(
      $this->settings['to'],
      $this->settings['headers'],
      $mail_body,
      $mail_subject,
      $this->settings['language'],
      $this->settings['encoding']
    );

    if ($this->settings['options']['auto_reply']) {
      $this->createReply();
    }
  }

  /**
   * 自動返信用のメーラーオブジェクトを生成する。
   */
  public function createReply()
  {
    $reply_to_field = $this->settings['options']['auto_reply_to_field'];
    $reply_to = $this->post[$reply_to_field];

    if (!Validator::isEmail($reply_to)) return false;

    // 送信データと設定に基づいた件名を用意する。
    $reply_mail_subject = Formatter::getSubject($this->post, $this->settings['options']['auto_reply_options']['subject']);

    // 送信データとテンプレートから本文を用意する。
    $reply_template_string = file_get_contents($this->settings['options']['auto_reply_options']['template']);
    $reply_mail_body = Formatter::getMailBody($this->post, $reply_template_string);

    // 自動返信用のメーラーインスタンスを生成する。
    $this->mailer_reply = new Mailer(
      $reply_to,
      $this->settings['options']['auto_reply_headers'],
      $reply_mail_body,
      $reply_mail_subject,
      $this->settings['language'],
      $this->settings['encoding']
    );
  }

  /**
   * アクセス元のドメインが設定済みのドメイン（host）リストの中にあるか調べ、、
   * なければプログラムを終了する。
   */
  public function checkDomain($domains)
  {
    $host = $_SERVER['HTTP_REFERER'];
    return in_array($host, $domains);
  }

  /**
   * 罠フィールドに値があるかを調べる
   */
  public function honeypotHasValue()
  {
    $honeypot_field = $this->settings['options']['honeypot_field'];
    if ($honeypot_field) {
      return $this->post[$honeypot_field];
    }
    return false;
  }

  /**
   * 送信されたデータを返す。
   * 取得に失敗した場合はfalseを返す。
   */
  public function getData()
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
  public function getDataFromJson()
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

  /**
   * メーラにメールを送信させる。
   */
  public function send()
  {
    if ($this->mailer->send()) $this->reply();
  }

  /**
   * 自動返信メーラに返信させる。
   */
  public function reply()
  {
    $this->mailer_reply->reply();
  }
}
