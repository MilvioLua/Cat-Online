
<style media="screen">
.mail-env .mail-sidebar .mail-menu > li:hover a.customize_group {
  background: none;
  color: #607D8B;
}
.mail-env .mail-sidebar .mail-menu > li.active a.customize_group {
    background: none;
    font-weight: bold;
}
</style>
<div class="pull-right" style="text-align: right; margin-top: -30px;">
  <a href="<?php echo site_url('admin/message'); ?>" class="btn btn-blue"><i class="fa fa-comment" aria-hidden="true"></i> <?php echo get_phrase('private_message'); ?></a>
</div>
<hr />
<div class="mail-env">

    <!-- Mail Body -->
    <div class="mail-body">

        <!-- message page body -->
        <?php include $message_inner_page_name . '.php'; ?>
    </div>

    <!-- Sidebar -->
    <div class="mail-sidebar" style="min-height: 800px;">
      <!-- compose new email button -->
      <div class="mail-sidebar-row hidden-xs">
          <a href="#" onclick="showAjaxModal('<?php echo site_url('modal/popup/create_group/');?>');" class="btn btn-success btn-block">
              <?php echo get_phrase('create_group'); ?>
          </a>
      </div>
        <!-- message user inbox list -->
        <ul class="mail-menu">

            <?php
              $group_messages = $this->db->get('group_message_thread')->result_array();
              if (sizeof($group_messages) > 0):
              foreach ($group_messages as $row):?>
              <li class="col-md-12 <?php if (isset($current_message_thread_code) && $current_message_thread_code == $row['group_message_thread_code']) echo 'active'; ?>">
                <div class="col-sm-10" style="text-align:left; margin: 0; padding: 0;">
                  <a href="<?php echo site_url('admin/group_message/group_message_read/'.$row['group_message_thread_code']); ?>" style="padding:12px;">
                      <i class="entypo-dot"></i>
                      <?php echo $row['group_name'] ?>
                  </a>
                </div>
                <div class="col-sm-2" style="text-align:right; margin: 0 0; padding: 12px 5px;">
                  <a href="#" class="customize_group" onclick="showAjaxModal('<?php echo site_url('modal/popup/edit_group/'.$row['group_message_thread_code']);?>');" style="margin: 0; padding: 0;"><i class="fa fa-cog" aria-hidden="true"></i></a>
                </div>
              </li>
            <?php endforeach; ?>
          <?php endif;
            if (sizeof($group_messages) == 0):?>
            <div class="col-sm-12" style="text-align: center; margin-top: 25px; color: #607D8B; font-size: 13px;">
              <?php echo '( '.get_phrase('create_a_group_now').' )'; ?>
            </div>
            <?php endif ?>
        </ul>

    </div>

</div>
