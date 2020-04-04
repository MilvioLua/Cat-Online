<div class="sidebar-menu">
    <header class="logo-env" >
        <!-- logo -->
        <!-- <div class="logo" style="">
            <a href="<?php echo site_url('login'); ?>">
                <img src="<?php echo base_url('uploads/logo.png');?>"  style="max-height:30px;"/>
            </a>
        </div> -->

        <!-- logo collapse icon -->
        <div class="sidebar-collapse" style="margin-top: 0px;">
            <a href="#" class="sidebar-collapse-icon" onclick="hide_brand()">
                <i class="entypo-menu"></i>
            </a>
        </div>
        <script type="text/javascript">
            function hide_brand() {
                $('#branding_element').toggle();
            }
        </script>

        <!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
        <div class="sidebar-mobile-menu visible-xs">
            <a href="#" class="with-animation">
                <i class="entypo-menu"></i>
            </a>
        </div>
    </header>

    <div style=""></div>
    <ul id="main-menu" class="">
        <!-- add class "multiple-expanded" to allow multiple submenus to open -->
        <!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
        <!-- <li id="search">
			<form class="" action="<?php echo site_url($account_type . '/student_details'); ?>" method="post">
				<input type="text" class="search-input" name="student_identifier" placeholder="<?php echo get_phrase('student_name').' / '.get_phrase('code').'...'; ?>" value="" required style="font-family: 'Poppins', sans-serif !important; background-color: #2C2E3E !important; color: #868AA8; border-bottom: 1px solid #3F3E5F;">
				<button type="submit">
					<i class="entypo-search"></i>
				</button>
			</form>
	    </li> -->
        <div style="text-align: -webkit-center;" id="branding_element">
            <img src="<?php echo base_url('uploads/logo.png');?>"  style="max-height:21px;"/>
            <h4 style="color: #a2a3b7;text-align: -webkit-center;margin-bottom: 25px;font-weight: 300;margin-top: 10px;">
                <?php echo $system_name;?>
            </h4>
        </div>

        <!-- Home -->
        <li class="<?php if ($page_name == 'dashboard') echo 'active'; ?> " style="border-top:1px solid #232540;">
            <a href="<?php echo site_url('admin/dashboard'); ?>">
                <i class="flaticon-home-2"></i>
                <span><?php echo get_phrase('admin_home'); ?></span>
            </a>
        </li>

        <!-- Manage Students -->
        <li class="<?php if ($page_name == 'student_add' ||
                                $page_name == 'student_bulk_add' ||
                                    $page_name == 'student_information' ||
                                        $page_name == 'student_marksheet' ||
                                            $page_name == 'student_promotion' ||
                                              $page_name == 'student_profile')
                                                echo 'opened active has-sub';
        ?> ">
            <a href="#">
                <i class="fa flaticon-avatar"></i>
                <span><?php echo get_phrase('manage_students'); ?></span>
            </a>
            <ul>
                <!-- STUDENT ADMISSION -->
                <li class="<?php if ($page_name == 'student_add') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/student_add'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('admit_student'); ?></span>
                    </a>
                </li>

                <!-- STUDENT BULK ADMISSION -->
                <li class="<?php if ($page_name == 'student_bulk_add') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/student_bulk_add'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('admit_bulk_student'); ?></span>
                    </a>
                </li>

                <!-- STUDENT INFORMATION -->
                <li class="<?php if ($page_name == 'student_information' || $page_name == 'student_marksheet') echo 'opened active'; ?> ">
                    <a href="#">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('student_information'); ?></span>
                    </a>
                    <ul>
                        <?php
                        $classes = $this->db->get('class')->result_array();
                        foreach ($classes as $row):
                            ?>
                            <li class="<?php if ($page_name == 'student_information' && $class_id == $row['class_id'] || $page_name == 'student_marksheet' && $class_id == $row['class_id']) echo 'active'; ?>">
                            <!--<li class="<?php if ($page_name == 'student_information' && $page_name == 'student_marksheet' && $class_id == $row['class_id']) echo 'active'; ?>">-->
                                <a href="<?php echo site_url('admin/student_information/' . $row['class_id']); ?>">
                                    <span><?php echo get_phrase('class'); ?> <?php echo $row['name']; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>

                <!-- STUDENT PROMOTION -->
                <li class="<?php if ($page_name == 'student_promotion') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/student_promotion'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('student_promotion'); ?></span>
                    </a>
                </li>

            </ul>
        </li>

        <!-- Manage Users -->
        <li class="<?php if ( $page_name == 'admin' ||
                              $page_name == 'teacher' ||
                              $page_name == 'parent' ||
                              $page_name == 'librarian' ||
                              $page_name == 'accountant')
                                echo 'opened active has-sub';?>">
            <a href="#">
                <i class="fa flaticon-users"></i>
                <span><?php echo get_phrase('manage_users'); ?></span>
            </a>
            <ul>
                <!-- manage admins -->
                <li class="<?php if ($page_name == 'admin') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/admin'); ?>">
                        <i class="fa entypo-dot"></i>
                        <span><?php echo get_phrase('admin'); ?></span>
                    </a>
                </li>
                <!-- manage teachers -->
                <li class="<?php if ($page_name == 'teacher') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/teacher'); ?>">
                        <i class="entypo-dot"></i>
                        <span><?php echo get_phrase('teacher'); ?></span>
                    </a>
                </li>
                <!-- manage parents -->
                <li class="<?php if ($page_name == 'parent') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/parent'); ?>">
                        <i class="entypo-dot"></i>
                        <span><?php echo get_phrase('parents'); ?></span>
                    </a>
                </li>
                <!-- librarian -->
                <li class="<?php if ($page_name == 'librarian') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/librarian'); ?>">
                        <i class="fa entypo-dot"></i>
                        <span><?php echo get_phrase('librarian'); ?></span>
                    </a>
                </li>
                <!-- accountant -->
                <li class="<?php if ($page_name == 'accountant') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/accountant'); ?>">
                        <i class="entypo-dot"></i>
                        <span><?php echo get_phrase('accountant'); ?></span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- CLASS -->
        <li class="<?php
        if ($page_name == 'class' ||
                $page_name == 'section' ||
                    $page_name == 'academic_syllabus' ||
                        $page_name == 'study_material')
            echo 'opened active';
        ?> ">
            <a href="#">
                <i class="flaticon-layers"></i>
                <span><?php echo get_phrase('academic_&_classes'); ?></span>
            </a>
            <ul>
                <li class="<?php if ($page_name == 'class') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/classes'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('manage_classes'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'section') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/section'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('manage_sections'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'academic_syllabus') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/academic_syllabus'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('academic_syllabus'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'study_material') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/study_material'); ?>">
                        <i class="entypo-dot"></i>
                        <span><?php echo get_phrase('study_material'); ?></span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- SUBJECT -->
        <li class="<?php if ($page_name == 'subject') echo 'opened active'; ?> ">
            <a href="#">
                <i class="flaticon-list-3"></i>
                <span><?php echo get_phrase('subject'); ?></span>
            </a>
            <ul>
                <?php
                $classes = $this->db->get('class')->result_array();
                foreach ($classes as $row):
                    ?>
                    <li class="<?php if ($page_name == 'subject' && $class_id == $row['class_id']) echo 'active'; ?>">
                        <a href="<?php echo site_url('admin/subject/' . $row['class_id']); ?>">
                            <span><?php echo get_phrase('class'); ?> <?php echo $row['name']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>

        <!-- CLASS ROUTINE -->
        <li class="<?php if ($page_name == 'class_routine_view' ||
                                $page_name == 'class_routine_add')
                                    echo 'opened active'; ?> ">
            <a href="#">
                <i class="flaticon-calendar-3"></i>
                <span><?php echo get_phrase('class_routine'); ?></span>
            </a>
            <ul>
                <li class="<?php if ($page_name == 'class_routine_add') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/class_routine_add'); ?>">
                        <span><i class="entypo-dot"></i> 
                            <?php echo get_phrase('add_class_routine'); ?></span>
                    </a>
                </li>
                <?php
                $classes = $this->db->get('class')->result_array();
                foreach ($classes as $row):
                    ?>
                    <li class="<?php if ($page_name == 'class_routine_view' && $class_id == $row['class_id']) echo 'active'; ?>">
                        <a href="<?php echo site_url('admin/class_routine_view/' . $row['class_id']); ?>">
                            <span><i class="entypo-dot"></i> 
                                <?php echo get_phrase('class'); ?> 
                                    <?php echo $row['name']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>

        <!-- DAILY ATTENDANCE -->
        <li class="<?php if ($page_name == 'manage_attendance' ||
                                $page_name == 'manage_attendance_view' || $page_name == 'attendance_report' || $page_name == 'attendance_report_view')
                                    echo 'opened active'; ?> ">
            <a href="#">
                <i class="flaticon-clipboard"></i>
                <span><?php echo get_phrase('daily_attendance'); ?></span>
            </a>
            <ul>

                    <li class="<?php if (($page_name == 'manage_attendance' || $page_name == 'manage_attendance_view')) echo 'active'; ?>">
                        <a href="<?php echo site_url('admin/manage_attendance'); ?>">
                            <span><i class="entypo-dot"></i><?php echo get_phrase('daily_atendance'); ?></span>
                        </a>
                    </li>

            </ul>
            <ul>

                    <li class="<?php if (( $page_name == 'attendance_report' || $page_name == 'attendance_report_view')) echo 'active'; ?>">
                        <a href="<?php echo site_url('admin/attendance_report'); ?>">
                            <span><i class="entypo-dot"></i><?php echo get_phrase('attendance_report'); ?></span>
                        </a>
                    </li>

            </ul>
        </li>


        <!-- EXAMS -->
        <li class="<?php
        if ($page_name == 'exam' ||
                $page_name == 'grade' ||
                $page_name == 'marks_manage' ||
                    $page_name == 'exam_marks_sms' ||
                        $page_name == 'tabulation_sheet' ||
                            $page_name == 'marks_manage_view' || $page_name == 'question_paper')
                                echo 'opened active';
        ?> ">
            <a href="#">
                <i class="flaticon-edit"></i>
                <span><?php echo get_phrase('exam'); ?></span>
            </a>
            <ul>
                <li class="<?php if ($page_name == 'exam') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/exam'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('exam_list'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'grade') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/grade'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('exam_grades'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'marks_manage' || $page_name == 'marks_manage_view') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/marks_manage'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('manage_marks'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'exam_marks_sms') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/exam_marks_sms'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('send_marks_by_sms'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'tabulation_sheet') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/tabulation_sheet'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('tabulation_sheet'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'question_paper') echo 'active'; ?>">
                    <a href="<?php echo site_url('admin/question_paper'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('question_paper'); ?></span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- ONLINE EXAMS -->
        <li class="<?php if ($page_name == 'manage_online_exam' || $page_name == 'add_online_exam' || $page_name == 'edit_online_exam' || $page_name == 'manage_online_exam_question' || $page_name == 'update_online_exam_question' || $page_name == 'view_online_exam_results') echo 'opened active'; ?> ">
            <a href="#">
                <i class="flaticon-placeholder-2"></i>
                <span><?php echo get_phrase('online_exam'); ?></span>
            </a>
            <ul>
            <li class="<?php if ($page_name == 'add_online_exam') echo 'active'; ?> ">
              <a href="<?php echo site_url($account_type.'/create_online_exam'); ?>">
                  <span><i class="entypo-dot"></i> <?php echo get_phrase('create_online_exam'); ?></span>
              </a>
            </li>

            <li class="<?php if ($page_name == 'manage_online_exam' || $page_name == 'edit_online_exam' || $page_name == 'manage_online_exam_question' || $page_name == 'view_online_exam_results') echo 'active'; ?> ">
                <a href="<?php echo site_url($account_type.'/manage_online_exam'); ?>">
                    <span><i class="entypo-dot"></i> <?php echo get_phrase('manage_online_exam'); ?></span>
                </a>
            </li>
            </ul>
        </li>

        

        <!-- PAYMENT -->
        <!-- <li class="<?php //if ($page_name == 'invoice') echo 'active'; ?> ">
            <a href="<?php //echo base_url(); ?>index.php?admin/invoice">
                <i class="entypo-credit-card"></i>
                <span><?php //echo get_phrase('payment'); ?></span>
            </a>
        </li> -->

        <!-- ACCOUNTING -->
        <li class="<?php
        if ($page_name == 'income' ||
                $page_name == 'expense' ||
                    $page_name == 'expense_category' ||
                        $page_name == 'student_payment')
                            echo 'opened active';
        ?> ">
            <a href="#">
                <i class="flaticon-statistics"></i>
                <span><?php echo get_phrase('accounting'); ?></span>
            </a>
            <ul>
                <li class="<?php if ($page_name == 'student_payment') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/student_payment'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('create_student_payment'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'income') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/income'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('student_payments'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'expense') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/expense'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('expense'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'expense_category') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/expense_category'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('expense_category'); ?></span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Manage back office -->
        <li class="<?php if ( $page_name == 'book' ||
                              $page_name == 'transport' ||
                              $page_name == 'dormitory' ||
                              $page_name == 'noticeboard' ||
                              $page_name == 'noticeboard_edit'||
                              $page_name == 'message'||
                              $page_name == 'group_message')
                                echo 'opened active has-sub';?>">
            <a href="#">
                <i class="flaticon-imac"></i>
                <span><?php echo get_phrase('manage_office'); ?></span>
            </a>
            <ul>
                <!-- LIBRARY -->
                <li class="<?php if ($page_name == 'book') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/book'); ?>">
                        <i class="entypo-dot"></i>
                        <span><?php echo get_phrase('library'); ?></span>
                    </a>
                </li>

                <!-- TRANSPORT -->
                <li class="<?php if ($page_name == 'transport') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/transport'); ?>">
                        <i class="entypo-dot"></i>
                        <span><?php echo get_phrase('transport'); ?></span>
                    </a>
                </li>

                <!-- DORMITORY -->
                <li class="<?php if ($page_name == 'dormitory') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/dormitory'); ?>">
                        <i class="entypo-dot"></i>
                        <span><?php echo get_phrase('dormitory'); ?></span>
                    </a>
                </li>

                <!-- NOTICEBOARD -->
                <li class="<?php if ($page_name == 'noticeboard' || $page_name == 'noticeboard_edit') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/noticeboard'); ?>">
                        <i class="entypo-dot"></i>
                        <span><?php echo get_phrase('noticeboard'); ?></span>
                    </a>
                </li>

                <!-- MESSAGE -->
                <li class="<?php if ($page_name == 'message' || $page_name == 'group_message') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/message'); ?>">
                        <i class="entypo-dot"></i>
                        <span><?php echo get_phrase('message'); ?></span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- SETTINGS -->
        <li class="<?php
        if ($page_name == 'system_settings' ||
            $page_name == 'frontend_pages' ||
            $page_name == 'manage_language' ||
            $page_name == 'sms_settings'||
            $page_name == 'payment_settings' ||
            $page_name == 'smtp_settings')
              echo 'opened active';
        ?> ">
            <a href="#">
                <i class="flaticon-interface-7"></i>
                <span><?php echo get_phrase('settings'); ?></span>
            </a>
            <ul>
                <li class="<?php if ($page_name == 'system_settings') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/system_settings'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('general_settings'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'frontend_pages') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/frontend_pages'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('school_website'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'sms_settings') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/sms_settings'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('sms_settings'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'manage_language') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/manage_language'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('language_settings'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'payment_settings') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/payment_settings'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo get_phrase('payment_settings'); ?></span>
                    </a>
                </li>
                <li class="<?php if ($page_name == 'smtp_settings') echo 'active'; ?> ">
                    <a href="<?php echo site_url('admin/smtp_settings'); ?>">
                        <span><i class="entypo-dot"></i> <?php echo 'SMTP'.' '.get_phrase('settings'); ?></span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Session selector -->
        <li class="root-level" style="border-top: 1px solid #262a44; padding: 10px 0px;text-align: -webkit-center;">
            <?php echo form_open(site_url('admin/change_session') , array('id' => 'session_change'));?>
            <li>
                
                <div class="form-group">
                    <select name="running_year" class="form-control" onchange="submit()" 
                         style="width: 60%;background-color: #232640;border-color: #242742;color: #a1a2b6;">
                        <?php $running_year = $this->db->get_where('settings' , array('type'=>'running_year'))->row()->description;?>
                        <?php for($i = 0; $i < 10; $i++):?>
                            <option value="<?php echo (2016+$i);?>-<?php echo (2016+$i+1);?>"
                            <?php if($running_year == (2016+$i).'-'.(2016+$i+1)) echo 'selected';?>>
                                Session : <?php echo (2016+$i);?>-<?php echo (2016+$i+1);?>
                            </option>
                      <?php endfor;?>
                    </select>
                </div>
            </li>
            <?php echo form_close();?>
        </li>

    </ul>

</div>
