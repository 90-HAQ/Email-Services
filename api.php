<?php

// shows database errors
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *"); 
// allows everyone to access your rest-api

header("Access-Control-Allow-Headers: access"); 
// all header access is allowed 

header("Access-Control-Allow-Methods: POST"); 
//header used to insert data

header("Content-Type: application/json"); 
// used to return json format

header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); 
// the names of the headers that we will be used

include 'validation.php';
include 'customer.php';
include 'send_email.php';


class Api extends Rest
{

    public $dbConn;

    public function __construct()
    {
        parent::__construct();
    }

    public function customer_login()
    {
        // to check if it is working or not.
        //print_r($this->param);

        $email = $this->param['email'];
        $pass = $this->param['pass'];

        //check if we are getting email .
        //echo $email;
        //check if we are getting password. 
        //echo $pass;

        // check validation method 
        $validate = new Validate;

        $check1 = true;
        $check2 = true;

        // validating email                                           
        if(!$validate->Email_Validate($email))  
        {
            $check1=false; 
        }   

        // validating password
        if(!$validate->Password_Validate($pass))  
        { 
            $check2=false; 
        }  

        //check value in checks for confirmation.
        //echo ($check1."<br>");
        //echo ($check2."<br>");

        if($check1 == false && $check2 == false)
        {
            $this->returnResponse(VALIDATE_LOGIN_CREDENTIALS, "Please provide correct login credentials.");
        }
        else
        {
            //echo "login credentials approved";

            // build db connection
            $db = new DbConnect;
            $this->dbConn = $db->build_connection();


            $q1 = "SELECT * FROM merchent WHERE mr_email ='{$email}' AND mr_password ='{$pass}'";


            $result1 = mysqli_query($this->dbConn, $q1);

            if (mysqli_num_rows($result1) > 0) 
            {                
                $data = mysqli_fetch_assoc($result1);

                //echo "ok";
                //print_r($data);

                $payload = 
                [
                    // issue at when you have issued it / when the token is generated
                    'iat' => time(),
                    // who has issued it / issuer
                    'iss' => 'localhost',
                    // expiery / when this token should expire
                    // so that we can use time function / the current timestamp() + (60)sec it will be valid for 1-minute
                    // ['1-minute'=> 'time()+(60)', '15-minutes'=>'time()+(15*60)'];
                    // ['30-minutes'=>'time()+(30*60)'];
                    'exp' => time() + (30*60), 
                    // public data
                    'userId' => $data['mr_email']
                ];
                $token = JWT::encode($payload, SECRETE_KEY);
                
                // to check if token is fenerated or not 
                //echo $token;

                // to check if jwt token gets value or not.
                if(!$token == "")
                {
                    // now insert customer value into customer urf (merchent) table
                    $customer = new Customer();
                    $customer->setToken($token);

                    $tk = $customer->addTokenToCustomerData($email);

                    if($tk == 1)
                    {

                        //echo "Token value updated in Database.";    
                        $dvalue = ['token' => $token];
                        $this->returnResponse(SUCCESS_RESPONSE, $dvalue);

                    }
                    else
                    {
                        echo "Email does not exists to store token value.";
                        exit();
                    }
                }
                else
                {
                    $this->returnResponse(JWT_PROCESSING_ERROR, "Error getting JWT Token Value.");
                }
            }
            else 
            {
                $this->returnResponse(INVALID_USER_PASS, "Email or Password is incorrect.");
            }
            
        }
    }

