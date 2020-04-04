<hr />
<div class="row">
	<div class="col-md-12">

    	<!---CONTROL TABS START-->
		<ul class="nav nav-tabs bordered">
			<li class="active">
            	<a href="#list" data-toggle="tab"><i class="entypo-menu"></i>
					<?php echo get_phrase('book_list');?>
                    	</a></li>
			<li>
            	<a href="#add" data-toggle="tab"><i class="entypo-plus-circled"></i>
					<?php echo get_phrase('add_book');?>
                    	</a></li>
		</ul>
    	<!---CONTROL TABS END-->


		<div class="tab-content">
        <br>
            <!----TABLE LISTING STARTS-->
            <div class="tab-pane box active" id="list">

                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" id="books">
                	<thead>
                		<tr>
                            <th width="40"><div><?php echo get_phrase('book_id');?></div></th>
                            <th><div><?php echo get_phrase('name');?></div></th>
                    		<th><div><?php echo get_phrase('author');?></div></th>
                    		<th><div><?php echo get_phrase('description');?></div></th>
                    		<th><div><?php echo get_phrase('price');?></div></th>
                    		<th><div><?php echo get_phrase('class');?></div></th>
                    		<!--<th><div><?php echo get_phrase('status');?></div></th>-->
                            <th><div><?php echo get_phrase('download');?></div></th>
                    		<th><div><?php echo get_phrase('options');?></div></th>
						</tr>
					</thead>
                </table>
			</div>
            <!----TABLE LISTING ENDS--->


			<!----CREATION FORM STARTS---->
			<div class="tab-pane box" id="add" style="padding: 5px">
                <div class="box-content">
                	<?php echo form_open(site_url('admin/book/create') , array('class' => 'form-horizontal form-groups-bordered validate','target'=>'_top','enctype'=>'multipart/form-data'));?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo get_phrase('name');?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="name"
                                        data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo get_phrase('author');?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="author"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo get_phrase('description');?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="description"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo get_phrase('price');?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="price"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo get_phrase('class');?></label>
                                <div class="col-sm-5">
                                    <select name="class_id" id = "class_id" class="form-control selectboxit" style="width:100%;">
                                    <option value=""><?php echo get_phrase('select_class'); ?></option>
                                    	<?php
										$classes = $this->db->get('class')->result_array();
										foreach($classes as $row):
										?>
                                    		<option value="<?php echo $row['class_id'];?>"><?php echo $row['name'];?></option>
                                        <?php
										endforeach;
										?>
                                    </select>
                                </div>
                            </div>
                             <div class="form-group"> <label class="col-sm-3 control-label">File</label> <div class="col-sm-5"> <input type="file" name="file_name" class="form-control"> </div> </div>

                        		<div class="form-group">
                              <div class="col-sm-offset-3 col-sm-5">
                                  <button type="submit" id = "submit" class="btn btn-info"><?php echo get_phrase('add_book');?></button>
                              </div>
								</div>
                    </form>
                </div>
			</div>
		</div>
	</div>
</div>


<script type = 'text/javascript'>
    var class_id = '';
    jQuery(document).ready(function($) {
        $.fn.dataTable.ext.errMode = 'throw';
        $('#books').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax":{
                "url": "<?php echo site_url('admin/get_books') ?>",
                "dataType": "json",
                "type": "POST",
            },
            "columns": [
                { "data": "book_id" },
                { "data": "name" },
                { "data": "author" },
                { "data": "description" },
                { "data": "price" },
                { "data": "class" },
                { "data": "download" },
                { "data": "options" },
            ],
            "columnDefs": [
                {
                    "targets": [1,2,3,5,6,7],
                    "orderable": false
                },
            ]
        });

        $("#submit").attr('disabled', 'disabled');
    });

    function book_edit_modal(book_id) {
        showAjaxModal('<?php echo site_url('modal/popup/modal_edit_book/');?>' + book_id);
    }

    function book_delete_confirm(book_id) {
        confirm_modal('<?php echo site_url('admin/book/delete/');?>' + book_id);
    }

    function check_validation(){
        if(class_id !== ''){
            $('#submit').removeAttr('disabled');
        }
        else{
            $("#submit").attr('disabled', 'disabled');
        }
    }
    $('#class_id').change(function(){
        class_id = $('#class_id').val();
        check_validation();
    });
</script>
