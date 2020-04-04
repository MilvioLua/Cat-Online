<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Crud_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function clear_cache() {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }

    function get_type_name_by_id($type = '', $type_id = '', $field = 'name') {
        if ($type_id != null && $type_id != 0){
            return $this->db->get_where($type, array($type.'_id' => $type_id))->row()->$field;
        }

    }

    ////////STUDENT/////////////
    function get_students($class_id) {
        $query = $this->db->get_where('student', array('class_id' => $class_id));
        return $query->result_array();
    }

    function get_student_info($student_id) {
        $query = $this->db->get_where('student', array('student_id' => $student_id));
        return $query->result_array();
    }

    function get_student_info_by_id($student_id) {
        $query = $this->db->get_where('student', array('student_id' => $student_id))->row_array();
        return $query;
    }

    /////////TEACHER/////////////
    function get_teachers() {
        $query = $this->db->get('teacher');
        return $query->result_array();
    }

    function get_teacher_name($teacher_id) {
        $query = $this->db->get_where('teacher', array('teacher_id' => $teacher_id));
        $res = $query->result_array();
        foreach ($res as $row)
            return $row['name'];
    }

    function get_teacher_info($teacher_id) {
        $query = $this->db->get_where('teacher', array('teacher_id' => $teacher_id));
        return $query->result_array();
    }

    //////////SUBJECT/////////////
    function get_subjects() {
        $query = $this->db->get('subject');
        return $query->result_array();
    }

    function get_subject_info($subject_id) {
        $query = $this->db->get_where('subject', array('subject_id' => $subject_id));
        return $query->result_array();
    }

    function get_subjects_by_class($class_id) {
        $query = $this->db->get_where('subject', array('class_id' => $class_id));
        return $query->result_array();
    }

    function get_subject_name_by_id($subject_id) {
        $query = $this->db->get_where('subject', array('subject_id' => $subject_id))->row();
        return $query->name;
    }

    ////////////CLASS///////////
    function get_class_name($class_id) {
        $query = $this->db->get_where('class', array('class_id' => $class_id));
        $res = $query->result_array();
        foreach ($res as $row)
            return $row['name'];
    }

    function get_class_name_numeric($class_id) {
        $query = $this->db->get_where('class', array('class_id' => $class_id));
        $res = $query->result_array();
        foreach ($res as $row)
            return $row['name_numeric'];
    }

    function get_classes() {
        $query = $this->db->get('class');
        return $query->result_array();
    }

    function get_class_info($class_id) {
        $query = $this->db->get_where('class', array('class_id' => $class_id));
        return $query->result_array();
    }

    //////////EXAMS/////////////
    function get_exams() {
        $query = $this->db->get_where('exam' , array(
            'year' => $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description
        ));
        return $query->result_array();
    }

    function get_exam_info($exam_id) {
        $query = $this->db->get_where('exam', array('exam_id' => $exam_id));
        return $query->result_array();
    }

    //////////GRADES/////////////
    function get_grades() {
        $query = $this->db->get('grade');
        return $query->result_array();
    }

    function get_grade_info($grade_id) {
        $query = $this->db->get_where('grade', array('grade_id' => $grade_id));
        return $query->result_array();
    }

    function get_obtained_marks( $exam_id , $class_id , $subject_id , $student_id) {
        $marks = $this->db->get_where('mark' , array(
                                    'subject_id' => $subject_id,
                                        'exam_id' => $exam_id,
                                            'class_id' => $class_id,
                                                'student_id' => $student_id))->result_array();

        foreach ($marks as $row) {
            echo $row['mark_obtained'];
        }
    }

    function get_highest_marks( $exam_id , $class_id , $subject_id ) {
        $this->db->where('exam_id' , $exam_id);
        $this->db->where('class_id' , $class_id);
        $this->db->where('subject_id' , $subject_id);
        $this->db->select_max('mark_obtained');
        $highest_marks = $this->db->get('mark')->result_array();
        foreach($highest_marks as $row) {
            echo $row['mark_obtained'];
        }
    }

    function get_grade($mark_obtained) {
        $query = $this->db->get('grade');
        $grades = $query->result_array();
        foreach ($grades as $row) {
            if ($mark_obtained >= $row['mark_from'] && $mark_obtained <= $row['mark_upto'])
                return $row;
        }
    }

    function create_log($data) {
        $data['timestamp'] = strtotime(date('Y-m-d') . ' ' . date('H:i:s'));
        $data['ip'] = $_SERVER["REMOTE_ADDR"];
        $location = new SimpleXMLElement(file_get_contents('http://freegeoip.net/xml/' . $_SERVER["REMOTE_ADDR"]));
        $data['location'] = $location->City . ' , ' . $location->CountryName;
        $this->db->insert('log', $data);
    }

    function get_system_settings() {
        $query = $this->db->get('settings');
        return $query->result_array();
    }

    ////////BACKUP RESTORE/////////
    function create_backup($type) {
        $this->load->dbutil();


        $options = array(
            'format' => 'txt', // gzip, zip, txt
            'add_drop' => TRUE, // Whether to add DROP TABLE statements to backup file
            'add_insert' => TRUE, // Whether to add INSERT data to backup file
            'newline' => "\n"               // Newline character used in backup file
        );


        if ($type == 'all') {
            $tables = array('');
            $file_name = 'system_backup';
        } else {
            $tables = array('tables' => array($type));
            $file_name = 'backup_' . $type;
        }

        $backup = & $this->dbutil->backup(array_merge($options, $tables));


        $this->load->helper('download');
        force_download($file_name . '.sql', $backup);
    }

    /////////RESTORE TOTAL DB/ DB TABLE FROM UPLOADED BACKUP SQL FILE//////////
    function restore_backup() {
        //move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/backup.sql');
        $this->load->dbutil();


        $prefs = array(
            'filepath' => base_url().'uploads/backup.sql',
            'delete_after_upload' => TRUE,
            'delimiter' => ';'
        );
        $restore = & $this->dbutil->restore($prefs);
        unlink($prefs['filepath']);
    }

    /////////DELETE DATA FROM TABLES///////////////
    function truncate($type) {
        if ($type == 'all') {
            $this->db->truncate('student');
            $this->db->truncate('mark');
            $this->db->truncate('teacher');
            $this->db->truncate('subject');
            $this->db->truncate('class');
            $this->db->truncate('exam');
            $this->db->truncate('grade');
        } else {
            $this->db->truncate($type);
        }
    }

    ////////IMAGE URL//////////
    function get_image_url($type = '', $id = '') {
        if (file_exists('uploads/' . $type . '_image/' . $id . '.jpg'))
            $image_url = base_url('uploads/' . $type . '_image/' . $id . '.jpg');
        else
            $image_url = base_url('uploads/user.jpg');

        return $image_url;
    }

    ////////STUDY MATERIAL//////////
    function save_study_material_info()
    {
        $data['timestamp']         = strtotime($this->input->post('timestamp'));
        $data['title'] 		         = html_escape($this->input->post('title'));
        $data['teacher_id'] 		   = $this->session->userdata('teacher_id');
        $data['description']       = html_escape($this->input->post('description'));
        $data['file_name'] 	       = html_escape($_FILES["file_name"]["name"]);
        $data['file_type']     	   = html_escape($this->input->post('file_type'));
        $data['class_id'] 	       = $this->input->post('class_id');
        $data['subject_id']        = $this->input->post('subject_id');
        $this->db->insert('document',$data);

        $document_id            = $this->db->insert_id();
        move_uploaded_file($_FILES["file_name"]["tmp_name"], "uploads/document/" . $_FILES["file_name"]["name"]);
    }

    function select_study_material_info()
    {
        $this->db->order_by("timestamp", "desc");
        return $this->db->get_where('document')->result_array();
    }
    //selecting study material info for specific teacher

    function select_study_material_info_for_teacher()
    {
        $this->db->order_by("timestamp", "desc");
        return $this->db->get_where('document',array('teacher_id'=>$this->session->userdata('teacher_id')))->result_array();
    }

    function select_study_material_info_for_student()
    {
        $student_id = $this->session->userdata('student_id');
        $class_id   = $this->db->get_where('enroll', array(
            'student_id' => $student_id,
                'year' => $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description
            ))->row()->class_id;
        $this->db->order_by("timestamp", "desc");
        return $this->db->get_where('document', array('class_id' => $class_id))->result_array();
    }

    function update_study_material_info($document_id)
    {
        $data['timestamp']      = strtotime($this->input->post('timestamp'));
        $data['title'] 		= html_escape($this->input->post('title'));
        $data['description']    = html_escape($this->input->post('description'));
        $data['class_id'] 	= $this->input->post('class_id');
        $data['subject_id']     = $this->input->post('subject_id');
        $this->db->where('document_id',$document_id);
        $this->db->update('document',$data);
    }

    function delete_study_material_info($document_id)
    {
        $this->db->where('document_id',$document_id);
        $this->db->delete('document');
    }

    ////////private message//////
    function send_new_private_message() {
        $message    = $this->input->post('message');
        $timestamp  = strtotime(date("Y-m-d H:i:s"));

        $reciever   = $this->input->post('reciever');
        $sender     = $this->session->userdata('login_type') . '-' . $this->session->userdata('login_user_id');

        //check if the thread between those 2 users exists, if not create new thread
        $num1 = $this->db->get_where('message_thread', array('sender' => $sender, 'reciever' => $reciever))->num_rows();
        $num2 = $this->db->get_where('message_thread', array('sender' => $reciever, 'reciever' => $sender))->num_rows();

        //check if file is attached or not
        if ($_FILES['attached_file_on_messaging']['name'] != "") {
          $data_message['attached_file_name'] = $_FILES['attached_file_on_messaging']['name'];
        }

        if ($num1 == 0 && $num2 == 0) {
            $message_thread_code                        = substr(md5(rand(100000000, 20000000000)), 0, 15);
            $data_message_thread['message_thread_code'] = $message_thread_code;
            $data_message_thread['sender']              = $sender;
            $data_message_thread['reciever']            = $reciever;
            $this->db->insert('message_thread', $data_message_thread);
        }
        if ($num1 > 0)
            $message_thread_code = $this->db->get_where('message_thread', array('sender' => $sender, 'reciever' => $reciever))->row()->message_thread_code;
        if ($num2 > 0)
            $message_thread_code = $this->db->get_where('message_thread', array('sender' => $reciever, 'reciever' => $sender))->row()->message_thread_code;


        $data_message['message_thread_code']    = $message_thread_code;
        $data_message['message']                = $message;
        $data_message['sender']                 = $sender;
        $data_message['timestamp']              = $timestamp;
        $this->db->insert('message', $data_message);

        // notify email to email reciever
//        $this->email_model->notify_email('new_message_notification', $this->db->insert_id());

        return $message_thread_code;
    }

    function send_reply_message($message_thread_code) {
        $message    = $this->input->post('message');
        $timestamp  = strtotime(date("Y-m-d H:i:s"));
        $sender     = $this->session->userdata('login_type') . '-' . $this->session->userdata('login_user_id');
        //check if file is attached or not
        if ($_FILES['attached_file_on_messaging']['name'] != "") {
          $data_message['attached_file_name'] = $_FILES['attached_file_on_messaging']['name'];
        }
        $data_message['message_thread_code']    = $message_thread_code;
        $data_message['message']                = $message;
        $data_message['sender']                 = $sender;
        $data_message['timestamp']              = $timestamp;
        $this->db->insert('message', $data_message);

        // notify email to email reciever
        //$this->email_model->notify_email('new_message_notification', $this->db->insert_id());
    }

    function send_reply_group_message($message_thread_code) {
        $message    = $this->input->post('message');
        $timestamp  = strtotime(date("Y-m-d H:i:s"));
        $sender     = $this->session->userdata('login_type') . '-' . $this->session->userdata('login_user_id');
        //check if file is attached or not
        if ($_FILES['attached_file_on_messaging']['name'] != "") {
          $data_message['attached_file_name'] = $_FILES['attached_file_on_messaging']['name'];
        }
        $data_message['group_message_thread_code']    = $message_thread_code;
        $data_message['message']                = $message;
        $data_message['sender']                 = $sender;
        $data_message['timestamp']              = $timestamp;
        $this->db->insert('group_message', $data_message);
    }

    function mark_thread_messages_read($message_thread_code) {
        // mark read only the oponnent messages of this thread, not currently logged in user's sent messages
        $current_user = $this->session->userdata('login_type') . '-' . $this->session->userdata('login_user_id');
        $this->db->where('sender !=', $current_user);
        $this->db->where('message_thread_code', $message_thread_code);
        $this->db->update('message', array('read_status' => 1));
    }

    function count_unread_message_of_thread($message_thread_code) {
        $unread_message_counter = 0;
        $current_user = $this->session->userdata('login_type') . '-' . $this->session->userdata('login_user_id');
        $messages = $this->db->get_where('message', array('message_thread_code' => $message_thread_code))->result_array();
        foreach ($messages as $row) {
            if ($row['sender'] != $current_user && $row['read_status'] == '0')
                $unread_message_counter++;
        }
        return $unread_message_counter;
    }

    // QUESTION PAPER
    function create_question_paper()
    {
        $data['title']          = html_escape($this->input->post('title'));
        $data['class_id']       = $this->input->post('class_id');
        $data['exam_id']        = $this->input->post('exam_id');
        $data['question_paper'] = html_escape($this->input->post('question_paper'));
        $data['teacher_id']     = $this->session->userdata('login_user_id');

        $this->db->insert('question_paper', $data);
    }

    function update_question_paper($question_paper_id = '')
    {
        $data['title']          = html_escape($this->input->post('title'));
        $data['class_id']       = $this->input->post('class_id');
        $data['exam_id']        = $this->input->post('exam_id');
        $data['question_paper'] = html_escape($this->input->post('question_paper'));

        $this->db->update('question_paper', $data, array('question_paper_id' => $question_paper_id));
    }

    function delete_question_paper($question_paper_id = '')
    {
        $this->db->where('question_paper_id', $question_paper_id);
        $this->db->delete('question_paper');
    }

    // BOOK REQUEST
    function create_book_request()
    {
        $data['book_id']            = $this->input->post('book_id');
        $data['student_id']         = $this->session->userdata('login_user_id');
        $data['issue_start_date']   = strtotime($this->input->post('issue_start_date'));
        $data['issue_end_date']     = strtotime($this->input->post('issue_end_date'));

        $this->db->insert('book_request', $data);
    }

    function curl_request($code = '') {

        $product_code = $code;

        $personal_token = "FkA9UyDiQT0YiKwYLK3ghyFNRVV9SeUn";
        $url = "https://api.envato.com/v3/market/author/sale?code=".$product_code;
        $curl = curl_init($url);

        //setting the header for the rest of the api
        $bearer   = 'bearer ' . $personal_token;
        $header   = array();
        $header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json; charset=utf-8';
        $header[] = 'Authorization: ' . $bearer;

        $verify_url = 'https://api.envato.com/v1/market/private/user/verify-purchase:'.$product_code.'.json';
        $ch_verify = curl_init( $verify_url . '?code=' . $product_code );

        curl_setopt( $ch_verify, CURLOPT_HTTPHEADER, $header );
        curl_setopt( $ch_verify, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch_verify, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch_verify, CURLOPT_CONNECTTIMEOUT, 5 );
        curl_setopt( $ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $cinit_verify_data = curl_exec( $ch_verify );
        curl_close( $ch_verify );

        $response = json_decode($cinit_verify_data, true);

        if (count($response['verify-purchase']) > 0) {
            return true;
        } else {
            return false;
        }

  	}


    function delete_student($student_id) {
      // deleting data of student from all associated tables
      $tables = array('student', 'attendance', 'book_request', 'enroll', 'invoice', 'mark', 'payment');
      $this->db->delete($tables, array('student_id' => $student_id));
      // deleting data from messages
      $threads = $this->db->get('message_thread')->result_array();
      if (count($threads) > 0) {
        foreach ($threads as $row) {
          $sender = explode('-', $row['sender']);
          $receiver = explode('-', $row['reciever']);
          if (($sender[0] == 'student' && $sender[1] == $student_id) || ($receiver[0] == 'student' && $receiver[1] == $student_id)) {
            $thread_code = $row['message_thread_code'];
            $this->db->delete('message', array('message_thread_code' => $thread_code));
            $this->db->delete('message_thread', array('message_thread_code' => $thread_code));
          }
        }
      }
    }

    // Group messaging portion
    function create_group(){
      $data = array();
      $data['group_message_thread_code'] = substr(md5(rand(100000000, 20000000000)), 0, 15);
      $data['created_timestamp'] = strtotime(date("Y-m-d H:i:s"));
      $data['group_name'] = html_escape($this->input->post('group_name'));
      if(!empty($_POST['user'])) {
          array_push($_POST['user'], $this->session->userdata('login_type').'_'.$this->session->userdata('admin_id'));
          $data['members'] = json_encode($_POST['user']);
      }
      else{
        $_POST['user'] = array();
        array_push($_POST['user'], $this->session->userdata('login_type').'_'.$this->session->userdata('admin_id'));
        $data['members'] = json_encode($_POST['user']);
      }
      $this->db->insert('group_message_thread', $data);
      redirect(site_url('admin/group_message'), 'refresh');
    }
    // Group messaging portion
    function update_group($thread_code = ""){
      $data = array();
      $data['group_name'] = html_escape($this->input->post('group_name'));
      if(!empty($_POST['user'])) {
          array_push($_POST['user'], $this->session->userdata('login_type').'_'.$this->session->userdata('admin_id'));
          $data['members'] = json_encode($_POST['user']);
      }
      else{
        $_POST['user'] = array();
        array_push($_POST['user'], $this->session->userdata('login_type').'_'.$this->session->userdata('admin_id'));
        $data['members'] = json_encode($_POST['user']);
      }
      $this->db->where('group_message_thread_code', $thread_code);
      $this->db->update('group_message_thread', $data);
        redirect(site_url('admin/group_message'), 'refresh');
    }

    function get_settings($type)
    {
        $des = $this->db->get_where('settings', array('type' => $type))->row()->description;
        return $des;
    }

    function update_payumoney_keys(){
      $data['description'] = html_escape($this->input->post('payumoney_merchant_key'));
      $this->db->where('type' , 'payumoney_merchant_key');
      $this->db->update('settings' , $data);

      $data['description'] = html_escape($this->input->post('payumoney_salt_id'));
      $this->db->where('type' , 'payumoney_salt_id');
      $this->db->update('settings' , $data);
    }

    // update paypal keys
    function update_paypal_keys() {
        $info = array();

        $paypal['active'] = $this->input->post('paypal_active');
        $paypal['mode'] = $this->input->post('paypal_mode');
        $paypal['sandbox_client_id'] = html_escape($this->input->post('sandbox_client_id'));
        $paypal['production_client_id'] = html_escape($this->input->post('production_client_id'));

        array_push($info, $paypal);

        $data['description']    =   json_encode($info);
        $this->db->where('type', 'paypal');
        $this->db->update('settings', $data);
    }

    // update stripe keys
    function update_stripe_keys() {
        $info = array();

        $stripe['active'] = html_escape($this->input->post('stripe_active'));
        $stripe['testmode'] = html_escape($this->input->post('testmode'));
        $stripe['public_key'] = html_escape($this->input->post('public_key'));
        $stripe['secret_key'] = html_escape($this->input->post('secret_key'));
        $stripe['public_live_key'] = html_escape($this->input->post('public_live_key'));
        $stripe['secret_live_key'] = html_escape($this->input->post('secret_live_key'));

        array_push($info, $stripe);

        $data['description']    =   json_encode($info);
        $this->db->where('type', 'stripe_keys');
        $this->db->update('settings', $data);
    }

    function update_smtp_settings(){
        $protocol['description'] = $this->input->post('protocol');
        $this->db->where('type', 'protocol');
        $this->db->update('settings', $protocol);

        $smtp_user['description'] = $this->input->post('smtp_user');
        $this->db->where('type', 'smtp_user');
        $this->db->update('settings', $smtp_user);

        $smtp_pass['description'] = $this->input->post('smtp_pass');
        $this->db->where('type', 'smtp_pass');
        $this->db->update('settings', $smtp_pass);

        $smtp_host['description'] = $this->input->post('smtp_host');
        $this->db->where('type', 'smtp_host');
        $this->db->update('settings', $smtp_host);

        $smtp_port['description'] = $this->input->post('smtp_port');
        $this->db->where('type', 'smtp_port');
        $this->db->update('settings', $smtp_port);
    }

    function create_online_exam(){
        $data['code']  = substr(md5(uniqid(rand(), true)), 0, 7);
        $data['title'] = html_escape($this->input->post('exam_title'));
        $data['class_id'] = $this->input->post('class_id');
        $data['section_id'] = $this->input->post('section_id');
        $data['subject_id'] = $this->input->post('subject_id');
        $data['minimum_percentage'] = html_escape($this->input->post('minimum_percentage'));
        $data['instruction'] = html_escape($this->input->post('instruction'));
        $data['exam_date'] = strtotime(html_escape($this->input->post('exam_date')));
        $data['time_start'] = html_escape($this->input->post('time_start'));
        $data['time_end'] = html_escape($this->input->post('time_end'));
        $data['duration'] = strtotime(date('Y-m-d', $data['exam_date']).' '.$data['time_end']) - strtotime(date('Y-m-d', $data['exam_date']).' '.$data['time_start']);
        $data['running_year'] = $this->db->get_where('settings' , array('type' => 'running_year'))->row()->description;

        /*print_r($data);
        echo '<br/>';
        echo gmdate("H:i:s", '18305');
        die();*/
        $this->db->insert('online_exam', $data);
    }

    function update_online_exam(){

        $data['title'] = html_escape($this->input->post('exam_title'));
        $data['class_id'] = $this->input->post('class_id');
        $data['section_id'] = $this->input->post('section_id');
        $data['subject_id'] = $this->input->post('subject_id');
        $data['minimum_percentage'] = html_escape($this->input->post('minimum_percentage'));
        $data['instruction'] = html_escape($this->input->post('instruction'));
        $data['exam_date'] = strtotime(html_escape($this->input->post('exam_date')));
        $data['time_start'] = html_escape($this->input->post('time_start'));
        $data['time_end'] = html_escape($this->input->post('time_end'));
        $data['duration'] = strtotime(date('Y-m-d', $data['exam_date']).' '.$data['time_end']) - strtotime(date('Y-m-d', $data['exam_date']).' '.$data['time_start']);

        $this->db->where('online_exam_id', $this->input->post('online_exam_id'));
        $this->db->update('online_exam', $data);
    }

    // multiple_choice_question crud functions
    function add_multiple_choice_question_to_online_exam($online_exam_id){
        if (sizeof($this->input->post('options')) != $this->input->post('number_of_options')) {
            $this->session->set_flashdata('error_message' , get_phrase('no_options_can_be_blank'));
            return;
        }
        foreach ($this->input->post('options') as $option) {
            if ($option == "") {
                $this->session->set_flashdata('error_message' , get_phrase('no_options_can_be_blank'));
                return;
            }
        }
        if (sizeof($this->input->post('correct_answers')) == 0) {
            $correct_answers = [""];
        }
        else{
            $correct_answers = $this->input->post('correct_answers');
        }
        $data['online_exam_id']     = $online_exam_id;
        $data['question_title']     = html_escape($this->input->post('question_title'));
        $data['mark']               = html_escape($this->input->post('mark'));
        $data['number_of_options']  = html_escape($this->input->post('number_of_options'));
        $data['type']               = 'multiple_choice';
        $data['options']            = json_encode($this->input->post('options'));
        $data['correct_answers']    = json_encode($correct_answers);
        $this->db->insert('question_bank', $data);
        $this->session->set_flashdata('flash_message' , get_phrase('question_added'));
    }

    function update_multiple_choice_question($question_id){
        if (sizeof($this->input->post('options')) != $this->input->post('number_of_options')) {
            $this->session->set_flashdata('error_message' , get_phrase('no_options_can_be_blank'));
            return;
        }
        foreach ($this->input->post('options') as $option) {
            if ($option == "") {
                $this->session->set_flashdata('error_message' , get_phrase('no_options_can_be_blank'));
                return;
            }
        }

        if (sizeof($this->input->post('correct_answers')) == 0) {
            $correct_answers = [""];
        }
        else{
            $correct_answers = $this->input->post('correct_answers');
        }

        $data['question_title']     = html_escape($this->input->post('question_title'));
        $data['mark']               = html_escape($this->input->post('mark'));
        $data['number_of_options']  = html_escape($this->input->post('number_of_options'));
        $data['options']            = json_encode($this->input->post('options'));
        $data['correct_answers']    = json_encode($correct_answers);
        $this->db->where('question_bank_id', $question_id);
        $this->db->update('question_bank', $data);
        $this->session->set_flashdata('flash_message' , get_phrase('question_updated'));
    }

    // true false questions crud functions
    function add_true_false_question_to_online_exam($online_exam_id){
        $data['online_exam_id']     = $online_exam_id;
        $data['question_title']     = html_escape($this->input->post('question_title'));
        $data['type']               = 'true_false';
        $data['mark']               = html_escape($this->input->post('mark'));
        $data['correct_answers']    = html_escape($this->input->post('true_false_answer'));
        $this->db->insert('question_bank', $data);
        $this->session->set_flashdata('flash_message' , get_phrase('question_added'));
    }
    function update_true_false_question($question_id){
        $data['question_title']     = html_escape($this->input->post('question_title'));
        $data['mark']               = html_escape($this->input->post('mark'));
        $data['correct_answers']    = html_escape($this->input->post('true_false_answer'));

        $this->db->where('question_bank_id', $question_id);
        $this->db->update('question_bank', $data);
        $this->session->set_flashdata('flash_message' , get_phrase('question_updated'));
    }

    // fill in the blanks question portion
    function add_fill_in_the_blanks_question_to_online_exam($online_exam_id){
        $suitable_words_array = explode(',', html_escape($this->input->post('suitable_words')));
        $suitable_words = array();
        foreach ($suitable_words_array as $row) {
          array_push($suitable_words, strtolower($row));
        }
        $data['online_exam_id']     = $online_exam_id;
        $data['question_title']     = html_escape($this->input->post('question_title'));
        $data['type']               = 'fill_in_the_blanks';
        $data['mark']               = html_escape($this->input->post('mark'));
        $data['correct_answers']    = json_encode(array_map('trim',$suitable_words));
        $this->db->insert('question_bank', $data);
        $this->session->set_flashdata('flash_message' , get_phrase('question_added'));
    }
    function update_fill_in_the_blanks_question($question_id){
        $suitable_words_array = explode(',', html_escape($this->input->post('suitable_words')));
        $suitable_words = array();
        foreach ($suitable_words_array as $row) {
          array_push($suitable_words, strtolower($row));
        }
        $data['question_title']     = html_escape($this->input->post('question_title'));
        $data['mark']               = html_escape($this->input->post('mark'));
        $data['correct_answers']    = json_encode(array_map('trim',$suitable_words));

        $this->db->where('question_bank_id', $question_id);
        $this->db->update('question_bank', $data);
        $this->session->set_flashdata('flash_message' , get_phrase('question_updated'));
    }
    function delete_question_from_online_exam($question_id){
        $this->db->where('question_bank_id', $question_id);
        $this->db->delete('question_bank');
    }
    function manage_online_exam_status($online_exam_id = "", $status = ""){
        $checker = array(
            'online_exam_id' => $online_exam_id
        );
        $updater = array(
            'status' => $status
        );

        $this->db->where($checker);
        $this->db->update('online_exam', $updater);
        $this->session->set_flashdata('flash_message' , get_phrase('exam').' '.$status);
    }

    function available_exams($student_id) {
        $running_year = get_settings('running_year');
        $class_id = $this->db->get_where('enroll', array('student_id' => $student_id))->row()->class_id;
        $section_id = $this->db->get_where('enroll', array('student_id' => $student_id))->row()->section_id;
        $match = array('running_year' => $running_year, 'class_id' => $class_id, 'section_id' => $section_id, 'status' => 'published');
        $this->db->order_by("exam_date", "dsc");
        $exams = $this->db->where($match)->get('online_exam')->result_array();
        return $exams;
    }

    function change_online_exam_status_to_attended_for_student($online_exam_id = ""){

        $checker = array(
            'online_exam_id' => $online_exam_id,
            'student_id' => $this->session->userdata('login_user_id')
        );

        if($this->db->get_where('online_exam_result', $checker)->num_rows() == 0){
            $inserted_array = array(
                'status' => 'attended',
                'online_exam_id' => $online_exam_id,
                'student_id' => $this->session->userdata('login_user_id'),
                'exam_started_timestamp' => strtotime("now")
            );
            $this->db->insert('online_exam_result', $inserted_array);
        }
    }

    function submit_online_exam($online_exam_id = "", $answer_script = ""){

        $checker = array(
            'online_exam_id' => $online_exam_id,
            'student_id' => $this->session->userdata('login_user_id')
        );
        $updated_array = array(
            'status' => 'submitted',
            'answer_script' => $answer_script
        );

        $this->db->where($checker);
        $this->db->update('online_exam_result', $updated_array);

        $this->calculate_exam_mark($online_exam_id);
    }

    function calculate_exam_mark($online_exam_id) {

        $checker = array(
            'online_exam_id' => $online_exam_id,
            'student_id' => $this->session->userdata('login_user_id')
        );


        $obtained_marks = 0;
        $online_exam_result = $this->db->get_where('online_exam_result', $checker);
        if ($online_exam_result->num_rows() == 0) {

            $data['obtained_mark'] = 0;
        }
        else{
            $results = $online_exam_result->row_array();
            $answer_script = json_decode($results['answer_script'], true);
            foreach ($answer_script as $row) {

                if ($row['submitted_answer'] == $row['correct_answers']) {

                    $obtained_marks = $obtained_marks + $this->get_question_details_by_id($row['question_bank_id'], 'mark');
                }
            }
            $data['obtained_mark'] = $obtained_marks;
        }
        $total_mark = $this->get_total_mark($online_exam_id);
        $query = $this->db->get_where('online_exam', array('online_exam_id' => $online_exam_id))->row_array();
        $minimum_percentage = $query['minimum_percentage'];

        $minumum_required_marks = ($total_mark * $minimum_percentage) / 100;
        if ($minumum_required_marks > $obtained_marks) {
            $data['result'] = 'fail';
        }
        else {
            $data['result'] = 'pass';
        }
        $this->db->where($checker);
        $this->db->update('online_exam_result', $data);
    }

    function get_total_mark($online_exam_id){
        $added_question_info = $this->db->get_where('question_bank', array('online_exam_id' => $online_exam_id))->result_array();
        $total_mark = 0;
        if (sizeof($added_question_info) > 0){
            foreach ($added_question_info as $single_question) {
                $total_mark = $total_mark + $single_question['mark'];
            }
        }
        return $total_mark;
    }

    function get_question_details_by_id($question_bank_id, $column_name = "") {

        return $this->db->get_where('question_bank', array('question_bank_id' => $question_bank_id))->row()->$column_name;
    }

    function check_availability_for_student($online_exam_id){

        $result = $this->db->get_where('online_exam_result', array('online_exam_id' => $online_exam_id, 'student_id' => $this->session->userdata('login_user_id')))->row_array();

        return $result['status'];
    }

    function get_correct_answer($question_bank_id = ""){

        $question_details = $this->db->get_where('question_bank', array('question_bank_id' => $question_bank_id))->row_array();
        return $question_details['correct_answers'];
    }

    function get_online_exam_result($student_id){
        $match = array('student_id' => $student_id, 'status' => 'submitted');
        $exams = $this->db->where($match)->get('online_exam_result')->result_array();
        return $exams;
    }
}
