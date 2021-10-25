<?php

    class DbConnect
    {
        public function build_connection()
        {   
            //build sql database connection 
    
            $conn = new mysqli("localhost","root","","email_services");
    
            if ($conn->connect_error)
            {
                echo "Database Connection Error";
            }
            else
            {
                // to check if the database connection is established or not 
                //echo "Connected to Database."; 
                return $conn;
            }
            
        }
        public function close_connection($conn)
        {
            //close database connection
            $conn->close();
        }
    }

    // to check if database is connected or not  
    //$db = new DbConnect;
    //$conn = $db->build_connection();

?>