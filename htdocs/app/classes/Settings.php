<?php
class Settings
{
  public $merged = [];
  public static $defaults = [
    'domains' => [],
    'to' => '',
    'headers' => [
      'MIME-Version' => '1.0',
      'Content-Transfer-Encoding' => 'base64',
      'Content-Type' => 'text/plain; charset=UTF-8',
      'From' => '',
      'Sender' => '',
      'Reply-To' => '',
    ],
    'options' => [
      'subject' => 'ウェブサイトからのお問い合わせ',
      'template' => './templates/send.txt',
      'honeypot_field' => '',
      'auto_reply' => true,
      'auto_reply_to_field' => 'email',
      'auto_reply_headers' => [
        'MIME-Version' => '1.0',
        'Content-Transfer-Encoding' => 'base64',
        'Content-Type' => 'text/plain; charset=UTF-8',
        'From' => '',
        'Sender' => '',
        'Reply-To' => '',
      ],
      'auto_reply_options' => [
        'subject' => 'お問い合わせ内容のご確認',
        'template' => './templates/reply.txt',
      ],
    ],
    'language' => 'Japanese',
    'encoding' => 'UTF-8',
  ];

  public function __construct($settings)
  {
    $this->merged = $this->mergeSettings(self::$defaults, $settings);
  }

  public function mergeSettings($defaults, $settings)
  {
    return array_replace_recursive($defaults, $settings);
  }

  public function get()
  {
    return $this->merged;
  }
}
