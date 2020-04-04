
<!-- ========== MAIN ========== -->
<main id="content" role="main">
  <!-- Hero Section -->
  <div class="gradient-half-primary-v1">
    <div class="container text-center space-top-4 space-top-md-4 space-top-lg-3 space-bottom-1">
      <!-- Title -->
      <div class="w-md-80 w-lg-50 mx-auto mb-5">
        <h1 class="h1 text-white">
          <span class="font-weight-semi-bold">Contact Us</span>
        </h1>
      </div>
      <!-- End Title -->
    </div>
  </div>
  <!-- End Hero Section -->

  <!-- Contacts Info Section -->
  <div class="clearfix space-1">

    <!-- Title -->
    <div class="w-md-80 w-lg-50 text-center mx-md-auto mb-5">
      <span class="btn btn-xs btn-soft-success btn-pill mb-2">Contact info</span>
    </div>
    <!-- End Title -->
    <div class="row no-gutters">

      <div class="col-sm-6 col-lg-3 u-ver-divider u-ver-divider--none-lg">
        <!-- Contacts Info -->
        <div class="text-center py-5">
          <figure id="icon8" class="svg-preloader ie-height-56 max-width-8 mx-auto mb-3">
            <img class="js-svg-injector" src="<?php echo base_url();?>assets/frontend/ultimate/svg/icons/icon-8.svg" alt="SVG"
                 data-parent="#icon8">
          </figure>
          <h2 class="h6 mb-0"><?php echo get_phrase('address'); ?></h2>
          <p class="mb-0">
            <?php echo $this->frontend_model->get_frontend_general_settings('address'); ?>
          </p>
        </div>
        <!-- End Contacts Info -->
      </div>

      <div class="col-sm-6 col-lg-3 u-ver-divider u-ver-divider--none-lg">
        <!-- Contacts Info -->
        <div class="text-center py-5">
          <figure id="icon15" class="svg-preloader ie-height-56 max-width-8 mx-auto mb-3">
            <img class="js-svg-injector" src="<?php echo base_url();?>assets/frontend/ultimate/svg/icons/icon-15.svg" alt="SVG"
                 data-parent="#icon15">
          </figure>
          <h3 class="h6 mb-0"><?php echo get_phrase('email'); ?></h3>
          <p class="mb-0">
            <?php echo $this->frontend_model->get_frontend_general_settings('email'); ?>
          </p>
        </div>
        <!-- End Contacts Info -->
      </div>

      <div class="col-sm-6 col-lg-3 u-ver-divider u-ver-divider--none-lg">
        <!-- Contacts Info -->
        <div class="text-center py-5">
          <figure id="icon16" class="svg-preloader ie-height-56 max-width-8 mx-auto mb-3">
            <img class="js-svg-injector" src="<?php echo base_url();?>assets/frontend/ultimate/svg/icons/icon-16.svg" alt="SVG"
                 data-parent="#icon16">
          </figure>
          <h3 class="h6 mb-0"><?php echo get_phrase('phone'); ?></h3>
          <p class="mb-0">
            <?php echo $this->frontend_model->get_frontend_general_settings('phone'); ?>
          </p>
        </div>
        <!-- End Contacts Info -->
      </div>

      <div class="col-sm-6 col-lg-3">
        <!-- Contacts Info -->
        <div class="text-center py-5">
          <figure id="icon17" class="svg-preloader ie-height-56 max-width-8 mx-auto mb-3">
            <img class="js-svg-injector" src="<?php echo base_url();?>assets/frontend/ultimate/svg/icons/icon-17.svg" alt="SVG"
                 data-parent="#icon17">
          </figure>
          <h3 class="h6 mb-0"><?php echo get_phrase('fax'); ?></h3>
          <p class="mb-0">
            <?php echo $this->frontend_model->get_frontend_general_settings('fax'); ?>
          </p>
        </div>
        <!-- End Contacts Info -->
      </div>
    </div>
  </div>
  <!-- End Contacts Info Section -->

  <hr class="my-0">

  <!-- Contact Form Section -->
  <div class="container space-2 space-md-3">
    <!-- Title -->
    <div class="w-md-80 w-lg-50 text-center mx-md-auto mb-9">
      <span class="btn btn-xs btn-soft-success btn-pill mb-2">Contact form</span>
      <h2 class="text-primary font-weight-normal">
        Send us a message
      </h2>
    </div>
    <!-- End Title -->

    <div class="w-lg-80 mx-auto">
      <!-- Contacts Form -->
      <form action="<?php echo site_url('home/contact/send');?>" method="post" class="js-validate">
        <div class="row">
          <!-- Input -->
          <div class="col-sm-6 mb-6">
            <div class="js-form-message">
              <label class="form-label">
                First name
                <span class="text-danger">*</span>
              </label>

              <input type="text" class="form-control" name="first_name"  required
                     data-msg="Please enter your first name."
                     data-error-class="u-has-error"
                     data-success-class="u-has-success">
            </div>
          </div>
          <!-- End Input -->

          <!-- Input -->
          <div class="col-sm-6 mb-6">
            <div class="js-form-message">
              <label class="form-label">
                Last name
                <span class="text-danger">*</span>
              </label>

              <input type="text" class="form-control" name="last_name"  required
                     data-msg="Please enter your last name."
                     data-error-class="u-has-error"
                     data-success-class="u-has-success">
            </div>
          </div>
          <!-- End Input -->

          <!-- Input -->
          <div class="col-sm-6 mb-6">
            <div class="js-form-message">
              <label class="form-label">
                Your email address
                <span class="text-danger">*</span>
              </label>

              <input type="email" class="form-control" name="email" required
                     data-msg="Please enter a valid email address."
                     data-error-class="u-has-error"
                     data-success-class="u-has-success">
            </div>
          </div>
          <!-- End Input -->

          <!-- Input -->
          <div class="col-sm-6 mb-6">
            <div class="js-form-message">
              <label class="form-label">
                Your Phone Number
                <span class="text-danger">*</span>
              </label>

              <input type="number" class="form-control" name="phone" required
                     data-msg="Please enter a valid phone number."
                     data-error-class="u-has-error"
                     data-success-class="u-has-success">
            </div>
          </div>
          <!-- End Input -->

          <div class="w-100"></div>

          <!-- Input -->
          <div class="col-sm-12 mb-6">
            <div class="js-form-message">
              <label class="form-label">
                Location
                <span class="text-danger">*</span>
              </label>

              <input type="text" class="form-control" name="address" required
                     data-msg="Please enter your location."
                     data-error-class="u-has-error"
                     data-success-class="u-has-success">
            </div>
          </div>
          <!-- End Input -->
        </div>

        <!-- Input -->
        <div class="js-form-message mb-6">
          <label class="form-label">
            Comments / Questions
            <span class="text-danger">*</span>
          </label>

          <div class="input-group">
            <textarea class="form-control" rows="4" name="comment" required
                      data-msg="Please enter your message."
                      data-error-class="u-has-error"
                      data-success-class="u-has-success"></textarea>
          </div>
        </div>
        <!-- End Input -->

        <div class="text-center">
          <button type="submit" class="btn btn-primary btn-wide transition-3d-hover mb-4">Submit</button>
        </div>

      </form>
      <!-- End Contacts Form -->
    </div>
  </div>
  <!-- End Contact Form Section -->

  <!-- Open street maps -->
  <?php 
  $long_lat = $this->frontend_model->get_frontend_general_settings('school_location');
  $str_arr = explode (",", $long_lat); 
  $longitude  = $str_arr[0];
  $latitude   = $str_arr[1]; 
  

  ?>
  <!-- <div id="mapdiv" style="height: 300px; width: 100%;"></div> -->
  <!-- <script src="http://www.openlayers.org/api/OpenLayers.js"></script> -->
  <script>
    // map = new OpenLayers.Map("mapdiv");
    // map.addLayer(new OpenLayers.Layer.OSM());

    // var lonLat = new OpenLayers.LonLat( <?php echo $longitude;?> , <?php echo $longitude;?> )
    //       .transform(
    //         new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
    //         map.getProjectionObject() // to Spherical Mercator Projection
    //       );
          
    // var zoom=4;

    // var markers = new OpenLayers.Layer.Markers( "Markers" );
    // map.addLayer(markers);
    
    // markers.addMarker(new OpenLayers.Marker(lonLat));
    
    // map.setCenter (lonLat, zoom);
  </script>