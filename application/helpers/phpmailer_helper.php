<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    require 'phpmailer/PHPMailerAutoload.php';

    if ( ! function_exists('mailSend'))
    {
        function mailSend($subject, $sendto, $content, $attachment = '',$cc='')
        {
            // Get CI Instance
            $CI = get_instance();
    //         print_r($cc);
    // exit();
            $mail = new PHPMailer;

            // Settingan tambahan untuk versi php 5.6 ke atas
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                )
            );

            // $mail->SMTPDebug = 3;                                    // Enable verbose debug output

            $mail->isSMTP();                                            // Set mailer to use SMTP
            $mail->Host       = $CI->config->item('host','email');      // Specify main and backup SMTP servers
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = $CI->config->item('username','email');  // SMTP username
            $mail->Password   = $CI->config->item('password','email');  // SMTP password
            $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = $CI->config->item('port','email');      // TCP port to connect to

            $mail->setFrom($CI->config->item('username','email'), 'ASDP Ticketing System');
            $mail->addAddress($sendto);                                 // Add a recipient
            // $mail->addAddress('ellen@example.com');                  // Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            $mail->addCC($cc);
            // $mail->addBCC('bcc@example.com');

            $mail->addAttachment($attachment);                          // Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');       // Optional name
            $mail->isHTML(true);                                        // Set email format to HTML

            $mail->Subject = $subject;
            $mail->Body    = $content;
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            if(!$mail->send()) {
                /*echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;*/
                return 0;
            } else {
                // echo 'Message has been sent';
                return 1;
            }
        }
    }
