<?php
return [
  'to' => 'to@test.test',
  'headers' => [
    'From' => 'Website <no-reply@test.test>',
    'Sender' => 'Website <no-reply@test.test>',
    'Reply-To' => 'Website <no-reply@test.test>',
  ],
  'options' => [
    'subject' => 'メールフォームからのお問い合わせ（%s）',
    /**
     * 本文用テンプレートファイルのパス
     */
    'template' => 'send.txt',
    /**
     * ハニーポット
     */
    'honeypot_field' => 'name',
    'auto_reply' => true,
    'auto_reply_to_field' => 'email',
    'auto_reply_headers' => [
      'MIME-Version' => '1.0',
      'Content-Transfer-Encoding' => 'base64',
      'Content-Type' => 'text/plain; charset=UTF-8',
      'From' => 'Website Mail Form <no-reply@test.test>',
      'Sender' => 'Website Mail Form <no-reply@test.test>',
      'Reply-To' => 'Website Mail Form <no-reply@test.test>',
    ],
    'auto_reply_options' => [
      'subject' => 'お問い合わせ内容のご確認',
      'template' => 'reply.txt',
    ],
  ],
];
