
<div id="main-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo get_phrase('setup_payment_information') ?></h3>
                </div>

                <div class="panel-body">

                    <!-- vertical tabs -->
                    <div class="tabs-vertical-env">

                        <ul class="nav tabs-vertical"><!-- available classes "right-aligned" -->
                            <li class="active"><a href="#tab1" data-toggle="tab">PayPal</a></li>
                            <li class=""><a href="#tab2" data-toggle="tab">Stripe</a></li>
                           <li  class=""><a href="#tab3" data-toggle="tab">PayUMoney</a></li>
<!--                            <li><a href="#tab4" data-toggle="tab">PayTM</a></li>-->
                        </ul>

                        <div class="tab-content">

                            <div class="tab-pane active" id="tab1">
                            <?php
                                $paypal_settings = get_settings('paypal');
                                $paypal = json_decode($paypal_settings);
                            ?>
                                <form action="<?php echo site_url('admin/payment_settings/update_paypal_keys');?>"
                                    class="form-horizontal form-groups" method="post">

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            <?php echo get_phrase('active') ?>
                                        </label>
                                        <div class="col-sm-7">
                                            <select name="paypal_active" class="form-control">
                                                <option value="0"
                                                    <?php if ($paypal[0]->active == 0) echo 'selected';?>>
                                                        <?php echo get_phrase('no');?></option>
                                                <option value="1"
                                                    <?php if ($paypal[0]->active == 1) echo 'selected';?>>
                                                        <?php echo get_phrase('yes');?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            <?php echo get_phrase('mode') ?>
                                        </label>
                                        <div class="col-sm-7">
                                            <select name="paypal_mode" class="form-control">
                                                <option value="sandbox"
                                                    <?php if ($paypal[0]->mode == 'sandbox') echo 'selected';?>>
                                                    <?php echo get_phrase('sandbox');?></option>
                                                <option value="production"
                                                    <?php if ($paypal[0]->mode == 'production') echo 'selected';?>>
                                                    <?php echo get_phrase('production');?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            <?php echo get_phrase('client_id').' ('.get_phrase('sandbox').')'; ?>
                                        </label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control"
                                                   name="sandbox_client_id"
                                                   value="<?php echo $paypal[0]->sandbox_client_id;?>" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            <?php echo get_phrase('client_id').' ('.get_phrase('production').')'; ?>
                                        </label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control"
                                                   name="production_client_id"
                                                   value="<?php echo $paypal[0]->production_client_id;?>" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-5">
                                            <button type="submit" class="btn btn-info">
                                                <?php echo get_phrase('save_changes'); ?>
                                            </button>
                                        </div>
                                    </div>

                                </form>
                            </div>

                            <div class="tab-pane" id="tab2">
                            <?php
                                $stripe_settings = get_settings('stripe_keys');
                                $stripe = json_decode($stripe_settings);
                            ?>
                            <form action="<?php echo site_url('admin/payment_settings/update_stripe_keys');?>"
                                class="form-horizontal form-groups" method="post">

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            <?php echo get_phrase('active') ?>
                                        </label>
                                        <div class="col-sm-7">
                                            <select name="stripe_active" class="form-control">
                                                <option value="0"
                                                    <?php if ($stripe[0]->active == 0) echo 'selected';?>>
                                                        <?php echo get_phrase('no');?></option>
                                                <option value="1"
                                                    <?php if ($stripe[0]->active == 1) echo 'selected';?>>
                                                        <?php echo get_phrase('yes');?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            <?php echo get_phrase('test_mode') ?>
                                        </label>
                                        <div class="col-sm-7">
                                            <select name="testmode" class="form-control">
                                            <option value="on"
                                                <?php if ($stripe[0]->testmode == 'on') echo 'selected';?>>
                                                    <?php echo get_phrase('on');?></option>
                                            <option value="off"
                                                <?php if ($stripe[0]->testmode == 'off') echo 'selected';?>>
                                                    <?php echo get_phrase('off');?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            <?php echo get_phrase('test_secret_key') ?>
                                        </label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control"
                                                name="secret_key"
                                                    value="<?php echo $stripe[0]->secret_key;?>" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            <?php echo get_phrase('test_public_key') ?>
                                        </label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control"
                                                name="public_key"
                                                    value="<?php echo $stripe[0]->public_key;?>" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            <?php echo get_phrase('live_secret_key') ?>
                                        </label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control"
                                                name="secret_live_key"
                                                    value="<?php echo $stripe[0]->secret_live_key;?>" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            <?php echo get_phrase('live_public_key') ?>
                                        </label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control"
                                                name="public_live_key"
                                                    value="<?php echo $stripe[0]->public_live_key;?>" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-5">
                                            <button type="submit" class="btn btn-info">
                                                <?php echo get_phrase('save_changes'); ?>
                                            </button>
                                        </div>
                                    </div>

                                </form>
                            </div>

                           <div class="tab-pane" id="tab3">
                             <form action="<?php echo site_url('admin/payment_settings/update_payumoney_keys');?>"
                                 class="form-horizontal form-groups" method="post">
                               <div class="form-group">
                                   <label  class="col-sm-3 control-label"><?php echo get_phrase('payumoney_merchant_key');?></label>
                                   <div class="col-sm-9">
                                       <input type="text" class="form-control" name="payumoney_merchant_key"
                                           value="<?php echo $this->db->get_where('settings' , array('type' =>'payumoney_merchant_key'))->row()->description;?>">
                                   </div>
                               </div>

                               <div class="form-group">
                                   <label  class="col-sm-3 control-label"><?php echo get_phrase('payumoney_salt_id');?></label>
                                   <div class="col-sm-9">
                                       <input type="text" class="form-control" name="payumoney_salt_id"
                                           value="<?php echo $this->db->get_where('settings' , array('type' =>'payumoney_salt_id'))->row()->description;?>">
                                   </div>
                               </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-5">
                                        <button type="submit" class="btn btn-info">
                                            <?php echo get_phrase('save_changes'); ?>
                                        </button>
                                    </div>
                                </div>
                              </form>
                            </div>
<!---->
<!--                            <div class="tab-pane" id="tab4">-->
<!--                                -->
<!--                            </div>-->
                        </div>

                    </div>
                    <!-- vertical tabs -->

                </div>
            </div>
        </div>
    </div>
</div>
