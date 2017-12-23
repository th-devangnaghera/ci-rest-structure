<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
    
    function __construct(){
    	parent::__construct();
    	$this->set_error_delimiters('', '');
    }
    
    // --------------------------------------------------------------------

    /**
	 * Error String
	 *
	 * Returns the error messages as a string, wrapped in the error delimiters
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function error_string($prefix = '', $suffix = '')
	{
		// No errors, validation passes!
		if (count($this->_error_array) === 0)
		{
			return '';
		}

		if ($prefix === '')
		{
			$prefix = $this->_error_prefix;
		}

		if ($suffix === '')
		{
			$suffix = $this->_error_suffix;
		}

		// Generate the error string
		$str = '';
		foreach ($this->_error_array as $val)
		{
			if ($val !== '')
			{
				// ORIGNAL : $str .= $prefix.$val.$suffix."\n";
				// Removed  \n as we are  using form validations for apis
				$str .= $prefix.$val.$suffix;
			}
		}

		return $str;
	}

	/**
	 * Set Rules
	 *
	 * This function takes an array of field names and validation
	 * rules as input, any custom error messages, validates the info,
	 * and stores it
	 *
	 * @param	mixed	$field
	 * @param	string	$label
	 * @param	mixed	$rules
	 * @param	array	$errors
	 * @return	CI_Form_validation
	 */
	/*public function set_rules($field, $label = '', $rules = array(), $errors = array())
	{	
		// No reason to set rules if we have no POST data
		// or a validation array has not been specified
		// if ($this->CI->input->method() !== 'post' && empty($this->validation_data))
		// {
		// 	return $this;
		// }

		// If an array was passed via the first parameter instead of individual string
		// values we cycle through it and recursively call this function.
		if (is_array($field))
		{
			foreach ($field as $row)
			{
				// Houston, we have a problem...
				if ( ! isset($row['field'], $row['rules']))
				{
					continue;
				}

				// If the field label wasn't passed we use the field name
				$label = isset($row['label']) ? $row['label'] : $row['field'];

				// Add the custom error message array
				$errors = (isset($row['errors']) && is_array($row['errors'])) ? $row['errors'] : array();

				// Here we go!
				$this->set_rules($row['field'], $label, $row['rules'], $errors);
			}

			return $this;
		}

		// No fields or no rules? Nothing to do...
		if ( ! is_string($field) OR $field === '' OR empty($rules))
		{
			return $this;
		}
		elseif ( ! is_array($rules))
		{
			// BC: Convert pipe-separated rules string to an array
			if ( ! is_string($rules))
			{
				return $this;
			}

			$rules = preg_split('/\|(?![^\[]*\])/', $rules);
		}

		// If the field label wasn't passed we use the field name
		$label = ($label === '') ? $field : $label;

		$indexes = array();

		// Is the field name an array? If it is an array, we break it apart
		// into its components so that we can fetch the corresponding POST data later
		if (($is_array = (bool) preg_match_all('/\[(.*?)\]/', $field, $matches)) === TRUE)
		{
			sscanf($field, '%[^[][', $indexes[0]);

			for ($i = 0, $c = count($matches[0]); $i < $c; $i++)
			{
				if ($matches[1][$i] !== '')
				{
					$indexes[] = $matches[1][$i];
				}
			}
		}

		// Build our master array
		$this->_field_data[$field] = array(
			'field'		=> $field,
			'label'		=> $label,
			'rules'		=> $rules,
			'errors'	=> $errors,
			'is_array'	=> $is_array,
			'keys'		=> $indexes,
			'postdata'	=> NULL,
			'error'		=> ''
		);

		return $this;
	}*/

	public function set_rules($field, $label = '', $rules = array(), $errors = array())
	{
		// No reason to set rules if we have no POST data
		// or a validation array has not been specified
		/*if ($this->CI->input->method() !== 'post' && empty($this->validation_data))
		{
			return $this;
		}*/

		// If an array was passed via the first parameter instead of individual string
		// values we cycle through it and recursively call this function.
		if (is_array($field))
		{
			foreach ($field as $row)
			{
				// Houston, we have a problem...
				if ( ! isset($row['field'], $row['rules']))
				{
					continue;
				}

				// If the field label wasn't passed we use the field name
				$label = isset($row['label']) ? $row['label'] : $row['field'];

				// Add the custom error message array
				$errors = (isset($row['errors']) && is_array($row['errors'])) ? $row['errors'] : array();

				// Here we go!
				$this->set_rules($row['field'], $label, $row['rules'], $errors);
			}

			return $this;
		}

		// No fields or no rules? Nothing to do...
		if ( ! is_string($field) OR $field === '' OR empty($rules))
		{
			return $this;
		}
		elseif ( ! is_array($rules))
		{
			// BC: Convert pipe-separated rules string to an array
			if ( ! is_string($rules))
			{
				return $this;
			}

			$rules = preg_split('/\|(?![^\[]*\])/', $rules);
		}

		// If the field label wasn't passed we use the field name
		$label = ($label === '') ? $field : $label;

		$indexes = array();

		// Is the field name an array? If it is an array, we break it apart
		// into its components so that we can fetch the corresponding POST data later
		if (($is_array = (bool) preg_match_all('/\[(.*?)\]/', $field, $matches)) === TRUE)
		{
			sscanf($field, '%[^[][', $indexes[0]);

			for ($i = 0, $c = count($matches[0]); $i < $c; $i++)
			{
				if ($matches[1][$i] !== '')
				{
					$indexes[] = $matches[1][$i];
				}
			}
		}

		// Build our master array
		$this->_field_data[$field] = array(
			'field'		=> $field,
			'label'		=> $label,
			'rules'		=> $rules,
			'errors'	=> $errors,
			'is_array'	=> $is_array,
			'keys'		=> $indexes,
			'postdata'	=> NULL,
			'error'		=> ''
		);

		return $this;
	}
	
	/**
	 * need_data_as
	 *
	 * This function takes arry of data that we have and a set of keys that we only need from data
	 *
	 * @param	array	$data
	 * @param	array	$label
	 * @param	boolean	$rules
	 * @return	Array
	 */
    public function need_data_as($data = array(), $need = array(), $remove_extra_data = TRUE) {
		
		//If data not array then return blank array 			
		if(!is_array($data)){
			return array();
		}

		//Filtered data will appear here
		$out = $remove_extra_data === TRUE ? array() : $data;
		
		foreach ($need as $key => $new_key) {
			
			//If new key is null that means take old one else take new one
			if(isset($data[$key]) || (array_key_exists ( $key , $data ) && $data[$key] == null)){
				
				// If field is blank than set to null
				if($data[$key] === ''){
					$data[$key] = NULL;
				}

				$out[$new_key === NULL ? $key : $new_key] = $data[$key];
			}
		}   

		return $out;
    }

    // --------------------------------------------------------------------

	/**
	 * Alpha- spaces
	 *
	 * @param	string
	 * @return	bool
	 */
	public function alpha_spaces($str)
	{
		return (bool) preg_match('/^[A-Z ]+$/i', $str);
	}


	/**
	 * Value should be within an array of values
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function in_list_($value, $list)
	{
		return in_array($value, explode('_,_', $list), TRUE);
	}

	/**
	 * Rename Value to given one
	 * Basically we can use this function to specify a list of sort fields name
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function rename_to(&$value, $list)
	{
		$list = explode(',', $list);

		for ($i = 0; $i < count($list); $i++) { 

			$config = explode(':', $list[$i]);

			if(isset($config[0]) && isset($config[1])){
				if($config[0] === $value){
					$value = $config[1];
				}
			}
		}

		return $value;
	}

	/**
	 * Append new rule from extra_rules array
	 * @param Model Field
	 * @param bool
	 */
	public function add_rule(&$field, $validation_key) 
	{	

		if(isset($field['extra_rules']) && isset($field['extra_rules'][$validation_key])){
			$field['rules'] = $field['rules'] . '|' . $field['extra_rules'][$validation_key];
		} else {
			$field['rules'] = $field['rules'] . '|' . $validation_key;
		}
	}

	/**
	 * Match field with db
	 * @param  [Array] $field  [description]
	 * @param  [String] $params [table,column]
	 * @return [Boolean]
	 */
    public function field_match($field, $params) 
    {

    	//This params contain comma separated in order (table,field)
    	//Get those in array
    	$params = explode(',', $params);

    	//Add field in where
		$this->CI->db->where($params[1], $field);
		
		//Set form table
		$this->CI->db->from($params[0]);

		return $this->CI->db->count_all_results() > 0;
	}

	/**
	 * Valid url format
	 * @param  [Array] $field  [description]
	 * @param  [String] $params [table,column]
	 * @return [Boolean]
	 */
    public function valid_url_format($value) 
    {
    	return (bool) preg_match('/(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/', $value);
	}

	/**
	 * Valid money format
	 * @param  [Numeric] $value
	 * @return [Boolean]
	 */
	public function is_money($value){
		return (bool) preg_match('/\b\d{1,3}(?:,?\d{3})*(?:\.\d{2})?\b/', $value);
	}

	/**
	 * Valid ISO Date format
	 * @param  [String] $field  [ISO Format Date]
	 * @return [Boolean]
	 */
	public function iso_date($date)
	{
		if (preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})Z$/', $date, $parts) == true) {
			
			$time = gmmktime($parts[4], $parts[5], $parts[6], $parts[2], $parts[3], $parts[1]);

        	$input_time = strtotime($date);
        	
        	if ($input_time === false) {
        		return false;
        	}

        	return $input_time == $time;

    	} else {
    		return false;
    	}
    }

	/**
	 * Valid Date format
	 * @param  [String] $field  [String Format Date]
	 * @return [Boolean]
	 */
	public function valid_date($date)
	{
		if (strtotime($date)) {		
        	return true;
    	} else {
    		return false;
    	}
    }

    /**
	 * No White Space
	 * @param  [String] $field  [ISO Format Date]
	 * @return [Boolean]
	 */
	public function white_space($value)
	{
		return (bool) !preg_match('/\s/', $value);
    }

    /**
	 * Convert integer to string 
	 * @param Model Field
	 * @param string
	 */
	public function str_val($field) 
	{	
		if(isset($field)){
            return strval($field);
		}				
	}

	/**
	 * Valid Time 
	 * @param Model Field
	 * @param string
	 */
	public function valid_time($str)
	{
		//Assume $str SHOULD be entered as HH:MM

		list($hh, $mm) = explode(':', $str);

		if (!is_numeric($hh) || !is_numeric($mm))
		{
		    return FALSE;
		}
		else if ((int) $hh > 24 || (int) $mm > 59)
		{
		    return FALSE;
		}
		else if (mktime((int) $hh, (int) $mm) === FALSE)
		{
		    return FALSE;
		}

		return TRUE;
	}

	/**
	 * Value should be within an array of values
	 *
	 * @param string
	 * @param string
	 * @return bool
	 */
	public function in_set(&$value, $list, $unique = FALSE)
	{ 
		$input = explode(',', $value);
		
		$output = array_intersect($input, explode('|', $list));
		
		if(count($output) === 0){
			return false;
		}

		if($unique){
			$value = array_unique($value); 
		}
		
		return true;
	}

	public function set_required(&$rules, $params)
	{	

		if(gettype($params) === 'string'){
			$params = array($params);
		}

		foreach($rules as $key=>$rule){
			if(in_array($rules[$key]['field'], $params)){
				if(!isset($rule['rules']) || empty($rule['rules'])){
					$rules[$key]['rules'] = 'required';
				} else {
					$rules[$key]['rules'] = 'required|'.$rule['rules'];
				}
			}
		}
	}

}
