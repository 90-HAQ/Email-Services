<?php

    class Customer
    {

        private $name;
        private $email;
        private $image;
        private $pass;
        private $token;
        private $status;
        private $tableName = 'merchent';
        private $dbconn;

        // default constructor
        public function __construct()
        {
            
        }

        // setting customer values

        function setName($name)               {$this->name = $name;}
        function setEmail($email)             {$this->email = $email;}
        function setPassword($pass)           {$this->pass = $pass;}
        function setImage($image)             {$this->image = $image;}
        function setToken($token)             {$this->token = $token;}
        function setStatus($status)           {$this->status = $status;}
        
        // getting customer values.
        function getName()                    {return $this->name;}
        function getEmail()                   {return $this->email;}
        function getPassword()                {return $this->pass;}
        function getImage()                   {return $this->image;}
        function getToken()                   {return $this->token;}
        function getStatus()                  {return $this->status;}

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

            $sql = "select * from merchent WHERE mr_email = '{$email}'";
            
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

            $sql = "INSERT INTO " .$this->tableName. " (mr_name, mr_email, mr_password, mr_status, mr_create_time, mr_current_time) VALUES
            ('$this->name', '$this->email', '$this->pass', '$this->status', current_timestamp(), current_timestamp())";

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

        public function updateCustomerData()
        {
            // $sql = "UPDATE $this->tableName SET";

            // if(null != $this->getName())
            // {
            //     $sql .= " name = '" .$this->getName(). "', ";
            // }

            // if(null != $this->getAddress())
            // {
            //     $sql .= " name = '" .$this->getAddress(). "', ";
            // }

            // if(null != $this->getMobile())
            // {
            //     $sql .= " name = '" .$this->getMobile(). "', ";
            // }

            // $sql.= " updated_by = {'$this->updatedBy'},
            //          updated_on = {'$this->updatedOn'}
            //          WHERE id = {'$this->id'}";


            // $result = mysqli_query($this->dbconn, $sql) or die('failed');

            // if($result)
            // {
            //     echo "Data Updated Successfully.";
            //     return 1;
            // }
            // else
            // {
            //     echo "Something went wrong will updating data.";
            //     return 0;
            // }
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