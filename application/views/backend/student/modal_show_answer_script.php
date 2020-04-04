<?php

    $result_details = $this->db->get_where('online_exam_result', array('online_exam_id' => $param2, 'student_id' => $this->session->userdata('login_user_id')))->row_array();
    $answer_script_array = json_decode($result_details['answer_script'], true);
    if (sizeof($answer_script_array) > 0) {

        include 'answer_script_with_submitted_answers.php';
    }

    else {
        include 'answer_script_without_submitted_answers.php';
    }

?>
