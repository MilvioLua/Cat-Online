<hr>
<div class="row">
	<div class="col-md-12">
		<a href="<?php echo site_url('student/online_exam'); ?>" class="btn btn-<?php echo $data == 'active' ? 'primary' : 'white'; ?>">
			<?php echo get_phrase('active_exams');?>
		</a>
		<a href="<?php echo site_url('student/online_exam_result'); ?>" class="btn btn-<?php echo $data == 'result' ? 'primary' : 'white'; ?>">
			<?php echo get_phrase('view_results');?>
		</a>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-md-12">
		<table class="table table-bordered" id="table_export">
			<thead>
                <tr>
                    <th><div><?php echo get_phrase('exam_name');?></div></th>
                    <th><div><?php echo get_phrase('subject');?></div></th>
                    <th><div><?php echo get_phrase('exam_date');?></div></th>
                    <th><div><?php echo get_phrase('total_marks');?></div></th>
                    <th><div><?php echo get_phrase('obtained_marks');?></div></th>
                    <th><div><?php echo get_phrase('result');?></div></th>
                    <th><div><?php echo get_phrase('answer_script');?></div></th>
                </tr>
            </thead>
            <tbody>
            	<?php
                    foreach ($exams as $row):

                    $online_exam_details = $this->db->get_where('online_exam', array('online_exam_id' => $row['online_exam_id']))->row_array();
                    $current_time = time();
                    $exam_end_time = strtotime(date('Y-m-d', $online_exam_details['exam_date']).' '.$online_exam_details['time_end']);
                    ?>
                    <tr>
                    	<td>
                            <?php
                                echo $online_exam_details['title'];
                            ?>
                        </td>
                    	<td>
                    		<?php echo $this->db->get_where('subject', array('subject_id' => $online_exam_details['subject_id']))->row()->name;?>
                    	</td>
                    	<td>
                    		<?php
                            echo '<b>'.get_phrase('date').':</b> '.date('M d, Y', $online_exam_details['exam_date']).'<br>'.'<b>'.get_phrase('time').':</b> '.$online_exam_details['time_start'].' - '.$online_exam_details['time_end'];
                        ?>
                    	</td>
						<td>
							<?php
								echo $this->crud_model->get_total_mark($row['online_exam_id']);
							?>
						</td>
                    	<td>
                            <?php
							 if ($current_time > $exam_end_time){
								$query = $this->db->get_where('online_exam_result', array('student_id' => $this->session->userdata('login_user_id'), 'online_exam_id' => $row['online_exam_id']));
								if ($query->num_rows() > 0) {
									$query_result = $query->row_array();
									$obtained_marks = $query_result['obtained_mark'];
								}
								else {
									$obtained_marks = 0;
								}

								echo $obtained_marks;
							 }
							?>
                        </td>
						<td>
                            <?php
							 if ($current_time > $exam_end_time){
								$query = $this->db->get_where('online_exam_result', array('student_id' => $this->session->userdata('login_user_id'), 'online_exam_id' => $row['online_exam_id']));
								if ($query->num_rows() > 0) {
									$query_result = $query->row_array();
									$result = get_phrase($query_result['result']);
								}
								else {
									$result = get_phrase('fail').'( '.get_phrase('absent').' )';
								}

								echo $result;
							 }
							?>
                        </td>
                    	<td>
                            <?php if ($current_time > $exam_end_time): ?>
                                <a href="#" type="button" class = "btn btn-success" onclick="showAjaxModal('<?php echo site_url('modal/popup/modal_show_answer_script/'.$row['online_exam_id']);?>');">
                                    <?php echo get_phrase('answer_script');?>
                                </a>
                            <?php else: ?>
                                <a href="#" type="button" class = "btn btn-warning">
                                    <i class="fa fa-sticky-note"></i>
                                    <?php echo get_phrase('please_wait');?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
            <?php endforeach; ?>
            </tbody>
		</table>
	</div>
</div>
