<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/MY_REST_Controller.php';
require  'vendor/autoload.php';

use \Firebase\JWT\JWT;

class User extends MY_REST_Controller {

	public function __construct()
	{		
		parent::__construct();
	}

	public function index_post()
	{
		//Validate user
		$this->validate_token($this->input->get_request_header(X_AUTH_TOKEN));
		
		$this->load->library('form_validation');

		// Set validations
		$this->form_validation->set_rules('name', 'name', 'required|alpha|trim|min_length[1]|max_length[50]');
		$this->form_validation->set_rules('email', 'email', 'required|valid_email');
		$this->form_validation->set_rules('password', 'password', 'required|min_length[5]|max_length[20]');
		
		// Set data to validate
		$this->form_validation->set_data($this->post());
		
		// Run Validations
		if ($this->form_validation->run() == FALSE) {
			return $this->set_response(
				array(),
				$this->lang->line('text_invalid_params'),
				REST_Controller::HTTP_BAD_REQUEST
			);
		}

		$this->load->model('User_model');

		// Check email availability 
		$email_available = $this->User_model->check_email_availability($this->post('email'));

		if(!$email_available){
			return $this->set_response(
				array(), 
				$this->lang->line('text_duplicate_email'),
				REST_Controller::HTTP_CONFLICT
			);
		}
		
		// Get needed data of user
		$user_data = $this->form_validation->need_data_as($this->post(), array(
			'name' => null,
			'email' => null,
			'password' => null
		));

		// Finally save the user			
		$user_id = $this->User_model->add($user_data);

		if(!$user_id){
			return $this->set_response(
				array(),
				$this->lang->line('text_server_error'),
				REST_Controller::HTTP_INTERNAL_SERVER_ERROR
			);
		}
			
		return $this->set_response(
			array(),
			$this->lang->line('text_registration_success'),
			REST_Controller::HTTP_CREATED
		);
	}

	public function check_email_availability_get()
	{
		$this->load->library('form_validation');

		// Validating inputs
		$this->form_validation->set_rules('email', 'email', 'required|valid_email');
		$this->form_validation->set_data($this->get());
		
		if ($this->form_validation->run() == false) {
			return $this->set_response(
				array(),
				$this->lang->line('text_invalid_params'),
				REST_Controller::HTTP_BAD_REQUEST
			);
		}

		$this->load->model('User_model');

		// Check email availability 
		$email_available = $this->User_model->check_email_availability($this->get('email'));
		
		if(!$email_available){
			return $this->set_response(
				array(),
				$this->lang->line('text_duplicate_email'),
				REST_Controller::HTTP_CONFLICT
			);
		}

		return $this->set_response(
			array(),
			$this->lang->line('text_email_available'),
			REST_Controller::HTTP_OK
		);
	}

	public function update_put($id = null)
	{
		$this->load->library('form_validation');

		// Set validations
		$this->form_validation->set_rules('name', 'name', 'alpha|trim|min_length[1]|max_length[50]');
		$this->form_validation->set_rules('email', 'email', 'valid_email');

		// Set data to validate
		$this->form_validation->set_data($this->post());

		//Run Validations
		if ($this->form_validation->run() == FALSE) {
			return $this->set_response(
				array(),
				$this->lang->line('text_invalid_params'),
				REST_Controller::HTTP_BAD_REQUEST
			);
		}

		// Check email availability
		if(isset($data['email'])){
			$email_available = $this->check_email_availability($data['email'], $data['id']);

			if($email_available['status'] !== true){
				return $email_available;
			}
		}

		// Getting needed user data
		$user_data = $this->form_validation->need_data_as($data, array('name'=>null, 'email'=>null));


		$this->load->model('User_model');
		$data = $this->put();
		$data['id'] = $id;
		$res = $this->User_model->update_profile($data);
		$this->set_response($res, $res['statusCode']);
	}

	public function login_post(){
		
		$this->load->helper('date');
		
		$timestamp = now();

		$token = array(
			"iss" => "http://example.org",
			"aud" => "http://example.com",
			"userdetail"=>array("fname"=>"Devang","lname"=>"naghera"),
			"iat" => $timestamp
		);
		
		$jwt = JWT::encode($token, $this->config->item('jwt_key'));
		$this->set_response(array("token"=>$jwt),"Login SuccessFully.!",MY_REST_Controller::HTTP_OK);
	}

}
