<?php
class Settings
{
  public $base_url = '';
  public $the_settings = [];
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
      'subject' => 'Contact from the website.',
      'template' => 'send.txt',
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
        'subject' => 'Thank you for your contact.',
        'template' => 'reply.txt',
      ],
    ],
    'language' => 'Japanese',
    'encoding' => 'UTF-8',
  ];

  public function __construct($setting_name, $base_url)
  {
    $this->base_url = $base_url;
    $this->loadSettings($setting_name);
    $this->merged = $this->mergeSettings(self::$defaults, $this->the_settings);
  }

  public function loadSettings($setting_name)
  {
    $setting_file_name = $this->base_url . '/settings/' . $setting_name . '.php';
    if (!file_exists($setting_file_name)) {
      return;
    }
    $this->the_settings = include($setting_file_name);
  }

  public function mergeSettings($defaults, $settings)
  {
    $merged = array_replace_recursive($defaults, $settings);
    return $merged;
  }

  public function get()
  {
    return $this->merged;
  }
}
