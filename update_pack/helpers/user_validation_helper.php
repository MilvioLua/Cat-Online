<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */


if ( ! function_exists('email_validation'))
{
	function email_validation($email){
		$ci=& get_instance();
		$num_rows = 0;
		$user_array = array('admin', 'teacher', 'parent', 'student', 'accountant', 'librarian');
		$size = sizeof($user_array);

		for($i = 0; $i < $size; $i++){
			$ci->db->where('email', $email);
			$num_rows = $ci->db->get($user_array[$i])->num_rows();
			if($num_rows > 0){
				return 0;
			}
		}
		return 1;
	}
}

if ( ! function_exists('email_validation_for_edit')){
	function email_validation_for_edit($email, $id, $type){
		$num_rows = 0;
		$ci=& get_instance();
		$user_array = array('admin', 'teacher', 'parent', 'student', 'accountant', 'librarian');
		$size = sizeof($user_array);
		for($i = 0; $i < $size; $i++){
			if($type == $user_array[$i]){
				$ci->db->where_not_in($user_array[$i].'_id', $id);
				$ci->db->where('email', $email);
				$num_rows = $ci->db->get($user_array[$i])->num_rows();
				if($num_rows > 0){
					return 0;
				}
			}
			else{
				$ci->db->where('email', $email);
				$num_rows = $ci->db->get($user_array[$i])->num_rows();
				if($num_rows > 0){
					return 0;
				}
			}
		}
		return 1;
	}
}

// Section duplication on create
if ( ! function_exists('duplication_of_section_on_create')){
	function duplication_of_section_on_create($class_id, $section_name){
		$ci=& get_instance();
		$num_rows = 0;
		$data = array(
		'class_id' => $class_id,
		'name' => $section_name
		);
		$ci->db->where($data);
		$num_rows = $ci->db->get('section')->num_rows();
		if($num_rows == 0){
			return 1;
		}
		else if($num_rows > 1){
			return 0;
		}
	}
}
// section duplication on edit
if ( ! function_exists('duplication_of_section_on_edit')){
	function duplication_of_section_on_edit($section_id, $class_id, $section_name){
		$ci=& get_instance();
		$num_rows = 0;
		$data = array(
		'class_id' => $class_id,
		'name' => $section_name
		);
		$ci->db->where_not_in('section_id', $section_id);
		$ci->db->where($data);
		$num_rows = $ci->db->get('section')->num_rows();
		if($num_rows == 0){
			return 1;
		}
		else if($num_rows > 1){
			return 0;
		}
	}
}

// class routine duplication on create
if ( ! function_exists('duplication_of_class_routine_on_create')){
	function duplication_of_class_routine_on_create($data){
		$ci=& get_instance();
		$num_rows = 0;
		$ci->db->where($data);
		$num_rows = $ci->db->get('class_routine')->num_rows();
		if($num_rows == 0){
			return 1;
		}
		else if($num_rows > 1){
			return 0;
		}
	}
}

// class routine duplication on edit
if ( ! function_exists('duplication_of_class_routine_on_edit')){
	function duplication_of_class_routine_on_edit($data, $class_routine_id){
		$ci=& get_instance();
		$num_rows = 0;
		$ci->db->where_not_in('class_routine_id', $class_routine_id);
		$ci->db->where($data);
		$num_rows = $ci->db->get('class_routine')->num_rows();
		if($num_rows == 0){
			return 1;
		}
		else if($num_rows > 1){
			return 0;
		}
	}
}

//student id duplication on insert
if ( ! function_exists('code_validation_insert')){
    function code_validation_insert($student_code){
        $ci=& get_instance();
        $num_rows = 0;

        $num_rows = $ci->db->get_where('student',array('student_code'=>$student_code))->num_rows();
        if($num_rows == 0){
            return true;
        }
        else if($num_rows > 1){
            return false;
        }
    }
}

//student id duplication in update

if ( ! function_exists('code_validation_update')){
    function code_validation_update($student_code,$student_id){
        $ci=& get_instance();
        $num_rows = 0;
        $ci->db->where('student_id !=', $student_id);
        $ci->db->where('student_code', $student_code);
        $num_rows = $ci->db->get('student')->num_rows();
        if($num_rows == 0){
            return true;
        }
        else if($num_rows > 1){
            return false;
        }

    }
}

// helper to find receivers email from message id
if ( ! function_exists('get_receiver_email')){
    function get_receiver_email($message_id){
        $ci=& get_instance();
        $message_detail = $ci->db->get_where('message', array('message_id' => $message_id))->row();
        $thread_code = $message_detail->message_thread_code;
        $sender = $message_detail->sender;
        $thread_details = $ci->db->get_where('message_thread', array('message_thread_code' => $thread_code))->row();
        if ($sender == $thread_details->sender)
            $receiver = $thread_details->reciever;
        else
            $receiver = $thread_details->sender;

        $receiver = explode('-', $receiver);
        $email = $ci->db->get_where($receiver[0], array($receiver[0].'_id' => $receiver[1]))->row()->email;
        return $email;

    }
}




// ------------------------------------------------------------------------
/* End of file User_validation.php */
/* Location: ./system/helpers/User_validation.php */
