<?php echo form_open(site_url('admin/bulk_student_add_using_csv/import') ,
			array('class' => 'form-inline validate',   'enctype' => 'multipart/form-data'));?>
<div class="row">
	<div class="col-md-8">
		<div class="panel panel-primary " data-collapsed="0">
		    <div class="panel-heading">
		        <div class="panel-title">
		            <i class="fa fa-calendar"></i>
		            <?php echo get_phrase('create_multiple_students');?>
		        </div>
		    </div>
		    <div class="panel-body">
		        <div class="row">
					<div class="col-md-3"></div>
					<div class="col-md-3">
						<div class="form_group">
							<label class="control-label" style="margin-bottom: 5px;"><?php echo get_phrase('class');?></label>
							<select name="class_id" id="class_id" class="form-control selectboxit" required
								onchange="get_sections(this.value)"  data-validate="required"  data-message-required="<?php echo get_phrase('value_required');?>">
								<option value=""><?php echo get_phrase('select_class');?></option>
								<?php
									$classes = $this->db->get('class')->result_array();
									foreach($classes as $row):
								?>
								<option value="<?php echo $row['class_id'];?>"><?php echo $row['name'];?></option>
								<?php endforeach;?>
							</select>
						</div>
					</div>
					<div id="section_holder" class="col-md-3">
						<label class="control-label" style="margin-bottom: 5px;"><?php echo get_phrase('section');?></label>
						<select name="section_id" id="section_id" class="form-control selectboxit">
							<option value=""><?php echo get_phrase('select_class_first');?></option>
						</select>
					</div>
					<div class="col-md-3"></div>
				</div>
				<div class="row">
					<div class="col-md-offset-4 col-md-4" style="padding: 15px;">
						<button type="button" class="btn btn-primary" name="generate_csv" id="generate_csv"><?php echo get_phrase('generate_').'CSV '.get_phrase('file'); ?></button>
					</div>
					<div class="col-md-offset-4 col-md-4" style="padding-bottom:15px;">
					<input type="file" name="userfile" class="form-control file2 inline btn btn-info" data-label="<i class='entypo-tag'></i> Select CSV File"
					                   	data-validate="required" data-message-required="<?php echo get_phrase('required'); ?>"
					               		accept="text/csv, .csv" />
					</div>
					<div class="col-md-offset-4 col-md-4">
						<button type="submit" class="btn btn-success" name="import_csv" id="import_csv"><?php echo get_phrase('import_CSV'); ?></button>
					</div>
				</div>
		    </div>
		</div>
	</div>
	<div class="col-md-4">
		<blockquote class="blockquote-blue">
			<p>
				<strong>
					Bulk student adding instruction
				</strong>
			</p>
			<p>
				<ol style="color: #ffffff;font-weight: 200;line-height: 1.5;font-size: 13px;padding-left: 15px;">
					<li style="padding: 5px;"><?php echo get_phrase('at_first_select_the_class_and_section').'.'; ?></li>
					<li style="padding: 5px;"><?php echo get_phrase('after_selecting_class_and_section_click_').'"Generate CSV File".'; ?></li>
					<li style="padding: 5px;"><?php echo get_phrase('open_the_downloaded_').'"bulk_student.csv" File. '.get_phrase('enter_student_details_as_written_in_there_and_remember_take_the_parent_ID_from_parent_table').'.';?></li>
					<li style="padding: 5px;"><?php echo get_phrase('save_the_edited_').'"bulk_student.csv" File.';?></li>
					<li style="padding: 5px;"><?php echo get_phrase('click_the_').'"Select CSV File" '.get_phrase('and_choose_the_file_you_just_edited').'.';?></li>
					<li style="padding: 5px;"><?php echo get_phrase('import_that_file.');?></li>
					<li style="padding: 5px;"><?php echo get_phrase('hit_').'"Import CSV File".';?></li>
				</ol>
			</p>
			<p style="font-weight: 500;">
				***<?php echo get_phrase('this_system_keeps_track_of_duplication_in_email_ID.').' '.get_phrase('so_please_enter_unique_email_ID_for_every_student').'.'; ?>
			</p>
		</blockquote>
	</div>
</div>

<?php echo form_close();?>

		


<a href="" download="bulk_student.csv" style="display: none;" id = "bulk">Download</a>

<script>

</script>
<script type="text/javascript">
var class_selection = '';
jQuery(document).ready(function($) {
$('#submit_button').attr('disabled', 'disabled');

});
	function get_sections(class_id) {
		if (class_id != "") {
			$.ajax({
	            url: '<?php echo site_url('admin/get_sections/');?>' + class_id ,
	            success: function(response)
	            {
	                jQuery('#section_holder').html(response);
	                jQuery('#bulk_add_form').show();
	            }
	        });
		}
	}
	$("#generate_csv").click(function(){
		var class_id 	= $('#class_id').val();
		var section_id 	= $('#section_id').val();

		if(class_id == '' || section_id == '')
			toastr.error("<?php echo get_phrase('please_make_sure_class_and_section_is_selected'); ?>");
		else {
			$.ajax({
			  	url: '<?php echo site_url('admin/generate_bulk_student_csv/');?>' + class_id + '/' + section_id,
			  	success: function(response) {
			    	toastr.success("<?php echo get_phrase('file_generated'); ?>");
						$("#bulk").attr('href', response);
						jQuery('#bulk')[0].click();
			    	//document.location = response;
			  	}
			});
		}
	});
</script>
