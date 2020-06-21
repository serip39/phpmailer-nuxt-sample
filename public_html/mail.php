<?php

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
$params = json_decode(file_get_contents('php://input'), true);

$client_name = $params['name'];
$client_email = $params['email'];
$client_context = $params['contents'];

//.envの保存場所指定（カレントに設定）
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// envで取得した後に、最初と最後の'"'が文字とみなされるので削除
function strTrim ($str) {
  $str = ltrim($str, '"');
  $str = rtrim($str, '"');
  return $str;
}

$USER_NAME = strTrim($_ENV['USER_NAME']);
$USER_PASSWORD = strTrim($_ENV['USER_PASSWORD']);

$mail = new PHPMailer(true);
try {
  $host = strTrim($_ENV['HOST']);

  //差出人
  $from = $USER_NAME;
  $fromname = '株式会社XXX';

  // 送信先
  $info_mail_address = strTrim($_ENV['INFO_MAIL']);
  $info_name = 'HPからのお問い合わせ';

  //本文
  $form_data = "【お名前】\r\n{$client_name}\r\n【メールアドレス】\r\n{$client_email}\r\n【お問い合わせ内容】\r\n{$client_context}\r\n";
  $form_data_html = "<dl>
    <dt>【お名前】</dt>
    <dd>{$client_name}</dd>
    <dt>【メールアドレス】</dt>
    <dd>{$client_email}</dd>
    <dt>【お問い合わせ内容】</dt>
    <dd>{$client_context}</dd>
  </dl>";

  $client_message = "{$client_name} 様\r\n\r\nお世話になっております。株式会社XXXへのお問い合わせありがとうございました。\r\n2営業日以内に、ご連絡させていただきます。\r\n\r\n※このメールはシステムからの自動返信です。\r\nhttps://test.com\r\n\r\n----------------\r\n";
  $client_message_html = "<p>{$client_name} 様</p>
  <p>お世話になっております。株式会社XXXへのお問い合わせありがとうございました。</p>
  <p>2営業日以内に、ご連絡させていただきます。</p>
  <p>※このメールはシステムからの自動返信です。</p>
  <a>https://test.com</a>
  <hr>";

  $info_bottom = "----------------\r\nこのメールは、株式会社XXX(https://test.com)のお問い合わせフォームから送信されました。";
  $info_bottom_html = "<hr>
  <p>このメールは、株式会社XXX(https://test.com)のお問い合わせフォームから送信されました。</p>";

  //メール設定
  $mail->SMTPDebug = 2; //デバッグ用
  $mail->isSMTP();
  $mail->SMTPAuth = true;
  $mail->Host = $host;
  $mail->SMTPSecure = 'tls';
  $mail->Port = 587;
  $mail->Username = $USER_NAME;
  $mail->Password = $USER_PASSWORD;
  $mail->CharSet = "utf-8";
  $mail->Encoding = "base64";
  $mail->setFrom($from, $fromname);

  // 自動返信メール
  $mail->addAddress($client_email, $client_name);
  $mail->isHTML(true);
  $mail->Subject = '株式会社XXX: お問い合わせいただきありがとうございます。';
  $client_message = $client_message;
  $client_message .= $form_data;
  $client_message_html = $client_message_html;
  $client_message_html .= $form_data_html;
  $mail->Body = $client_message_html;
  $mail->AltBody = $client_message;
  $mail->send();

  // 宛先は一度、Reset
  $mail->ClearAddresses();

  // infoへのメール
  $mail->addAddress($info_mail_address, $info_name);
  $mail->addReplyTo($client_email, $client_name);
  $mail->Subject = $client_name . '様【HPからのお問い合わせ】';
  $mail->isHTML(true);
  $info_message = $form_data;
  $info_message .= $info_bottom;
  $info_message_html = $form_data_html;
  $info_message_html .= $info_bottom_html;
  $mail->Body = $info_message_html;
  $mail->AltBody = $info_message;
  $mail->send();
  echo 'success';

} catch (Exception $e) {
  echo 'failed: ', $mail->ErrorInfo;
}
