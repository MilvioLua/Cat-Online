<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 *  @author     : Creativeitem
 *  date        : 14 september, 2017
 *  Ekattor School Management System Pro
 *  http://codecanyon.net/user/Creativeitem
 *  http://support.creativeitem.com
 */
class Admin extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
        $this->load->library('session');
        $this->load->model('Barcode_model');
        $this->load->model(array('Ajaxdataload_model' => 'ajaxload'));

       /*cache control*/
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
    }

    /***default functin, redirects to login page if no admin logged in yet***/
    public function index()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($this->session->userdata('admin_login') == 1)
            redirect(site_url('admin/dashboard'), 'refresh');
    }

    /***ADMIN DASHBOARD***/
    function dashboard()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        $page_data['page_name']  = 'dashboard';
        $page_data['page_title'] = get_phrase('admin_dashboard');
        $this->load->view('backend/index', $page_data);
    }

    /***MANAGE ADMIN***/
    function admin($param1 = "", $param2 = "") {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

            if ($param1 == 'create') {
                $data['name']       = html_escape($this->input->post('name'));
                $data['email']      = html_escape($this->input->post('email'));
                $data['password']   = sha1($this->input->post('password'));
                $data['phone']      = html_escape($this->input->post('phone'));
                $data['address']    = html_escape($this->input->post('address'));

                $validation = email_validation($data['email']);
                if ($validation == 1) {
                    $this->db->insert('admin', $data);
                    $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
                    $this->email_model->account_opening_email('admin', $data['email'], $this->input->post('password')); //SEND EMAIL ACCOUNT OPENING EMAIL
                }
                else{
                    $this->session->set_flashdata('error_message' , get_phrase('this_email_id_is_not_available'));
                }

                redirect(site_url('admin/admin'), 'refresh');
            }

            if ($param1 == 'edit') {
                $data['name']   = html_escape($this->input->post('name'));
                $data['email']  = html_escape($this->input->post('email'));
                $data['phone']  = html_escape($this->input->post('phone'));
                $data['address']  = html_escape($this->input->post('address'));

                $validation = email_validation_for_edit($data['email'], $param2, 'admin');
                if($validation == 1){
                    $this->db->where('admin_id' , $param2);
                    $this->db->update('admin' , $data);
                    $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
                }
                else{
                    $this->session->set_flashdata('error_message' , get_phrase('this_email_id_is_not_available'));
                }

                redirect(site_url('admin/admin'), 'refresh');
            }

            if ($param1 == 'delete') {
                $this->db->where('admin_id' , $param2);
                $this->db->delete('admin');

                $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
                redirect(site_url('admin/admin'), 'refresh');
            }

		$page_data['page_name']  = 'admin';
		$page_data['page_title'] = get_phrase('manage_admin');
		$this->load->view('backend/index', $page_data);
    }

    /****MANAGE STUDENTS CLASSWISE*****/
	function student_add()
	{
		if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

		$page_data['page_name']  = 'student_add';
		$page_data['page_title'] = get_phrase('add_student');
		$this->load->view('backend/index', $page_data);
	}

	function student_bulk_add()
	{
		if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
		$page_data['page_name']  = 'student_bulk_add';
		$page_data['page_title'] = get_phrase('add_bulk_student');
		$this->load->view('backend/index', $page_data);
	}

  function student_profile($student_id)
  {
    if ($this->session->userdata('admin_login') != 1) {
      redirect(site_url('login'), 'refresh');
    }
    $page_data['page_name']  = 'student_profile';
		$page_data['page_title'] = get_phrase('student_profile');
    $page_data['student_id']  = $student_id;
		$this->load->view('backend/index', $page_data);
  }

    function get_sections($class_id)
    {
        $page_data['class_id'] = $class_id;
        $this->load->view('backend/admin/student_bulk_add_sections' , $page_data);
    }

	function student_information($class_id = '')
	{
		if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

		$page_data['page_name']  	= 'student_information';
		$page_data['page_title'] 	= get_phrase('student_information'). " - ".get_phrase('class')." : ".
											$this->crud_model->get_class_name($class_id);
		$page_data['class_id'] 	= $class_id;
		$this->load->view('backend/index', $page_data);
	}

    function get_students($class_id, $running_year) {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $columns = array(
            0 => 'id',
            1 => 'photo',
            2 => 'name',
            3 => 'address',
            4 => 'email',
            5 => 'options',
            6 => 'id'
        );

        $limit = html_escape($this->input->post('length'));
        $start = html_escape($this->input->post('start'));
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir   = $this->input->post('order')[0]['dir'];

        $totalData = $this->ajaxload->all_students_count();
        $totalFiltered = $totalData;

        if(empty($this->input->post('search')['value'])) {
            $students = $this->ajaxload->all_students($limit,$start,$order,$dir);
        }
        else {
            $search = $this->input->post('search')['value'];
            $students =  $this->ajaxload->student_search($limit,$start,$search,$order,$dir);
            $totalFiltered = $this->ajaxload->student_search_count($search);
        }

        $data = array();
        if(!empty($students)) {
            foreach ($students as $row) {
                $nestedData['id'] = $row->enroll_code;
                $nestedData['photo'] = '1';
                $nestedData['name'] = '2';
                $nestedData['address'] = '3';
                $nestedData['email'] = '4';
                $nestedData['options'] = '5';

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);
    }

    function student_marksheet($student_id = '') {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        $class_id     = $this->db->get_where('enroll' , array(
            'student_id' => $student_id , 'year' => $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description
        ))->row()->class_id;
        $student_name = $this->db->get_where('student' , array('student_id' => $student_id))->row()->name;
        $class_name   = $this->db->get_where('class' , array('class_id' => $class_id))->row()->name;
        $page_data['page_name']  =   'student_marksheet';
        $page_data['page_title'] =   get_phrase('marksheet_for') . ' ' . $student_name . ' (' . get_phrase('class') . ' ' . $class_name . ')';
        $page_data['student_id'] =   $student_id;
        $page_data['class_id']   =   $class_id;
        $this->load->view('backend/index', $page_data);
    }

    function student_marksheet_print_view($student_id , $exam_id) {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $class_id     = $this->db->get_where('enroll' , array(
            'student_id' => $student_id , 'year' => $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description
        ))->row()->class_id;
        $class_name   = $this->db->get_where('class' , array('class_id' => $class_id))->row()->name;

        $page_data['student_id'] =   $student_id;
        $page_data['class_id']   =   $class_id;
        $page_data['exam_id']    =   $exam_id;
        $this->load->view('backend/admin/student_marksheet_print_view', $page_data);
    }

    function student($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $running_year = $this->db->get_where('settings' , array(
            'type' => 'running_year'
        ))->row()->description;

        if ($param1 == 'create') {
            $data['name']         = html_escape($this->input->post('name'));
            if(html_escape($this->input->post('birthday')) != null){
              $data['birthday']     = html_escape($this->input->post('birthday'));
            }
            if($this->input->post('sex') != null){
              $data['sex']          = $this->input->post('sex');
            }
            if(html_escape($this->input->post('address')) != null){
              $data['address']      = html_escape($this->input->post('address'));
            }
            if(html_escape($this->input->post('phone')) != null){
              $data['phone']        = html_escape($this->input->post('phone'));
            }
            if(html_escape($this->input->post('student_code')) != null){
                $data['student_code'] = html_escape($this->input->post('student_code'));
                $code_validation = code_validation_insert(html_escape($data['student_code']));
                if(!$code_validation) {
                    $this->session->set_flashdata('error_message' , get_phrase('this_id_no_is_not_available'));
                    redirect(site_url('admin/student_add'), 'refresh');
                }
            }

            $data['email']        = html_escape($this->input->post('email'));
            $data['password']     = sha1($this->input->post('password'));


            if($this->input->post('parent_id') != null){
                $data['parent_id']    = $this->input->post('parent_id');
            }
            if($this->input->post('dormitory_id') != null){
                $data['dormitory_id'] = $this->input->post('dormitory_id');
            }
            if($this->input->post('transport_id') != null){
                $data['transport_id'] = $this->input->post('transport_id');
            }
            $validation = email_validation($data['email']);
            if($validation == 1) {
                $this->db->insert('student', $data);
                $student_id = $this->db->insert_id();

                $data2['student_id']     = $student_id;
                $data2['enroll_code']    = substr(md5(rand(0, 1000000)), 0, 7);
                if($this->input->post('class_id') != null){
                  $data2['class_id']       = $this->input->post('class_id');
                }
                if ($this->input->post('section_id') != '') {
                    $data2['section_id'] = $this->input->post('section_id');
                }
                if (html_escape($this->input->post('roll')) != '') {
                    $data2['roll']           = html_escape($this->input->post('roll'));
                }
                $data2['date_added']     = strtotime(date("Y-m-d H:i:s"));
                $data2['year']           = $running_year;
                $this->db->insert('enroll', $data2);
                move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/student_image/' . $student_id . '.jpg');

                $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
                $this->email_model->account_opening_email('student', $data['email']); //SEND EMAIL ACCOUNT OPENING EMAIL
            }
            else {
                $this->session->set_flashdata('error_message' , get_phrase('this_email_id_is_not_available'));
            }
            redirect(site_url('admin/student_add'), 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']           = html_escape($this->input->post('name'));
            $data['email']          = html_escape($this->input->post('email'));
            $data['parent_id']      = $this->input->post('parent_id');
            if (html_escape($this->input->post('birthday')) != null) {
                $data['birthday']   = html_escape($this->input->post('birthday'));
            }
            if ($this->input->post('sex') != null) {
                $data['sex']            = $this->input->post('sex');
            }
            if (html_escape($this->input->post('address')) != null) {
               $data['address']        = html_escape($this->input->post('address'));
            }
            if (html_escape($this->input->post('phone')) != null) {
                $data['phone']          = html_escape($this->input->post('phone'));
            }
            if ($this->input->post('dormitory_id') != null) {
               $data['dormitory_id']   = $this->input->post('dormitory_id');
            }
            if ($this->input->post('transport_id') != null) {
                $data['transport_id']   = $this->input->post('transport_id');
            }

            //student id
            if(html_escape($this->input->post('student_code')) != null){
                $data['student_code'] = html_escape($this->input->post('student_code'));
                $code_validation = code_validation_update($data['student_code'],$param2);
                if(!$code_validation){
                    $this->session->set_flashdata('error_message' , get_phrase('this_id_no_is_not_available'));
                    redirect(site_url('admin/student_information/' . $param3), 'refresh');
                }
            }

            $validation = email_validation_for_edit($data['email'], $param2, 'student');
            if($validation == 1){
                $this->db->where('student_id', $param2);
                $this->db->update('student', $data);

                $data2['section_id'] = $this->input->post('section_id');
                if (html_escape($this->input->post('roll')) != null) {
                  $data2['roll'] = html_escape($this->input->post('roll'));
                }
                else{
                  $data2['roll'] = null;
                }
                $running_year = $this->db->get_where('settings' , array('type'=>'running_year'))->row()->description;
                $this->db->where('student_id' , $param2);
                $this->db->where('year' , $running_year);
                $this->db->update('enroll' , array(
                    'section_id' => $data2['section_id'] , 'roll' => $data2['roll']
                ));

                move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/student_image/' . $param2 . '.jpg');
                $this->crud_model->clear_cache();
                $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
           }
           else{
             $this->session->set_flashdata('error_message' , get_phrase('this_email_id_is_not_available'));
           }
            redirect(site_url('admin/student_information/' . $param3), 'refresh');
        }
    }

    function delete_student($student_id = '', $class_id = '') {
      $this->crud_model->delete_student($student_id);
      $this->session->set_flashdata('flash_message' , get_phrase('student_deleted'));
      redirect(site_url('admin/student_information/' . $class_id), 'refresh');
    }

    // STUDENT PROMOTION
    function student_promotion($param1 = '' , $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        if($param1 == 'promote') {
            $running_year  =   $this->input->post('running_year');
            $from_class_id =   $this->input->post('promotion_from_class_id');
            $students_of_promotion_class =   $this->db->get_where('enroll' , array(
                'class_id' => $from_class_id , 'year' => $running_year
            ))->result_array();
            foreach($students_of_promotion_class as $row) {
                $sections = $this->db->get_where('section', array('class_id' => $this->input->post('promotion_status_'.$row['student_id'])))->row_array();
                $enroll_data['enroll_code']     =   substr(md5(rand(0, 1000000)), 0, 7);
                $enroll_data['student_id']      =   $row['student_id'];
                $enroll_data['class_id']        =   $this->input->post('promotion_status_'.$row['student_id']);
                $enroll_data['section_id']      =   $sections['section_id'];
                $enroll_data['year']            =   $this->input->post('promotion_year');
                $enroll_data['date_added']      =   strtotime(date("Y-m-d H:i:s"));
                $this->db->insert('enroll' , $enroll_data);
            }
            $this->session->set_flashdata('flash_message' , get_phrase('new_enrollment_successfull'));
            redirect(site_url('admin/student_promotion'), 'refresh');
        }

        $page_data['page_title']    = get_phrase('student_promotion');
        $page_data['page_name']  = 'student_promotion';
        $this->load->view('backend/index', $page_data);
    }

    function get_students_to_promote($class_id_from , $class_id_to , $running_year , $promotion_year)
    {
        $page_data['class_id_from']     =   $class_id_from;
        $page_data['class_id_to']       =   $class_id_to;
        $page_data['running_year']      =   $running_year;
        $page_data['promotion_year']    =   $promotion_year;
        $this->load->view('backend/admin/student_promotion_selector' , $page_data);
    }


     /****MANAGE PARENTS CLASSWISE*****/
    function parent($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'create') {
            $data['name']        			= html_escape($this->input->post('name'));
            $data['email']       			= html_escape($this->input->post('email'));
            $data['password']    			= sha1($this->input->post('password'));
            if (html_escape($this->input->post('phone')) != null) {
               $data['phone'] = html_escape($this->input->post('phone'));
            }
            if (html_escape($this->input->post('address')) != null) {
               $data['address'] = html_escape($this->input->post('address'));
            }
            if (html_escape($this->input->post('profession')) != null) {
               $data['profession'] = html_escape($this->input->post('profession'));
            }
            $validation = email_validation($data['email']);
            if($validation == 1){
                $this->db->insert('parent', $data);
                $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
                $this->email_model->account_opening_email('parent', $data['email']); //SEND EMAIL ACCOUNT OPENING EMAIL
            }
            else{
                $this->session->set_flashdata('error_message' , get_phrase('this_email_id_is_not_available'));
            }

            redirect(site_url('admin/parent'), 'refresh');
        }
        if ($param1 == 'edit') {
            $data['name']                   = html_escape($this->input->post('name'));
            $data['email']                  = html_escape($this->input->post('email'));
            if (html_escape($this->input->post('phone')) != null) {
               $data['phone'] = html_escape($this->input->post('phone'));
            }
            else{
              $data['phone'] = null;
            }
            if (html_escape($this->input->post('address')) != null) {
                $data['address'] = html_escape($this->input->post('address'));
            }
            else{
               $data['address'] = null;
            }
            if (html_escape($this->input->post('profession')) != null) {
                $data['profession'] = html_escape($this->input->post('profession'));
            }
            else{
                $data['profession'] = null;
            }
            $validation = email_validation_for_edit($data['email'], $param2, 'parent');
            if ($validation == 1) {
                $this->db->where('parent_id' , $param2);
                $this->db->update('parent' , $data);
                $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            }
            else{
                $this->session->set_flashdata('error_message' , get_phrase('this_email_id_is_not_available'));
            }

            redirect(site_url('admin/parent'), 'refresh');
        }
        if ($param1 == 'delete') {
            $this->db->where('parent_id' , $param2);
            $this->db->delete('parent');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/parent'), 'refresh');
        }
        $page_data['page_title'] 	= get_phrase('all_parents');
        $page_data['page_name']  = 'parent';
        $this->load->view('backend/index', $page_data);
    }

    function get_parents() {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $columns = array(
            0 => 'parent_id',
            1 => 'name',
            2 => 'email',
            3 => 'phone',
            4 => 'profession',
            5 => 'options',
            6 => 'parent_id'
        );

        $limit = html_escape($this->input->post('length'));
        $start = html_escape($this->input->post('start'));
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir   = $this->input->post('order')[0]['dir'];

        $totalData = $this->ajaxload->all_parents_count();
        $totalFiltered = $totalData;

        if(empty($this->input->post('search')['value'])) {
            $parents = $this->ajaxload->all_parents($limit,$start,$order,$dir);
        }
        else {
            $search = $this->input->post('search')['value'];
            $parents =  $this->ajaxload->parent_search($limit,$start,$search,$order,$dir);
            $totalFiltered = $this->ajaxload->parent_search_count($search);
        }

        $data = array();
        if(!empty($parents)) {
            foreach ($parents as $row) {

                $options = '<div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                    Action <span class="caret"></span></button><ul class="dropdown-menu dropdown-default pull-right" role="menu"><li><a href="#" onclick="parent_edit_modal('.$row->parent_id.')"><i class="entypo-pencil"></i>&nbsp;'.get_phrase('edit').'</a></li><li class="divider"></li><li><a href="#" onclick="parent_delete_confirm('.$row->parent_id.')"><i class="entypo-trash"></i>&nbsp;'.get_phrase('delete').'</a></li></ul></div>';

                $nestedData['parent_id'] = $row->parent_id;
                $nestedData['name'] = $row->name;
                $nestedData['email'] = $row->email;
                $nestedData['phone'] = $row->phone;
                $nestedData['profession'] = $row->profession;
                $nestedData['options'] = $options;

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);
    }


    /****MANAGE TEACHERS*****/
    function teacher($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'create') {
            $data['name']     = html_escape($this->input->post('name'));
            $data['email']    = html_escape($this->input->post('email'));
            $data['password'] = sha1($this->input->post('password'));
            if (html_escape($this->input->post('birthday')) != null) {
                $data['birthday'] = html_escape($this->input->post('birthday'));
            }
            if ($this->input->post('sex') != null) {
               $data['sex'] = $this->input->post('sex');
            }
            if (html_escape($this->input->post('address')) != null) {
                $data['address'] = html_escape($this->input->post('address'));
            }
            if (html_escape($this->input->post('phone')) != null) {
                $data['phone'] = html_escape($this->input->post('phone'));
            }
            if (html_escape($this->input->post('designation')) != null) {
                $data['designation'] = html_escape($this->input->post('designation'));
            }
            if ($this->input->post('show_on_website') != null) {
                $data['show_on_website'] = $this->input->post('show_on_website');
            }
            if ($this->input->post('about') != null) {
                $data['about'] = $this->input->post('about');
            }
            $links = array();
            $social['facebook'] = html_escape($this->input->post('facebook'));
            $social['twitter'] = html_escape($this->input->post('twitter'));
            $social['linkedin'] = html_escape($this->input->post('linkedin'));
            array_push($links, $social);
            $data['social_links'] = json_encode($links);

            $validation = email_validation($data['email']);
            if($validation == 1){
                $this->db->insert('teacher', $data);
                $teacher_id = $this->db->insert_id();
                move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/teacher_image/' . $teacher_id . '.jpg');
                $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
                $this->email_model->account_opening_email('teacher', $data['email']); //SEND EMAIL ACCOUNT OPENING EMAIL
            }
            else{
                $this->session->set_flashdata('error_message' , get_phrase('this_email_id_is_not_available'));
            }

            redirect(site_url('admin/teacher'), 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']        = html_escape($this->input->post('name'));
            $data['email']       = html_escape($this->input->post('email'));

            if (html_escape($this->input->post('birthday')) != null) {
                $data['birthday'] = html_escape($this->input->post('birthday'));
            }
            else{
              $data['birthday'] = null;
            }
            if ($this->input->post('sex') != null) {
                $data['sex']         = $this->input->post('sex');
            }
            if (html_escape($this->input->post('address')) != null) {
                $data['address']     = html_escape($this->input->post('address'));
            }
            else{
              $data['address'] = null;
            }
            if (html_escape($this->input->post('phone')) != null) {
               $data['phone']       = html_escape($this->input->post('phone'));
            }
            else{
              $data['phone'] = null;
            }
            if (html_escape($this->input->post('designation')) != null) {
               $data['designation']       = html_escape($this->input->post('designation'));
            }
            else{
              $data['designation'] = null;
            }
            if ($this->input->post('show_on_website') != null) {
               $data['show_on_website']       = $this->input->post('show_on_website');
            }
            else{
              $data['show_on_website'] = null;
            }

            if ($this->input->post('about') != null) {
                $data['about'] = $this->input->post('about');
            }else{
                $data['about'] = null;
            }
            $links = array();
            $social['facebook'] = html_escape($this->input->post('facebook'));
            $social['twitter'] = html_escape($this->input->post('twitter'));
            $social['linkedin'] = html_escape($this->input->post('linkedin'));
            array_push($links, $social);
            $data['social_links'] = json_encode($links);

            $validation = email_validation_for_edit($data['email'], $param2, 'teacher');
            if($validation == 1){
                $this->db->where('teacher_id', $param2);
                $this->db->update('teacher', $data);
                move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/teacher_image/' . $param2 . '.jpg');
                $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            }
            else{
                $this->session->set_flashdata('error_message' , get_phrase('this_email_id_is_not_available'));
            }

            redirect(site_url('admin/teacher'), 'refresh');
        }
        else if ($param1 == 'personal_profile') {
            $page_data['personal_profile']   = true;
            $page_data['current_teacher_id'] = $param2;
        }
        else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('teacher', array(
                'teacher_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('teacher_id', $param2);
            $this->db->delete('teacher');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/teacher'), 'refresh');
        }
        $page_data['teachers']   = $this->db->get('teacher')->result_array();
        $page_data['page_name']  = 'teacher';
        $page_data['page_title'] = get_phrase('manage_teacher');
        $this->load->view('backend/index', $page_data);
    }

    function get_teachers() {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $columns = array(
            0 => 'teacher_id',
            1 => 'photo',
            2 => 'name',
            3 => 'email',
            4 => 'phone',
            5 => 'options',
            6 => 'teacher_id'
        );

        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir   = $this->input->post('order')[0]['dir'];

        $totalData = $this->ajaxload->all_teachers_count();
        $totalFiltered = $totalData;

        if(empty($this->input->post('search')['value'])) {
            $teachers = $this->ajaxload->all_teachers($limit,$start,$order,$dir);
        }
        else {
            $search = $this->input->post('search')['value'];
            $teachers =  $this->ajaxload->teacher_search($limit,$start,$search,$order,$dir);
            $totalFiltered = $this->ajaxload->teacher_search_count($search);
        }

        $data = array();
        if(!empty($teachers)) {
            foreach ($teachers as $row) {

                $photo = '<img src="'.$this->crud_model->get_image_url('teacher', $row->teacher_id).'" class="img-circle" width="30" />';

                $options = '<div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                    Action <span class="caret"></span></button><ul class="dropdown-menu dropdown-default pull-right" role="menu"><li><a href="#" onclick="teacher_edit_modal('.$row->teacher_id.')"><i class="entypo-pencil"></i>&nbsp;'.get_phrase('edit').'</a></li><li class="divider"></li><li><a href="#" onclick="teacher_delete_confirm('.$row->teacher_id.')"><i class="entypo-trash"></i>&nbsp;'.get_phrase('delete').'</a></li></ul></div>';

                $nestedData['teacher_id'] = $row->teacher_id;
                $nestedData['photo'] = $photo;
                $nestedData['name'] = $row->name;
                $nestedData['email'] = $row->email;
                $nestedData['phone'] = $row->phone;
                $nestedData['options'] = $options;

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);
    }

    /****MANAGE SUBJECTS*****/
    function subject($param1 = '', $param2 = '' , $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'create') {
            $data['name']       = html_escape($this->input->post('name'));
            $data['class_id']   = $this->input->post('class_id');
            $data['year']       = $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;
            if ($this->input->post('teacher_id') != null) {
                $data['teacher_id'] = $this->input->post('teacher_id');
            }

            $this->db->insert('subject', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(site_url('admin/subject/' . $data['class_id']), 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']       = html_escape($this->input->post('name'));
            $data['class_id']   = $this->input->post('class_id');
            $data['teacher_id'] = $this->input->post('teacher_id');
            $data['year']       = $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;

            $this->db->where('subject_id', $param2);
            $this->db->update('subject', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/subject/' . $data['class_id']), 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('subject', array(
                'subject_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('subject_id', $param2);
            $this->db->delete('subject');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/subject/' . $param3), 'refresh');
        }
        $running_year = $this->db->get_where('settings', array('type' => 'running_year'))->row()->description;
		    $page_data['class_id']   = $param1;
        $page_data['subjects']   = $this->db->get_where('subject' , array('class_id' => $param1, 'year' => $running_year))->result_array();
        $page_data['page_name']  = 'subject';
        $page_data['page_title'] = get_phrase('manage_subject');
        $this->load->view('backend/index', $page_data);
    }

    /****MANAGE CLASSES*****/
    function classes($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'create') {
            $data['name']         = html_escape($this->input->post('name'));
            $data['teacher_id']   = $this->input->post('teacher_id');
            if ($this->input->post('name_numeric') != null) {
                $data['name_numeric'] = html_escape($this->input->post('name_numeric'));
            }

            $this->db->insert('class', $data);
            $class_id = $this->db->insert_id();
            //create a section by default
            $data2['class_id']  =   $class_id;
            $data2['name']      =   'A';
            $data2['teacher_id']=$data['teacher_id'];
            $this->db->insert('section' , $data2);

            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(site_url('admin/classes'), 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']         = html_escape($this->input->post('name'));
            $data['teacher_id']   = $this->input->post('teacher_id');
            if ($this->input->post('name_numeric') != null) {
                $data['name_numeric'] = html_escape($this->input->post('name_numeric'));
            }
            else{
               $data['name_numeric'] = null;
            }
            $this->db->where('class_id', $param2);
            $this->db->update('class', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/classes'), 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('class', array(
                'class_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('class_id', $param2);
            $this->db->delete('class');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/classes'), 'refresh');
        }
        $page_data['classes']    = $this->db->get('class')->result_array();
        $page_data['page_name']  = 'class';
        $page_data['page_title'] = get_phrase('manage_class');
        $this->load->view('backend/index', $page_data);
    }
     function get_subject($class_id)
    {
        $subject = $this->db->get_where('subject' , array(
            'class_id' => $class_id
        ))->result_array();
        foreach ($subject as $row) {
            echo '<option value="' . $row['subject_id'] . '">' . $row['name'] . '</option>';
        }
    }
    // ACADEMIC SYLLABUS
    function academic_syllabus($class_id = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        // detect the first class
        if ($class_id == '')
            $class_id           =   $this->db->get('class')->first_row()->class_id;

        $page_data['page_name']  = 'academic_syllabus';
        $page_data['page_title'] = get_phrase('academic_syllabus');
        $page_data['class_id']   = $class_id;
        $this->load->view('backend/index', $page_data);
    }

    function upload_academic_syllabus()
    {
        $data['academic_syllabus_code'] =   substr(md5(rand(0, 1000000)), 0, 7);
        if ($this->input->post('description') != null) {
           $data['description'] = html_escape($this->input->post('description'));
        }
        $data['title']                  =   html_escape($this->input->post('title'));
        $data['class_id']               =   $this->input->post('class_id');
        $data['subject_id']             =   $this->input->post('subject_id');
        $data['uploader_type']          =   $this->session->userdata('login_type');
        $data['uploader_id']            =   $this->session->userdata('login_user_id');
        $data['year']                   =   $this->db->get_where('settings',array('type'=>'running_year'))->row()->description;
        $data['timestamp']              =   strtotime(date("Y-m-d H:i:s"));
        //uploading file using codeigniter upload library
        $files = $_FILES['file_name'];
        $this->load->library('upload');
        $config['upload_path']   =  'uploads/syllabus/';
        $config['allowed_types'] =  '*';
        $_FILES['file_name']['name']     = $files['name'];
        $_FILES['file_name']['type']     = $files['type'];
        $_FILES['file_name']['tmp_name'] = $files['tmp_name'];
        $_FILES['file_name']['size']     = $files['size'];
        $this->upload->initialize($config);
        $this->upload->do_upload('file_name');

        $data['file_name'] = $_FILES['file_name']['name'];

        $this->db->insert('academic_syllabus', $data);
        $this->session->set_flashdata('flash_message' , get_phrase('syllabus_uploaded'));
        redirect(site_url('admin/academic_syllabus/' . $data['class_id']), 'refresh');

    }

    function download_academic_syllabus($academic_syllabus_code)
    {
        $file_name = $this->db->get_where('academic_syllabus', array(
            'academic_syllabus_code' => $academic_syllabus_code
        ))->row()->file_name;
        $this->load->helper('download');
        $data = file_get_contents("uploads/syllabus/" . $file_name);
        $name = $file_name;

        force_download($name, $data);
    }

    function delete_academic_syllabus($academic_syllabus_code) {
      $file_name = $this->db->get_where('academic_syllabus', array(
          'academic_syllabus_code' => $academic_syllabus_code
      ))->row()->file_name;
      if (file_exists('uploads/syllabus/'.$file_name)) {
        // unlink('uploads/syllabus/'.$file_name);
      }
      $this->db->where('academic_syllabus_code', $academic_syllabus_code);
      $this->db->delete('academic_syllabus');

      $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
      redirect(site_url('admin/academic_syllabus'), 'refresh');

    }

    /****MANAGE SECTIONS*****/
    function section($class_id = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        // detect the first class
        if ($class_id == '')
            $class_id           =   $this->db->get('class')->first_row()->class_id;

        $page_data['page_name']  = 'section';
        $page_data['page_title'] = get_phrase('manage_sections');
        $page_data['class_id']   = $class_id;
        $this->load->view('backend/index', $page_data);
    }

    function sections($param1 = '' , $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'create') {
            $data['name']       =   html_escape($this->input->post('name'));
            $data['class_id']   =   $this->input->post('class_id');
            $data['teacher_id'] =   $this->input->post('teacher_id');
            if ($this->input->post('nick_name') != null) {
               $data['nick_name'] = html_escape($this->input->post('nick_name'));
            }
            $validation = duplication_of_section_on_create($data['class_id'], $data['name']);
            if($validation == 1){
                $this->db->insert('section' , $data);
                $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            }
            else{
                $this->session->set_flashdata('error_message' , get_phrase('duplicate_name_of_section_is_not_allowed'));
            }

            redirect(site_url('admin/section/' . $data['class_id']), 'refresh');
        }

        if ($param1 == 'edit') {
            $data['name']       =   html_escape($this->input->post('name'));
            $data['class_id']   =   $this->input->post('class_id');
            $data['teacher_id'] =   $this->input->post('teacher_id');
            if ($this->input->post('nick_name') != null) {
                $data['nick_name'] = html_escape($this->input->post('nick_name'));
            }
            else{
                $data['nick_name'] = null;
            }
            $validation = duplication_of_section_on_edit($param2, $data['class_id'], $data['name']);
            if ($validation == 1) {
               $this->db->where('section_id' , $param2);
               $this->db->update('section' , $data);
               $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            }
            else{
               $this->session->set_flashdata('error_message' , get_phrase('duplicate_name_of_section_is_not_allowed'));
            }

            redirect(site_url('admin/section/' . $data['class_id']), 'refresh');
        }

        if ($param1 == 'delete') {
            $this->db->where('section_id' , $param2);
            $this->db->delete('section');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/section'), 'refresh');
        }
    }

    function get_class_section($class_id)
    {
        $sections = $this->db->get_where('section' , array(
            'class_id' => $class_id
        ))->result_array();
        foreach ($sections as $row) {
            echo '<option value="' . $row['section_id'] . '">' . $row['name'] . '</option>';
        }
    }

    function get_class_section_selector($class_id){
        $page_data['class_id'] = $class_id;
        $this->load->view('backend/admin/get_class_section_selector', $page_data);
    }

    function get_class_subject_selector($class_id){
        $page_data['class_id'] = $class_id;
        $this->load->view('backend/admin/get_class_subject_selector', $page_data);
    }

    function get_class_subject($class_id)
    {
        $subjects = $this->db->get_where('subject' , array(
            'class_id' => $class_id
        ))->result_array();
        foreach ($subjects as $row) {
            echo '<option value="' . $row['subject_id'] . '">' . $row['name'] . '</option>';
        }
    }

    function get_class_students($class_id)
    {
        $students = $this->db->get_where('enroll' , array(
            'class_id' => $class_id , 'year' => $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description
        ))->result_array();
        foreach ($students as $row) {
            $name = $this->db->get_where('student' , array('student_id' => $row['student_id']))->row()->name;
            echo '<option value="' . $row['student_id'] . '">' . $name . '</option>';
        }
    }

    function get_class_students_mass($class_id)
    {
        $students = $this->db->get_where('enroll' , array(
            'class_id' => $class_id , 'year' => $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description
        ))->result_array();
        echo '<div class="form-group">
                <label class="col-sm-3 control-label">' . get_phrase('students') . '</label>
                <div class="col-sm-9">';
        foreach ($students as $row) {
             $name = $this->db->get_where('student' , array('student_id' => $row['student_id']))->row()->name;
            echo '<div class="checkbox">
                    <label><input type="checkbox" class="check" name="student_id[]" value="' . $row['student_id'] . '">' . $name .'</label>
                </div>';
        }
        echo '<br><button type="button" class="btn btn-default" onClick="select()">'.get_phrase('select_all').'</button>';
        echo '<button style="margin-left: 5px;" type="button" class="btn btn-default" onClick="unselect()"> '.get_phrase('select_none').' </button>';
        echo '</div></div>';
    }



    /****MANAGE EXAMS*****/
    function exam($param1 = '', $param2 = '' , $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'create') {
            $data['name']    = html_escape($this->input->post('name'));
            $data['date']    = html_escape($this->input->post('date'));
            $data['year']    = $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;
            if ($this->input->post('comment') != null) {
                $data['comment'] = html_escape($this->input->post('comment'));
            }
            $this->db->insert('exam', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(site_url('admin/exam'), 'refresh');
        }
        if ($param1 == 'edit' && $param2 == 'do_update') {
            $data['name']    = html_escape($this->input->post('name'));
            $data['date']    = html_escape($this->input->post('date'));
            if ($this->input->post('comment') != null) {
                $data['comment'] = html_escape($this->input->post('comment'));
            }
            else{
              $data['comment'] = null;
            }
            $data['year']    = $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;

            $this->db->where('exam_id', $param3);
            $this->db->update('exam', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/exam'), 'refresh');
        }
        else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('exam', array(
                'exam_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('exam_id', $param2);
            $this->db->delete('exam');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/exam'), 'refresh');
        }
        $running_year = $this->db->get_where('settings', array('type' => 'running_year'))->row()->description;
        $page_data['exams']      = $this->db->get_where('exam', array('year' => $running_year))->result_array();
        $page_data['page_name']  = 'exam';
        $page_data['page_title'] = get_phrase('manage_exam');
        $this->load->view('backend/index', $page_data);
    }

    /****** SEND EXAM MARKS VIA SMS ********/
    function exam_marks_sms($param1 = '' , $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        if ($param1 == 'send_sms') {

            $exam_id    =   $this->input->post('exam_id');
            $class_id   =   $this->input->post('class_id');
            $receiver   =   $this->input->post('receiver');
            if ($exam_id != '' && $class_id != '' && $receiver != '') {
            // get all the students of the selected class
            $students = $this->db->get_where('enroll' , array(
                'class_id' => $class_id,
                    'year' => $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description
            ))->result_array();
            // get the marks of the student for selected exam
            foreach ($students as $row) {
                if ($receiver == 'student')
                    $receiver_phone = $this->db->get_where('student' , array('student_id' => $row['student_id']))->row()->phone;
                if ($receiver == 'parent') {
                    $parent_id =  $this->db->get_where('student' , array('student_id' => $row['student_id']))->row()->parent_id;
                    if($parent_id != '' || $parent_id != null) {
                        $receiver_phone = $this->db->get_where('parent' , array('parent_id' => $row['parent_id']))->row()->phone;
                        if($receiver_phone == null){
                          $this->session->set_flashdata('error_message' , get_phrase('parent_phone_number_is_not_found'));
                        }
                    }
                }
                $running_year = $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;
                $this->db->where('exam_id' , $exam_id);
                $this->db->where('student_id' , $row['student_id']);
                $this->db->where('year', $running_year);
                $marks = $this->db->get('mark')->result_array();

                $message = '';
                foreach ($marks as $row2) {
                    $subject       = $this->db->get_where('subject' , array('subject_id' => $row2['subject_id']))->row()->name;
                    $mark_obtained = $row2['mark_obtained'];
                    $message      .= $row2['student_id'] . $subject . ' : ' . $mark_obtained . ' , ';

                }
                // send sms
                $this->sms_model->send_sms( $message , $receiver_phone );
            }
            $this->session->set_flashdata('flash_message' , get_phrase('message_sent'));
          }
          else{
            $this->session->set_flashdata('error_message' , get_phrase('select_all_the_fields'));
          }
            redirect(site_url('admin/exam_marks_sms'), 'refresh');
        }

        $page_data['page_name']  = 'exam_marks_sms';
        $page_data['page_title'] = get_phrase('send_marks_by_sms');
        $this->load->view('backend/index', $page_data);
    }

    function marks_manage()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        $page_data['page_name']  =   'marks_manage';
        $page_data['page_title'] = get_phrase('manage_exam_marks');
        $this->load->view('backend/index', $page_data);
    }

    function marks_manage_view($exam_id = '' , $class_id = '' , $section_id = '' , $subject_id = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        $page_data['exam_id']    =   $exam_id;
        $page_data['class_id']   =   $class_id;
        $page_data['subject_id'] =   $subject_id;
        $page_data['section_id'] =   $section_id;
        $page_data['page_name']  =   'marks_manage_view';
        $page_data['page_title'] = get_phrase('manage_exam_marks');
        $this->load->view('backend/index', $page_data);
    }

    function marks_selector()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $data['exam_id']    = $this->input->post('exam_id');
        $data['class_id']   = $this->input->post('class_id');
        $data['section_id'] = $this->input->post('section_id');
        $data['subject_id'] = $this->input->post('subject_id');
        $data['year']       = $this->db->get_where('settings' , array('type'=>'running_year'))->row()->description;
        if($data['class_id'] != '' && $data['exam_id'] != ''){
        $query = $this->db->get_where('mark' , array(
                    'exam_id' => $data['exam_id'],
                        'class_id' => $data['class_id'],
                            'section_id' => $data['section_id'],
                                'subject_id' => $data['subject_id'],
                                    'year' => $data['year']
                ));
        if($query->num_rows() < 1) {
            $students = $this->db->get_where('enroll' , array(
                'class_id' => $data['class_id'] , 'section_id' => $data['section_id'] , 'year' => $data['year']
            ))->result_array();
            foreach($students as $row) {
                $data['student_id'] = $row['student_id'];
                $this->db->insert('mark' , $data);
            }
        }
        redirect(site_url('admin/marks_manage_view/' . $data['exam_id'] . '/' . $data['class_id'] . '/' . $data['section_id'] . '/' . $data['subject_id']), 'refresh');
    }
    else{
        $this->session->set_flashdata('error_message' , get_phrase('select_all_the_fields'));
        $page_data['page_name']  =   'marks_manage';
        $page_data['page_title'] = get_phrase('manage_exam_marks');
        $this->load->view('backend/index', $page_data);
    }
}

    function marks_update($exam_id = '' , $class_id = '' , $section_id = '' , $subject_id = '')
    {
        if ($class_id != '' && $exam_id != '') {
        $running_year = $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;
        $marks_of_students = $this->db->get_where('mark' , array(
            'exam_id' => $exam_id,
                'class_id' => $class_id,
                    'section_id' => $section_id,
                        'year' => $running_year,
                            'subject_id' => $subject_id
        ))->result_array();
        foreach($marks_of_students as $row) {
            $obtained_marks = html_escape($this->input->post('marks_obtained_'.$row['mark_id']));
            $comment = html_escape($this->input->post('comment_'.$row['mark_id']));
            $this->db->where('mark_id' , $row['mark_id']);
            $this->db->update('mark' , array('mark_obtained' => $obtained_marks , 'comment' => $comment));
        }
        $this->session->set_flashdata('flash_message' , get_phrase('marks_updated'));
        redirect(site_url('admin/marks_manage_view/' . $exam_id . '/' . $class_id . '/' . $section_id . '/' . $subject_id), 'refresh');
    }
    else{
        $this->session->set_flashdata('error_message' , get_phrase('select_all_the_fields'));
        $page_data['page_name']  =   'marks_manage';
        $page_data['page_title'] = get_phrase('manage_exam_marks');
        $this->load->view('backend/index', $page_data);
    }
}
    function marks_get_subject($class_id)
    {
        $page_data['class_id'] = $class_id;
        $this->load->view('backend/admin/marks_get_subject' , $page_data);
    }

    // TABULATION SHEET
    function tabulation_sheet($class_id = '' , $exam_id = '') {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        if ($this->input->post('operation') == 'selection') {
            $page_data['exam_id']    = html_escape($this->input->post('exam_id'));
            $page_data['class_id']   = html_escape($this->input->post('class_id'));

            if ($page_data['exam_id'] > 0 && $page_data['class_id'] > 0) {
                redirect(site_url('admin/tabulation_sheet/' . $page_data['class_id'] . '/' . $page_data['exam_id']), 'refresh');
            } else {
                $this->session->set_flashdata('mark_message', 'Choose class and exam');
                redirect(site_url('admin/tabulation_sheet'), 'refresh');
            }
        }
        $page_data['exam_id']    = $exam_id;
        $page_data['class_id']   = $class_id;

        $page_data['page_info'] = 'Exam marks';

        $page_data['page_name']  = 'tabulation_sheet';
        $page_data['page_title'] = get_phrase('tabulation_sheet');
        $this->load->view('backend/index', $page_data);

    }

    function tabulation_sheet_print_view($class_id , $exam_id) {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        $page_data['class_id'] = $class_id;
        $page_data['exam_id']  = $exam_id;
        $this->load->view('backend/admin/tabulation_sheet_print_view' , $page_data);
    }


    /****MANAGE GRADES*****/
    function grade($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'create') {
            $data['name']        = html_escape($this->input->post('name'));
            $data['grade_point'] = html_escape($this->input->post('grade_point'));
            $data['mark_from']   = html_escape($this->input->post('mark_from'));
            $data['mark_upto']   = html_escape($this->input->post('mark_upto'));
            if ($this->input->post('comment') != null) {
                $data['comment'] = html_escape($this->input->post('comment'));
            }

            $this->db->insert('grade', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(site_url('admin/grade'), 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']        = html_escape($this->input->post('name'));
            $data['grade_point'] = html_escape($this->input->post('grade_point'));
            $data['mark_from']   = html_escape($this->input->post('mark_from'));
            $data['mark_upto']   = html_escape($this->input->post('mark_upto'));
            if ($this->input->post('comment') != null) {
                $data['comment'] = html_escape($this->input->post('comment'));
            }
            else{
              $data['comment'] = null;
            }

            $this->db->where('grade_id', $param2);
            $this->db->update('grade', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/grade'), 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('grade', array(
                'grade_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('grade_id', $param2);
            $this->db->delete('grade');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/grade'), 'refresh');
        }
        $page_data['grades']     = $this->db->get('grade')->result_array();
        $page_data['page_name']  = 'grade';
        $page_data['page_title'] = get_phrase('manage_grade');
        $this->load->view('backend/index', $page_data);
    }

    /**********MANAGING CLASS ROUTINE******************/
    function class_routine($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'create') {

            if($this->input->post('class_id') != null){
               $data['class_id']       = $this->input->post('class_id');
            }

            $data['section_id']     = $this->input->post('section_id');
            $data['subject_id']     = $this->input->post('subject_id');

            // 12 AM for starting time
            if ($this->input->post('time_start') == 12 && $this->input->post('starting_ampm') == 1) {
                $data['time_start'] = 24;
            }
            // 12 PM for starting time
            else if ($this->input->post('time_start') == 12 && $this->input->post('starting_ampm') == 2) {
                $data['time_start'] = 12;
            }
            // otherwise for starting time
            else{
                $data['time_start']     = $this->input->post('time_start') + (12 * ($this->input->post('starting_ampm') - 1));
            }
            // 12 AM for ending time
            if ($this->input->post('time_end') == 12 && $this->input->post('ending_ampm') == 1) {
                $data['time_end'] = 24;
            }
            // 12 PM for ending time
            else if ($this->input->post('time_end') == 12 && $this->input->post('ending_ampm') == 2) {
                $data['time_end'] = 12;
            }
            // otherwise for ending time
            else{
                $data['time_end']       = $this->input->post('time_end') + (12 * ($this->input->post('ending_ampm') - 1));
            }

            $data['time_start_min'] = $this->input->post('time_start_min');
            $data['time_end_min']   = $this->input->post('time_end_min');
            $data['day']            = $this->input->post('day');
            $data['year']           = $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;
            // checking duplication
            $array = array(
               'section_id'    => $data['section_id'],
               'class_id'      => $data['class_id'],
               'time_start'    => $data['time_start'],
               'time_end'      => $data['time_end'],
               'time_start_min'=> $data['time_start_min'],
               'time_end_min'  => $data['time_end_min'],
               'day'           => $data['day'],
               'year'          => $data['year']
            );
            $validation = duplication_of_class_routine_on_create($array);
            if ($validation == 1) {
                $this->db->insert('class_routine', $data);
                $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            }
            else{
                $this->session->set_flashdata('error_message' , get_phrase('time_conflicts'));
            }

            redirect(site_url('admin/class_routine_add'), 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['class_id']       = $this->input->post('class_id');
            if($this->input->post('section_id') != '') {
                $data['section_id'] = $this->input->post('section_id');
            }
            $data['subject_id']     = $this->input->post('subject_id');

            // 12 AM for starting time
            if ($this->input->post('time_start') == 12 && $this->input->post('starting_ampm') == 1) {
                $data['time_start'] = 24;
            }
            // 12 PM for starting time
            else if ($this->input->post('time_start') == 12 && $this->input->post('starting_ampm') == 2) {
                $data['time_start'] = 12;
            }
            // otherwise for starting time
            else{
                $data['time_start']     = $this->input->post('time_start') + (12 * ($this->input->post('starting_ampm') - 1));
            }
            // 12 AM for ending time
            if ($this->input->post('time_end') == 12 && $this->input->post('ending_ampm') == 1) {
                $data['time_end'] = 24;
            }
            // 12 PM for ending time
            else if ($this->input->post('time_end') == 12 && $this->input->post('ending_ampm') == 2) {
                $data['time_end'] = 12;
            }
            // otherwise for ending time
            else{
                $data['time_end']       = $this->input->post('time_end') + (12 * ($this->input->post('ending_ampm') - 1));
            }

            $data['time_start_min'] = $this->input->post('time_start_min');
            $data['time_end_min']   = $this->input->post('time_end_min');
            $data['day']            = $this->input->post('day');
            $data['year']           = $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;
            if ($data['subject_id'] != '') {
            // checking duplication
            $array = array(
               'section_id'    => $data['section_id'],
               'class_id'      => $data['class_id'],
               'time_start'    => $data['time_start'],
               'time_end'      => $data['time_end'],
               'time_start_min'=> $data['time_start_min'],
               'time_end_min'  => $data['time_end_min'],
               'day'           => $data['day'],
               'year'          => $data['year']
            );
            $validation = duplication_of_class_routine_on_edit($array, $param2);

            if ($validation == 1) {
                $this->db->where('class_routine_id', $param2);
                $this->db->update('class_routine', $data);
                $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            }
            else{
                $this->session->set_flashdata('error_message' , get_phrase('time_conflicts'));
            }
          }
          else{
            $this->session->set_flashdata('error_message' , get_phrase('subject_is_not_found'));
          }

            redirect(site_url('admin/class_routine_view/' . $data['class_id']), 'refresh');
        }
        else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('class_routine', array(
                'class_routine_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $class_id = $this->db->get_where('class_routine' , array('class_routine_id' => $param2))->row()->class_id;
            $this->db->where('class_routine_id', $param2);
            $this->db->delete('class_routine');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/class_routine_view/' . $class_id), 'refresh');
        }

    }

    function class_routine_add()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        $page_data['page_name']  = 'class_routine_add';
        $page_data['page_title'] = get_phrase('add_class_routine');
        $this->load->view('backend/index', $page_data);
    }

    function class_routine_view($class_id)
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        $page_data['page_name']  = 'class_routine_view';
        $page_data['class_id']  =   $class_id;
        $page_data['page_title'] = get_phrase('class_routine');
        $this->load->view('backend/index', $page_data);
    }

    function class_routine_print_view($class_id , $section_id)
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        $page_data['class_id']   =   $class_id;
        $page_data['section_id'] =   $section_id;
        $this->load->view('backend/admin/class_routine_print_view' , $page_data);
    }

    function get_class_section_subject($class_id)
    {
        $page_data['class_id'] = $class_id;
        $this->load->view('backend/admin/class_routine_section_subject_selector' , $page_data);
    }

    function section_subject_edit($class_id , $class_routine_id)
    {
        $page_data['class_id']          =   $class_id;
        $page_data['class_routine_id']  =   $class_routine_id;
        $this->load->view('backend/admin/class_routine_section_subject_edit' , $page_data);
    }

    function manage_attendance()
    {
        if($this->session->userdata('admin_login')!=1)
            redirect(site_url('login'), 'refresh');

        $page_data['page_name']  =  'manage_attendance';
        $page_data['page_title'] =  get_phrase('manage_attendance_of_class');
        $this->load->view('backend/index', $page_data);
    }

    function manage_attendance_view($class_id = '' , $section_id = '' , $timestamp = '')
    {
        if($this->session->userdata('admin_login')!=1)
            redirect(site_url('login'), 'refresh');

        $class_name = $this->db->get_where('class' , array(
            'class_id' => $class_id
        ))->row()->name;
        $page_data['class_id'] = $class_id;
        $page_data['timestamp'] = $timestamp;
        $page_data['page_name'] = 'manage_attendance_view';
        $section_name = $this->db->get_where('section' , array(
            'section_id' => $section_id
        ))->row()->name;
        $page_data['section_id'] = $section_id;
        $page_data['page_title'] = get_phrase('manage_attendance_of_class') . ' ' . $class_name . ' : ' . get_phrase('section') . ' ' . $section_name;
        $this->load->view('backend/index', $page_data);
    }
    function get_section($class_id) {
          $page_data['class_id'] = $class_id;
          $this->load->view('backend/admin/manage_attendance_section_holder' , $page_data);
    }
    function attendance_selector()
    {
        $data['class_id']   = $this->input->post('class_id');
        $data['year']       = $this->input->post('year');
        $data['timestamp']  = strtotime($this->input->post('timestamp'));
        $data['section_id'] = $this->input->post('section_id');
        $query = $this->db->get_where('attendance' ,array(
            'class_id'=>$data['class_id'],
                'section_id'=>$data['section_id'],
                    'year'=>$data['year'],
                        'timestamp'=>$data['timestamp']
        ));
        if($query->num_rows() < 1) {
            $students = $this->db->get_where('enroll' , array(
                'class_id' => $data['class_id'] , 'section_id' => $data['section_id'] , 'year' => $data['year']
            ))->result_array();

            foreach($students as $row) {
                $attn_data['class_id']   = $data['class_id'];
                $attn_data['year']       = $data['year'];
                $attn_data['timestamp']  = $data['timestamp'];
                $attn_data['section_id'] = $data['section_id'];
                $attn_data['student_id'] = $row['student_id'];
                $this->db->insert('attendance' , $attn_data);
            }

        }
        redirect(site_url('admin/manage_attendance_view/' . $data['class_id'] . '/' . $data['section_id'] . '/' . $data['timestamp']),'refresh');
    }

    function attendance_update($class_id = '' , $section_id = '' , $timestamp = '')
    {
        $running_year = $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;
        $active_sms_service = $this->db->get_where('settings' , array('type' => 'active_sms_service'))->row()->description;
        $attendance_of_students = $this->db->get_where('attendance' , array(
            'class_id'=>$class_id,'section_id'=>$section_id,'year'=>$running_year,'timestamp'=>$timestamp
        ))->result_array();
        foreach($attendance_of_students as $row) {
            $attendance_status = $this->input->post('status_'.$row['attendance_id']);
            $this->db->where('attendance_id' , $row['attendance_id']);
            $this->db->update('attendance' , array('status' => $attendance_status));

            if ($attendance_status == 2) {

                if ($active_sms_service != '' || $active_sms_service != 'disabled') {
                    $student_name   = $this->db->get_where('student' , array('student_id' => $row['student_id']))->row()->name;
                    $parent_id      = $this->db->get_where('student' , array('student_id' => $row['student_id']))->row()->parent_id;
                    $message        = 'Your child' . ' ' . $student_name . 'is absent today.';
                    if($parent_id != null && $parent_id != 0){
                        $receiver_phone = $this->db->get_where('parent' , array('parent_id' => $parent_id))->row()->phone;
                        if($receiver_phone != '' || $receiver_phone != null){
                            $this->sms_model->send_sms($message,$receiver_phone);
                        }
                        else{
                            $this->session->set_flashdata('error_message' , get_phrase('parent_phone_number_is_not_found'));
                        }
                    }
                    else{
                        $this->session->set_flashdata('error_message' , get_phrase('parent_phone_number_is_not_found'));
                    }
                }
            }
        }
        $this->session->set_flashdata('flash_message' , get_phrase('attendance_updated'));
        redirect(site_url('admin/manage_attendance_view/'.$class_id.'/'.$section_id.'/'.$timestamp) , 'refresh');
    }

	/****** DAILY ATTENDANCE *****************/
	function manage_attendance2($date='',$month='',$year='',$class_id='' , $section_id = '' , $session = '')
	{
		if($this->session->userdata('admin_login')!=1)
            redirect(site_url('login') , 'refresh');

        $active_sms_service = $this->db->get_where('settings' , array('type' => 'active_sms_service'))->row()->description;
        $running_year = $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;


		if($_POST)
		{
			// Loop all the students of $class_id
            $this->db->where('class_id' , $class_id);
            if($section_id != '') {
                $this->db->where('section_id' , $section_id);
            }
            //$session = base64_decode( urldecode( $session ) );
            $this->db->where('year' , $session);
            $students = $this->db->get('enroll')->result_array();
            foreach ($students as $row)
            {
                $attendance_status  =   $this->input->post('status_' . $row['student_id']);

                $this->db->where('student_id' , $row['student_id']);
                $this->db->where('date' , $date);
                $this->db->where('year' , $year);
                $this->db->where('class_id' , $row['class_id']);
                if($row['section_id'] != '' && $row['section_id'] != 0) {
                    $this->db->where('section_id' , $row['section_id']);
                }
                $this->db->where('session' , $session);

                $this->db->update('attendance' , array('status' => $attendance_status));

                if ($attendance_status == 2) {

                    if ($active_sms_service != '' || $active_sms_service != 'disabled') {
                        $student_name   = $this->db->get_where('student' , array('student_id' => $row['student_id']))->row()->name;
                        $parent_id      = $this->db->get_where('student' , array('student_id' => $row['student_id']))->row()->parent_id;
                        $receiver_phone = $this->db->get_where('parent' , array('parent_id' => $parent_id))->row()->phone;
                        $message        = 'Your child' . ' ' . $student_name . 'is absent today.';
                        $this->sms_model->send_sms($message,$receiver_phone);
                    }
                }

            }

			$this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
			redirect(site_url('admin/manage_attendance/'.$date.'/'.$month.'/'.$year.'/'.$class_id.'/'.$section_id.'/'.$session) , 'refresh');
		}
        $page_data['date']       =	$date;
        $page_data['month']      =	$month;
        $page_data['year']       =	$year;
        $page_data['class_id']   =  $class_id;
        $page_data['section_id'] =  $section_id;
        $page_data['session']    =  $session;

        $page_data['page_name']  =	'manage_attendance';
        $page_data['page_title'] =	get_phrase('manage_daily_attendance');
		$this->load->view('backend/index', $page_data);
	}
	function attendance_selector2()
	{
        //$session = $this->input->post('session');
        //$encoded_session = urlencode( base64_encode( $session ) );
		redirect(site_url('admin/manage_attendance/'.$this->input->post('date').'/'.
					$this->input->post('month').'/'.
						$this->input->post('year').'/'.
							$this->input->post('class_id').'/'.
                                $this->input->post('section_id').'/'.
                                    $this->input->post('session')) , 'refresh');
	}
        ///////ATTENDANCE REPORT /////
     function attendance_report() {
         $page_data['month']        = date('m');
         $page_data['page_name']    = 'attendance_report';
         $page_data['page_title']   = get_phrase('attendance_report');
         $this->load->view('backend/index',$page_data);
     }
     function attendance_report_view($class_id = '', $section_id = '', $month = '', $sessional_year = '')
     {
         if($this->session->userdata('admin_login')!=1)
            redirect(base_url() , 'refresh');

        $class_name                     = $this->db->get_where('class', array('class_id' => $class_id))->row()->name;
        $section_name                   = $this->db->get_where('section', array('section_id' => $section_id))->row()->name;
        $page_data['class_id']          = $class_id;
        $page_data['section_id']        = $section_id;
        $page_data['month']             = $month;
        $page_data['sessional_year']    = $sessional_year;
        $page_data['page_name']         = 'attendance_report_view';
        $page_data['page_title']        = get_phrase('attendance_report_of_class') . ' ' . $class_name . ' : ' . get_phrase('section') . ' ' . $section_name;
        $this->load->view('backend/index', $page_data);
     }
     function attendance_report_print_view($class_id ='' , $section_id = '' , $month = '', $sessional_year = '') {
          if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $page_data['class_id']          = $class_id;
        $page_data['section_id']        = $section_id;
        $page_data['month']             = $month;
        $page_data['sessional_year']    = $sessional_year;
        $this->load->view('backend/admin/attendance_report_print_view' , $page_data);
    }

    function attendance_report_selector()
    {   if($this->input->post('class_id') == '' || $this->input->post('sessional_year') == '') {
            $this->session->set_flashdata('error_message' , get_phrase('please_make_sure_class_and_sessional_year_are_selected'));
            redirect(site_url('admin/attendance_report'), 'refresh');
        }
        $data['class_id']       = $this->input->post('class_id');
        $data['section_id']     = $this->input->post('section_id');
        $data['month']          = $this->input->post('month');
        $data['sessional_year'] = $this->input->post('sessional_year');
        redirect(site_url('admin/attendance_report_view/' . $data['class_id'] . '/' . $data['section_id'] . '/' . $data['month'] . '/' . $data['sessional_year']), 'refresh');
    }

    /******MANAGE BILLING / INVOICES WITH STATUS*****/
    function invoice($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        if ($param1 == 'create') {
            $data['student_id']         = $this->input->post('student_id');
            $data['title']              = html_escape($this->input->post('title'));
            $data['amount']             = html_escape($this->input->post('amount'));
            $data['amount_paid']        = html_escape($this->input->post('amount_paid'));
            $data['due']                = $data['amount'] - $data['amount_paid'];
            $data['status']             = html_escape($this->input->post('status'));
            $data['creation_timestamp'] = strtotime($this->input->post('date'));
            $data['year']               = $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;
            if ($this->input->post('description') != null) {
                $data['description']    = $this->input->post('description');
            }

            $this->db->insert('invoice', $data);
            $invoice_id = $this->db->insert_id();

            $data2['invoice_id']        =   $invoice_id;
            $data2['student_id']        =   $this->input->post('student_id');
            $data2['title']             =   html_escape($this->input->post('title'));
            $data2['payment_type']      =  'income';
            $data2['method']            =   $this->input->post('method');
            $data2['amount']            =   html_escape($this->input->post('amount_paid'));
            $data2['timestamp']         =   strtotime($this->input->post('date'));
            $data2['year']              =  $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;
            if ($this->input->post('description') != null) {
                $data2['description']    = html_escape($this->input->post('description'));
            }
            $this->db->insert('payment' , $data2);

            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(site_url('admin/student_payment'), 'refresh');
        }

        if ($param1 == 'create_mass_invoice') {
            foreach ($this->input->post('student_id') as $id) {

                $data['student_id']         = $id;
                $data['title']              = html_escape($this->input->post('title'));
                $data['description']        = html_escape($this->input->post('description'));
                $data['amount']             = html_escape($this->input->post('amount'));
                $data['amount_paid']        = html_escape($this->input->post('amount_paid'));
                $data['due']                = $data['amount'] - $data['amount_paid'];
                $data['status']             = $this->input->post('status');
                $data['creation_timestamp'] = strtotime($this->input->post('date'));
                $data['year']               = $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;

                $this->db->insert('invoice', $data);
                $invoice_id = $this->db->insert_id();

                $data2['invoice_id']        =   $invoice_id;
                $data2['student_id']        =   $id;
                $data2['title']             =   html_escape($this->input->post('title'));
                $data2['description']       =   html_escape($this->input->post('description'));
                $data2['payment_type']      =  'income';
                $data2['method']            =   $this->input->post('method');
                $data2['amount']            =   html_escape($this->input->post('amount_paid'));
                $data2['timestamp']         =   strtotime($this->input->post('date'));
                $data2['year']               =   $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;

                $this->db->insert('payment' , $data2);
            }

            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(site_url('admin/student_payment'), 'refresh');
        }

        if ($param1 == 'do_update') {
            $data['student_id']         = $this->input->post('student_id');
            $data['title']              = html_escape($this->input->post('title'));
            $data['description']        = html_escape($this->input->post('description'));
            $data['amount']             = html_escape($this->input->post('amount'));
            $data['status']             = $this->input->post('status');
            $data['creation_timestamp'] = strtotime($this->input->post('date'));

            $this->db->where('invoice_id', $param2);
            $this->db->update('invoice', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/income'), 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('invoice', array(
                'invoice_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'take_payment') {
            $data['invoice_id']   =   $this->input->post('invoice_id');
            $data['student_id']   =   $this->input->post('student_id');
            $data['title']        =   html_escape($this->input->post('title'));
            $data['description']  =   html_escape($this->input->post('description'));
            $data['payment_type'] =   'income';
            $data['method']       =   $this->input->post('method');
            $data['amount']       =   html_escape($this->input->post('amount'));
            $data['timestamp']    =   strtotime($this->input->post('timestamp'));
            $data['year']         =   $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;
            $this->db->insert('payment' , $data);

            $status['status']   =   $this->input->post('status');
            $this->db->where('invoice_id' , $param2);
            $this->db->update('invoice' , array('status' => $status['status']));

            $data2['amount_paid']   =   html_escape($this->input->post('amount'));
            $data2['status']        =   $this->input->post('status');
            $this->db->where('invoice_id' , $param2);
            $this->db->set('amount_paid', 'amount_paid + ' . $data2['amount_paid'], FALSE);
            $this->db->set('due', 'due - ' . $data2['amount_paid'], FALSE);
            $this->db->update('invoice');

            $this->session->set_flashdata('flash_message' , get_phrase('payment_successfull'));
            redirect(site_url('admin/income'), 'refresh');
        }

        if ($param1 == 'delete') {
            $this->db->where('invoice_id', $param2);
            $this->db->delete('invoice');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/income'), 'refresh');
        }
        $page_data['page_name']  = 'invoice';
        $page_data['page_title'] = get_phrase('manage_invoice/payment');
        $this->db->order_by('creation_timestamp', 'desc');
        $page_data['invoices'] = $this->db->get('invoice')->result_array();
        $this->load->view('backend/index', $page_data);
    }

    function income($param1 = 'invoices', $param2 = '', $param3 = '') {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $page_data['page_name'] = 'income';
        $page_data['inner'] = $param1;
        $page_data['page_title'] = get_phrase('student_payments');
        $this->load->view('backend/index', $page_data);
    }

    function get_invoices() {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $columns = array(
            0 => 'invoice_id',
            1 => 'student',
            2 => 'title',
            3 => 'total',
            4 => 'paid',
            5 => 'status',
            6 => 'date',
            7 => 'options',
            8 => 'invoice_id'
        );

        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir   = $this->input->post('order')[0]['dir'];

        $totalData = $this->ajaxload->all_invoices_count();
        $totalFiltered = $totalData;

        if(empty($this->input->post('search')['value'])) {
            $invoices = $this->ajaxload->all_invoices($limit,$start,$order,$dir);
        }
        else {
            $search = $this->input->post('search')['value'];
            $invoices =  $this->ajaxload->invoice_search($limit,$start,$search,$order,$dir);
            $totalFiltered = $this->ajaxload->invoice_search_count($search);
        }

        $data = array();
        if(!empty($invoices)) {
            foreach ($invoices as $row) {

                if ($row->due == 0) {
                    $status = '<button class="btn btn-success btn-xs">'.get_phrase('paid').'</button>';
                    $payment_option = '';
                } else {
                    $status = '<button class="btn btn-danger btn-xs">'.get_phrase('unpaid').'</button>';
                    $payment_option = '<li><a href="#" onclick="invoice_pay_modal('.$row->invoice_id.')"><i class="entypo-bookmarks"></i>&nbsp;'.get_phrase('take_payment').'</a></li><li class="divider"></li>';
                }


                $options = '<div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                    Action <span class="caret"></span></button><ul class="dropdown-menu dropdown-default pull-right" role="menu">'.$payment_option.'<li><a href="#" onclick="invoice_view_modal('.$row->invoice_id.')"><i class="entypo-credit-card"></i>&nbsp;'.get_phrase('view_invoice').'</a></li><li class="divider"></li><li><a href="#" onclick="invoice_edit_modal('.$row->invoice_id.')"><i class="entypo-pencil"></i>&nbsp;'.get_phrase('edit').'</a></li><li class="divider"></li><li><a href="#" onclick="invoice_delete_confirm('.$row->invoice_id.')"><i class="entypo-trash"></i>&nbsp;'.get_phrase('delete').'</a></li></ul></div>';

                $nestedData['invoice_id'] = $row->invoice_id;
                $nestedData['student'] = $this->crud_model->get_type_name_by_id('student',$row->student_id);
                $nestedData['title'] = $row->title;
                $nestedData['total'] = $row->amount;
                $nestedData['paid'] = $row->amount_paid;
                $nestedData['status'] = $status;
                $nestedData['date'] = date('d M,Y', $row->creation_timestamp);
                $nestedData['options'] = $options;

                $data[] = $nestedData;

            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);
    }

    function get_payments() {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $columns = array(
            0 => 'payment_id',
            1 => 'title',
            2 => 'description',
            3 => 'method',
            4 => 'amount',
            5 => 'date',
            6 => 'options',
            7 => 'payment_id'
        );

        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir   = $this->input->post('order')[0]['dir'];

        $totalData = $this->ajaxload->all_payments_count();
        $totalFiltered = $totalData;

        if(empty($this->input->post('search')['value'])) {
            $payments = $this->ajaxload->all_payments($limit,$start,$order,$dir);
        }
        else {
            $search = $this->input->post('search')['value'];
            $payments =  $this->ajaxload->payment_search($limit,$start,$search,$order,$dir);
            $totalFiltered = $this->ajaxload->payment_search_count($search);
        }

        $data = array();
        if(!empty($payments)) {
            foreach ($payments as $row) {

                if ($row->method == 1)
                    $method = get_phrase('cash');
                else if ($row->method == 2)
                    $method = get_phrase('cheque');
                else if ($row->method == 3)
                    $method = get_phrase('card');
                else if ($row->method == 'Paypal')
                    $method = 'Paypal';
                else
                    $method = 'Stripe';


                $options = '<a href="#" onclick="invoice_view_modal('.$row->invoice_id.')"><i class="entypo-credit-card"></i>&nbsp;'.get_phrase('view_invoice').'</a>';

                $nestedData['payment_id'] = $row->payment_id;
                $nestedData['title'] = $row->title;
                $nestedData['description'] = $row->description;
                $nestedData['method'] = $method;
                $nestedData['amount'] = $row->amount;
                $nestedData['date'] = date('d M,Y', $row->timestamp);
                $nestedData['options'] = $options;

                $data[] = $nestedData;

            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);
    }

    function student_payment($param1 = '' , $param2 = '' , $param3 = '') {

        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        $page_data['page_name']  = 'student_payment';
        $page_data['page_title'] = get_phrase('create_student_payment');
        $this->load->view('backend/index', $page_data);
    }

    function expense($param1 = '' , $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'create') {
            $data['title']               =   html_escape($this->input->post('title'));
            $data['expense_category_id'] =   $this->input->post('expense_category_id');
            $data['payment_type']        =   'expense';
            $data['method']              =   $this->input->post('method');
            $data['amount']              =   html_escape($this->input->post('amount'));
            $data['timestamp']           =   strtotime($this->input->post('timestamp'));
            $data['year']                =   $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;
            if ($this->input->post('description') != null) {
                $data['description']     =   html_escape($this->input->post('description'));
            }
            $this->db->insert('payment' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(site_url('admin/expense'), 'refresh');
        }

        if ($param1 == 'edit') {
            $data['title']               =   html_escape($this->input->post('title'));
            $data['expense_category_id'] =   $this->input->post('expense_category_id');
            $data['payment_type']        =   'expense';
            $data['method']              =   $this->input->post('method');
            $data['amount']              =   html_escape($this->input->post('amount'));
            $data['timestamp']           =   strtotime($this->input->post('timestamp'));
            $data['year']                =   $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;
            if ($this->input->post('description') != null) {
                $data['description']     =   html_escape($this->input->post('description'));
            }
            else{
                $data['description']     =   null;
            }
            $this->db->where('payment_id' , $param2);
            $this->db->update('payment' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/expense'), 'refresh');
        }

        if ($param1 == 'delete') {
            $this->db->where('payment_id' , $param2);
            $this->db->delete('payment');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/expense'), 'refresh');
        }

        $page_data['page_name']  = 'expense';
        $page_data['page_title'] = get_phrase('expenses');
        $this->load->view('backend/index', $page_data);
    }

    function get_expenses() {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $columns = array(
            0 => 'payment_id',
            1 => 'title',
            2 => 'category',
            3 => 'method',
            4 => 'amount',
            5 => 'date',
            6 => 'options',
            7 => 'payment_id'
        );

        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir   = $this->input->post('order')[0]['dir'];

        $totalData = $this->ajaxload->all_expenses_count();
        $totalFiltered = $totalData;

        if(empty($this->input->post('search')['value'])) {
            $expenses = $this->ajaxload->all_expenses($limit,$start,$order,$dir);
        }
        else {
            $search = $this->input->post('search')['value'];
            $expenses =  $this->ajaxload->expense_search($limit,$start,$search,$order,$dir);
            $totalFiltered = $this->ajaxload->expense_search_count($search);
        }

        $data = array();
        if(!empty($expenses)) {
            foreach ($expenses as $row) {
                $category = $this->db->get_where('expense_category', array('expense_category_id' => $row->expense_category_id))->row()->name;
                if ($row->method == 1)
                    $method = get_phrase('cash');
                else if ($row->method == 2)
                    $method = get_phrase('cheque');
                else
                    $method = get_phrase('card');
                $options = '<div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                    Action <span class="caret"></span></button><ul class="dropdown-menu dropdown-default pull-right" role="menu"><li><a href="#" onclick="expense_edit_modal('.$row->payment_id.')"><i class="entypo-pencil"></i>&nbsp;'.get_phrase('edit').'</a></li><li class="divider"></li><li><a href="#" onclick="expense_delete_confirm('.$row->payment_id.')"><i class="entypo-trash"></i>&nbsp;'.get_phrase('delete').'</a></li></ul></div>';

                $nestedData['payment_id'] = $row->payment_id;
                $nestedData['title'] = $row->title;
                $nestedData['category'] = $category;
                $nestedData['method'] = $method;
                $nestedData['amount'] = $row->amount;
                $nestedData['date'] = date('d M,Y', $row->timestamp);
                $nestedData['options'] = $options;

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);
    }

    function expense_category($param1 = '' , $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'create') {
            $data['name']   =   html_escape($this->input->post('name'));
            $this->db->insert('expense_category' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(site_url('admin/expense_category'), 'refresh');
        }
        if ($param1 == 'edit') {
            $data['name']   =   html_escape($this->input->post('name'));
            $this->db->where('expense_category_id' , $param2);
            $this->db->update('expense_category' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/expense_category'), 'refresh');
        }
        if ($param1 == 'delete') {
            $this->db->where('expense_category_id' , $param2);
            $this->db->delete('expense_category');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/expense_category'), 'refresh');
        }

        $page_data['page_name']  = 'expense_category';
        $page_data['page_title'] = get_phrase('expense_category');
        $this->load->view('backend/index', $page_data);
    }

    /**********MANAGE LIBRARY / BOOKS********************/
    function book($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'create') {
            $data['name']        = html_escape($this->input->post('name'));
            $data['class_id']    = $this->input->post('class_id');
            if ($this->input->post('description') != null) {
               $data['description'] = html_escape($this->input->post('description'));
            }
            if ($this->input->post('price') != null) {
               $data['price'] = html_escape($this->input->post('price'));
            }
            if ($this->input->post('author') != null) {
               $data['author'] = html_escape($this->input->post('author'));
            }
            if(!empty($_FILES["file_name"]["name"])) {
                $data['file_name'] = $_FILES["file_name"]["name"];
            }

            $this->db->insert('book', $data);

            if(!empty($_FILES["file_name"]["name"])) {
                move_uploaded_file($_FILES["file_name"]["tmp_name"], "uploads/document/" . $_FILES["file_name"]["name"]);
            }

            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(site_url('admin/book'), 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']        = html_escape($this->input->post('name'));
            $data['class_id']    = $this->input->post('class_id');
            if ($this->input->post('description') != null) {
               $data['description'] = html_escape($this->input->post('description'));
            }
            else{
               $data['description'] = null;
            }
            if ($this->input->post('price') != null) {
               $data['price'] = html_escape($this->input->post('price'));
            }
            else{
                $data['price'] = null;
            }
            if ($this->input->post('author') != null) {
               $data['author'] = html_escape($this->input->post('author'));
            }
            else{
               $data['author'] = null;
            }

            if(!empty($_FILES["file_name"]["name"])) {
                $data['file_name'] = $_FILES["file_name"]["name"];
            }

            $this->db->where('book_id', $param2);
            $this->db->update('book', $data);

            if(!empty($_FILES["file_name"]["name"])) {
                move_uploaded_file($_FILES["file_name"]["tmp_name"], "uploads/document/" . $_FILES["file_name"]["name"]);
            }

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/book'), 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('book', array(
                'book_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('book_id', $param2);
            $this->db->delete('book');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/book'), 'refresh');
        }
        $page_data['page_name']  = 'book';
        $page_data['page_title'] = get_phrase('manage_library_books');
        $this->load->view('backend/index', $page_data);

    }

    function get_books() {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $columns = array(
            0 => 'book_id',
            1 => 'name',
            2 => 'author',
            3 => 'description',
            4 => 'price',
            5 => 'class',
            6 => 'download',
            7 => 'options',
            8 => 'book_id'
        );

        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir   = $this->input->post('order')[0]['dir'];

        $totalData = $this->ajaxload->all_books_count();
        $totalFiltered = $totalData;

        if(empty($this->input->post('search')['value'])) {
            $books = $this->ajaxload->all_books($limit,$start,$order,$dir);
        }
        else {
            $search = $this->input->post('search')['value'];
            $books =  $this->ajaxload->book_search($limit,$start,$search,$order,$dir);
            $totalFiltered = $this->ajaxload->book_search_count($search);
        }

        $data = array();
        if(!empty($books)) {
            foreach ($books as $row) {
                if ($row->file_name == null)
                    $download = '';
                else
                    $download = '<a href="'.site_url("uploads/document/$row->file_name").'" class="btn btn-blue btn-icon icon-left"><i class="entypo-download"></i>'.get_phrase('download').'</a>';

                $options = '<div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                    Action <span class="caret"></span></button><ul class="dropdown-menu dropdown-default pull-right" role="menu"><li><a href="#" onclick="book_edit_modal('.$row->book_id.')"><i class="entypo-pencil"></i>&nbsp;'.get_phrase('edit').'</a></li><li class="divider"></li><li><a href="#" onclick="book_delete_confirm('.$row->book_id.')"><i class="entypo-trash"></i>&nbsp;'.get_phrase('delete').'</a></li></ul></div>';

                $nestedData['book_id'] = $row->book_id;
                $nestedData['name'] = $row->name;
                $nestedData['author'] = $row->author;
                $nestedData['description'] = $row->description;
                $nestedData['price'] = $row->price;
                $nestedData['class'] = $this->db->get_where('class', array('class_id' => $row->class_id))->row()->name;
                $nestedData['download'] = $download;
                $nestedData['options'] = $options;

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);
    }
    /**********MANAGE TRANSPORT / VEHICLES / ROUTES********************/
    function transport($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');
        if ($param1 == 'create') {
            $data['route_name']        = html_escape($this->input->post('route_name'));
            $data['number_of_vehicle'] = html_escape($this->input->post('number_of_vehicle'));
            if ($this->input->post('description') != null) {
               $data['description']    = html_escape($this->input->post('description'));
            }
            if ($this->input->post('route_fare') != null) {
               $data['route_fare']     = html_escape($this->input->post('route_fare'));
            }

            $this->db->insert('transport', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(site_url('admin/transport'), 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['route_name']        = html_escape($this->input->post('route_name'));
            $data['number_of_vehicle'] = html_escape($this->input->post('number_of_vehicle'));
            if ($this->input->post('description') != null) {
               $data['description']    = html_escape($this->input->post('description'));
            }
            else{
                $data['description'] = null;
            }
            if ($this->input->post('route_fare') != null) {
               $data['route_fare']   = html_escape($this->input->post('route_fare'));
            }
            else{
                $data['route_fare']  = null;
            }

            $this->db->where('transport_id', $param2);
            $this->db->update('transport', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/transport'), 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('transport', array(
                'transport_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('transport_id', $param2);
            $this->db->delete('transport');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/transport'), 'refresh');
        }
        $page_data['transports'] = $this->db->get('transport')->result_array();
        $page_data['page_name']  = 'transport';
        $page_data['page_title'] = get_phrase('manage_transport');
        $this->load->view('backend/index', $page_data);

    }
    /**********MANAGE DORMITORY / HOSTELS / ROOMS ********************/
    function dormitory($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'create') {
            $data['name']           = html_escape($this->input->post('name'));
            $data['number_of_room'] = html_escape($this->input->post('number_of_room'));
            if ($this->input->post('description') != null) {
                $data['description']    = html_escape($this->input->post('description'));
            }

            $this->db->insert('dormitory', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(site_url('admin/dormitory'), 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']           = html_escape($this->input->post('name'));
            $data['number_of_room'] = html_escape($this->input->post('number_of_room'));
            if ($this->input->post('description') != null) {
                $data['description']    = html_escape($this->input->post('description'));
            }
            else{
                $data['description'] = null;
            }
            $this->db->where('dormitory_id', $param2);
            $this->db->update('dormitory', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/dormitory'), 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('dormitory', array(
                'dormitory_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('dormitory_id', $param2);
            $this->db->delete('dormitory');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/dormitory'), 'refresh');
        }
        $page_data['dormitories'] = $this->db->get('dormitory')->result_array();
        $page_data['page_name']   = 'dormitory';
        $page_data['page_title']  = get_phrase('manage_dormitory');
        $this->load->view('backend/index', $page_data);

    }

    /***MANAGE EVENT / NOTICEBOARD, WILL BE SEEN BY ALL ACCOUNTS DASHBOARD**/
    function noticeboard($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        if ($param1 == 'create') {
            $data['notice_title']     = html_escape($this->input->post('notice_title'));
            $data['notice']           = html_escape($this->input->post('notice'));
            $data['show_on_website']  = $this->input->post('show_on_website');
            $data['create_timestamp'] = strtotime($this->input->post('create_timestamp'));
            if ($_FILES['image']['name'] != '') {
              $data['image']  = $_FILES['image']['name'];
              move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/frontend/noticeboard/'. $_FILES['image']['name']);
            }
            $this->db->insert('noticeboard', $data);

            $check_sms_send = $this->input->post('check_sms');

            if ($check_sms_send == 1) {
                // sms sending configurations

                $parents  = $this->db->get('parent')->result_array();
                $students = $this->db->get('student')->result_array();
                $teachers = $this->db->get('teacher')->result_array();
                $date     = $this->input->post('create_timestamp');
                $message  = $data['notice_title'] . ' ';
                $message .= get_phrase('on') . ' ' . $date;
                foreach($parents as $row) {
                    $reciever_phone = $row['phone'];
                    $this->sms_model->send_sms($message , $reciever_phone);
                }
                foreach($students as $row) {
                    $reciever_phone = $row['phone'];
                    $this->sms_model->send_sms($message , $reciever_phone);
                }
                foreach($teachers as $row) {
                    $reciever_phone = $row['phone'];
                    $this->sms_model->send_sms($message , $reciever_phone);
                }
            }

            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(site_url('admin/noticeboard'), 'refresh');
        }
        if ($param1 == 'do_update') {
            $image = $this->db->get_where('noticeboard', array('notice_id' => $param2))->row()->image;
            $data['notice_title']     = html_escape($this->input->post('notice_title'));
            $data['notice']           = html_escape($this->input->post('notice'));
            $data['show_on_website']  = html_escape($this->input->post('show_on_website'));
            $data['create_timestamp'] = strtotime($this->input->post('create_timestamp'));
            if ($_FILES['image']['name'] != '') {
              $data['image']  = $_FILES['image']['name'];
              move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/frontend/noticeboard/'. $_FILES['image']['name']);
            } else {
              $data['image']  = $image;
            }

            $this->db->where('notice_id', $param2);
            $this->db->update('noticeboard', $data);

            $check_sms_send = $this->input->post('check_sms');

            if ($check_sms_send == 1) {
                // sms sending configurations

                $parents  = $this->db->get('parent')->result_array();
                $students = $this->db->get('student')->result_array();
                $teachers = $this->db->get('teacher')->result_array();
                $date     = $this->input->post('create_timestamp');
                $message  = $data['notice_title'] . ' ';
                $message .= get_phrase('on') . ' ' . $date;
                foreach($parents as $row) {
                    $reciever_phone = $row['phone'];
                    $this->sms_model->send_sms($message , $reciever_phone);
                }
                foreach($students as $row) {
                    $reciever_phone = $row['phone'];
                    $this->sms_model->send_sms($message , $reciever_phone);
                }
                foreach($teachers as $row) {
                    $reciever_phone = $row['phone'];
                    $this->sms_model->send_sms($message , $reciever_phone);
                }
            }

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/noticeboard'), 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('noticeboard', array(
                'notice_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('notice_id', $param2);
            $this->db->delete('noticeboard');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/noticeboard'), 'refresh');
        }
        if ($param1 == 'mark_as_archive') {
            $this->db->where('notice_id' , $param2);
            $this->db->update('noticeboard' , array('status' => 0));
            redirect(site_url('admin/noticeboard'), 'refresh');
        }

        if ($param1 == 'remove_from_archived') {
            $this->db->where('notice_id' , $param2);
            $this->db->update('noticeboard' , array('status' => 1));
            redirect(site_url('admin/noticeboard'), 'refresh');
        }
        $page_data['page_name']  = 'noticeboard';
        $page_data['page_title'] = get_phrase('manage_noticeboard');
        $this->load->view('backend/index', $page_data);
    }

    function noticeboard_edit($notice_id) {
      if ($this->session->userdata('admin_login') != 1)
          redirect(site_url('login'), 'refresh');

      $page_data['page_name']  = 'noticeboard_edit';
      $page_data['notice_id'] = $notice_id;
      $page_data['page_title'] = get_phrase('edit_notice');
      $this->load->view('backend/index', $page_data);
    }

    function reload_noticeboard() {
        $this->load->view('backend/admin/noticeboard');
    }
    /* private messaging */

    function message($param1 = 'message_home', $param2 = '', $param3 = '') {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        $max_size = 2097152;
        if ($param1 == 'send_new') {
            if (!file_exists('uploads/private_messaging_attached_file/')) {
              $oldmask = umask(0);  // helpful when used in linux server
              mkdir ('uploads/private_messaging_attached_file/', 0777);
            }
            if ($_FILES['attached_file_on_messaging']['name'] != "") {
              if($_FILES['attached_file_on_messaging']['size'] > $max_size){
                $this->session->set_flashdata('error_message' , get_phrase('file_size_can_not_be_larger_that_2_Megabyte'));
                redirect(site_url('admin/message/message_new'), 'refresh');
              }
              else{
                $file_path = 'uploads/private_messaging_attached_file/'.$_FILES['attached_file_on_messaging']['name'];
                move_uploaded_file($_FILES['attached_file_on_messaging']['tmp_name'], $file_path);
              }
            }

            $message_thread_code = $this->crud_model->send_new_private_message();
            $this->session->set_flashdata('flash_message', get_phrase('message_sent!'));
            redirect(site_url('admin/message/message_read/' . $message_thread_code), 'refresh');
        }

        if ($param1 == 'send_reply') {

            if (!file_exists('uploads/private_messaging_attached_file/')) {
              $oldmask = umask(0);  // helpful when used in linux server
              mkdir ('uploads/private_messaging_attached_file/', 0777);
            }
            if ($_FILES['attached_file_on_messaging']['name'] != "") {
              if($_FILES['attached_file_on_messaging']['size'] > $max_size){
                $this->session->set_flashdata('error_message' , get_phrase('file_size_can_not_be_larger_that_2_Megabyte'));
                redirect(site_url('admin/message/message_read/' . $param2), 'refresh');
              }
              else{
                $file_path = 'uploads/private_messaging_attached_file/'.$_FILES['attached_file_on_messaging']['name'];
                move_uploaded_file($_FILES['attached_file_on_messaging']['tmp_name'], $file_path);
              }
            }

            $this->crud_model->send_reply_message($param2);  //$param2 = message_thread_code
            $this->session->set_flashdata('flash_message', get_phrase('message_sent!'));
            redirect(site_url('admin/message/message_read/' . $param2), 'refresh');
        }

        if ($param1 == 'message_read') {
            $page_data['current_message_thread_code'] = $param2;  // $param2 = message_thread_code
            $this->crud_model->mark_thread_messages_read($param2);
        }

        $page_data['message_inner_page_name']   = $param1;
        $page_data['page_name']                 = 'message';
        $page_data['page_title']                = get_phrase('private_messaging');
        $this->load->view('backend/index', $page_data);
    }

    function group_message($param1 = "group_message_home", $param2 = ""){
      if ($this->session->userdata('admin_login') != 1)
          redirect(site_url('login'), 'refresh');
      $max_size = 2097152;
      if ($param1 == "create_group") {
        $this->crud_model->create_group();
      }
      elseif ($param1 == "edit_group") {
        $this->crud_model->update_group($param2);
      }
      elseif ($param1 == 'group_message_read') {
        $page_data['current_message_thread_code'] = $param2;
      }
      else if($param1 == 'send_reply'){
        if (!file_exists('uploads/group_messaging_attached_file/')) {
          $oldmask = umask(0);  // helpful when used in linux server
          mkdir ('uploads/group_messaging_attached_file/', 0777);
        }
        if ($_FILES['attached_file_on_messaging']['name'] != "") {
          if($_FILES['attached_file_on_messaging']['size'] > $max_size){
            $this->session->set_flashdata('error_message' , get_phrase('file_size_can_not_be_larger_that_2_Megabyte'));
            redirect(site_url('admin/group_message/group_message_read/' . $param2), 'refresh');
          }
          else{
            $file_path = 'uploads/group_messaging_attached_file/'.$_FILES['attached_file_on_messaging']['name'];
            move_uploaded_file($_FILES['attached_file_on_messaging']['tmp_name'], $file_path);
          }
        }

        $this->crud_model->send_reply_group_message($param2);  //$param2 = message_thread_code
        $this->session->set_flashdata('flash_message', get_phrase('message_sent!'));
        redirect(site_url('admin/group_message/group_message_read/' . $param2), 'refresh');
      }
      $page_data['message_inner_page_name']   = $param1;
      $page_data['page_name']                 = 'group_message';
      $page_data['page_title']                = get_phrase('group_messaging');
      $this->load->view('backend/index', $page_data);
    }
    /*****SITE/SYSTEM SETTINGS*********/
    function system_settings($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        if ($param1 == 'do_update') {

            if (isset($_POST['disable_frontend'])) {
                $data['description'] = 1;
                $this->db->where('type', 'disable_frontend');
                $this->db->update('settings' , $data);
            }
            else{
              $data['description'] = 0;
              $this->db->where('type', 'disable_frontend');
              $this->db->update('settings' , $data);
            }

            $this->update_default_controller();

            $data['description'] = html_escape($this->input->post('system_name'));
            $this->db->where('type' , 'system_name');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('system_title'));
            $this->db->where('type' , 'system_title');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('address'));
            $this->db->where('type' , 'address');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('phone'));
            $this->db->where('type' , 'phone');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('paypal_email'));
            $this->db->where('type' , 'paypal_email');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('currency'));
            $this->db->where('type' , 'currency');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('system_email'));
            $this->db->where('type' , 'system_email');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('system_name'));
            $this->db->where('type' , 'system_name');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('language'));
            $this->db->where('type' , 'language');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('text_align'));
            $this->db->where('type' , 'text_align');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('running_year'));
            $this->db->where('type' , 'running_year');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('purchase_code'));
            $this->db->where('type' , 'purchase_code');
            $this->db->update('settings' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/system_settings'), 'refresh');
        }
        if ($param1 == 'upload_logo') {
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/logo.png');
            $this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
            redirect(site_url('admin/system_settings'), 'refresh');
        }
        if ($param1 == 'change_skin') {
            $data['description'] = $param2;
            $this->db->where('type' , 'skin_colour');
            $this->db->update('settings' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('theme_selected'));
            redirect(site_url('admin/system_settings'), 'refresh');
        }
        $page_data['languages']  = $this->get_all_languages();
        $page_data['page_name']  = 'system_settings';
        $page_data['page_title'] = get_phrase('system_settings');
        $page_data['settings']   = $this->db->get('settings')->result_array();
        $this->load->view('backend/index', $page_data);
    }

    // update default controller
    function update_default_controller() {
      $status = $this->db->get_where('settings' , array('type' =>'disable_frontend'))->row()->description;
      if ($status == 1) {
        $default_controller          = 'login';
        $previous_default_controller = 'home';
      }else{
        $default_controller          = 'home';
        $previous_default_controller = 'login';
      }
      // write routes.php
      $data = file_get_contents('./application/config/routes.php');
      $data = str_replace($previous_default_controller,	$default_controller,	$data);
      file_put_contents('./application/config/routes.php', $data);
    }

    //Payment settings
    function payment_settings($param1 = ""){
      if ($this->session->userdata('admin_login') != 1)
          redirect(site_url('login'), 'refresh');

      if ($param1 == 'update_stripe_keys') {
            $this->crud_model->update_stripe_keys();
            $this->session->set_flashdata('flash_message', get_phrase('payment_settings_updated'));
            redirect(site_url('admin/payment_settings'), 'refresh');
      }

      if ($param1 == 'update_paypal_keys') {
          $this->crud_model->update_paypal_keys();
          $this->session->set_flashdata('flash_message', get_phrase('payment_settings_updated'));
          redirect(site_url('admin/payment_settings'), 'refresh');
      }
      if ($param1 == 'update_payumoney_keys') {
        $this->crud_model->update_payumoney_keys();
        $this->session->set_flashdata('flash_message', get_phrase('payment_settings_updated'));
        redirect(site_url('admin/payment_settings'), 'refresh');
      }
      $page_data['page_name']  = 'payment_settings';
      $page_data['page_title'] = get_phrase('payment_settings');
      $page_data['settings']   = $this->db->get('settings')->result_array();
      $this->load->view('backend/index', $page_data);
    }

    function smtp_settings($param1 = '', $param2 = ''){
        if ($this->session->userdata('admin_login') != 1) {
            redirect(site_url('login'), 'refresh');
        }

        if($param1 == 'update'){
            $this->crud_model->update_smtp_settings();
            $this->session->set_flashdata('flash_message', 'SMTP'.' '.get_phrase('settings_updated'));
            redirect(site_url('admin/smtp_settings'), 'refresh');
        }

        $page_data['page_name']  = 'smtp_settings';
        $page_data['page_title'] = 'SMTP'.' '.get_phrase('settings');
        $page_data['settings']   = $this->db->get('settings')->result_array();
        $this->load->view('backend/index', $page_data);
    }

    // FRONTEND
    function frontend_pages($param1 = '', $param2 = '', $param3 = '') {
      if ($this->session->userdata('admin_login') != 1) {
        redirect(site_url('login'), 'refresh');
      }
      if ($param1 == 'events') {
        $page_data['page_content']  = 'frontend_events';
      }
      if ($param1 == 'gallery') {
        $page_data['page_content']  = 'frontend_gallery';
      }
      if ($param1 == 'privacy_policy') {
        $page_data['page_content']  = 'frontend_privacy_policy';
      }
      if ($param1 == 'about_us') {
        $page_data['page_content']  = 'frontend_about_us';
      }
      if ($param1 == 'terms_conditions') {
        $page_data['page_content']  = 'frontend_terms_conditions';
      }
      if ($param1 == 'homepage_slider') {
        $page_data['page_content']  = 'frontend_slider';
      }
      if ($param1 == '' || $param1 == 'general') {
        $page_data['page_content']  = 'frontend_general_settings';
      }
      if ($param1 == 'gallery_image') {
        $page_data['page_content']  = 'frontend_gallery_image';
        $page_data['gallery_id']  = $param2;
      }
      $page_data['page_name'] = 'frontend_pages';
      $page_data['page_title']  = get_phrase('pages');
      $this->load->view('backend/index', $page_data);
    }

    function frontend_events($param1 = '', $param2 = '') {
      if ($param1 == 'add_event') {
        $this->frontend_model->add_event();
        $this->session->set_flashdata('flash_message' , get_phrase('event_added_successfully'));
        redirect(site_url('admin/frontend_pages/events'), 'refresh');
      }
      if ($param1 == 'edit_event') {
        $this->frontend_model->edit_event($param2);
        $this->session->set_flashdata('flash_message' , get_phrase('event_updated_successfully'));
        redirect(site_url('admin/frontend_pages/events'), 'refresh');
      }
      if ($param1 == 'delete') {
        $this->frontend_model->delete_event($param2);
        $this->session->set_flashdata('flash_message' , get_phrase('event_deleted'));
        redirect(site_url('admin/frontend_pages/events'), 'refresh');
      }
    }

    function frontend_gallery($param1 = '', $param2 = '', $param3 = '') {
      if ($param1 == 'add_gallery') {
        $this->frontend_model->add_gallery();
        $this->session->set_flashdata('flash_message' , get_phrase('gallery_added_successfully'));
        redirect(site_url('admin/frontend_pages/gallery'), 'refresh');
      }
      if ($param1 == 'edit_gallery') {
        $this->frontend_model->edit_gallery($param2);
        $this->session->set_flashdata('flash_message' , get_phrase('gallery_updated_successfully'));
        redirect(site_url('admin/frontend_pages/gallery'), 'refresh');
      }
      if ($param1 == 'upload_images') {
        $this->frontend_model->add_gallery_images($param2);
        $this->session->set_flashdata('flash_message' , get_phrase('images_uploaded'));
        redirect(site_url('admin/frontend_pages/gallery_image/'.$param2), 'refresh');
      }
      if ($param1 == 'delete_image') {
        $this->frontend_model->delete_gallery_image($param2);
        $this->session->set_flashdata('flash_message' , get_phrase('images_deleted'));
        redirect(site_url('admin/frontend_pages/gallery_image/'.$param3), 'refresh');
      }

    }

    function frontend_news($param1 = '', $param2 = '') {
      if ($param1 == 'add_news') {
        $this->frontend_model->add_news();
        $this->session->set_flashdata('flash_message' , get_phrase('news_added_successfully'));
        redirect(site_url('admin/frontend_pages/news'), 'refresh');
      }
      if ($param1 == 'edit_news') {

      }
      if ($param1 == 'delete') {
        $this->frontend_model->delete_news($param2);
        $this->session->set_flashdata('flash_message' , get_phrase('news_was_deleted'));
        redirect(site_url('admin/frontend_pages/news'), 'refresh');
      }
    }

    function frontend_settings($task) {
      if ($task == 'update_terms_conditions') {
        $this->frontend_model->update_terms_conditions();
        $this->session->set_flashdata('flash_message' , get_phrase('terms_updated'));
        redirect(site_url('admin/frontend_pages/terms_conditions'), 'refresh');
      }
      if ($task == 'update_about_us') {
        $this->frontend_model->update_about_us();
        $this->session->set_flashdata('flash_message' , get_phrase('about_us_updated'));
        redirect(site_url('admin/frontend_pages/about_us'), 'refresh');
      }
      if ($task == 'update_privacy_policy') {
        $this->frontend_model->update_privacy_policy();
        $this->session->set_flashdata('flash_message' , get_phrase('privacy_policy_updated'));
        redirect(site_url('admin/frontend_pages/privacy_policy'), 'refresh');
      }
      if ($task == 'update_general_settings') {
        $this->frontend_model->update_frontend_general_settings();
        $this->session->set_flashdata('flash_message' , get_phrase('general_settings_updated'));
        redirect(site_url('admin/frontend_pages/general'), 'refresh');
      }
      if ($task == 'update_slider_images') {
        $this->frontend_model->update_slider_images();
        $this->session->set_flashdata('flash_message' , get_phrase('slider_images_updated'));
        redirect(site_url('admin/frontend_pages/homepage_slider'), 'refresh');
      }
    }

    function frontend_themes() {
      if ($this->session->userdata('admin_login') != 1) {
        redirect(site_url('login'), 'refresh');
      }
      $page_data['page_name'] = 'frontend_themes';
      $page_data['page_title']  = get_phrase('themes');
      $this->load->view('backend/index', $page_data);
    }

    // FRONTEND


    function get_session_changer()
    {
        $this->load->view('backend/admin/change_session');
    }

    function change_session()
    {
        $data['description'] = $this->input->post('running_year');
        $this->db->where('type' , 'running_year');
        $this->db->update('settings' , $data);
        $this->session->set_flashdata('flash_message' , get_phrase('session_changed'));
        redirect(site_url('admin/dashboard'), 'refresh');
    }

	/***** UPDATE PRODUCT *****/

	function update( $task = '', $purchase_code = '' ) {

        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        // Create update directory.
        $dir    = 'update';
        if ( !is_dir($dir) )
            mkdir($dir, 0777, true);

        $zipped_file_name   = $_FILES["file_name"]["name"];
        $path               = 'update/' . $zipped_file_name;

        move_uploaded_file($_FILES["file_name"]["tmp_name"], $path);

        // Unzip uploaded update file and remove zip file.
        $zip = new ZipArchive;
        $res = $zip->open($path);
        if ($res === TRUE) {
            $zip->extractTo('update');
            $zip->close();
            unlink($path);
        }

        $unzipped_file_name = substr($zipped_file_name, 0, -4);
        $str                = file_get_contents('./update/' . $unzipped_file_name . '/update_config.json');
        $json               = json_decode($str, true);

		// Run php modifications
		require './update/' . $unzipped_file_name . '/update_script.php';

        // Create new directories.
        if(!empty($json['directory'])) {
            foreach($json['directory'] as $directory) {
                if ( !is_dir( $directory['name']) )
                    mkdir( $directory['name'], 0777, true );
            }
        }

        // Create/Replace new files.
        if(!empty($json['files'])) {
            foreach($json['files'] as $file)
                copy($file['root_directory'], $file['update_directory']);
        }

        $this->session->set_flashdata('flash_message' , get_phrase('product_updated_successfully'));
        redirect(site_url('admin/system_settings'), 'refresh');
    }

    /*****SMS SETTINGS*********/
    function sms_settings($param1 = '' , $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'clickatell') {

            $data['description'] = html_escape($this->input->post('clickatell_user'));
            $this->db->where('type' , 'clickatell_user');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('clickatell_password'));
            $this->db->where('type' , 'clickatell_password');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('clickatell_api_id'));
            $this->db->where('type' , 'clickatell_api_id');
            $this->db->update('settings' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/sms_settings'), 'refresh');
        }

        if ($param1 == 'twilio') {

            $data['description'] = html_escape($this->input->post('twilio_account_sid'));
            $this->db->where('type' , 'twilio_account_sid');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('twilio_auth_token'));
            $this->db->where('type' , 'twilio_auth_token');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('twilio_sender_phone_number'));
            $this->db->where('type' , 'twilio_sender_phone_number');
            $this->db->update('settings' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/sms_settings'), 'refresh');
        }
        if ($param1 == 'msg91') {

            $data['description'] = html_escape($this->input->post('authentication_key'));
            $this->db->where('type' , 'msg91_authentication_key');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('sender_ID'));
            $this->db->where('type' , 'msg91_sender_ID');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('msg91_route'));
            $this->db->where('type' , 'msg91_route');
            $this->db->update('settings' , $data);

            $data['description'] = html_escape($this->input->post('msg91_country_code'));
            $this->db->where('type' , 'msg91_country_code');
            $this->db->update('settings' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/sms_settings'), 'refresh');
        }

        if ($param1 == 'active_service') {

            $data['description'] = html_escape($this->input->post('active_sms_service'));
            $this->db->where('type' , 'active_sms_service');
            $this->db->update('settings' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(site_url('admin/sms_settings'), 'refresh');
        }

        $page_data['page_name']  = 'sms_settings';
        $page_data['page_title'] = get_phrase('sms_settings');
        $page_data['settings']   = $this->db->get('settings')->result_array();
        $this->load->view('backend/index', $page_data);
    }




























    function get_list_of_directories_and_files($dir = APPPATH, &$results = array()) {
        $files = scandir($dir);
        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                $this->get_list_of_directories_and_files($path, $results);
                $results[] = $path;
            }
        }
        return $results;
    }

    function get_all_php_files() {
        $all_files = $this->get_list_of_directories_and_files();
        foreach ($all_files as $file) {
            $info = pathinfo($file);
            if( isset($info['extension']) && strtolower($info['extension']) == 'php') {
                // echo $file.' <br/> ';
                if ($fh = fopen($file, 'r')) {
                    while (!feof($fh)) {
                        $line = fgets($fh);
                        preg_match_all('/get_phrase\(\'(.*?)\'\)\;/s', $line, $matches);
                        foreach ($matches[1] as $matche) {
                            get_phrase($matche);
                        }
                    }
                    fclose($fh);
                }
            }
        }

        echo 'I Am So Lit';
    }

    function get_list_of_language_files($dir = APPPATH.'/language', &$results = array()) {
        $files = scandir($dir);
        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                $this->get_list_of_directories_and_files($path, $results);
                $results[] = $path;
            }
        }
        return $results;
    }

    function get_all_languages() {
        $language_files = array();
        $all_files = $this->get_list_of_language_files();
        foreach ($all_files as $file) {
            $info = pathinfo($file);
            if( isset($info['extension']) && strtolower($info['extension']) == 'json') {
                $file_name = explode('.json', $info['basename']);
                array_push($language_files, $file_name[0]);
            }
        }

        return $language_files;
    }

    // Language Functions
    public function manage_language($param1 = '', $param2 = '', $param3 = ''){
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        if ($param1 == 'add_language') {
            saveDefaultJSONFile($this->input->post('language'));
            $this->session->set_flashdata('flash_message', get_phrase('language_added_successfully'));
            redirect(site_url('admin/manage_language'), 'refresh');
        }

        if ($param1 == 'delete_language') {
            if (file_exists('application/language/'.$param2.'.json')) {
                unlink('application/language/'.$param2.'.json');
                $this->session->set_flashdata('flash_message', get_phrase('language_deleted_successfully'));
                redirect(site_url('admin/manage_language'), 'refresh');
            }
        }
        
        if ($param1 == 'add_phrase') {
            $new_phrase = get_phrase($this->input->post('phrase'));
            $this->session->set_flashdata('flash_message', $new_phrase.' '.get_phrase('has_been_added_successfully'));
            redirect(site_url('admin/manage_language'), 'refresh');
        }

        if ($param1 == 'edit_phrase') {
            $page_data['edit_profile'] = $param2;
        }

        $page_data['languages']             = $this->get_all_languages();
        $page_data['page_name']             =   'manage_language';
        $page_data['page_title']            =   get_phrase('multi_language_settings');
        $this->load->view('backend/index', $page_data);
    }

    public function update_phrase_with_ajax() {
        $current_editing_language = $this->input->post('currentEditingLanguage');
        $updatedValue = $this->input->post('updatedValue');
        $key = $this->input->post('key');
        saveJSONFile($current_editing_language, $key, $updatedValue);
        echo $current_editing_language.' '.$key.' '.$updatedValue;
        
    }
























    /******MANAGE OWN PROFILE AND CHANGE PASSWORD***/
    function manage_profile($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'update_profile_info') {
            $data['name']  = html_escape($this->input->post('name'));
            $data['email'] = html_escape($this->input->post('email'));

            $admin_id = $param2;

            $validation = email_validation_for_edit($data['email'], $admin_id, 'admin');
            if($validation == 1){
                $this->db->where('admin_id', $this->session->userdata('admin_id'));
                $this->db->update('admin', $data);
                move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/admin_image/' . $this->session->userdata('admin_id') . '.jpg');
                $this->session->set_flashdata('flash_message', get_phrase('account_updated'));
            }
            else{
                $this->session->set_flashdata('error_message', get_phrase('this_email_id_is_not_available'));
            }
            redirect(site_url('admin/manage_profile'), 'refresh');
        }
        if ($param1 == 'change_password') {
            $data['password']             = sha1($this->input->post('password'));
            $data['new_password']         = sha1($this->input->post('new_password'));
            $data['confirm_new_password'] = sha1($this->input->post('confirm_new_password'));

            $current_password = $this->db->get_where('admin', array(
                'admin_id' => $this->session->userdata('admin_id')
            ))->row()->password;
            if ($current_password == $data['password'] && $data['new_password'] == $data['confirm_new_password']) {
                $this->db->where('admin_id', $this->session->userdata('admin_id'));
                $this->db->update('admin', array(
                    'password' => $data['new_password']
                ));
                $this->session->set_flashdata('flash_message', get_phrase('password_updated'));
            } else {
                $this->session->set_flashdata('error_message', get_phrase('password_mismatch'));
            }
            redirect(site_url('admin/manage_profile'), 'refresh');
        }
        $page_data['page_name']  = 'manage_profile';
        $page_data['page_title'] = get_phrase('manage_profile');
        $page_data['edit_data']  = $this->db->get_where('admin', array(
            'admin_id' => $this->session->userdata('admin_id')
        ))->result_array();
        $this->load->view('backend/index', $page_data);
    }

    // VIEW QUESTION PAPERS
    function question_paper($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $data['page_name']  = 'question_paper';
        $data['page_title'] = get_phrase('question_paper');
        $this->load->view('backend/index', $data);
    }

    // MANAGE LIBRARIANS
    function librarian($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        if ($param1 == 'create') {
            $data['name']       = html_escape($this->input->post('name'));
            $data['email']      = html_escape($this->input->post('email'));
            $data['password']   = sha1($this->input->post('password'));
            $validation = email_validation($data['email']);
            if ($validation == 1) {
                $this->db->insert('librarian', $data);
                $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
                $this->email_model->account_opening_email('librarian', $data['email'], $this->input->post('password')); //SEND EMAIL ACCOUNT OPENING EMAIL
            }
            else{
                $this->session->set_flashdata('error_message' , get_phrase('this_email_id_is_not_available'));
            }
            redirect(site_url('admin/librarian'), 'refresh');
        }

        if ($param1 == 'edit') {
            $data['name']   = html_escape($this->input->post('name'));
            $data['email']  = html_escape($this->input->post('email'));
            $validation = email_validation_for_edit($data['email'], $param2, 'librarian');
            if ($validation == 1) {
                $this->db->where('librarian_id' , $param2);
                $this->db->update('librarian' , $data);
                $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            }
            else{
                $this->session->set_flashdata('error_message' , get_phrase('this_email_id_is_not_available'));
            }

            redirect(site_url('admin/librarian'), 'refresh');
        }

        if ($param1 == 'delete') {
            $this->db->where('librarian_id' , $param2);
            $this->db->delete('librarian');

            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/librarian'), 'refresh');
        }

        $page_data['page_title']    = get_phrase('all_librarians');
        $page_data['page_name']     = 'librarian';
        $this->load->view('backend/index', $page_data);
    }

    // MANAGE ACCOUNTANTS
    function accountant($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        if ($param1 == 'create') {
            $data['name']       = html_escape($this->input->post('name'));
            $data['email']      = html_escape($this->input->post('email'));
            $data['password']   = sha1($this->input->post('password'));

            $validation = email_validation($data['email']);
            if ($validation == 1) {
                $this->db->insert('accountant', $data);
                $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
                $this->email_model->account_opening_email('accountant', $data['email'], html_escape($this->input->post('password'))); //SEND EMAIL ACCOUNT OPENING EMAIL
            }
            else{
                $this->session->set_flashdata('error_message' , get_phrase('this_email_id_is_not_available'));
            }

            redirect(site_url('admin/accountant'), 'refresh');
        }

        if ($param1 == 'edit') {
            $data['name']   = html_escape($this->input->post('name'));
            $data['email']  = html_escape($this->input->post('email'));

            $validation = email_validation_for_edit($data['email'], $param2, 'accountant');
            if($validation == 1){
                $this->db->where('accountant_id' , $param2);
                $this->db->update('accountant' , $data);
                $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            }
            else{
                $this->session->set_flashdata('error_message' , get_phrase('this_email_id_is_not_available'));
            }

            redirect(site_url('admin/accountant'), 'refresh');
        }

        if ($param1 == 'delete') {
            $this->db->where('accountant_id' , $param2);
            $this->db->delete('accountant');

            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/accountant'), 'refresh');
        }

        $page_data['page_title']    = get_phrase('all_accountants');
        $page_data['page_name']     = 'accountant';
        $this->load->view('backend/index', $page_data);
    }


    // bulk student_add using CSV
    function generate_bulk_student_csv($class_id = '', $section_id = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $data['class_id']   = $class_id;
        $data['section_id'] = $section_id;
        $data['year']       = $this->db->get_where('settings', array('type'=>'running_year'))->row()->description;

        $file   = fopen("uploads/bulk_student.csv", "w");
        $line   = array('StudentName', 'Id', 'Email', 'Password', 'Phone', 'Address', 'ParentID', 'Gender');
        fputcsv($file, $line, ',');
       echo $file_path = base_url() . 'uploads/bulk_student.csv';
    }
    // CSV IMPORT
    function bulk_student_add_using_csv($param1 = '') {

        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

       if ($param1 == 'import') {
          if ($this->input->post('class_id') != '' && $this->input->post('section_id') != '') {

              move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/bulk_student.csv');
              $csv = array_map('str_getcsv', file('uploads/bulk_student.csv'));
              $count = 1;
              $array_size = sizeof($csv);

             foreach ($csv as $row) {
                  if ($count == 1) {
                      $count++;
                      continue;
                  }
                  $password = $row[3];

                  $data['name']      = $row[0];
                  $data['student_code']  = $row[1];
                  $data['email']     = $row[2];
                  $data['password']  = sha1($row[3]);
                  $data['phone']     = $row[4];
                  $data['address']   = $row[5];
                  $data['parent_id'] = $row[6];
                  $data['sex']       = strtolower($row[7]);
                 //student id (code) validation
                 $code_validation = code_validation_insert($data['student_code']);
                 if(!$code_validation){
                     $this->session->set_flashdata('error_message' , get_phrase('this_id_no_is_not_available'));
                     redirect(site_url('admin/student_add'), 'refresh');
                 }
                 //student id validation ends

                  $validation = email_validation($data['email']);
                  if ($validation == 1) {
                    $this->db->insert('student', $data);
                    $student_id = $this->db->insert_id();

                    $data2['student_id']  = $student_id;
                    $data2['class_id']    = $this->input->post('class_id');
                    $data2['section_id']  = $this->input->post('section_id');
//                    $data2['roll']        = $row[1];
                    $data2['enroll_code'] =   substr(md5(rand(0, 1000000)), 0, 7);
                    $data2['date_added']  =   strtotime(date("Y-m-d H:i:s"));
                    $data2['year']        =   $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;
                    $this->db->insert('enroll' , $data2);
                  }
                  else{
                    if ($array_size == 2) {
                      $this->session->set_flashdata('error_message', get_phrase('this_email_id_"').$data['email'].get_phrase('"_is_not_available'));
                      redirect(site_url('admin/student_bulk_add'), 'refresh');
                    }
                    elseif($array_size > 2){
                      $this->session->set_flashdata('error_message', get_phrase('some_email_IDs_are_not_available'));
                    }
                  }

              }


              $this->session->set_flashdata('flash_message', get_phrase('student_imported'));
              redirect(site_url('admin/student_bulk_add'), 'refresh');
           }
           else{
             $this->session->set_flashdata('error_message', get_phrase('please_make_sure_class_and_section_is_selected'));
             redirect(site_url('admin/student_bulk_add'), 'refresh');
           }
        }
        $page_data['page_name']  = 'student_bulk_add';
        $page_data['page_title'] = get_phrase('add_bulk_student');
        $this->load->view('backend/index', $page_data);
    }

    function study_material($task = "", $document_id = "")
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        if ($task == "create")
        {
            $this->crud_model->save_study_material_info();
            $this->session->set_flashdata('flash_message' , get_phrase('study_material_info_saved_successfuly'));
            redirect(site_url('admin/study_material'), 'refresh');
        }

        if ($task == "update")
        {
            $this->crud_model->update_study_material_info($document_id);
            $this->session->set_flashdata('flash_message' , get_phrase('study_material_info_updated_successfuly'));
            redirect(site_url('admin/study_material'), 'refresh');
        }

        if ($task == "delete")
        {
            $this->crud_model->delete_study_material_info($document_id);
            redirect(site_url('admin/study_material'), 'refresh');
        }

        $data['study_material_info']    = $this->crud_model->select_study_material_info();
        $data['page_name']              = 'study_material';
        $data['page_title']             = get_phrase('study_material');
        $this->load->view('backend/index', $data);
    }

    //new code
    function print_id($id){
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        $data['id'] = $id;
        $this->load->view('backend/admin/print_id', $data);
    }

    function create_barcode($student_id)
    {

        return $this->Barcode_model->create_barcode($student_id);

    }

    // Details of searched student
    function student_details($param1 = ""){
      if ($this->session->userdata('admin_login') != 1)
          redirect(site_url('login'), 'refresh');

      $student_identifier = html_escape($this->input->post('student_identifier'));
      $query_by_code = $this->db->get_where('student', array('student_code' => $student_identifier));

      if ($query_by_code->num_rows() == 0) {
        $this->db->like('name', $student_identifier);
        $query_by_name = $this->db->get('student');
        if ($query_by_name->num_rows() == 0) {
          $this->session->set_flashdata('error_message' , get_phrase('no_student_found'));
          redirect(site_url('admin/dashboard'), 'refresh');
        }
        else{
          $page_data['student_information'] = $query_by_name->result_array();
        }
      }
      else{
        $page_data['student_information'] = $query_by_code->result_array();
      }
      $page_data['page_name']  	= 'search_result';
  		$page_data['page_title'] 	= get_phrase('search_result');
  		$this->load->view('backend/index', $page_data);
    }


    // online exam
    function manage_online_exam($param1 = "", $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $running_year = get_settings('running_year');

        if ($param1 == '') {
            $match = array('status !=' => 'expired', 'running_year' => $running_year);
            $page_data['status'] = 'active';
            $this->db->order_by("exam_date", "dsc");
            $page_data['online_exams'] = $this->db->where($match)->get('online_exam')->result_array();
        }

        if ($param1 == 'expired') {
            $match = array('status' => 'expired', 'running_year' => $running_year);
            $page_data['status'] = 'expired';
            $this->db->order_by("exam_date", "dsc");
            $page_data['online_exams'] = $this->db->where($match)->get('online_exam')->result_array();
        }

        if ($param1 == 'create') {
            if ($this->input->post('class_id') > 0 && $this->input->post('section_id') > 0 && $this->input->post('subject_id') > 0) {
                $this->crud_model->create_online_exam();
                $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
                redirect(site_url('admin/manage_online_exam'), 'refresh');
            }
            else {
                $this->session->set_flashdata('error_message' , get_phrase('make_sure_to_select_valid_class_').','.get_phrase('_section_and_subject'));
                redirect(site_url('admin/manage_online_exam'), 'refresh');
            }
        }
        if ($param1 == 'edit') {
            if ($this->input->post('class_id') > 0 && $this->input->post('section_id') > 0 && $this->input->post('subject_id') > 0) {
                $this->crud_model->update_online_exam();
                $this->session->set_flashdata('flash_message' , get_phrase('data_updated_successfully'));
                redirect(site_url('admin/manage_online_exam'), 'refresh');
            }
            else{
                $this->session->set_flashdata('error_message' , get_phrase('make_sure_to_select_valid_class_').','.get_phrase('_section_and_subject'));
                redirect(site_url('admin/manage_online_exam'), 'refresh');
            }
        }
        if ($param1 == 'delete') {
            $this->db->where('online_exam_id', $param2);
            $this->db->delete('online_exam');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(site_url('admin/manage_online_exam'), 'refresh');
        }
        $page_data['page_name'] = 'manage_online_exam';
        $page_data['page_title'] = get_phrase('manage_online_exam');
        $this->load->view('backend/index', $page_data);
    }

    function online_exam_questions_print_view($online_exam_id, $answers) {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        $page_data['online_exam_id'] = $online_exam_id;
        $page_data['answers'] = $answers;
        $page_data['page_title'] = get_phrase('questions_print');
        $this->load->view('backend/admin/online_exam_questions_print_view', $page_data);
    }

    function create_online_exam(){
        $page_data['page_name'] = 'add_online_exam';
        $page_data['page_title'] = get_phrase('add_an_online_exam');
        $this->load->view('backend/index', $page_data);
    }

    function update_online_exam($param1 = ""){
        $page_data['online_exam_id'] = $param1;
        $page_data['page_name'] = 'edit_online_exam';
        $page_data['page_title'] = get_phrase('update_online_exam');
        $this->load->view('backend/index', $page_data);
    }

    function manage_online_exam_status($online_exam_id = "", $status = ""){
        $this->crud_model->manage_online_exam_status($online_exam_id, $status);
        redirect(site_url('admin/manage_online_exam'), 'refresh');
    }

    function load_question_type($type, $online_exam_id) {
        $page_data['question_type'] = $type;
        $page_data['online_exam_id'] = $online_exam_id;
        $this->load->view('backend/admin/online_exam_add_'.$type, $page_data);
    }

    function manage_online_exam_question($online_exam_id = "", $task = "", $type = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        if ($task == 'add') {
            if ($type == 'multiple_choice') {
                $this->crud_model->add_multiple_choice_question_to_online_exam($online_exam_id);
            }
            elseif ($type == 'true_false') {
                $this->crud_model->add_true_false_question_to_online_exam($online_exam_id);
            }
            elseif ($type == 'fill_in_the_blanks') {
                $this->crud_model->add_fill_in_the_blanks_question_to_online_exam($online_exam_id);
            }
            redirect(site_url('admin/manage_online_exam_question/'.$online_exam_id), 'refresh');
        }

        $page_data['online_exam_id'] = $online_exam_id;
        $page_data['page_name'] = 'manage_online_exam_question';
        $page_data['page_title'] = $this->db->get_where('online_exam', array('online_exam_id'=>$online_exam_id))->row()->title;
        $this->load->view('backend/index', $page_data);
    }

    function update_online_exam_question($question_id = "", $task = "", $online_exam_id = "") {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        $online_exam_id = $this->db->get_where('question_bank', array('question_bank_id' => $question_id))->row()->online_exam_id;
        $type = $this->db->get_where('question_bank', array('question_bank_id' => $question_id))->row()->type;
        if ($task == "update") {
            if ($type == 'multiple_choice') {
                $this->crud_model->update_multiple_choice_question($question_id);
            }
            elseif($type == 'true_false'){
                $this->crud_model->update_true_false_question($question_id);
            }
            elseif($type == 'fill_in_the_blanks'){
                $this->crud_model->update_fill_in_the_blanks_question($question_id);
            }
            redirect(site_url('admin/manage_online_exam_question/'.$online_exam_id), 'refresh');
        }
        $page_data['question_id'] = $question_id;
        $page_data['page_name'] = 'update_online_exam_question';
        $page_data['page_title'] = get_phrase('update_question');
        $this->load->view('backend/index', $page_data);
    }

    function delete_question_from_online_exam($question_id){
        $online_exam_id = $this->db->get_where('question_bank', array('question_bank_id' => $question_id))->row()->online_exam_id;
        $this->crud_model->delete_question_from_online_exam($question_id);
        $this->session->set_flashdata('flash_message' , get_phrase('question_deleted'));
        redirect(site_url('admin/manage_online_exam_question/'.$online_exam_id), 'refresh');
    }

    function manage_multiple_choices_options() {
        $page_data['number_of_options'] = $this->input->post('number_of_options');
        $this->load->view('backend/admin/manage_multiple_choices_options', $page_data);
    }

    function get_sections_for_ssph($class_id) {
        $sections = $this->db->get_where('section', array('class_id' => $class_id))->result_array();
        $options = '';
        foreach ($sections as $row) {
            $options .= '<option value="'.$row['section_id'].'">'.$row['name'].'</option>';
        }
        echo '<select class="" name="section_id" id="section_id">'.$options.'</select>';
    }

    function get_students_for_ssph($class_id, $section_id) {
        $enrolls = $this->db->get_where('enroll', array('class_id' => $class_id, 'section_id' => $section_id))->result_array();
        $options = '';
        foreach ($enrolls as $row) {
            $name = $this->db->get_where('student', array('student_id' => $row['student_id']))->row()->name;
            $options .= '<option value="'.$row['student_id'].'">'.$name.'</option>';
        }
        echo '<select class="" name="student_id" id="student_id">'.$options.'</select>';
    }

    function get_payment_history_for_ssph($student_id) {
        $page_data['student_id'] = $student_id;
        $this->load->view('backend/admin/student_specific_payment_history_table', $page_data);
    }

    function view_online_exam_result($online_exam_id){
        $page_data['page_name'] = 'view_online_exam_results';
        $page_data['page_title'] = get_phrase('result');
        $page_data['online_exam_id'] = $online_exam_id;
        $this->load->view('backend/index',$page_data);
    }
}
