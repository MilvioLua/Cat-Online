<?php 
	$class_id = isset($class_id) ? $class_id : '';
	$section_id = isset($section_id) ? $section_id : '';
	$student_id = isset($student_id) ? $student_id : '';
?>
<div class="row">
	<div class="col-md-3">
		<label><?php echo get_phrase('class');?></label>
		<select class="" name="class_id" id="class_id">
			<option value=""><?php echo get_phrase('select_a_class');?></option>
			<?php 
				$classes = $this->db->get('class')->result_array();
				foreach ($classes as $row):
			?>
			<option value="<?php echo $row['class_id'];?>">
				<?php echo $row['name'];?>
			</option>
		<?php endforeach;?>
		</select>
	</div>
	<div class="col-md-3">
		<label><?php echo get_phrase('section');?></label>
		<div id="section_holder">
			<select class="" name="section_id" id="section_id" disabled>
			    <option value=""><?php echo get_phrase('select_a_class_first');?></option>
		    </select>
		</div>
	</div>
	<div class="col-md-3">
		<label><?php echo get_phrase('student');?></label>
		<div id="student_holder">
			<select class="" name="student_id" id="student_id" disabled>
			    <option value=""><?php echo get_phrase('select_a_class_and_section');?></option>
		    </select>
		</div>
	</div>
	<div class="col-md-2">
		<label></label>
		<button type="button" class="btn btn-info btn-block" id="find">
			<?php echo get_phrase('find_payments');?>
		</button>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-md-12">
		<div id="data">
			<?php include 'student_specific_payment_history_table.php'; ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {

		$('#class_id').select2();
		$('#section_id').select2();
		$('#student_id').select2();

		$('#class_id').on('change', function() {
			var class_id = $(this).val();
			$.ajax({
				url: '<?php echo site_url('accountant/get_sections_for_ssph/');?>' + class_id
			}).done(function(response) {
				$('#section_holder').html(response);
				$('#section_id').select2();
				var section_id = $('#section_id').val();
				$.ajax({
					url: '<?php echo site_url('accountant/get_students_for_ssph/');?>' + class_id + '/' + section_id
				}).done(function(response) {
					$('#student_holder').html(response);
					$('#student_id').select2();
				});
			});
		});

		$('#section_id').on('change', function() {
			var section_id = $(this).val();
			var class_id = $('#class_id').val();
			$.ajax({
				url: '<?php echo site_url('accountant/get_students_for_ssph/');?>' + class_id + '/' + section_id
			}).done(function(response) {
				$('#student_holder').html(response);
				$('#student_id').select2();
			});
		});

		$('#find').on('click', function() {
			var student_id = $('#student_id').val();
			$.ajax({
				url: '<?php echo site_url('accountant/get_payment_history_for_ssph/');?>' + student_id
			}).done(function(response) {
				$('#data').html(response);
			});
		});

	});
</script>