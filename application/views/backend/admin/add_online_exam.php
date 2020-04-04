<div class="row">
    <?php echo form_open(site_url('admin/manage_online_exam/create') , array('class' => 'form-horizontal form-groups-bordered validate','target'=>'_top'));?>
        <div class="col-md-6">
            <div class="panel panel-primary" data-collapsed="0">
                <div class="panel-heading">
                    <div class="panel-title" >
                        <i class="entypo-plus-circled"></i>
                        <?php echo get_phrase('online_exam');?>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo get_phrase('exam_title');?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="exam_title" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo get_phrase('class');?></label>
                        <div class="col-sm-9">
                            <select name="class_id" class="form-control selectboxit" data-validate="required" id="class_id"
                            data-message-required="<?php echo get_phrase('value_required');?>"
                            onchange="return get_class_sections(this.value)" required>
                                <option value=""><?php echo get_phrase('select_class');?></option>
                                    <?php
                                    $classes = $this->db->get('class')->result_array();
                                    foreach($classes as $row):?>
                                        <option value="<?php echo $row['class_id'];?>">
                                    <?php echo $row['name'];?>
                                </option>
                            <?php
                            endforeach;
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-2" class="col-sm-3 control-label"><?php echo get_phrase('section');?></label>
                        <div class="col-sm-9" id="section_selector_holder">
                            <select name="section_id" class="form-control selectboxit" id = "section_id">
                                <option value=""><?php echo get_phrase('select_class_first');?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="field-2" class="col-sm-3 control-label"><?php echo get_phrase('subject');?></label>
                        <div class="col-sm-9" id="subject_selector_holder">
                            <select name="subject_id" class="form-control selectboxit" id = "subject_id">
                                <option value=""><?php echo get_phrase('select_class_first');?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-primary" data-collapsed="0">
                <div class="panel-heading">
                    <div class="panel-title" >
                        <i class="entypo-plus-circled"></i>
                        <?php echo get_phrase('online_exam');?>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo get_phrase('exam_date');?></label>
                        <div class="col-sm-9">
                            <input type="text" class="datepicker form-control" name="exam_date" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo get_phrase('exam_time');?></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="text" class="form-control timepicker" name="time_start" id="time_start" data-template="dropdown" data-show-seconds="true" data-default-time="11:00" data-show-meridian="false" data-minute-step="5" data-second-step="5" value="" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" />
                                
                                <div class="input-group-addon">
                                    <a href="#"><i class="entypo-clock"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-1"><h5><strong><?php echo get_phrase('to');?></strong></h5></div>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="text" class="form-control timepicker" name="time_end" id="time_end" data-template="dropdown" data-show-seconds="true" data-default-time="11:30" data-show-meridian="false" data-minute-step="5" data-second-step="5" value="" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" />
                                
                                <div class="input-group-addon">
                                    <a href="#"><i class="entypo-clock"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo get_phrase('minimum_percentage');?></label>
                        <div class="col-sm-9">
                            <label class="sr-only" for="exampleInputAmount"><?php echo get_phrase('minimum_percentage_for_passing'); ?></label>
                            <div class="input-group">
                              <input type="text" class="form-control" name = "minimum_percentage" id="exampleInputAmount" placeholder="<?php echo get_phrase('minimum_percentage_for_passing'); ?>" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" required>
                              <div class="input-group-addon">%</div>
                            </div>
                        </div>
                   </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo get_phrase('instruction');?></label>
                        <div class="col-sm-9">
                            <textarea name="instruction" class = "form-control" rows="8" cols="80" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12" style="text-align: center;">
                <button type="submit" class="btn btn-info"><?php echo get_phrase('add_exam');?></button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
	function get_class_sections(class_id) {

    	$.ajax({
            url: '<?php echo site_url('admin/get_class_section_selector/');?>' + class_id ,
            success: function(response)
            {
                jQuery('#section_selector_holder').html(response);
            }
        });

        get_class_subject(class_id);
    }

    function get_class_subject(class_id) {

    	$.ajax({
            url: '<?php echo site_url('admin/get_class_subject_selector/');?>' + class_id ,
            success: function(response)
            {
                jQuery('#subject_selector_holder').html(response);
            }
        });
    }

</script>
