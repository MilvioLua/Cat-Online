<!DOCTYPE html>
<html lang="en">
<head>
    <title>Paypal | <?php echo $this->crud_model->get_settings('system_name');?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo base_url('assets/payment/css/stripe.css');?>"
          rel="stylesheet">
</head>
<body>
<?php
    $user_type = $this->session->userdata('login_type');
    if ($user_type == 'parent') {
      $user_type = 'parents';
    }
    $paypal_keys = $this->crud_model->get_settings('paypal');
    $paypal = json_decode($paypal_keys);
?>
<!--required for getting the stripe token-->

<img src="<?php echo base_url('uploads/logo.png');?>" width="25%;"
     style="opacity: 0.05;">

<div class="package-details">
    <strong><?php echo get_phrase('student_name');?> | <?php echo $student_details->name;?></strong> <br>
    <strong><?php echo get_phrase('due');?> | <?php echo $invoice_details->due;?></strong> <br>
    <?php echo get_phrase('title');?> | <?php echo $invoice_details->title;?> <br>
    <div id="paypal-button" style="margin-top: 20px;"></div><br>
</div>

<img src="https://www.paypalobjects.com/webstatic/i/logo/rebrand/ppcom-white.svg" width="25%;"
     style="opacity: 0.05;">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://www.paypalobjects.com/api/checkout.js"></script>

<script>
    paypal.Button.render({
        env: '<?php echo $paypal[0]->mode;?>', // 'sandbox' or 'production'
        style: {
            label: 'paypal',
            size:  'medium',    // small | medium | large | responsive
            shape: 'rect',     // pill | rect
            color: 'blue',     // gold | blue | silver | black
            tagline: false
        },
        client: {
            sandbox:    '<?php echo $paypal[0]->sandbox_client_id;?>',
            production: '<?php echo $paypal[0]->production_client_id;?>'
        },

        commit: true, // Show a 'Pay Now' button

        payment: function(data, actions) {
            return actions.payment.create({
                payment: {
                    transactions: [
                        {
                            amount: { total: '<?php echo $invoice_details->due;?>', currency: 'USD' }
                        }
                    ]
                }
            });
        },

        onAuthorize: function(data, actions) {
            // executes the payment
            return actions.payment.execute().then(function() {
                // make an ajax call for saving the payment info
                $.ajax({
                   url: '<?php echo site_url($user_type . '/pay/paypal/' . $invoice_details->invoice_id);?>'
                }).done(function () {
                    window.location = '<?php echo site_url($user_type . '/invoice/' . $student_details->student_id);?>';
                });
            });
        }

    }, '#paypal-button');
</script>

</body>
</html>
