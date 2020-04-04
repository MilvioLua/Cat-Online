<?php 
	$group_info = $this->db->get_where('group_message_thread', array('group_message_thread_code' => $param2))->row_array();
	$user_info  = json_decode($group_info['members']);
 ?>
<div class="col-md-12" style="margin-top: 10px;">
    <table  class="table table-bordered">
      <span class="col-md-12" style="font-size: 13px; color: #616161; text-align: center; padding: 0; margin: 0;"><?php echo ucfirst($group_info['group_name']); ?></span>
      <thead>
        <tr>
          <th><?php echo get_phrase('image'); ?></th>
          <th><?php echo get_phrase('email'); ?></th>
          <th><?php echo get_phrase('name'); ?></th>
        </tr>
      </thead>
      <?php for($i = 0; $i < sizeof($user_info); $i++):
      	$user_data = explode('_', $user_info[$i]);
      	$user_type = $user_data[0];
      	$user_id   = $user_data[1];
      ?>
        <tr>
          <td width = "20%"> <img src="<?php echo $this->crud_model->get_image_url($user_type, $user_id); ?>" class="img-circle" width="30"></td>
          <td width = "40%"><span><?php echo $this->db->get_where($user_type, array($user_type . '_id' => $user_id))->row()->email; ?></span></td>
          <td width = "40%"><span><?php echo $this->db->get_where($user_type, array($user_type . '_id' => $user_id))->row()->name; ?></span></td>
        </tr>
      <?php endfor ?>
    </table>
  </div>