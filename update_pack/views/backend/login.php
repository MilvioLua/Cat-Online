<?php
$system_name  = $this->db->get_where('settings', array('type' => 'system_name'))->row()->description;
$header_logo  = $this->frontend_model->get_frontend_general_settings('header_logo');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="Creativeitem" />
    <meta name="description" content="<?php echo get_settings('system_name'); ?>" />
    <meta name="author" content="Creativeitem" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link name="favicon" type="image/x-icon" href="<?php echo base_url();?>uploads/frontend/<?php echo $header_logo;?>" rel="shortcut icon" />
    <title>Ekattor login | <?php echo get_settings('system_name'); ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo base_url();?>uploads/frontend/<?php echo $header_logo;?>" />

    <!-- font -->
    <link  rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,500,500i,600,700,800,900|Poppins:200,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900">


    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/login/plugins-css.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/login/typography.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/login/style.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/login/responsive.css'); ?>" />


    <link rel="stylesheet" href="<?php echo base_url('assets/css/neon-forms.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/font-icons/entypo/css/entypo.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/custom.css'); ?>">

</head>

<body>

    <div class="wrapper">

        <section class="gray-bg height-100vh d-flex align-items-center page-section-ptb ">
            <div class="container">
                <div class="row no-gutters justify-content-center">
                    <div class="col-lg-4 col-md-6 login-fancy-bg bg-overlay-black-10" style="background: url(<?php echo base_url();?>assets/login/login_bg.jpg);">
                        <div class="login-fancy pos-r">
                            <div class="text-center">
                                <div style="padding: 55px;" class="d-none d-md-block"></div>
                                <img src="<?php echo base_url().'uploads/logo.png'; ?>" height="25" />
                                <h2 class="text-white mb-20"></h2>
                                <h4 class="text-white mb-20"><?php echo get_settings('system_name');?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 white-bg">
                        <div class="login-fancy pb-40 clearfix" id = "login_area">
                            <h3 class="mb-30"><?php echo get_phrase('login'); ?></h3>
                            <form action="<?php echo site_url('login/validate_login'); ?>" method="post">
                                <div class="section-field mb-20">
                                    <label class="mb-10" for="name"><?php echo get_phrase('email'); ?>* </label>
                                    <input id="email" class="web form-control" type="email" placeholder="<?php echo get_phrase('email'); ?>" name="email" required>
                                </div>
                                <div class="section-field mb-20">
                                    <label class="mb-10" for="Password"><?php echo get_phrase('password'); ?>* </label>
                                    <input id="Password" class="Password form-control" type="password" placeholder="<?php echo get_phrase('password'); ?>" name="password" required>
                                </div>
                                <button type="submit" class="btn btn-primary"><?php echo get_phrase('login'); ?></button>
                            </form>

                            <div class="section-field">
                                <div class="remember-checkbox mb-30">
                                    <a href="#" class="float-right" id = "forgot_password_button" onclick="toggleView(this)"><?php echo get_phrase('forgot_password'); ?>?</a>
                                
                                    <a href="<?php echo base_url();?>" class="float-left">
                                        <i class="entypo-left-open"></i><?php echo get_phrase('back_to_website'); ?></a>
                                </div>
                            </div>
                        </div>

                        <div class="login-fancy pb-40 clearfix" id = "forgot_password_area" style="display: none;">
                            <h3 class="mb-30"><?php echo get_phrase('forgot_password'); ?></h3>
                            <form class="" action="<?php echo site_url('login/reset_password');?>" method="post">
                                <div class="section-field mb-20">
                                    <label class="mb-10" for="name"><?php echo get_phrase('email'); ?>* </label>
                                    <input id="forgot_password_email" class="web form-control" type="email" placeholder="<?php echo get_phrase('email'); ?>" name="email" required>
                                </div>
                                <button type="submit" class="btn btn-primary"><?php echo get_phrase('send_mail'); ?></button>
                            </form>

                            <div class="section-field">
                                <div class="remember-checkbox mb-30">
                                    <a href="#" class="float-right" id = "login_button" onclick="toggleView(this)"><?php echo get_phrase('back_to_login'); ?>?</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <!-- jquery -->
    <script src="<?php echo base_url('assets/login/jquery-3.3.1.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/toastr.js'); ?>"></script>
    <script type="text/javascript">
        function toggleView(elem) {
            if (elem.id === 'forgot_password_button') {
                $('#login_area').hide();
                $('#forgot_password_area').show();
            }else if (elem.id === 'login_button') {
                $('#login_area').show();
                $('#forgot_password_area').hide();
            }
        }
    </script>
    <!-- SHOW TOASTR NOTIFIVATION -->
    <?php if ($this->session->flashdata('flash_message') != ""):?>

    <script type="text/javascript">
    	toastr.success('<?php echo $this->session->flashdata("flash_message");?>');
    </script>

    <?php endif;?>

    <?php if ($this->session->flashdata('error_message') != ""):?>

    <script type="text/javascript">
    	toastr.error('<?php echo $this->session->flashdata("error_message");?>');
    </script>

    <?php endif;?>
</body>
</html>
