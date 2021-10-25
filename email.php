
<?php
$to_email = "hussainhashmi1426@gmail.com";
$subject = "Simple Email Test via PHP";
$body = "Hi, My name is Hussain Ali, this is test email send by PHP Script. I am sending this email to you from xampp localhost, if you recieve this email please reply back with message (recived). Thank you...!!!";
$headers = "From: sender\'s email";

if (mail($to_email, $subject, $body, $headers)) 
{
    echo "Email successfully sent to $to_email...";
} 
else 
{
    echo "Email sending failed...";
}

?>