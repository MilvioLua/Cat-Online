<div class="row">
	<div class="col-md-12">
		<table class="table table-bordered" id="invoices">
			<thead>
        		<tr>
        			<th width="40"><div><?php echo get_phrase('id');?></div></th>
            		<th><div><?php echo get_phrase('student');?></div></th>
            		<th><div><?php echo get_phrase('title');?></div></th>
                    <th><div><?php echo get_phrase('total');?></div></th>
                    <th><div><?php echo get_phrase('paid');?></div></th>
                    <th><div><?php echo get_phrase('status');?></div></th>
            		<th><div><?php echo get_phrase('date');?></div></th>
            		<th><div><?php echo get_phrase('options');?></div></th>
				</tr>
			</thead>
		</table>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$.fn.dataTable.ext.errMode = 'throw';
        $('#invoices').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax":{
                "url": "<?php echo site_url('admin/get_invoices') ?>",
                "dataType": "json",
                "type": "POST",
            },
            "columns": [
                { "data": "invoice_id" },
                { "data": "student" },
                { "data": "title" },
                { "data": "total" },
                { "data": "paid" },
                { "data": "status" },
                { "data": "date" },
                { "data": "options" },
            ],
            "columnDefs": [
            	{
					"targets": [1,3,4,6,7],
					"orderable": false
				},
			]
        });
	});

	function invoice_pay_modal(invoice_id) {
        showAjaxModal('<?php echo site_url('modal/popup/modal_take_payment/');?>' + invoice_id);
    }

	function invoice_view_modal(invoice_id) {
        showAjaxModal('<?php echo site_url('modal/popup/modal_view_invoice/');?>' + invoice_id);
    }

	function invoice_edit_modal(invoice_id) {
        showAjaxModal('<?php echo site_url('modal/popup/modal_edit_invoice/');?>' + invoice_id);
    }

    function invoice_delete_confirm(invoice_id) {
        confirm_modal('<?php echo site_url('admin/invoice/delete/');?>' + invoice_id);
    }
</script>