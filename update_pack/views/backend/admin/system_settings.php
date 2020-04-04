<div class="row">
    <?php echo form_open(site_url('admin/system_settings/do_update') ,
      array('class' => 'form-horizontal form-groups-bordered','target'=>'_top'));?>
        <div class="col-md-6">

            <div class="panel panel-primary" >

                <div class="panel-heading">
                    <div class="panel-title">
                        <?php echo get_phrase('system_settings');?>
                    </div>
                </div>

                <div class="panel-body">

                  <div class="form-group">
                      <label  class="col-sm-3 control-label"><?php echo get_phrase('school_name');?></label>
                      <div class="col-sm-9">
                          <input type="text" required="true" class="form-control" name="system_name"
                              value="<?php echo $this->db->get_where('settings' , array('type' =>'system_name'))->row()->description;?>" required>
                      </div>
                  </div>

                  <div class="form-group">
                      <label  class="col-sm-3 control-label"><?php echo get_phrase('application_title');?></label>
                      <div class="col-sm-9">
                          <input type="text" class="form-control" name="system_title"
                              value="<?php echo $this->db->get_where('settings' , array('type' =>'system_title'))->row()->description;?>" required>
                      </div>
                  </div>

                  <div class="form-group">
                      <label  class="col-sm-3 control-label"><?php echo get_phrase('address');?></label>
                      <div class="col-sm-9">
                          <input type="text" class="form-control" name="address"
                              value="<?php echo $this->db->get_where('settings' , array('type' =>'address'))->row()->description;?>" required>
                      </div>
                  </div>

                  <div class="form-group">
                      <label  class="col-sm-3 control-label"><?php echo get_phrase('phone');?></label>
                      <div class="col-sm-9">
                          <input type="text" class="form-control" name="phone"
                              value="<?php echo $this->db->get_where('settings' , array('type' =>'phone'))->row()->description;?>" required>
                      </div>
                  </div>

                  <!-- <div class="form-group">
                      <label  class="col-sm-3 control-label"><?php echo get_phrase('paypal_email');?></label>
                      <div class="col-sm-9">
                          <input type="text" class="form-control" name="paypal_email"
                              value="<?php echo $this->db->get_where('settings' , array('type' =>'paypal_email'))->row()->description;?>">
                      </div>
                  </div> -->

                  <div class="form-group">
                      <label  class="col-sm-3 control-label"><?php echo get_phrase('currency');?></label>
                      <div class="col-sm-9">
                          <input type="text" class="form-control" name="currency"
                              value="<?php echo $this->db->get_where('settings' , array('type' =>'currency'))->row()->description;?>" required>
                      </div>
                  </div>

                  <div class="form-group">
                      <label  class="col-sm-3 control-label"><?php echo get_phrase('system_email');?></label>
                      <div class="col-sm-9">
                          <input type="text" class="form-control" name="system_email"
                              value="<?php echo $this->db->get_where('settings' , array('type' =>'system_email'))->row()->description;?>" required>
                      </div>
                  </div>

                  <div class="form-group">
                      <label  class="col-sm-3 control-label"><?php echo get_phrase('running_session');?></label>
                      <div class="col-sm-9">
                          <select name="running_year" class="form-control selectboxit">
                          <?php $running_year = $this->db->get_where('settings' , array('type'=>'running_year'))->row()->description;?>
                          <option value="" disabled="true"><?php echo get_phrase('select_running_session');?></option>
                          <?php for($i = 0; $i < 10; $i++):?>
                              <option value="<?php echo (2016+$i);?>-<?php echo (2016+$i+1);?>"
                                <?php if($running_year == (2016+$i).'-'.(2016+$i+1)) echo 'selected';?>>
                                  <?php echo (2016+$i);?>-<?php echo (2016+$i+1);?>
                              </option>
                          <?php endfor;?>
                          </select>
                      </div>
                  </div>

                  <div class="form-group">
                      <label  class="col-sm-3 control-label"><?php echo get_phrase('language');?></label>
                      <div class="col-sm-9">
                          
                        <select class="form-control" data-toggle="select2" name="language" id="language">
                          <?php foreach ($languages as $language): ?>
                              <option value="<?php echo $language; ?>" <?php if(get_settings('language') == $language) echo 'selected'; ?>><?php echo ucfirst($language); ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                  </div>

                  <div class="form-group">
                      <label  class="col-sm-3 control-label"><?php echo get_phrase('text_align');?></label>
                      <div class="col-sm-9">
                          <select name="text_align" class="form-control selectboxit">
                          	  <?php $text_align	=	$this->db->get_where('settings' , array('type'=>'text_align'))->row()->description;?>
                              <option value="left-to-right" <?php if ($text_align == 'left-to-right')echo 'selected';?>> left-to-right</option>
                              <option value="right-to-left" <?php if ($text_align == 'right-to-left')echo 'selected';?>> right-to-left</option>
                          </select>
                      </div>
                  </div>
                  <div class="form-group">
                      <label  class="col-sm-3 control-label"><?php echo get_phrase('disable_frontend');?></label>
                      <div class="col-sm-9">
                        <div class="container">
                          <label class="control control--checkbox">
                            <input type="checkbox" name="disable_frontend" <?php if ($this->db->get_where('settings' , array('type' =>'disable_frontend'))->row()->description == 1) echo 'checked'; ?> />
                            <div class="control__indicator"></div>
                          </label>
                        </div>
                      </div>
                  </div>

                  <div class="form-group">
                      <label  class="col-sm-3 control-label"><?php echo get_phrase('purchase_code');?></label>
                      <div class="col-sm-9">
                          <input type="text" class="form-control" name="purchase_code"
                              value="<?php echo $this->db->get_where('settings' , array('type' =>'purchase_code'))->row()->description;?>" required>
                      </div>
                  </div>

                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <button type="submit" class="btn btn-info"><?php echo get_phrase('save');?></button>
                    </div>
                  </div>
                    <?php echo form_close();?>

                </div>

            </div>



        </div>

            <?php echo form_open(site_url('admin/system_settings/upload_logo') , array(
            'class' => 'form-horizontal form-groups-bordered validate','target'=>'_top' , 'enctype' => 'multipart/form-data'));?>

              <div class="col-md-6">
                <div class="panel panel-primary" >

                  <div class="panel-heading">
                      <div class="panel-title">
                          <?php echo get_phrase('upload_logo');?>
                      </div>
                  </div>

                  <div class="panel-body">


                      <div class="form-group">
                          <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('photo');?></label>

                          <div class="col-sm-9">
                              <div class="fileinput fileinput-new" data-provides="fileinput">
                                  <div class="fileinput-new thumbnail" style="width: 100px; height: 100px;" data-trigger="fileinput">
                                      <img src="<?php echo base_url();?>uploads/logo.png" alt="...">
                                  </div>
                                  <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px"></div>
                                  <div>
                                      <span class="btn btn-white btn-file">
                                          <span class="fileinput-new">Select image</span>
                                          <span class="fileinput-exists">Change</span>
                                          <input type="file" name="userfile" accept="image/*" required="required">
                                      </span>
                                      <a href="#" class="btn btn-orange fileinput-exists" data-dismiss="fileinput">Remove</a>
                                  </div>
                              </div>
                          </div>
                      </div>


                    <div class="form-group">
                      <div class="col-sm-offset-3 col-sm-9">
                          <button type="submit" class="btn btn-info"><?php echo get_phrase('upload');?></button>
                      </div>
                    </div>

                  </div>

              </div>

                    <div class="panel panel-primary" data-collapsed="0">

                      <div class="panel-heading">
                          <div class="panel-title">
                              <?php echo get_phrase('update_product');?>
                          </div>
                      </div>


                      <div class="panel-body form-horizontal form-groups-bordered">
                          <?php echo form_open(site_url('updater/update') , array('class' => 'form-horizontal form-groups-bordered', 'enctype' => 'multipart/form-data'));?>

                              <div class="form-group">
                                  <label class="col-sm-3 control-label"><?php echo get_phrase('file'); ?></label>

                                  <div class="col-sm-5">

                                      <input type="file" name="file_name" class="form-control file2 inline btn btn-primary" data-label="<i class='glyphicon glyphicon-file'></i> Browse" />

                                  </div>
                              </div>

                              <div class="form-group">
                                  <div class="col-sm-offset-3 col-sm-5">
                                      <input type="submit" class="btn btn-info" value="<?php echo get_phrase('install_update'); ?>" />
                                  </div>
                              </div>

                          <?php echo form_close(); ?>
                      </div>

                  </div>
              
              </div>

            <?php echo form_close();?>
        </div>
    </div>
