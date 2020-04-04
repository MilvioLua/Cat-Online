<div class="row">
  <div class="col-md-6">
    <div class="panel panel-primary" >
      <div class="panel-heading">
          <div class="panel-title">
              <?php echo 'SMTP'.' '.get_phrase('settings');?>
          </div>
      </div>
      <div class="panel-body">
        <form method="post" action="<?php echo site_url('admin/smtp_settings/update'); ?>">
        <?php foreach($settings as $smtp_settings){ ?>
          <?php if($smtp_settings['type'] == 'protocol'): ?>
            <div class="form-group">
              <label  class="col-sm-3 control-label text-right"><?php echo get_phrase('protocol');?></label>
              <div class="col-sm-9" style="margin-bottom: 15px;">
                  <input type="text" required="true" class="form-control" name="protocol"
                      value="<?php echo $smtp_settings['description']; ?>" required>
              </div>
            </div>
          <?php endif; ?>
      
          <?php if($smtp_settings['type'] == 'smtp_host'): ?>
            <div class="form-group">
              <label  class="col-sm-3 control-label text-right"><?php echo 'SMTP'.' '.get_phrase('host');?></label>
              <div class="col-sm-9" style="margin-bottom: 15px;">
                  <input type="text" required="true" class="form-control" name="smtp_host"
                      value="<?php echo $smtp_settings['description']; ?>" required>
              </div>
            </div>
          <?php endif; ?>

          <?php if($smtp_settings['type'] == 'smtp_port'): ?>
            <div class="form-group">
              <label  class="col-sm-3 control-label text-right"><?php echo 'SMTP'.' '.get_phrase('port');?></label>
              <div class="col-sm-9" style="margin-bottom: 15px;">
                  <input type="text" required="true" class="form-control" name="smtp_port"
                      value="<?php echo $smtp_settings['description']; ?>" required>
              </div>
            </div>
          <?php endif; ?>

          <?php if($smtp_settings['type'] == 'smtp_user'): ?>
            <div class="form-group">
              <label  class="col-sm-3 control-label text-right"><?php echo 'SMTP'.' '.get_phrase('username');?></label>
              <div class="col-sm-9" style="margin-bottom: 15px;">
                  <input type="text" class="form-control" name="smtp_user"
                      value="<?php echo $smtp_settings['description']; ?>">
              </div>
            </div>
          <?php endif; ?>

          <?php if($smtp_settings['type'] == 'smtp_pass'): ?>
            <div class="form-group">
              <label  class="col-sm-3 control-label text-right"><?php echo 'SMTP'.' '.get_phrase('password');?></label>
              <div class="col-sm-9" style="margin-bottom: 15px;">
                  <input type="text" class="form-control" name="smtp_pass"
                      value="<?php echo $smtp_settings['description']; ?>">
              </div>
            </div>
          <?php endif; ?>
        <?php }  ?>
          <div class="form-group">
            <label  class="col-sm-3 control-label text-right"></label>
            <div class="col-sm-9" style="margin-bottom: 15px;">
                <button type="submit" class="btn btn-info">Save</button>
            </div>
          </div>

      </div>
    </div>
  </div>
</div>