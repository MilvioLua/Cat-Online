<style type="text/css" media="screen">
	.red {
        color: #f44336;
    }
</style>
<div class="alert alert-info">
	<strong><?php echo get_phrase('instruction').': '; ?></strong>
	<?php echo get_phrase('this_insrtuction_is_only_required_for_fill_in_the_gaps_type_question').'. '.get_phrase('when_you_will_need_to_insert_a_gap_you_can_simply_enter').' \'^\' '.get_phrase('to_get_a_blank').' . '.get_phrase('you_can_check_it_on_preview').'...'; ?>
</div>

<?php echo form_open(site_url('admin/manage_online_exam_question/'.$online_exam_id.'/add/fill_in_the_blanks') , array('class' => 'form-horizontal form-groups-bordered validate','target'=>'_top'));?>

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

<div class="form-group">
    <label class="col-sm-3 control-label"><?php echo get_phrase('preview');?></label>
    <div class="col-sm-8">
        <div class="" id="preview"></div>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label"><?php echo get_phrase('suitable_words');?></label>
    <div class="col-sm-8">
        <textarea name="suitable_words" class = "form-control" rows="8" cols="80" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" placeholder="<?php echo get_phrase('this_area_will_contain_suitable_words_for_the_blanks').'. '.get_phrase('please_write_down_the_suitable_words_side_by_side_separated_by_a_comma'); ?>"></textarea>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <button type="submit" class="btn btn-info btn-block"><?php echo get_phrase('add_question');?></button>
    </div>
</div>
<?php echo form_close();?>

<script type="text/javascript">
	function changeTheBlankColor() {
        var alpha = ["^"];
        var res = "", cls = "";
        var t = jQuery("#question_title").val();

        for (i=0; i<t.length; i++) {
            for (j=0; j<alpha.length; j++) {
                if (t[i] == alpha[j])
                {
                    cls = "red";
                }
            }
            if (t[i] === "^") {
                res += "<span class='"+cls+"'>"+'__'+"</span>";
            }
            else{
                res += "<span class='"+cls+"'>"+t[i]+"</span>";
            }
            cls="";
        }
        jQuery("#preview").html(res);
    }
</script>