<style media="screen">
.container {

}

.control-group {
display: inline-block;
vertical-align: top;
background: #fff;
text-align: left;
box-shadow: 0 1px 2px rgba(0,0,0,0.1);
padding: 30px;
width: 200px;
height: 210px;
margin: 10px;
}
.control {
display: block;
position: relative;
padding-left: 40px;
margin-bottom: 15px;
cursor: pointer;
font-size: 18px;
}
.control input {
position: absolute;
z-index: -1;
opacity: 0;
}
.control__indicator {
position: absolute;
top: 2px;
left: -11px;
height: 20px;
width: 20px;
background: #e6e6e6;
}
.control--radio .control__indicator {
border-radius: 50%;
}
.control:hover input ~ .control__indicator,
.control input:focus ~ .control__indicator {
background: #ccc;
}
.control input:checked ~ .control__indicator {
background: #2aa1c0;
}
.control:hover input:not([disabled]):checked ~ .control__indicator,
.control input:checked:focus ~ .control__indicator {
background: #0e647d;
}
.control input:disabled ~ .control__indicator {
background: #e6e6e6;
opacity: 0.6;
pointer-events: none;
}
.control__indicator:after {
content: '';
position: absolute;
display: none;
}
.control input:checked ~ .control__indicator:after {
display: block;
}
.control--checkbox .control__indicator:after {
      left: 8px;
      top: 5px;
      width: 4px;
      height: 9px;
      border: 3px solid #fff;
      border-width: 0 2px 2px 0;
      transform: rotate(45deg);
}
.control--checkbox input:disabled ~ .control__indicator:after {
border-color: #7b7b7b;
}


</style>
