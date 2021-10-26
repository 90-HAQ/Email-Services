<?php

    class Customer
    {
        // merchent variables
        private $tableName = 'merchent';
        private $name;
        private $email;
        private $image;
        private $pass;
        private $token;
        
        // card variables        
        private $tableNamec = 'card';
        private $crd_card_NO;
        private $crd_credit;
        private $crd_cvc;
        private $crd_valid_from;
        private $crd_valid_through;

        // database variable
        private $dbconn;


        // setting customer values
        function setName($name)               {$this->name = $name;}
        function setEmail($email)             {$this->email = $email;}
        function setPassword($pass)           {$this->pass = $pass;}
        function setImage($image)             {$this->image = $image;}
        function setToken($token)             {$this->token = $token;}
        // getting customer values.
        function getName()                    {return $this->name;}
        function getEmail()                   {return $this->email;}
        function getPassword()                {return $this->pass;}
        function getImage()                   {return $this->image;}
        function getToken()                   {return $this->token;}
        //function getStatus()                  {return $this->status;}


        // setting card values
        function setCardNo($crd_card_NO)                {$this->crd_card_NO = $crd_card_NO;}
        function setCredit($crd_credit)                 {$this->crd_credit = $crd_credit;}
        function setCVC($crd_cvc)                       {$this->crd_cvc = $crd_cvc;}
        function setValidFrom($crd_valid_from)          {$this->crd_valid_from = $crd_valid_from;}
        function setValidThrough($crd_valid_through)    {$this->crd_valid_through = $crd_valid_through;}
        // getting card values
        function getCardNo()                {return $this->crd_card_NO;}
        function getCredit()                {return $this->crd_credit;}
        function getCVC()                   {return $this->crd_cvc;}
        function getValidFrom()             {return $this->crd_valid_from;}
        function getValidThrough()          {return $this->crd_valid_through;}
        
        public function getAllCustomers()
        {
            $sql = "SELECT * FROM " .$this->tableName;
            $result = mysqli_query($this->dbConn, $sql);
            $data = mysqli_fetch_assoc($result);
            return $data;
        }

        function checkUserExist($email)
        {

            // connect to db
            $db = new DbConnect;
            $this->dbconn = $db->build_connection();  

            $sql = "select * from ".$this->tableName." WHERE mr_email = '{$email}'";
            
            $result = $this->dbconn->query($sql) or die(mysqli_error($this->dbconn));
            
            if($result->num_rows > 0)
            {
                //echo "Data already exists.";
                return true;
            }
            else
            {
                //echo "Data does not exists.";
                return false;
            }
        }

        function checkUserCardExist($crd_card_NO)
        {
            // connect to db
            $db = new DbConnect;
            $this->dbconn = $db->build_connection();  

            $sql = "select * from ".$this->tableNamec." WHERE crd_card_No = '{$crd_card_NO}'";
            
            $result = $this->dbconn->query($sql) or die(mysqli_error($this->dbconn));
            
            if($result->num_rows > 0)
            {
                //echo "Data already exists.";
                return true;
            }
            else
            {
                //echo "Data does not exists.";
                return false;
            }
        }

        public function insertCustomerData()
        {
            // connect to db
            $db = new DbConnect;
            $this->dbconn = $db->build_connection();  

            $sql = "INSERT INTO " .$this->tableName. " (mr_name, mr_email, mr_password, mr_status) VALUES
            ('$this->name', '$this->email', '$this->pass', 0)";

            // to check what value is in sql query that is to e executed.
            //print_r($sql);

            // will tell rather mysqli query is executed or not
            // if not then display error
            $result = mysqli_query($this->dbconn, $sql) or die(mysqli_error($this->dbconn));

            if($result)
            {
                //echo "Data inserted successfully.";
                return 1;
            }
            else
            {
                //echo "Something went wrong will inserting data.";
                return 0;
            }
        }

        public function addTokenToCustomerData($email)
        {
            // connect to db
            $db = new DbConnect;
            $this->dbconn = $db->build_connection();  

            $sql1 = "UPDATE merchent SET mr_token = '{$this->token}' WHERE mr_email = '{$email}'";

            $sql2 = "UPDATE merchent SET mr_status = '1' WHERE mr_email = '{$email}'";

            $sql3 = "UPDATE merchent SET mr_create_time = current_timestamp() WHERE mr_email = '{$email}'";

            $sql4 = "UPDATE merchent SET mr_current_time = current_timestamp() WHERE mr_email = '{$email}'";


            // check sql query
            //print_r($sql);

            $check1 = false;
            $check1 = false;
            $check1 = false;
            $check1 = false;
        
            
            if(mysqli_query($this->dbconn, $sql1))  {$check1 = true;} //echo "token updated"
            if(mysqli_query($this->dbconn, $sql2))  {$check2 = true;} //echo "status updated"
            if(mysqli_query($this->dbconn, $sql3))  {$check3 = true;} //echo "create_time updated"
            if(mysqli_query($this->dbconn, $sql4))  {$check4 = true;} //echo "create_time updated"

            if($check1 == true && $check2 == true && $check3 == true && $check4 == true)
            { 
                //echo "Updated Variables in DB.";
                return 1;
            }
            else
            {
                return 0;
            }


        }

        public function getMerchentID($merchent_email)
        {
            // connect to db
            $db = new DbConnect;
            $this->dbconn = $db->build_connection();  

            $sql = "select * from merchent WHERE mr_email = '{$merchent_email}'";

            //print_r($sql);
            
            $result = $this->dbconn->query($sql) or die(mysqli_error($this->dbconn));
            
            if($result->num_rows > 0)
            {
                //echo "Data already exists.";
                // get merchent-id from merchent table
                $data = $result->fetch_assoc();
                $m_id = $data['mr_id'];
                return $m_id;
            }
            else
            {
                echo "Email does not exists.";
                exit();
            }
        }


        public function insertCustomerCardData($m_id)
        {
            // connect to db
            $db = new DbConnect;
            $this->dbconn = $db->build_connection();  

            $sql = "INSERT INTO " .$this->tableNamec. " (crd_merchent_id, crd_card_NO, crd_credit, crd_cvc, crd_valid_from, crd_valid_through) VALUES
            ('$m_id', '$this->crd_card_NO', '$this->crd_credit', '$this->crd_cvc', '$this->crd_valid_from', '$this->crd_valid_through')";

            // to check what value is in sql query that is to e executed.
            //print_r($sql);

            // will tell rather mysqli query is executed or not
            // if not then display error
            $result = mysqli_query($this->dbconn, $sql) or die(mysqli_error($this->dbconn));

            if($result)
            {
                //echo "Data inserted successfully.";
                return 1;
            }
            else
            {
                //echo "Something went wrong will inserting data.";
                return 0;
            }
        }


        public function findTokeninDB($token)
        {

            // connect to db
            $db = new DbConnect;
            $this->dbconn = $db->build_connection();  

            // check token value from 
            //echo $token;

            $sql = "SELECT * FROM merchent WHERE mr_token = '{$token}'";

            //print_r($sql);
            $result = mysqli_query($this->dbconn, $sql) or die(mysqli_error($this->dbconn));
            //

            if($result)
            {
                //echo "Token Found.";
                return 1;
            }
            else
            {
                //echo "Token Not Found.";
                return 0;
            }
        }


        public function deleteCustomerData()
        {
            $sql = "DELETE FROM ' .$this->tableName. ' WHERE id = {'$this->id'} ";

            $result = mysqli_query($this->dbconn, $sql) or die('failed');

            if($result)
            {
                echo "Data deleted successfully.";
                return 1;
            }
            else
            {
                echo "Something went wrong will deleted data.";
                return 0;
            }
        }
    }
?>