    public function insert_Customer()
    {

        // to check if it is working or not.
        //print_r($this->param);


        // connect to db
        $db = new DbConnect;
        $this->dbConn = $db->build_connection();   
        
        // get data from postman
        $name = $this->param["name"];
        $email = $this->param["email"];
        $pass = $this->param["pass"];
        //$image = $this->param["image"];

        $crd_card_NO = $this->param["card_no"];
        $crd_credit = $this->param["card_credit"];
        $crd_cvc = $this->param["card_cvc"];
        $crd_valid_from = $this->param["card_valid_from"];
        $crd_valid_through = $this->param["card_valid_through"];

        // check sign up credentials and card credentials alloted to respective variables
        // echo $name;
        // echo $email;
        // echo $pass;
        // echo $crd_card_NO;
        // echo $crd_credit;
        // echo $crd_cvc;
        // echo $crd_valid_from;
        // echo $crd_valid_through; 

        

        // customer class object.
        $cust = new Customer;

        // check if already exists
        $exists1 = $cust->checkUserExist($email);
        $exists2 = $cust->checkUserCardExist($crd_card_NO);

        echo $exists1;
        echo $exists2;
        
        if($exists1 == true)
        {
            //echo "User Exitence Message : ";
            $this->returnResponse(USER_ALREADY_EXISTS, "User already exists.");
            exit();
        }
        if($exists2 == true)
        {
            //echo "Card Number Exitence Message : ";
            $this->returnResponse(CARD_NO_ALREADY_EXISTS, "Card Number already exists.");
            exit();
        }
        else
        {        
            // check validation method 
            $validate = new Validate();
            
            $check1 = true;
            $check2 = true;
            $check3 = true;
            $check4 = true;
            $check5 = true;
            $check6 = true;

            if(!$validate->Name_Validate($name))                { return $check1 = false; } // check name validation
            if(!$validate->Email_Validate($email))              { return $check2 = false; } // check email validation
            if(!$validate->Password_Validate($pass))            { return $check3 = false; } // check password validation
            if(!$validate->Card_NO_Validate($crd_card_NO))      { return $check4 = false; } // check card_no validation
            if(!$validate->CVC_Validate($crd_cvc))              { return $check5 = false; } // check cvc validation
            if(!$validate->Credit_Validate($crd_credit))        { return $check5 = false; } // check credit validation
            
            
            

            if($check1 == false && $check2 == false && $check3 == false && $check4 == false && $check5 == false && $check6 == false)
            {    
                $this->returnResponse(VALIDATE_PARAMETER_REQUIRED, "Please provide correct credentials.");   
            }
            else
            {

                //echo "login credentials approved";                        
                $cust->setName($name);
                $cust->setEmail($email);
                $cust->setPassword($pass);
                
                $insert1 = $cust->insertCustomerData();

                $m_id = $cust->getMerchentID($email);

                $cust->setCardNo($crd_card_NO);
                $cust->setCredit($crd_credit);
                $cust->setCVC($crd_cvc);
                $cust->setValidFrom($crd_valid_from);
                $cust->setValidThrough($crd_valid_through); 

                $insert2 = $cust->insertCustomerCardData($m_id);

                
                // check the insert value
                //echo $insert1;
                //echo $insert2;

                if($insert1 == 0 && $insert2 == 0)
                {
                    $message = 'Failed to insert signup details and Card Details .';
                }
                else
                {
                    $message = "User Signedup and Card Details inserted successfully.";                    
                }

                $this->returnResponse(SUCCESS_RESPONSE, $message);
            }
        }
    }

    public function insert_secondary_user()
    {
        // to check if it is working or not.
        //print_r($this->param);


        // connect to db
        $db = new DbConnect;
        $this->dbConn = $db->build_connection();   
        
        // get data from postman
        $merchent_email = $this->param["sru_merchent_email"];
        $name = $this->param["sru_name"];
        $pass = $this->param["sru_pass"];
        $email = $this->param["sru_email"];
        $email_permission = $this->param["sru_email_permission"];
        $list_view_permission = $this->param["sru_list_view_permission"];
        $payment_permission = $this->param["sru_payment_permission"];


        $sec_user = new Secondary_Customer();
        

        $mid = $sec_user->getMerchentID($merchent_email);
        //echo "Merchent id is :".$mid;

        // check if user alread exists or not.
        $exists = $sec_user->checkUserExist($email);
        if($exists == true)
        {
            //echo "1";
            $this->returnResponse(USER_ALREADY_EXISTS, "User already exists.");
            exit();
        }
        else
        {
            
            // check validation method 
            $validate = new Validate();
            
            $check1 = true;
            $check2 = true;
            $check3 = true;
            
            // check name validation
            if(!$validate->Name_Validate($name))      { return $check1 = false; } 
            // check email validation
            if(!$validate->Email_Validate($email))    { return $check2 = false; }
            // check password validation
            if(!$validate->Password_Validate($pass))  { return $check3 = false; }

            
            if($check1 == false && $check2 == false && $check3 == false)
            {
                
                $this->returnResponse(VALIDATE_PARAMETER_REQUIRED, "Please provide correct credentials.");
                
            }
            else
            {

                //echo "login credentials approved";

                $sec_user->setName($name);
                $sec_user->setEmail($email);
                $sec_user->setPassword($pass);
                $sec_user->setEmailPermission($email_permission);
                $sec_user->setListViewPermission($list_view_permission);
                $sec_user->setPaymentPermission($payment_permission);

                $insert = $sec_user->insertCustomerData($mid);
                
                // check the insert value
                //echo $insert;

                if($insert == 0)
                {
                    $message = 'Failed to Insert Secondary User.';
                }
                else
                {
                    $message = "Inserted Secondary User Successfully.";
                }

                $this->returnResponse(SUCCESS_RESPONSE, $message);
            }
        }
    }


