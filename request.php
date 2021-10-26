<?php

class Request
{
    private $req_response_id;
    private $req_merchent_id;
    private $req_email_subject;
    private $req_email_from;
    private $req_send_to;
    private $req_cc;
    private $req_bcc;
    private $req_email_body;

    // setting values of request
    function setResponseID($req_response_id)        {$this->$req_response_id = $req_response_id;}
    function setMerchentID($req_merchent_id)        {$this->req_merchent_id = $req_merchent_id;}
    function setEmailSuject($req_email_subject)     {$this->req_email_subject = $req_email_subject;}
    function setEmailFrom($req_email_from)          {$this->req_email_from = $req_email_from;}
    function setSendTo($req_send_to)                {$this->req_send_to = $req_send_to;}
    function setBCC($req_bcc)                       {$this->req_bcc = $req_bcc;}
    function setCC($req_cc)                         {$this->req_cc = $req_cc;}
    function setEmailBody($req_email_body)          {$this->req_email_body = $req_email_body;}   

    // getting values of request
    function getResponseID()             {return $this->req_response_id;}
    function getMerchentID()             {return $this->req_merchent_id;}
    function getEmailSuject()            {return $this->req_email_subject;}
    function getEmailFrom()              {return $this->req_email_from;}
    function getSendTo()                 {return $this->req_send_to;}
    function getBcc()                    {return $this->req_bcc;}
    function getCC()                     {return $this->req_cc;}
    function getEmailBody()              {return $this->req_email_body;}

    public function generateRequest()
    {
        
    }

}


?>