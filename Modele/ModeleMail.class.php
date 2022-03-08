<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

class ModeleMail
{
    function __construct()
    {
        $this->mail = new PHPMailer();
        $this->mail->IsSMTP();
        $this->mail->CharSet = 'UTF-8';
        $this->mail->SMTPSecure = "tls";
        $this->mail->Host = "smtp.gmail.com";   
        $this->mail->SMTPDebug = 0;          
        $this->mail->SMTPAuth = true;            
        $this->mail->Port = 587;               
        $this->mail->Username = "avquoxx@gmail.com";           
        $this->mail->Password = "lewxkwqshlzinjlu";  
    }

    function sendMail($mail, $subject, $body, $altBody) {
        $this->mail->setFrom('avquoxx@gmail.com', 'Nathan OLIVE');
        $this->mail->addAddress($mail);
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body = $body;
        $this->mail->AltBody = $altBody;
        $this->mail->send();
    }
}
