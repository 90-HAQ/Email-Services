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
        if(!$validate->email_validate($email))  
        {
            $check1=false; 
        }   

        // validating password
        if(!$validate->password_validate($pass))  
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

                if($data['mr_status'] == 0)
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
                        'userId' => $data['mr_email']
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
        $status = $this->param["status"];

        // check sign up credentials
        // echo $name;
        // echo $email;
        // echo $pass;
        // echo $status;

        // customer class object.
        $cust = new Customer;

        // check if already exists
        $exists = $cust->checkUserExist($email);
        

        if($exists == true)
        {
            echo "1";
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

            
            
            if(!$validate->name_validate($name))      { return $check1 = false; }
            
            

            if(!$validate->email_validate($email))    { return $check2 = false; }
            
            

            if(!$validate->password_validate($pass))  { return $check3 = false; }

            
            if($check1 == false && $check2 == false && $check3 == false)
            {
                
                $this->returnResponse(VALIDATE_PARAMETER_REQUIRED, "Please provide correct credentials.");
                
            }
            else
            {
                // now insert data into db.

                //echo "login credentials approved";
                
                        
                $cust->setName($name);
                $cust->setEmail($email);
                $cust->setPassword($pass);
                $cust->setStatus($status);
                
                $insert = $cust->insertCustomerData();
                
                // check the insert value
                //echo $insert;

                if($insert == 0)
                {
                    $message = 'Failed to insert.';
                }
                else
                {
                    $message = "Inserted successfully.";
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
            if(!$validate->name_validate($name))      { return $check1 = false; } 
            // check email validation
            if(!$validate->email_validate($email))    { return $check2 = false; }
            // check password validation
            if(!$validate->password_validate($pass))  { return $check3 = false; }

            
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
        if(!$validate->email_validate($email))  
        {
            $check1=false; 
        }   

        // validating password
        if(!$validate->password_validate($pass))  
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

            print_r($q1);

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


        $to_email = $this->param['to_email'];
        $subject = $this->param['subject'];
        $body = $this->param['body'];
        $headers = "From: sender\'s email";

        $user_email = new Send_Email_To_Customer();

        $user_email->setToEmail($to_email);
        $user_email->setSuject($subject);
        $user_email->setBody($body);
        $user_email->setHeaders($headers);


        $user_email->send_email();

        // $to_email = "hussainhashmi1426@gmail.com";
        // $subject = "Simple Email Test via PHP";
        // $body = "Hi, My name is Hussain Ali, this is test email send by PHP Script. I am sending this email to you from xampp localhost, if you recieve this email please reply back with message (recived). Thank you...!!!";
        // $headers = "From: sender\'s email";


    }
}

?>