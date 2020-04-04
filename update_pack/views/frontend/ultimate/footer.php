<?php
$footer_logo = $this->frontend_model->get_frontend_general_settings('footer_logo');
$social = $this->frontend_model->get_frontend_general_settings('social_links');
$links = json_decode($social);
?>
<!-- ========== FOOTER ========== -->
<footer class="border-top">
  <div class="container space-2">
    <div class="row">
      <div class="col-sm-3 col-lg-2 order-sm-2 mb-4 mb-sm-0 ml-lg-auto">
        <h4 class="h6 font-weight-semi-bold">Contact</h4>

        <!-- Address -->
          <address>
            <ul class="list-group list-group-flush list-group-borderless mb-0">
              <li class="list-group-item">
                <?php echo $this->frontend_model->get_frontend_general_settings('phone'); ?>
              </li>
              <li class="list-group-item">
                <a href="mailto:<?php echo $this->frontend_model->get_frontend_general_settings('email'); ?>">
                  <?php echo $this->frontend_model->get_frontend_general_settings('email'); ?>
                </a>
              </li>
              <li class="list-group-item">
                <?php echo $this->frontend_model->get_frontend_general_settings('address'); ?>
              </li>
            </ul>
          </address>
          <!-- End Address -->
      </div>
      <div class="col-sm-3 col-lg-2 order-sm-2 mb-4 mb-sm-0 ml-lg-auto">
        <h4 class="h6 font-weight-semi-bold">About</h4>

        <!-- List Group -->
        <ul class="list-group list-group-flush list-group-borderless mb-0">
          <li><a class="list-group-item list-group-item-action" 
            href="<?php echo site_url('home/about');?>">About</a></li>
          <li><a class="list-group-item list-group-item-action" 
            href="<?php echo site_url('home/teachers');?>">Teachers </a></li>
          <li><a class="list-group-item list-group-item-action" 
            href="<?php echo site_url('home/gallery');?>">Gallery </a></li>
        </ul>
        <!-- End List Group -->
      </div>

      <div class="col-sm-3 col-lg-2 order-sm-3 mb-4 mb-sm-0">
        <h4 class="h6 font-weight-semi-bold">Resources</h4>

        <!-- List Group -->
        <ul class="list-group list-group-flush list-group-borderless mb-0">
          <li><a class="list-group-item list-group-item-action" 
            href="<?php echo site_url('home/terms_conditions');?>">Terms & Conditions</a></li>
          <li><a class="list-group-item list-group-item-action" 
            href="<?php echo site_url('home/privacy_policy');?>">Privacy Policy</a></li>
          <li><a class="list-group-item list-group-item-action" target="_blank"
            href="<?php echo site_url('login');?>">Login</a></li>
        </ul>
        <!-- End List Group -->
      </div>

      <div class="col-sm-6 col-lg-4 order-sm-1">
        <!-- Logo -->
        <a class="d-inline-flex align-items-center mb-2" href="<?php echo base_url();?>">
          <img src="<?php echo base_url();?>uploads/frontend/<?php echo $header_logo;?>" style="height:45px; width:45px;" />
          <span class="brand brand-primary">Ekattor</span>
        </a>
        <!-- End Logo -->

        <div class="mb-4">
          <p class="small text-muted">© 2019, Creativeitem. All rights reserved.</p>
        </div>

        <!-- Social Networks -->
        <ul class="list-inline mb-0">
          <li class="list-inline-item mx-0">
            <a class="btn btn-sm btn-icon btn-soft-secondary rounded-circle" 
              href="<?php echo $links[0]->facebook;?>" target="_blank">
              <span class="fab fa-facebook-f btn-icon__inner"></span>
            </a>
          </li>
          <li class="list-inline-item mx-0">
            <a class="btn btn-sm btn-icon btn-soft-secondary rounded-circle" 
              href="<?php echo $links[0]->instagram;?>" target="_blank">
              <span class="fab fa-instagram btn-icon__inner"></span>
            </a>
          </li>
          <li class="list-inline-item mx-0">
            <a class="btn btn-sm btn-icon btn-soft-secondary rounded-circle" 
              href="<?php echo $links[0]->twitter;?>" target="_blank">
              <span class="fab fa-twitter btn-icon__inner"></span>
            </a>
          </li>
          <li class="list-inline-item mx-0">
            <a class="btn btn-sm btn-icon btn-soft-secondary rounded-circle" 
              href="<?php echo $links[0]->google;?>" target="_blank">
              <span class="fab fa-google btn-icon__inner"></span>
            </a>
          </li>
          <li class="list-inline-item mx-0">
            <a class="btn btn-sm btn-icon btn-soft-secondary rounded-circle" 
              href="<?php echo $links[0]->youtube;?>" target="_blank">
              <span class="fab fa-youtube btn-icon__inner"></span>
            </a>
          </li>
          <li class="list-inline-item mx-0">
            <a class="btn btn-sm btn-icon btn-soft-secondary rounded-circle" 
              href="<?php echo $links[0]->linkedin;?>" target="_blank">
              <span class="fab fa-linkedin btn-icon__inner"></span>
            </a>
          </li>
        </ul>
        <!-- End Social Networks -->
      </div>
    </div>
  </div>
</footer>
<!-- ========== END FOOTER ========== -->

  <!-- Go to Top -->
  <a class="js-go-to u-go-to" href="#"
    data-position='{"bottom": 15, "right": 15 }'
    data-type="fixed"
    data-offset-top="400"
    data-compensation="#header"
    data-show-effect="slideInUp"
    data-hide-effect="slideOutDown">
    <span class="fas fa-arrow-up u-go-to__inner"></span>
  </a>
  <!-- End Go to Top -->