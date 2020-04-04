<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Paypal_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function pay($invoice_id) {

      $checker = array(
        'invoice_id' => $invoice_id
      );

      $updater = array(
        'due' => 0,
        'amount_paid' => $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row()->amount_paid + $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row()->due,
        'payment_timestamp' => strtotime(date("m/d/Y")),
        'payment_method' => 'Paypal',
        'status' => 'paid'
      );
      $this->db->where($checker);
      $this->db->update('invoice', $updater);
    }

}
