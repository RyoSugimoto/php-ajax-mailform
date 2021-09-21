<?php
class Controller
{
  public $base_url = '';
  public $settings = [];
  public $post = [];
  public $mailer = null;
  public $mailer_reply = null;

  public function __construct($setting_name, $base_url)
  {
    $this->base_url = $base_url;

    $settingObject = new Settings($setting_name, $this->base_url);
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
    $this->post = Data::getDataFromPost() ? : exit;

    // ハニーポットとしたフィールドに値があればプログラムを終了する。
    if ($this->honeypotHasValue()) exit;

    // 送信データと設定に基づいた件名を用意する。
    $mail_subject = Formatter::getSubject($this->post, $this->settings['options']['subject']);

    // 送信データとテンプレートから本文を用意する。
    $template_path = $this->base_url . '/templates/' . $this->settings['options']['template'];
    $template_string = file_get_contents($template_path);
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
    $reply_template_path = $this->base_url . '/templates/' . $this->settings['options']['auto_reply_options']['template'];
    $reply_template_string = file_get_contents($reply_template_path);
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
