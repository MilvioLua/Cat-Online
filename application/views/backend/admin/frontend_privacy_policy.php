<ul class="nav nav-tabs bordered">
  <li class="active">
    <a href="#tab1" data-toggle="tab">
      <span class="visible-xs"><i class="entypo-home"></i></span>
      <span class="hidden-xs"><?php echo get_phrase('privacy_policy'); ?></span>
    </a>
  </li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="tab1" style="margin-top: 20px;">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <form class="form-horizontal form-groups" method="post" id="jq-submit"
          action="<?php echo site_url('admin/frontend_settings/update_privacy_policy');?>">
          <div class="form-group">
             <textarea class="form-control wysihtml5" data-stylesheet-url="assets/css/wysihtml5-color.css"
               id="sample_wysiwyg"><?php echo $this->frontend_model->get_frontend_general_settings('privacy_policy'); ?></textarea>
            <input type="hidden" name="privacy_policy" id="privacy_policy">
          </div>
          <div class="form-group">
            <button type="button" class="btn btn-success"
              id="submit_button">
              <i class="entypo-check"></i> &nbsp; <?php echo get_phrase('save'); ?>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

    

<script type="text/javascript">

  $('#submit_button').on('click', function() {
    var value = $('#sample_wysiwyg').val();
    $('#privacy_policy').val(value);
    $('#jq-submit').submit();
  });

</script>
