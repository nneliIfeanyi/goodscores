<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require APPROOT . '/views/PHPMailer/src/Exception.php';
require APPROOT . '/views/PHPMailer/src/PHPMailer.php';
require APPROOT . '/views/PHPMailer/src/SMTP.php';

function sendMail($reciever)
{
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 2;
    $mail->SMTPSecure = 'tls';
    $mail->isSMTP();
    $mail->Host = 'live.smtp.mailtrap.io';
    $mail->SMTPAuth = true;
    $mail->Port = 587;
    $mail->Username = 'api';
    $mail->Password = '0dcbca9704cf4ae1be561df980807ca3';


    $mail->setFrom('goodscores@stanvic.com.ng', 'Good Scores');
    $mail->addAddress($reciever);
    //$mail->addAddress('receiver2@gfg.com', 'Name');

    $mail->isHTML(true);
    $mail->Subject = 'Email Verify';
    $mail->Body =
        "<div style='text-align:left;background-color:#f6f9ff;padding:0 10px 20px 10px;'>
            <h1 style='color:#0d6efd;padding-top: 10px;border-radius:6px;'>A warm welcome to GoodScores</h1>
        <p><b>Hello there,</b><br>
                My name is Victor, and I'm the CEO of GoodScores.
                Its Fantastic to have you onboard!</p>
            <p style='font-size:16px;'>
                Please click the button below to register a user
                either your school administrator or teachers<br>
                <b>Note that: </b><span>You can register as many users as possible</span><br><br>
                <a style='text-decoration:none;padding: 7px 12px;background-color:#0d6efd;color:antiquewhite;border-radius:10px;' href='https://goodscores.stanvic.com.ng/pages/verify_email/$reciever?email=$reciever'>Verify your email</a
            </p><br><br><br>
            <p>It's fantastic to have you!<br>
            Nneli Ifeanyi Victor<br>
            CEO <b>GoodScores</b></p><br><br><br>
            </div>";
    $mail->AltBody = 'A warm welcome to GoodScores, its fantastic to have you on board, navigate back to the page to continue..';
    if ($mail->send()) {
        echo "Mail has been sent successfully!";
    } else {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}


function sendMailToAdmin($reciever, $name, $phone, $course)
{
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 2;
        $mail->SMTPSecure = 'tls';
        $mail->isSMTP();
        $mail->Host = 'live.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 587;
        $mail->Username = 'api';
        $mail->Password = '0dcbca9704cf4ae1be561df980807ca3';


        $mail->setFrom('coding@stanvic.com.ng', 'Stanvic Coding Academy');
        $mail->addAddress($reciever);
        //$mail->addAddress('receiver2@gfg.com', 'Name');

        $mail->isHTML(true);
        $mail->Subject = 'Course Registration';
        $mail->Body =
            "<div style='text-align:center;background-color:antiquewhite;padding-bottom:20px;'>
            <h1 style='color:antiquewhite;padding: 28px;border-bottom:2px solid #ffc107;background-color:black;border-radius:6px;'>Stanvic Coding Academy</h1>
        
            <p style='font-size:21px;'>
                Someone just registered by name:$name, phone: $phone, course enrolled: $course.
                
            </p>
            
            </div>";
        //$mail->AltBody = 'Body in plain text for non-HTML mail clients';
        $mail->send();
        // echo "Mail has been sent successfully!";
    } catch (Exception $e) {
        // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
