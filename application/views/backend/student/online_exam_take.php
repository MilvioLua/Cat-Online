<hr>
<?php
	$exam_ends_timestamp = strtotime(date('d-M-Y', $this->db->get_where('online_exam', array('online_exam_id' => $online_exam_id))->row()->exam_date)." ".$this->db->get_where('online_exam', array('online_exam_id' => $online_exam_id))->row()->time_end);
	$current_timestamp = strtotime("now");

	$total_duration 	=	$exam_ends_timestamp - $current_timestamp;
	$total_hour 		= 	intval($total_duration / 3600);
	$total_duration 	-=	$total_hour * 3600;
	$total_minute 		=	intval($total_duration / 60);
	$total_second 		=	intval($total_duration % 60);

	$online_exam_row = $exam_info->row();
	$questions = $this->db->get_where('question_bank', array('online_exam_id' => $online_exam_id))->result_array();
	$total_marks = 0;
	foreach ($questions as $row) {
		$total_marks += $row['mark'];
	}
?>
<div class="row">
	<div class="col-md-12 text-center">
		<h3><?php echo $online_exam_row->title;?></h3>
		<h4>
			<b><?php echo get_phrase('subject');?></b>: <?php echo $this->db->get_where('subject', array('subject_id' => $online_exam_row->subject_id))->row()->name;?>
		</h4>
		<h4>
			<b><?php echo get_phrase('total_marks');?></b>: <?php echo $total_marks;?>
		</h4>
		<h4>
			<b><?php echo get_phrase('time');?></b>: <?php echo ($online_exam_row->duration / 60).' '.get_phrase('minutes');?>
		</h4>
		<h4>
			<b><?php echo get_phrase('exam_has_to_be_submitted_within').': '; ?></b>: <?php echo date('D d-M-Y h:i:s', $exam_ends_timestamp);?>
		</h4>
		<h5>
			<b><?php echo get_phrase('instructions');?></b>: <?php echo $online_exam_row->instruction;?>
		</h5>

		<center>
				<div style="height:30px; font-size:25px; font-weight:200; color: #212121;" id="timer_value">

					<!-- HOUR TIMER -->
					<span id="hour_timer"> 0 </span>
					<span style="font-size:20px;"><?php echo get_phrase('hour');?> </span>

					<!-- SEPARATOR -->
					<span class="blink_text">:</span>

					<!-- MINUTE TIMER -->
					<span id="minute_timer"> 0 </span>
					<span style="font-size:20px;"><?php echo get_phrase('minute');?> </span>

					<!-- SEPARATOR -->
					<span class="blink_text">:</span>

					<!-- SECOND TIMER -->
					<span id="second_timer"> 0 </span>
					<span style="font-size:20px;"><?php echo get_phrase('second');?> </span>
				</div>
			</center>

	</div>
</div>
<hr>

