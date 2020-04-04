<button onclick="showAjaxModal('<?php echo site_url('modal/popup/question_paper_add');?>');" 
    class="btn btn-primary pull-right">
        <?php echo get_phrase('add_question_paper'); ?>
</button>
<div style="clear:both;"></div>
<br>

<table class="table table-bordered" id="table_export">
    <thead>
        <tr>
            <th style="width: 60px;">#</th>
            <th><?php echo get_phrase('title');?></th>
            <th><?php echo get_phrase('class');?></th>
            <th><?php echo get_phrase('exam');?></th>
            <th><?php echo get_phrase('teacher');?></th>
            <th><?php echo get_phrase('options');?></th>
        </tr>
    </thead>

    <tbody>
        <?php
        $count = 1;
        $this->db->order_by('question_paper_id', 'desc');
        $question_papers = $this->db->get_where('question_paper', array('teacher_id' => $this->session->userdata('login_user_id')))->result_array();
        foreach ($question_papers as $row) { ?>   
            <tr>
                <td><?php echo $count++; ?></td>
                <td><?php echo $row['title']?></td>
                <td>
                    <?php echo $this->db->get_where('class', array('class_id' => $row['class_id']))->row()->name; ?>
                </td>
                <td>
                    <?php echo $this->db->get_where('exam', array('exam_id' => $row['exam_id']))->row()->name; ?>
                </td>
                <td>
                    <?php echo $this->db->get_where('teacher', array('teacher_id' => $row['teacher_id']))->row()->name; ?>
                </td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                            Action <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-default pull-right" role="menu">
                            <li>
                                <a href="#" onclick="showAjaxModal('<?php echo site_url('modal/popup/question_paper_edit/'.$row['question_paper_id']);?>');">
                                    <i class="entypo-pencil"></i>
                                    <?php echo get_phrase('edit');?>
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="showAjaxModal('<?php echo site_url('modal/popup/question_paper_view/'.$row['question_paper_id']);?>');">
                                    <i class="entypo-eye"></i>
                                    <?php echo get_phrase('view_question_paper');?>
                                </a>
                            </li>
                            <li class="divider"></li>
                            
                            <li>
                                <a href="#" onclick="confirm_modal('<?php echo site_url('teacher/question_paper/delete/'.$row['question_paper_id']);?>');">
                                    <i class="entypo-trash"></i>
                                    <?php echo get_phrase('delete');?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>