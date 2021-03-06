<?php
	/* @var $fulfillment_map \Flux\FulfillmentMap */
	$fulfillment_map = $this->getContext()->getRequest()->getAttribute("fulfillment_map", array());
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	<h4 class="modal-title" id="myModalLabel">Mapping Options</h4>
</div>
<form id="fulfillment_map_modal_form" method="POST" action="">
	<input type="hidden" id="fulfillment_map_modal_column_id" name="column_id" value="<?php echo $fulfillment_map->getColumnId() ?>" />
	<input type="hidden" id="fulfillment_map_modal_datafield_id" name="data_field[data_field_id]" value="<?php echo $fulfillment_map->getDataField()->getDataFieldId() ?>" />
	<input type="hidden" id="fulfillment_map_modal_default_value" name="default_value" value="<?php echo $fulfillment_map->getDefaultValue() ?>" />
	<div class="modal-body">
		<div class="help-block">Define a custom function that you can use to convert this field to a value that the API accepts</div>
		<p />
		<div class="help-text">
			<span class="text-success">
			/**<br />
			&nbsp;* Custom mapping function<br />
			&nbsp;* $value - Value from mapping<br />
			&nbsp;* $lead - \Flux\Lead object<br />
			&nbsp;*/<br />
			</span>
			<strong>
			$mapping_func = function ($value, $lead) {
			</strong>
		</div>
		<div class="col-sm-offset-1">
			<textarea id="fulfillment_map_modal_mapping_func" name="mapping_func" rows="12" class="form-control" placeholder="return $value;"><?php echo $fulfillment_map->getMappingFunc() ?></textarea>
		</div>
		<div class="help-text"><strong>}</strong></div>
		<hr />
		<div class="help-block">Set a default value that will be used for this field</div>
		<div class="form-group">
			<input type="text" id="fulfillment_map_modal_mapping_default" name="default_value" class="form-control" value="<?php echo $fulfillment_map->getDefaultValue() ?>" placeholder="enter default value (optional)" />
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" id="btn_validate" class="btn btn-info">Validate</button>
		<button type="submit" class="btn btn-primary">Save changes</button>
	</div>
</form>
<script>
//<!--
$(document).ready(function() {
	/* Validate the mapping function */
	$('#btn_validate').click(function() {
		var params = $('#fulfillment_map_modal_form').serialize();
		$.rad.post('/admin/fulfillment-map-validate', params, function(data) {
			$.rad.notify('Function validated', 'Validation was successful for this function');
		});
	});
	
	
	/* Handle a form submit by converting it to a text representative and hidden input fields on the main page */	
	$('#fulfillment_map_modal_form').on('submit', function(event) {
		event.preventDefault();
		var position = $('#fulfillment_map_modal_column_id').val();
		$('#mapping_func-' + position).val($('#fulfillment_map_modal_mapping_func').val());
		$('#mapping_default-' + position).val($('#fulfillment_map_modal_mapping_default').val());
		if ($('#fulfillment_map_modal_mapping_default').val() != '') {
			$('#map_defaults-' + position).html('Default Value: ' + $('#fulfillment_map_modal_mapping_default').val());
		} else {
			$('#map_defaults-' + position).html('');
		}
		$('#map_options-' + position).attr('href', '/admin/fulfillment-pane-map-options-modal?' + $(this).serialize());
		if (window.btoa($('#fulfillment_map_modal_mapping_func').val()) == '<?php echo base64_encode(\Flux\FulfillmentMap::getDefaultMappingFunc()) ?>' || $('#fulfillment_map_modal_mapping_func').val() == '') {
			$('#map_options-' + position).removeClass('btn-success').addClass('btn-default');
		} else {
 			$('#map_options-' + position).removeClass('btn-default').addClass('btn-success');
		}
		// Hide the modal
		$('#map_options_modal').modal('hide');
		event.preventDefault();
	});
});
//-->
</script>