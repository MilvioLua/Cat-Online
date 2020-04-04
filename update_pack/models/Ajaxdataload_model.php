<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajaxdataload_model extends CI_Model
{
	function __construct() {
        parent::__construct(); 
    }

	/*----------------------------- BOOKS -------------------------------*/

	function all_books_count()
	{
		$query = $this->db->get('book');
		return $query->num_rows();
	}

	function all_books($limit, $start, $col, $dir)
    {   
       $query = $this
                ->db
                ->limit($limit,$start)
                ->order_by($col,$dir)
                ->get('book');
        
        if($query->num_rows() > 0)
            return $query->result();
        else
            return null;
        
    }

    function book_search($limit, $start, $search, $col, $dir)
    {
        $query = $this
                ->db
                ->like('name', $search)
                ->or_like('author', $search)
                ->or_like('book_id', $search)
                ->or_like('price', $search)
                ->limit($limit, $start)
                ->order_by($col, $dir)
                ->get('book');
        
        if($query->num_rows() > 0)
            return $query->result();
        else
            return null;
    }

    function book_search_count($search)
    {
        $query = $this
                ->db
                ->like('name', $search)
                ->or_like('book_id', $search)
                ->or_like('author', $search)
                ->or_like('price', $search)
                ->get('book');
    
        return $query->num_rows();
    }

	/*----------------------------- BOOKS -------------------------------*/


    /*----------------------------- TEACHERS -------------------------------*/

    function all_teachers_count()
    {
        $query = $this->db->get('teacher');
        return $query->num_rows();
    }

    function all_teachers($limit, $start, $col, $dir)
    {   
       $query = $this
                ->db
                ->limit($limit,$start)
                ->order_by($col,$dir)
                ->get('teacher');
        
        if($query->num_rows() > 0)
            return $query->result();
        else
            return null;
        
    }

    function teacher_search($limit, $start, $search, $col, $dir)
    {
        $query = $this
                ->db
                ->like('teacher_id', $search)
                ->or_like('name', $search)
                ->or_like('email', $search)
                ->or_like('phone', $search)
                ->limit($limit, $start)
                ->order_by($col, $dir)
                ->get('teacher');
        
        if($query->num_rows() > 0)
            return $query->result();
        else
            return null;
    }

    function teacher_search_count($search)
    {
        $query = $this
                ->db
                ->like('teacher_id', $search)
                ->or_like('name', $search)
                ->or_like('email', $search)
                ->or_like('phone', $search)
                ->get('teacher');
    
        return $query->num_rows();
    }

    /*----------------------------- TEACHERS -------------------------------*/


    /*----------------------------- PARENTS -------------------------------*/

    function all_parents_count()
    {
        $query = $this->db->get('parent');
        return $query->num_rows();
    }

    function all_parents($limit, $start, $col, $dir)
    {   
       $query = $this
                ->db
                ->limit($limit,$start)
                ->order_by($col,$dir)
                ->get('parent');
        
        if($query->num_rows() > 0)
            return $query->result();
        else
            return null;
        
    }

    function parent_search($limit, $start, $search, $col, $dir)
    {
        $query = $this
                ->db
                ->like('parent_id', $search)
                ->or_like('name', $search)
                ->or_like('email', $search)
                ->or_like('phone', $search)
                ->or_like('profession', $search)
                ->limit($limit, $start)
                ->order_by($col, $dir)
                ->get('parent');
        
        if($query->num_rows() > 0)
            return $query->result();
        else
            return null;
    }

    function parent_search_count($search)
    {
        $query = $this
                ->db
                ->like('parent_id', $search)
                ->or_like('name', $search)
                ->or_like('email', $search)
                ->or_like('phone', $search)
                ->or_like('profession', $search)
                ->get('parent');
    
        return $query->num_rows();
    }

    /*----------------------------- PARENTS -------------------------------*/

    /*----------------------------- EXPENSES -------------------------------*/

    function all_expenses_count()
    {
        $array = array('payment_type' => 'expense', 'year' => get_settings('running_year'));
        $query = $this
                ->db
                ->where($array)
                ->get('payment');
        return $query->num_rows();
    }

    function all_expenses($limit, $start, $col, $dir)
    {
        $array = array('payment_type' => 'expense', 'year' => get_settings('running_year'));
        $query = $this
                ->db
                ->where($array)
                ->limit($limit,$start)
                ->order_by($col,$dir)
                ->get('payment');
        
        if($query->num_rows() > 0)
            return $query->result();
        else
            return null;
        
    }

    function expense_search($limit, $start, $search, $col, $dir)
    {
        $query = $this
                ->db
                ->like('payment_id', $search)
                ->or_like('title', $search)
                ->or_like('amount', $search)
                ->limit($limit, $start)
                ->order_by($col, $dir)
                ->get('payment');
        
        if($query->num_rows() > 0)
            return $query->result();
        else
            return null;
    }

    function expense_search_count($search)
    {
        $query = $this
                ->db
                ->like('payment_id', $search)
                ->or_like('title', $search)
                ->or_like('amount', $search)
                ->get('payment');
    
        return $query->num_rows();
    }

    /*----------------------------- EXPENSES -------------------------------*/


    /*----------------------------- INVOICES -------------------------------*/

    function all_invoices_count()
    {
        $array = array('year' => get_settings('running_year'));
        $query = $this
                ->db
                ->where($array)
                ->get('invoice');
        return $query->num_rows();
    }

    function all_invoices($limit, $start, $col, $dir)
    {
        $array = array('year' => get_settings('running_year'));
        $query = $this
                ->db
                ->where($array)
                ->limit($limit,$start)
                ->order_by($col,$dir)
                ->get('invoice');
        
        if($query->num_rows() > 0)
            return $query->result();
        else
            return null;
        
    }

    function invoice_search($limit, $start, $search, $col, $dir)
    {
        $query = $this
                ->db
                ->like('invoice_id', $search)
                ->or_like('title', $search)
                ->or_like('amount', $search)
                ->or_like('status', $search)
                ->limit($limit, $start)
                ->order_by($col, $dir)
                ->get('invoice');
        
        if($query->num_rows() > 0)
            return $query->result();
        else
            return null;
    }

    function invoice_search_count($search)
    {
        $query = $this
                ->db
                ->like('invoice_id', $search)
                ->or_like('title', $search)
                ->or_like('amount', $search)
                ->or_like('status', $search)
                ->get('invoice');
    
        return $query->num_rows();
    }

    /*----------------------------- INVOICES -------------------------------*/


    /*----------------------------- PAYMENTS -------------------------------*/

    function all_payments_count()
    {
        $array = array('payment_type' => 'income', 'year' => get_settings('running_year'));
        $query = $this
                ->db
                ->where($array)
                ->get('payment');
        return $query->num_rows();
    }

    function all_payments($limit, $start, $col, $dir)
    {
        $array = array('payment_type' => 'income', 'year' => get_settings('running_year'));
        $query = $this
                ->db
                ->where($array)
                ->limit($limit,$start)
                ->order_by($col,$dir)
                ->get('payment');
        
        if($query->num_rows() > 0)
            return $query->result();
        else
            return null;
        
    }

    function payment_search($limit, $start, $search, $col, $dir)
    {
        $query = $this
                ->db
                ->like('payment_id', $search)
                ->or_like('title', $search)
                ->or_like('amount', $search)
                ->limit($limit, $start)
                ->order_by($col, $dir)
                ->get('payment');
        
        if($query->num_rows() > 0)
            return $query->result();
        else
            return null;
    }

    function payment_search_count($search)
    {
        $query = $this
                ->db
                ->like('payment_id', $search)
                ->or_like('title', $search)
                ->or_like('amount', $search)
                ->get('payment');
    
        return $query->num_rows();
    }

    /*----------------------------- PAYMENTS -------------------------------*/
}