<?php

class User_model extends CI_Model {
	private $validations = array('email'=>'required|valid_email',"password"=>"required|min_length[5]|max_length[20]");
	function __construct() {
		
		parent::__construct();

		//Table Name
		$this->table_name = "users";
	}

	function check_email_availability($email = null, $user_id = null){
		
		$filters = array('email' => $email);

		// It will not count given user id
		if(!empty($user_id)){
			$filters['id!='] = $user_id;
		}

		$count = $this->db->from($this->table_name)->where($filters)->get()->num_rows();
		
		if($count > 0){
			return false;
		}

		return true;
	}

	function add($user_data = array()){
		
		// Encrypt password
		$user_data['password'] = md5($user_data['password']);

		// Saving the user
		$insert_query = $this->db->insert($this->table_name, $user_data);
		
		if(!$insert_query){
			return false;
		}

		return true;
	}

	function update($id = null, $user_data = array()){

		if(!empty($user_data)){

			$user_data['modified_at'] = date('Y-m-d H:i:s');

			$this->db->reset_query();

			// Updating the user
			$update_query = $this->db->where('id', $id)->update($this->table_name, $user_data);
			
			if(!$update_query){
				return false;
			}
		}

		return true;
	}

	function login($data = array()){

		$this->load->library('form_validation');

		// Setup validation rules
		$rules = array(
			$this->validations['email'],
			$this->validations['password']
		);

		// Set validation rules
		$this->form_validation->set_required($rules, 'email', 'password');
		$this->form_validation->set_rules($rules);

		// Set data to validate
		$this->form_validation->set_data($data);

		//Run Validations
		if ($this->form_validation->run() == FALSE) {
			return get(REST_Controller::HTTP_BAD_REQUEST, $this->lang->line('text_invalid_params'), false);
		}

		// Check email and pass
		$count = $this->db->from($this->table_name)->where('id', $data['id'])->get()->num_rows();
		
		if(!$count){
			return get(REST_Controller::HTTP_NOT_FOUND, $this->lang->line('text_invalid_creds'), false);
		}

		

		return get(REST_Controller::HTTP_INTERNAL_SERVER_ERROR, $this->lang->line('text_user_updated'), true);
	}
}

?>