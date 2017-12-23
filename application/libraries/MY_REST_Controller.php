<?php
/**
 * Custome class to set response format 
 * @package         CodeIgniter
 * @subpackage      Class
 * @category        Class
 * @author          Kapil Barad|Agile Infoways, Devang Naghera|Agile Infoways  
 */
defined('BASEPATH') OR exit('No direct script access allowed');
use \Firebase\JWT\JWT;
class MY_REST_Controller extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // For new set_response
    public function response_without_numeric_check($data = NULL, $http_code = NULL, $continue = FALSE)
    {
        // If the HTTP status is not NULL, then cast as an integer
        if ($http_code !== NULL)
        {
            // So as to be safe later on in the process
            $http_code = (int) $http_code;
        }

        // Set the output as NULL by default
        $output = NULL;

        // If data is NULL and no HTTP status code provided, then display, error and exit
        if ($data === NULL && $http_code === NULL)
        {
            $http_code = self::HTTP_NOT_FOUND;
        }

        // If data is not NULL and a HTTP status code provided, then continue
        elseif ($data !== NULL)
        {
            // If the format method exists, call and return the output in that format
            if (method_exists($this->format, 'to_' . $this->response->format))
            {
                // Set the format header
                $this->output->set_content_type($this->_supported_formats[$this->response->format], strtolower($this->config->item('charset')));
                
                if($this->response->format === 'json'){
                    $output = json_encode($data);
                }

                // An array must be parsed as a string, so as not to cause an array to string error
                // Json is the most appropriate form for such a datatype
                /*if ($this->response->format === 'array')
                {
                    $output = $this->format->factory($output)->{'to_json'}();
                }*/
            }
            else
            {
                // If an array or object, then parse as a json, so as to be a 'string'
                if (is_array($data) || is_object($data))
                {
                    $data = json_encode($data);
                }

                // Format is not supported, so output the raw data as a string
                $output = $data;
            }
        }

        // If not greater than zero, then set the HTTP status code as 200 by default
        // Though perhaps 500 should be set instead, for the developer not passing a
        // correct HTTP status code
        $http_code > 0 || $http_code = self::HTTP_OK;

        $this->output->set_status_header($http_code);

        // JC: Log response code only if rest logging enabled
        if ($this->config->item('rest_enable_logging') === TRUE)
        {
            $this->_log_response_code($http_code);
        }

        // Output the data
        $this->output->set_output($output);

        if ($continue === FALSE)
        {
            // Display the data and exit execution
            $this->output->_display();
            exit;
        }

        // Otherwise dump the output automatically
    }
    
    /** Overwrite the set_response
    * As it's default converting all string to number if string contains only numbers
    * @param NULL $data Data to be sent in api response
    * @param NULL $message Message to be sent for api
    * @param NULL $http_code Status code of api
    * @param FALSE $continueExe If want to continuwe execution 
    **/
    public function set_response($data = NULL,$message=NULL, $http_code = NULL,$continueExe = FALSE)
    {
        $response = array("status_code"=>$http_code,"message"=>$message,"data"=>$data);
        $this->response_without_numeric_check($response, $http_code, $continueExe);
    }

    /** 
    * Default Rest Controller response function is used to send response
    * @param NULL $data Data to be sent in api response
    * @param NULL $message Message to be sent for api
    * @param NULL $http_code Status code of api
    **/
    public function set_response_simple($data = NULL,$message="", $http_code = NULL)
    {
        $response = array("status_code"=>$http_code,"message"=>$message,"data"=>$data);
        $this->response($response, $http_code, TRUE);
    }

    /** 
    * Validate access token sent in headers
    * Modify this function according to your requirements
    * @param NULL $data Data to be sent in api response
    * @param NULL $message Message to be sent for api
    * @param NULL $http_code Status code of api
    **/
    public function validate_token($access_token)
    {        
        if(empty($access_token))
        {
            return $this->set_response(new stdclass(),"Access token missing",MY_REST_Controller::HTTP_BAD_REQUEST);
        }
        
        try
        {
            $token = JWT::decode($access_token, $this->config->item('jwt_key'), array('HS256'));

            $hoursDiff = (time() - $token->iat)/3600; 

            if($hoursDiff > $this->config->item('expire_time'))
            {
                return $this->set_response(new stdclass(),"Access token expired.",MY_REST_Controller::HTTP_UNAUTHORIZED);    
            }
            return $token;
        }catch(Exception $e){
            return $this->set_response(new stdclass(),"Invalid access token",MY_REST_Controller::HTTP_UNAUTHORIZED);
        }
    }
}
?>