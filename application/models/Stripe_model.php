<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Stripe_model extends CI_Model {

    function __construct() {
        parent::__construct();

        $this->load->library('stripegateway');
    }

    public function pay($invoice_id = '') {

        if (isset($_POST['stripeToken'])) {

            $invoice_details = $this->db->get_where('invoice', array(
                'invoice_id' => $invoice_id
            ))->row();

            $data['stripe_token']    = html_escape($this->input->post('stripeToken'));
            $data['parent_id']        = $this->session->userdata('login_user_id');
            $data['invoice_title']   = $invoice_details->title;
            $data['amount']          = $invoice_details->due;
            $data['timestamp']       = time();

            $this->stripegateway->checkout($data);

            // updating invoices
            $checker = array(
              'invoice_id' => $invoice_id
            );

            $updater = array(
              'due' => 0,
              'amount_paid' => $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row()->amount_paid + $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row()->due,
              'payment_timestamp' => strtotime(date("m/d/Y")),
              'payment_method' => 'Stripe',
              'status' => 'paid'
            );
            $this->db->where($checker);
            $this->db->update('invoice', $updater);

            return true;
        } else {
            return false;
        }
    }

}
