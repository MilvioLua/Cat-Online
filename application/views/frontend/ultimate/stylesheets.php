<?php
  $header_logo  = $this->frontend_model->get_frontend_general_settings('header_logo');
?>
<!-- Favicon -->
  <link rel="shortcut icon" href="<?php echo base_url();?>uploads/frontend/<?php echo $header_logo;?>">

  <!-- Google Fonts -->
  <link href="//fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

  <!-- CSS Implementing Plugins -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/frontend/<?php echo $theme;?>/vendor/font-awesome/css/fontawesome-all.min.css">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/frontend/<?php echo $theme;?>/vendor/animate.css/animate.min.css">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/frontend/<?php echo $theme;?>/vendor/hs-megamenu/src/hs.megamenu.css">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/frontend/<?php echo $theme;?>/vendor/fancybox/jquery.fancybox.css">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/frontend/<?php echo $theme;?>/vendor/slick-carousel/slick/slick.css">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/frontend/<?php echo $theme;?>/vendor/cubeportfolio/css/cubeportfolio.min.css">

  <!-- CSS Front Template -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/frontend/<?php echo $theme;?>/css/theme.css">