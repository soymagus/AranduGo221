<?php
declare(strict_types=1);

namespace AranduGo;

use PHPMailer\PHPMailer\PHPMailer;

final class Mailer
{
    public static function send(string $to, string $replyTo, string $replyName, string $subject, string $html, string $text): bool
    {
        $cfg=Support::config()['mail']; $mail=new PHPMailer(true); $mail->CharSet='UTF-8'; $transport=$cfg['transport'] ?? 'mail';
        try {
            if ($transport === 'smtp') {
                $smtp=$cfg['smtp']; $mail->isSMTP(); $mail->Host=$smtp['host']; $mail->Port=(int)$smtp['port']; $mail->SMTPAuth=!empty($smtp['username']); $mail->Username=$smtp['username']??''; $mail->Password=$smtp['password']??'';$security=strtolower((string)($smtp['security']??''));if($security==='ssl')$mail->SMTPSecure=PHPMailer::ENCRYPTION_SMTPS;elseif(in_array($security,['tls','starttls'],true))$mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS;else{$mail->SMTPSecure='';$mail->SMTPAutoTLS=false;}
            } elseif ($transport === 'sendmail') { $mail->isSendmail(); $mail->Sendmail=$cfg['sendmail_path'] ?: '/usr/sbin/sendmail -bs'; }
            else { $mail->isMail(); }
            $mail->setFrom($cfg['from_email'],$cfg['from_name']); $mail->addAddress($to); $mail->addReplyTo($replyTo,$replyName); $mail->isHTML(true); $mail->Subject=$subject; $mail->Body=$html; $mail->AltBody=$text; return $mail->send();
        } catch (\Throwable $e) { error_log('Arandu Go mail: '.$e->getMessage()); return false; }
    }
}
