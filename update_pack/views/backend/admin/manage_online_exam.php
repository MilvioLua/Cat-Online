<hr>
<div class="row">
    <div class="col-md-12">
        <a href="<?php echo site_url('admin/manage_online_exam');?>" class="btn btn-<?php echo $status == 'active' ? 'primary' : 'white'; ?>">
            <?php echo get_phrase('active_exams');?>
        </a>
        <a href="<?php echo site_url('admin/manage_online_exam/expired');?>" class="btn btn-<?php echo $status == 'expired' ? 'primary' : 'white'; ?>">
            <?php echo get_phrase('expired_exams');?>
        </a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table  class="table table-bordered datatable" id="table_export">
            <thead>
                <tr>
                    <th><div><?php echo get_phrase('exam_name');?></div></th>
                    <th><div><?php echo get_phrase('class_and_section');?></div></th>
                    <th><div><?php echo get_phrase('subject');?></div></th>
                    <th><div><?php echo get_phrase('exam_date');?></div></th>
                    <th><div><?php echo get_phrase('status');?></div></th>
                    <th><div><?php echo get_phrase('options');?></div></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($online_exams as $row):?>
                <tr>
                    <td>
                        <a href="<?php echo site_url('admin/manage_online_exam_question/').$row['online_exam_id']; ?>">
                            <?php echo $row['title'];?>
                        </a>
                    </td>
                    <td>
                        <?php
                            echo '<b>'.get_phrase('class').':</b> '.$this->db->get_where('class', array('class_id' => $row['class_id']))->row()->name.'<br/><b>'.get_phrase('section').':</b> '.$this->db->get_where('section', array('section_id' => $row['section_id']))->row()->name;
                        ?>
                    </td>
                    <td>
                        <?php
                            echo $this->db->get_where('subject', array('subject_id' => $row['subject_id']))->row()->name;
                         ?>
                    </td>
                    <td>
                        <?php
                            echo '<b>'.get_phrase('date').':</b> '.date('M d, Y', $row['exam_date']).'<br>'.'<b>'.get_phrase('time').':</b> '.$row['time_start'].' - '.$row['time_end'];
                        ?>
                    </td>
                    <td>
                        <button class="btn btn-<?php echo $row['status'] == 'published' ? 'success' : 'warning'; ?> btn-xs">
                            <?php echo get_phrase($row['status']);?>
                        </button>
                    </td>
                    <td style="text-align: center;">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                            Action <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-default pull-right" role="menu">

                            <li>
                                <a href="<?php echo site_url('admin/manage_online_exam_question/').$row['online_exam_id']; ?>">
                                    <i class="entypo-cog"></i>
                                    <?php echo get_phrase('manage_question');?>
                                </a>
                            </li>
                            <!-- EDITING LINK -->
                            <li>
                                <a href="<?php echo site_url('admin/update_online_exam/').$row['online_exam_id']; ?>" >
                                    <i class="entypo-pencil"></i>
                                    <?php echo get_phrase('edit_exam_info');?>
                                </a>
                            </li>
                            <li class="divider"></li>

                            <!-- DELETION LINK -->
                            <li>
                                <a href="#" onclick="confirm_modal('<?php echo site_url('admin/manage_online_exam/delete/'.$row['online_exam_id']);?>');">
                                    <i class="entypo-trash"></i>
                                    <?php echo get_phrase('delete');?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <?php if ($row['status'] == 'pending'): ?>
                        <a href="#" onclick="confirm_modal('<?php echo site_url('admin/manage_online_exam_status/'.$row['online_exam_id'].'/published'); ?>', 'generic_confirmation');" type="button" class = "btn btn-success btn-sm"><i class="fa fa-share-alt" aria-hidden="true"></i> <?php echo get_phrase('publish_now'); ?></a>
                    <?php elseif ($row['status'] == 'published'): ?>
                        <a href="#" onclick="confirm_modal('<?php echo site_url('admin/manage_online_exam_status/'.$row['online_exam_id'].'/expired'); ?>', 'generic_confirmation');" type="button" class = "btn btn-danger btn-sm"><i class="fa fa-times" aria-hidden="true"></i> <?php echo get_phrase('cancel_now'); ?></a>
                    <?php elseif($row['status'] == 'expired'): ?>
                        <a href="#" type="button" class = "btn btn-primary btn-sm"><i class="fa fa-ban" aria-hidden="true"></i> <?php echo get_phrase('expired'); ?></a>
                    <?php endif; ?>

                    <a href="<?php echo site_url('admin/view_online_exam_result/'.$row['online_exam_id']); ?>" type="button" class = "btn btn-primary"><i class="fa fa-eye" aria-hidden="true"></i> <?php echo get_phrase('view_result'); ?></a>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>

<!-----  DATA TABLE EXPORT CONFIGURATIONS ---->
<script type="text/javascript">

	jQuery(document).ready(function($)
    {
        $('#table_export').dataTable();
    });

</script>