<form class="" action="<?php echo site_url('student/submit_online_exam/'.$online_exam_id); ?>" method="post" enctype="multipart/form-data" id = "answer_script">
	<?php $count = 1; foreach ($questions as $question):?>
	<div class="row">
		<div class="col-md-11">
			<h4><b><?php echo $count++;?>.</b>  <?php echo ($question['type'] == 'fill_in_the_blanks') ? str_replace('^', '__________', $question['question_title']) : $question['question_title'];?></h4>
		</div>
		<div class="col-md-1 text-right">
			<h4><b><?php echo $question['mark'];?></b></h4>
		</div>
	</div>
	<div class="row" style="padding: 15px;">
		<!-- multiple choice -->
		<?php if ($question['type'] == 'multiple_choice'): ?>
			<?php
	            if ($question['options'] != '' || $question['options'] != null)
	            	$options = json_decode($question['options']);
	            else
	            	$options = array();
	            for ($i = 0; $i < $question['number_of_options']; $i++):
			?>
			<div class="col-md-12" style="margin-bottom: 15px;">
				<div class="checkbox checkbox-replace color-green">
				    <input type="checkbox" id="chk-23" name="<?php echo $question['question_bank_id'].'[]'; ?>" value="<?php echo $i + 1;?>">
				    <label style="color: #373e4a; font-size: 15px;">
				    	<?php echo $options[$i];?>
				    </label>
			    </div>
			</div>
		<?php endfor; endif;?>
		<!-- true / false -->
		<?php if ($question['type'] == 'true_false'): ?>
			<div class="col-md-12" style="margin-bottom: 15px;">
				<div class="checkbox checkbox-replace color-green">
				    <input type="radio" id="chk-23" name="<?php echo $question['question_bank_id'].'[]'; ?>" value="true">
				    <label style="color: #373e4a; font-size: 15px;">
				    	<?php echo get_phrase('true');?>
				    </label>
			    </div>
			</div>
			<div class="col-md-12" style="margin-bottom: 15px;">
				<div class="checkbox checkbox-replace color-green">
				    <input type="radio" id="chk-23" name="<?php echo $question['question_bank_id'].'[]'; ?>" value="false">
				    <label style="color: #373e4a; font-size: 15px;">
				    	<?php echo get_phrase('false');?>
				    </label>
			    </div>
			</div>
		<?php endif; ?>
		<!-- fill in the blanks -->
		<?php if ($question['type'] == 'fill_in_the_blanks'): ?>
			<div class="col-md-3">
				<div class="form-group">
					<input type="text" name="<?php echo $question['question_bank_id'].'[]'; ?>" value="" class="form-control" placeholder="<?php echo get_phrase('answer');?>">
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php endforeach;?>
	<div class="row">
		<div class="col-md-3">
			<button type="submit" class="btn btn-success btn-block">
				<?php echo get_phrase('finish_exam');?>
			</button>
		</div>
	</div>
</form>

<script type="text/javascript">

	// SET THE INITIAL VALUES TO TIMER PLACES
	var timer_starting_hour 	=	<?php echo $total_hour;?>;
	document.getElementById("hour_timer").innerHTML = timer_starting_hour;
	var timer_starting_minute 	=	<?php echo $total_minute;?>;
	document.getElementById("minute_timer").innerHTML = timer_starting_minute;
	var timer_starting_second 	=	<?php echo $total_second;?>;
	document.getElementById("second_timer").innerHTML = timer_starting_second;

	// INITIALIZE THE TIMER WITH SECOND DELAY
	var timer = timer_starting_second;
	var mytimer	=	setInterval(function () {run_timer()}, 1000);

	function run_timer() {
		if (timer == 0 && timer_starting_minute == 0 && timer_starting_hour == 0) {

				$("#answer_script").submit();
		}
		else {

			timer--;

		    if (timer < 0)
		    {
		    	timer = 59;
		    	timer_starting_minute--;
				if (timer_starting_minute >= 0) {
					document.getElementById("minute_timer").innerHTML = timer_starting_minute;
				}
		    }

		    if (timer_starting_minute < 0)
		    {
				timer_starting_minute = 59;
				document.getElementById("minute_timer").innerHTML = timer_starting_minute;
		    	timer_starting_hour--;
		    	document.getElementById("hour_timer").innerHTML = timer_starting_hour;
		    }

		    document.getElementById("second_timer").innerHTML = timer;
		}
	}
</script>

<style type="text/css">
.blink_text {

        -webkit-animation-name: blinker;
 -webkit-animation-duration: 1s;
 -webkit-animation-timing-function: linear;
 -webkit-animation-iteration-count: infinite;

 -moz-animation-name: blinker;
 -moz-animation-duration: 1s;
 -moz-animation-timing-function: linear;
 -moz-animation-iteration-count: infinite;
 animation-name: blinker;
 animation-duration: 1s;
 animation-timing-function: linear;
    animation-iteration-count: infinite;
}

@-moz-keyframes blinker {
    0% { opacity: 1.0; }
    50% { opacity: 0.0; }
    100% { opacity: 1.0; }
}

@-webkit-keyframes blinker {
    0% { opacity: 1.0; }
    50% { opacity: 0.0; }
    100% { opacity: 1.0; }
}

@keyframes blinker {
    0% { opacity: 1.0; }
    50% { opacity: 0.0; }
    100% { opacity: 1.0; }
}
</style>
