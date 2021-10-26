<?php

    require_once('constants.php');

    class Rest
    {
        protected $request;
        protected $serviceName;
        protected $param;


        public function __construct()
        {
            if($_SERVER['REQUEST_METHOD'] !== 'POST')
            {
                //echo "Method is not POST.";
                $this->throwError(REQUEST_NOT_VALID, 'Request Method is not Valid.');
            }
            else
            {
                $handler = fopen('php://input', 'r');
                $this->request = stream_get_contents($handler);

                $this->validateReuest();

                // to see if it is working or not.
                //echo $this->request = stream_get_contents($handler);
            }
        }

        public function validateReuest()
        {
            // to check if content-type is application/json.
            //echo $_SERVER['CONTENT_TYPE']; exit;

            if($_SERVER['CONTENT_TYPE'] !== 'application/json')
            {
                $this->throwError(REQUEST_CONTENTTYPE_NOT_VALID, 'Request Content-Type is not Valid');
            }

            // decode it into array format.
            $data = json_decode($this->request, true);
            // to check the data in array.
            //print_r($data);


            // check if api name is reuired or not
            if(!isset($data['name']) || $data['name'] == "")
            {
                $this->throwError(API_NAME_REQUIRED, "API Name is Required.");
            }
            $this->serviceName = $data['name'];


            // check if parameters name is reuired or not
            if(!is_array($data['param']))
            {
                $this->throwError(API_PARAM_REQUIRED, "API PARAM is Required.");
            }
            $this->param = $data['param'];
        }

        public function processApi()
        {
            $api = new Api;

            // in php there is one concept reflection method 
            // by using that concept reflection we call any function dynamically 
            // just we have to pass 2 arguments (class name, function name).
            $rMethod = new reflectionMethod('API', $this->serviceName);

            if(!method_exists($api, $this->serviceName))
            {
                $this->throwError(API_DOES_NOT_EXIST, "API Does Not Exists");
            }
            else
            {
                $rMethod->invoke($api);
            }
        }

        public function throwError($code, $message)
        {
            // header file
            header("content-tpe: application/json");

            // will return value in json format only
            $errorMsg = json_encode(['error'=> ['status'=>$code, 'message'=>$message]]);
            echo $errorMsg; 
            exit;        
        }

        public function returnResponse($code, $data)
        {
            // header file
            header("content-type: application/json");

            // will return value in json format only
            //$response = json_encode(['response'=> ['status' => $code, 'result' => $data]]);
            $response = json_encode(['status' => $code, 'result' => $data]);
            echo $response; 
            exit;        
        }


        // get header authorization 
        public function getAuthorizationHeader()
        {
            $headers = null;

            if(isset($_SERVER['Authorization']))
            {
                $headers = trim($_SERVER["Authorization"]);
            }
            else if(isset($_SERVER["HTTP_AUTHORIZATION"]))
            {
                //Ngix or fast CGI
                $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
            }
            elseif (function_exists('apache_request_headers'))
            {
                $requestHeaders = apache_request_headers();
                // server-side fix for bug in old Android versions
                // (a nice side-effect of this fix means we don't care about
                // capitalization for authorization)
                $requestHeaders = array_combine(array_map('ucwords',
                                  array_keys($requestHeaders)), 
                                  array_values($requestHeaders));

                if(isset($requestHeaders['Authorization']))
                {
                    $headers = trim($requestHeaders['Authorization']);
                }
            }
            return $headers;
        }


        // this function is used to get token from user through postman
        public function getBearerToken()
        {
            $header = $this->getAuthorizationHeader();
            // HEADER: Get the access token from header
            if(!empty($header))
            {
                if(preg_match('/Bearer\s(\S+)/', $header, $matches))
                {
                    return $matches[1];
                }
            }
            $this->throwError(ATHORIZATION_HEADER_NOT_FOUND, 'Access Token Not Found.');
        }


    }

?>