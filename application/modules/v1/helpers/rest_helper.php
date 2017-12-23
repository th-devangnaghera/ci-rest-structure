<?php 
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	function is_logged_in($login_required=true) {
		
		$user_id = null;

		$user = $this->session->userdata('user_data');
		if (!isset($user)) { 
			return false; 
		} else { 
			return true;
		}

		return $user_id;
	}
?>