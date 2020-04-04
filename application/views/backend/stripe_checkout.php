<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Stripe | <?php echo $this->crud_model->get_settings('system_name');?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo base_url('assets/payment/css/stripe.css');?>"
            rel="stylesheet">
    </head>
    <body>
<!--required for getting the stripe token-->
        <?php
            $stripe_keys = $this->crud_model->get_settings('stripe_keys');
            $values = json_decode($stripe_keys);
            if ($values[0]->testmode == 'on') {
                $public_key = $values[0]->public_key;
                $private_key = $values[0]->secret_key;
            } else {
                $public_key = $values[0]->public_live_key;
                $private_key = $values[0]->secret_live_key;
            }
        ?>

        <script>
            var stripe_key = '<?php echo $public_key;?>';
        </script>

<!--required for getting the stripe token-->

        <img src="<?php echo base_url('uploads/logo.png');?>" width="25%;"
            style="opacity: 0.05;">
            <form method="post"
              action="<?php echo site_url('parents/pay/stripe/' . $invoice_details->invoice_id);?>">
              <input type="hidden" name="student_id" value="<?php echo $student_details->student_id; ?>">
              <label>
                  <div id="card-element" class="field is-empty"></div>
                  <span><span><?php echo get_phrase('credit_/_debit_card');?></span></span>
              </label>
              <button type="submit">
                  <?php echo get_phrase('pay');?> $<?php echo $invoice_details->due;?>
              </button>
              <div class="outcome">
                  <div class="error" role="alert"></div>
                  <div class="success">
                  Success! Your Stripe token is <span class="token"></span>
                  </div>
              </div>
              <div class="package-details">
                  <strong><?php echo get_phrase('student_name');?> | <?php echo $student_details->name;?></strong> <br>
                  <?php echo get_phrase('package');?> | <?php echo $invoice_details->title;?> <br>
              </div>
              <input type="hidden" name="stripeToken" value="">
          </form>
        <img src="https://stripe.com/img/about/logos/logos/blue.png" width="25%;"
            style="opacity: 0.05;">
        <script src="https://js.stripe.com/v3/"></script>
        <script src="<?php echo base_url('assets/payment/js/stripe.js');?>"></script>

    </body>
</html>
