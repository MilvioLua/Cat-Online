<?php echo form_open(site_url('admin/manage_online_exam_question/'.$online_exam_id.'/add/multiple_choice') , array('class' => 'form-horizontal form-groups-bordered validate','target'=>'_top'));?>

<input type="hidden" name="type" value="<?php echo $question_type;?>">

<div class="form-group">
    <label class="col-sm-3 control-label"><?php echo get_phrase('mark');?></label>
    <div class="col-sm-8">
        <input type="number" class="form-control" name="mark" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" min="0"/>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label"><?php echo get_phrase('question_title');?></label>
    <div class="col-sm-8">
        <textarea onkeyup="changeTheBlankColor()" name="question_title" class="form-control" id="question_title" rows="8" cols="80" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>"></textarea>
    </div>
</div>

<div class="form-group" id='multiple_choice_question'>
    <label class="col-sm-3 control-label"><?php echo get_phrase('number_options');?></label>

    <div class="col-sm-8">
        <div class="input-group">
          <input type="number" class="form-control" name="number_of_options" id = "number_of_options" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" min="0"/>
          <div class="input-group-addon" style="padding: 0px"><button type="button" class = 'btn btn-sm pull-right' name="button" onclick="showOptions(jQuery('#number_of_options').val())" style="border-radius: 0px; background-color: #eeeeee;"><i class="fa fa-check"></i></button></div>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="col-sm-12">
        <button type="submit" class="btn btn-info btn-block"><?php echo get_phrase('add_question');?></button>
    </div>
</div>
<?php echo form_close();?>

<script type="text/javascript">
	function showOptions(number_of_options){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('admin/manage_multiple_choices_options'); ?>",
            data: {number_of_options : number_of_options},
            success: function(response){
                console.log(response);
                jQuery('.options').remove();
                jQuery('#multiple_choice_question').after(response);
            }
        });
    }
</script>
