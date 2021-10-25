<?php

class Send_Email_To_Customer
{
    private $to_email;
    private $subject;
    private $body;
    private $headers;

    public function setToEmail($to_email)    {$this->to_email = $to_email;}
    public function setSuject($subject)      {$this->subject = $subject;}
    public function setBody($body)           {$this->body = $body;}
    public function setHeaders($headers)     {$this->headers = $headers;}

    public function getToEmail()        {return $this->to_email;}
    public function getSubject()        {return $this->subject;}
    public function getBody()           {return $this->body;}
    public function getHeaders()        {return $this->headers;}

    public function send_email()
    {
        $to_email = $this->getToEmail();
        $subject = $this->getSubject();
        $body = $this->getBody();
        $headers = $this->getHeaders();

        if (mail($to_email, $subject, $body, $headers)) 
        {
            echo "Email successfully sent to $to_email...";
        } 
        else 
        {
            echo "Email sending failed...";
        }
    }

}

?>