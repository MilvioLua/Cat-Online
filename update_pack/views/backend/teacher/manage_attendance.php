
<?php echo form_open(site_url('teacher/attendance_selector/'));?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo get_phrase('attendance_of') ?></h3>
    </div>
    <div class="panel-body">
		<div class="row">
			<div class="col-md-3 col-sm-offset-2">
				<div class="form-group">
				<label class="control-label" style="margin-bottom: 5px;"><?php echo get_phrase('date');?></label>
					<input type="text" class="form-control datepicker" name="timestamp" data-format="dd-mm-yyyy"
						value="<?php echo date("d-m-Y");?>"/>
				</div>
			</div>

			<?php
				$query = $this->db->get_where('section' , array('class_id' => $class_id,'teacher_id'=>$this->session->userdata('teacher_id')));
				if($query->num_rows() > 0):
					$sections = $query->result_array();
			?>

			<div class="col-md-3">
				<div class="form-group">
				<label class="control-label" style="margin-bottom: 5px;"><?php echo get_phrase('section');?></label>
					<select class="form-control selectboxit" name="section_id">
						<?php foreach($sections as $row):?>
							<option value="<?php echo $row['section_id'];?>"><?php echo $row['name'];?></option>
						<?php endforeach;?>
					</select>
				</div>
			</div>
			<?php endif;?>
			<input type="hidden" name="class_id" value="<?php echo $class_id;?>">
			<input type="hidden" name="year" value="<?php echo $running_year;?>">

			<div class="col-md-3" style="margin-top: 20px;">
				<button type="submit" class="btn btn-info"><?php echo get_phrase('manage_attendance');?></button>
			</div>

		</div>
	</div>
</div>
<?php echo form_close();?>