<?php if ($student_id != ''): ?>
<h4 class="text-muted" style="margin-bottom: 20px;">
	<?php echo get_phrase('payment_history_for'); ?> <?php echo $this->db->get_where('student', array('student_id' => $student_id))->row()->name; ?>
</h4>
<?php endif; ?>
<table class="table table-bordered" id="student_payments">
	<thead>
        <tr>
            <th width="40"><div><?php echo get_phrase('id');?></div></th>
            <th><div><?php echo get_phrase('title');?></div></th>
            <th><div><?php echo get_phrase('description');?></div></th>
            <th><div><?php echo get_phrase('method');?></div></th>
            <th><div><?php echo get_phrase('amount');?></div></th>
            <th><div><?php echo get_phrase('date');?></div></th>
            <th><div><?php echo get_phrase('options');?></div></th>
        </tr>
    </thead>
    <tbody>
        <?php
    		$this->db->where('student_id', $student_id);
    		$this->db->where('payment_type' , 'income');
    		$this->db->order_by('timestamp' , 'desc');
    		$payments = $this->db->get('payment')->result_array();
    		foreach ($payments as $row): ?>
	        <tr>
	            <td><?php echo $row['payment_id'];?></td>
	            <td><?php echo $row['title'];?></td>
	            <td><?php echo $row['description'];?></td>
	            <td>
	            	<?php
	            		if ($row['method'] == 1)
	            			echo get_phrase('cash');
	            		if ($row['method'] == 2)
	            			echo get_phrase('check');
	            		if ($row['method'] == 3)
	            			echo get_phrase('card');
	                    if ($row['method'] == 'Paypal')
	                    	echo 'Paypal';
	                    if ($row['method'] == 'Stripe')
	                    	echo 'Stripe';
	            	?>
	            </td>
	            <td><?php echo $row['amount'];?></td>
	            <td><?php echo date('d M,Y', $row['timestamp']);?></td>
	            <td align="center">
	            	<a href="#" onclick="showAjaxModal('<?php echo site_url('modal/popup/modal_view_invoice/'.$row['invoice_id']);?>')">
	            			<i class="entypo-credit-card"></i>&nbsp;<?php echo get_phrase('view_invoice');?>
	            	</a>
	            </td>
	        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript">
	$(document).ready(function() {
		//$('#student_payments').dataTable();
	});
</script>