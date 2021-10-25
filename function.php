<?php

    spl_autoload_register(function($className) // we create oject of class it included we don't have to included every class file
    {
        //echo $path = strtolower($className).".php <br>"; // print file name
        $path = strtolower($className).".php";

        if(file_exists($path))
        {
            require_once($path);
        }
        else
        {
            echo "File $path is not found.";
        }
    })


?>