    public function secondary_customer_login()
    {
        // to check if it is working or not.
        //print_r($this->param);

        $email = $this->param['email'];
        $pass = $this->param['pass'];

        //check if we are getting email .
        //echo $email;
        //check if we are getting password. 
        //echo $pass;

        // check validation method 
        $validate = new Validate;

        $check1 = true;
        $check2 = true;

        // validating email                                           
        if(!$validate->Email_Validate($email))  
        {
            $check1=false; 
        }   

        // validating password
        if(!$validate->Password_Validate($pass))  
        { 
            $check2=false; 
        }  

        //check value in checks for confirmation.
        //echo ($check1."<br>");
        //echo ($check2."<br>");

        if($check1 == false && $check2 == false)
        {
            $this->returnResponse(VALIDATE_LOGIN_CREDENTIALS, "Please provide correct login credentials.");
        }
        else
        {
            //echo "login credentials approved";

            // build db connection
            $db = new DbConnect;
            $this->dbConn = $db->build_connection();


            $q1 = "SELECT * FROM secondary_user WHERE sru_email ='{$email}' AND sru_password ='{$pass}'";

            //print_r($q1);

            $result1 = mysqli_query($this->dbConn, $q1);

            if (mysqli_num_rows($result1) > 0) 
            {                
                $data = mysqli_fetch_assoc($result1);

                if($data['sru_status'] == 0)
                {
                    $this->returnResponse(USER_NOT_ACTIVE, "This user account is not active right now, please contact the admin.");
                }
                else
                {
                    //echo "ok";
                    //print_r($data);

                    $payload = 
                    [
                        // issue at when you have issued it / when the token is generated
                        'iat' => time(),
                        // who has issued it / issuer
                        'iss' => 'localhost',
                        // expiery / when this token should expire
                        // so that we can use time function / the current timestamp() + (60)sec it will be valid for 1-minute
                        // ['1-minute'=> 'time()+(60)', '15-minutes'=>'time()+(15*60)'];
                        // ['30-minutes'=>'time()+(30*60)'];
                        'exp' => time() + (30*60), 
                        // public data
                        'userId' => $data['sru_email']
                    ];
                    $token = JWT::encode($payload, SECRETE_KEY);
                    
                    // to check if toek is fenerated or not 
                    //echo $token;

                    // to check if jwt token gets value or not.
                    if(!$token == "")
                    {
                        $dvalue = ['token' => $token];
                        $this->returnResponse(SUCCESS_RESPONSE, $dvalue);
                    }
                    else
                    {
                        $this->returnResponse(JWT_PROCESSING_ERROR, "Error getting JWT Token Value.");
                    }
                }
            }
            else 
            {
                $this->returnResponse(INVALID_USER_PASS, "Email or Password is incorrect.");
            } 
        }
    }



    public function send_email_to_customer()
    {

        // to check if it is working or not.
        //print_r($this->param);

        $token = $this->param['token'];
        $to_email = $this->param['to_email'];
        $subject = $this->param['subject'];
        $body = $this->param['body'];
        $headers = "From: sender\'s email";


        if(isset($token))
        {

            $find_token = new Customer();
            $get_token = $find_token->findTokeninDB($token);

            if($get_token == 1)
            {
                $user_email = new Send_Email_To_Customer();
                $user_email->setToEmail($to_email);
                $user_email->setSuject($subject);
                $user_email->setBody($body);
                $user_email->setHeaders($headers);
        
                $user_email->send_email();
            }
            else
            {
                $this->returnResponse(ACCESS_TOKEN_ERROR_DOES_NOT_EXIST, "Token does not exist in database.");
            }
        }
        else
        {
            //echo "Token is not set..!!!";
            $this->returnResponse(ACCESS_TOKEN_ERRORS, "Token is not set..!!!");            
        }
              
    }
}

?>