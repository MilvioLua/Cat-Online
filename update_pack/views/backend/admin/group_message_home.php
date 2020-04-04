<div class="mail-header">
    <!-- title -->
    <h3 class="mail-title">
        <?php echo get_phrase('group_messages'); ?>
    </h3>
</div>

<div style="width:100%; text-align:center;padding:100px;color:#aaa;">

    <a href = "#" onclick="showAjaxModal('<?php echo site_url('modal/popup/create_group/');?>');" style="color: #aaa;">
      <i class="fa fa-plus-circle" aria-hidden="true" style="font-size: 50px;"></i>
      <br>
      <div>
          <?php echo get_phrase('create_a_new_group'); ?>
      </div>
    </a>
</div>
