<?php
    $question_details = $this->db->get_where('question_bank', array('question_bank_id' => $question_id))->row_array();

    if ($question_details['options'] != "" || $question_details['options'] != null) {
        $options = json_decode($question_details['options']);
    } else {

        $options = array();
    }
    if ($question_details['correct_answers'] != "" || $question_details['correct_answers'] != null) {
        $correct_answers= json_decode($question_details['correct_answers']);
    }
    else {
        $correct_answers = array();
    }

    $online_exam_details = $this->db->get_where('online_exam', array('online_exam_id' => $question_details['online_exam_id']))->row_array();

    $added_question_info = $this->db->get_where('question_bank', array('online_exam_id' => $online_exam_details['online_exam_id']))->result_array();
    if($question_details['type'] == 'fill_in_the_blanks'){
        $suitable_words = implode(',', json_decode($question_details['correct_answers']));
    }
?>
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title" >
                    <i class="entypo-plus-circled"></i>
                    <?php echo get_phrase('question_bank');?>
                </div>
            </div>
            <div class="panel-body">
                <?php echo form_open(site_url('admin/update_online_exam_question/'.$question_id.'/update'.'/'.$question_details['online_exam_id']) , array('class' => 'form-horizontal form-groups-bordered validate','target'=>'_top'));?>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo get_phrase('mark');?></label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" name="mark" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" min="0" value="<?php echo $question_details['mark']; ?>"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo get_phrase('question_title');?></label>
                        <div class="col-sm-10">
                            <textarea name="question_title" class = "form-control" id = "question_title" rows="8" cols="80" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" <?php if($question_details['type'] == 'fill_in_the_blanks') echo "onkeyup='changeTheBlankColor()'"; ?>><?php echo $question_details['question_title']; ?></textarea>
                        </div>
                    </div>

                    <!-- Multiple choice question portion -->
                    <?php if ($question_details['type'] == 'multiple_choice'): ?>
                        <div class="form-group" id = 'multiple_choice_question'>
                            <label class="col-sm-2 control-label"><?php echo get_phrase('number_options');?></label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                  <input type="number" class="form-control" name="number_of_options" id = "number_of_options" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" min="0" value="<?php echo $question_details['number_of_options']; ?>"/>
                                  <div class="input-group-addon" style="padding: 0px"><button type="button" class = 'btn btn-sm pull-right' name="button" onclick="showOptions(jQuery('#number_of_options').val())" style="border-radius: 0px; background-color: #eeeeee;"><i class="fa fa-check"></i></button></div>
                                </div>
                            </div>
                        </div>
                        <?php for ($i = 0; $i < $question_details['number_of_options']; $i++):?>
                            <div class="form-group options">
                                <label class="col-sm-2 control-label"><?php echo get_phrase('option_').($i+1);?></label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name = "options[]" id="option_<?php echo $i+1; ?>" placeholder="<?php echo get_phrase('option_').($i+1); ?>" required value="<?php echo $options[$i]; ?>">
                                      <div class="input-group-addon"><input type = 'checkbox' name = "correct_answers[]" value = <?php echo ($i+1); ?> <?php if(in_array(($i+1), $correct_answers)) echo 'checked'; ?>></div>
                                    </div>
                                </div>
                            </div>
                        <?php endfor;?>
                    <?php endif; ?>

                    <!-- True False question portion -->
                    <?php if ($question_details['type'] == 'true_false'): ?>
                        <div class="row"  style="margin-top: 10px; text-align: left;">
                            <div class="col-sm-10 col-sm-offset-2">
                                <p>
                                    <input type="radio" id="true" name="true_false_answer" value="true" <?php if($question_details['correct_answers'] == 'true') echo 'checked';  ?>>
                                    <label for="true"><?php echo get_phrase('true'); ?></label>
                                </p>
                            </div>
                            <div class="col-sm-10 col-sm-offset-2">
                                <p>
                                    <input type="radio" id="false" name="true_false_answer" value="false" <?php if($question_details['correct_answers'] == 'false') echo 'checked';  ?>>
                                    <label for="false"><?php echo get_phrase('false'); ?></label>
                                </p>
                            </div>

                        </div>
                    <?php endif; ?>

                    <!-- Fill In The Blanks question portion -->
                    <?php if ($question_details['type'] == 'fill_in_the_blanks'): ?>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo get_phrase('preview');?></label>
                            <div class="col-sm-10">
                                <div class="" id = "preview"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo get_phrase('suitable_words');?></label>
                            <div class="col-sm-10">
                                <textarea name="suitable_words" class = "form-control" rows="8" cols="80" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" placeholder="<?php echo get_phrase('this_area_will_contain_suitable_words_for_the_blanks').'. '.get_phrase('please_write_down_the_suitable_words_side_by_side_with_a_comma').' \',\' '.get_phrase('delimiter').'...'; ?>" ><?php echo $suitable_words; ?></textarea>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <div class="col-sm-12" style="text-align: center;">
                            <button type="submit" class="btn btn-info"><?php echo get_phrase('update_question');?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title" >
                    <i class="entypo-plus-circled"></i>
                    <?php echo get_phrase('exam_details');?>
                </div>
            </div>
            <div class="panel-body">
                <table  class="table table-bordered">
                    <tbody>
                        <tr>
                            <td width = 50% ><?php echo get_phrase('exam_title');?></td>
                            <td><?php echo $online_exam_details['title']; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo get_phrase('selected_class');?></td>
                            <td><?php echo $this->db->get_where('class', array('class_id' => $online_exam_details['class_id']))->row()->name; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo get_phrase('selected_section');?></td>
                            <td><?php echo $this->db->get_where('section', array('section_id' => $online_exam_details['section_id']))->row()->name; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo get_phrase('subject');?></td>
                            <td><?php echo $this->db->get_where('subject', array('subject_id' => $online_exam_details['subject_id']))->row()->name; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo get_phrase('exam_type');?></td>
                            <td><?php echo get_phrase($online_exam_details['exam_type']); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo get_phrase('validity');?></td>
                            <td><?php echo date('M d, Y', $online_exam_details['exam_valid_from']).' - '.date('M d, Y', $online_exam_details['exam_valid_to']); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo get_phrase('duration');?></td>
                            <td><?php echo gmdate('H:i:s', $online_exam_details['duration']);; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo get_phrase('minimum_percentage');?></td>
                            <td><?php echo $online_exam_details['minimum_percentage'].'%'; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo get_phrase('total_number');?></td>
                            <td>
                                <?php if (sizeof($added_question_info) == 0):?>
                                    <?php echo 0; ?>
                                <?php elseif (sizeof($added_question_info) > 0):?>
                                    <?php
                                        $total_mark = 0;
                                        foreach ($added_question_info as $single_question) {
                                            $total_mark = $total_mark + $single_question['mark'];
                                        }
                                        echo $total_mark;
                                     ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <br>
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title" >
                    <i class="entypo-plus-circled"></i>
                    <?php echo get_phrase('added_questions');?>
                </div>
            </div>
            <div class="panel-body">
                <table  class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align: center;"><div>#</div></th>
                            <th style="text-align: center;"><div><?php echo get_phrase('question');?></div></th>
                            <th style="text-align: center;"><div><?php echo get_phrase('mark');?></div></th>
                            <th style="text-align: center;" width = 15%><div><?php echo get_phrase('options');?></div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (sizeof($added_question_info) == 0):?>
                            <tr>
                                <td colspan="4"><?php echo get_phrase('no_question_has_been_added_yet'); ?></td>
                            </tr>

                            <?php
                            elseif (sizeof($added_question_info) > 0):
                            $i = 0;
                            foreach ($added_question_info as $added_question): ?>
                                <tr>
                                    <td style="text-align: center;"><?php echo ++$i; ?></td>
                                    <?php if ($online_exam_details['exam_type'] == 'fill_in_the_blanks'): ?>
                                        <td style="text-align: center;"><?php echo substr($added_question['question_title'], 0, 30).'...'; ?></td>
                                    <?php else: ?>
                                        <td style="text-align: center;"><?php echo $added_question['question_title']; ?></td>
                                    <?php endif; ?>
                                    <td style="text-align: center;"><?php echo $added_question['mark']; ?></td>
                                    <td style="text-align: center;">
                                        <a href="<?php echo site_url('admin/update_online_exam_question/'.$added_question['question_bank_id']); ?>" class = "btn btn-primary btn-xs" data-toggle="tooltip" title="<?php echo get_phrase('edit'); ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        <a href = "#" onclick="confirm_modal('<?php echo site_url('admin/delete_question_from_online_exam/'.$added_question['question_bank_id']);?>');" class = "btn btn-danger btn-xs"  data-toggle="tooltip" title="<?php echo get_phrase('delete'); ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style media="screen">
    [type="radio"]:checked,
    [type="radio"]:not(:checked) {
        position: absolute;
        left: -9999px;
    }
    [type="radio"]:checked + label,
    [type="radio"]:not(:checked) + label
    {
        position: relative;
        padding-left: 28px;
        cursor: pointer;
        line-height: 20px;
        display: inline-block;
        color: #666;
    }
    [type="radio"]:checked + label:before,
    [type="radio"]:not(:checked) + label:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 18px;
        height: 18px;
        border: 1px solid #ddd;
        border-radius: 100%;
        background: #fff;
    }
    [type="radio"]:checked + label:after,
    [type="radio"]:not(:checked) + label:after {
        content: '';
        width: 12px;
        height: 12px;
        background: #2aa1c0;
        position: absolute;
        top: 3px;
        left: 3px;
        border-radius: 100%;
        -webkit-transition: all 0.2s ease;
        transition: all 0.2s ease;
    }
    [type="radio"]:not(:checked) + label:after {
        opacity: 0;
        -webkit-transform: scale(0);
        transform: scale(0);
    }
    [type="radio"]:checked + label:after {
        opacity: 1;
        -webkit-transform: scale(1);
        transform: scale(1);
    }
    .red {
    /* background-color: #EEEEEE; */
    color: #f44336;
}
</style>

<!-----  DATA TABLE EXPORT CONFIGURATIONS ---->
<script type="text/javascript">

	jQuery(document).ready(function($)
	{
        changeTheBlankColor();
	});

    function showOptions(number_of_options){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('admin/manage_multiple_choices_options'); ?>",
            data: {number_of_options : number_of_options},
            success: function(response){
                console.log(response);
                jQuery('.options').remove();
                jQuery('#multiple_choice_question').after(response);
                //jQuery('#multiple_choice_option_area').html(response);
            }
        });
        //jQuery('.options').removeClass('hide');
    }

    function changeTheBlankColor(){
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
