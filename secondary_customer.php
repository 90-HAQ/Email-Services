<?php

    class Secondary_Customer
    {
        private $sru_name;
        private $sru_email;
        private $sru_pass;
        private $sru_token;
        private $sru_email_permission;
        private $sru_list_view_permission;
        private $sru_payment_permission;
        private $tableName = 'secondary_user';
        private $dbconn;

        // setting customer values

        
        function setName($sru_name)                                  {$this->sru_name = $sru_name;}
        function setEmail($sru_email)                                {$this->sru_email = $sru_email;}
        function setPassword($sru_pass)                              {$this->sru_pass = $sru_pass;}    
        function setEmailPermission($sru_email_permission)           {$this->sru_email_permission = $sru_email_permission;}
        function setListViewPermission($sru_list_view_permission)    {$this->sru_list_view_permission = $sru_list_view_permission;}
        function setPaymentPermission($sru_payment_permission)       {$this->sru_payment_permission = $sru_payment_permission;}
        
        // getting customer values.
        
        function getName()                    {return $this->sru_name;}
        function getEmail()                   {return $this->sru_email;}
        function getPassword()                {return $this->sru_pass;}
        function getToken()                   {return $this->sru_token;}
        function getEmailPermission()         {return $this->sru_email_permission;}
        function getListViewPermission()      {return $this->sru_list_view_permission;}
        function getPaymentPermission()       {return $this->sru_payment_permission;}

        
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
                $email = $data['mr_id'];
                return $email;
            }
            else
            {
                echo "Email does not exists.";
            }
        }


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

            $sql = "select * from ".$this->tableName." WHERE sru_email = '{$email}'";
            
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


        public function insertCustomerData($merchecntID)
        {

            // connect to db
            $db = new DbConnect;
            $this->dbconn = $db->build_connection();  

            $mid = $merchecntID;

            $sql = "INSERT INTO " .$this->tableName. " (sru_merchant_id, sru_name, sru_password, sru_email, sru_status, sru_email_permission, sru_list_view_permission, sru_payment_permission) VALUES
            ('$mid','$this->sru_name', '$this->sru_pass', '$this->sru_email', 1,'$this->sru_email_permission', '$this->sru_list_view_permission', '$this->sru_payment_permission')";

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