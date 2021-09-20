<?php
class Formatter
{
  /**
   * 件名を生成して返す。
   */
  public static function getSubject($post, $base_subject)
  {
    return self::escape(
      str_replace('%s', $post['subject'], $base_subject)
    );
  }

  /**
   * 本文テンプレート文字列に送信データを反映して返す。
   * @param array $post
   * @param string $template_string
   */
  public static function getMailBody($post, $template_string)
  {
    $arranged_post = self::getArrangedPost($post);
    $tags = self::getTagsFromTemplate($template_string);
    $body = self::applyDataToTemplate($arranged_post, $tags, $template_string);
    // 改行文字を調整する。
    $body = strtr($body, [
      "\r\n" => "\n",
      "\r" => "\n",
    ]);

    return self::escape($body);
  }

  /**
   * テンプレートにデータを適用して文字列として返す。
   * @param array $arranged_post 当てはめる値
   * @param array $tags テンプレート内に記述されているの置き換え対象タグ（{$name}）の配列
   * @param string $template
   */
  public static function applyDataToTemplate($arranged_post, $tags, $template_string)
  {
    // 置き換え対象のタグをループさせる。
    return array_reduce($tags, function($body_string, $tag) use ($arranged_post) {
      // $key = タグから'{}'を取り除いた文字列。
      $key = preg_replace('/\{\s*(\S+)\s*\}/', '$1', $tag);

      if ($key === 'all') {
        // {all}タグは、$_POSTの内容をすべて出力する。
        return str_replace($tag, self::getPostList($arranged_post), $body_string);
      }

      if (!isset($arranged_post[$key])) {
        // 送信データにタグの対象がない場合は何も出力しない。
        return str_replace($tag, '', $body_string);
      }

      $value = $arranged_post[$key];
      return str_replace($tag, $value, $body_string);
    }, $template_string);
  }

  /**
   * データの値に応じて、
   * それぞれの値を「出力向きの文字列」に変換し
   * それを値とする新しい配列を生成して返す。
   */
  public static function getArrangedPost($post)
  {
    $arranged_post = [];
    foreach ($post as $key => $value) {
      if (is_array($value)) {
        // 配列の場合は、すべての要素を1つの文字列として連結する。
        $arranged_post[$key] = implode(', ', $value);
      } else if (is_string($value)) {
        $arranged_post[$key] = $value;
      }
    };
    return $arranged_post;
  }

  /**
   * 受け取ったテンプレートから使用されているタグを抽出し、
   * 要素に被りのない配列として返却する。
   * @param string $template_string
   * @return array
   */
  public static function getTagsFromTemplate($template)
  {
    preg_match_all(
      '/\{\s*\S+\s*\}/',
      $template,
      $matches
    );
    return array_unique($matches[0]);
  }

  /**
   * 送信データ内のすべてのキーと値をリスト風の文字列にして返す。
   * @param string $marker
   * @return string
   */
  public static function getPostList($arranged_post, $marker = '* ', $separator = ': ')
  {
    $list = "";
    foreach ($arranged_post as $key => $value) {
      $list .= "\n" . $marker . $key . $separator;
      $list .= $value;
    };
    return trim($list);
  }

  /**
   * 渡された文字列をサニタイズする。
   * @param string string
   * @return string
   */
  public static function escape($string)
  {
    return htmlspecialchars($string);
  }
}
