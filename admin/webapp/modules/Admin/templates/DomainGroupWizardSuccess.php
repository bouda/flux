<?php
	/* @var $domain_group Flux\DomainGroup */
	$domain_group = $this->getContext()->getRequest()->getAttribute("domain_group", array());
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	<h4 class="modal-title"><?php echo ($domain_group->getId() > 0) ? 'Edit' : 'Add' ?> Domain Group</h4>
</div>
<form class="" id="domain_group_form_<?php echo $domain_group->getId() ?>" method="<?php echo ($domain_group->getId() > 0) ? 'PUT' : 'POST' ?>" action="/api" autocomplete="off" role="form">
	<input type="hidden" name="func" value="/admin/domain-group" />
	<input type="hidden" name="status" value="<?php echo \Flux\DomainGroup::DOMAIN_GROUP_STATUS_ACTIVE ?>" />
	<?php if ($domain_group->getId() > 0) { ?>
		<input type="hidden" name="_id" value="<?php echo $domain_group->getId() ?>" />
	<?php } ?>
	<div class="modal-body">
		<div class="help-block">Create a new domain group that you can use to organize emails</div>
		<div class="form-group">
			<label class="control-label hidden-xs" for="name">Name</label>
			<input type="text" id="name" name="name" class="form-control" placeholder="Name" value="<?php echo $domain_group->getName() ?>" />
		</div>
		<div class="form-group">
			<label class="control-label hidden-xs" for="description">Description</label>
			<textarea name="description" id="description" class="form-control" placeholder="Enter Description..."><?php echo $domain_group->getDescription() ?></textarea>
		</div>
		<div class="form-group">
			<label class="control-label hidden-xs" for="domains">Domains</label>
			<input type="text" name="domains" id="domains" class="form-control" placeholder="Enter Domains..." value="<?php echo implode(",", $domain_group->getDomains()) ?>" />
		</div>
	</div>
	<div class="modal-footer">
		<?php if ($domain_group->getId() > 0) { ?>
			<input type="button" class="btn btn-danger" value="Delete Domain Group" class="small" onclick="javascript:confirmDelete();" />
		<?php } ?>
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button type="submit" class="btn btn-primary">Save changes</button>
	</div>
</form>
<script>
//<!--
$(document).ready(function() {
	$('#domain_group_form_<?php echo $domain_group->getId() ?>').form(function(data) {
		$.rad.notify('Domain Group Updated', 'The domain group has been added/updated in the system');
		$('#domain_group_search_form').trigger('submit');
	}, {keep_form:1});
	
	$('#domains').selectize({
		delimiter: ',',
		persist: false,
		create: function(input) {
			return {
				value: input,
				text: input
			}
		}
	});
});

<?php if ($domain_group->getId() > 0) { ?>
function confirmDelete() {
	if (confirm('Are you sure you want to delete this domain group from the system?')) {
		$.rad.del({ func: '/admin/domain-group/<?php echo $domain_group->getId() ?>' }, function(data) {
			$.rad.notify('You have deleted this domain_group', 'You have deleted this domain group.  You will need to refresh this page to see your changes.');
			$('#domain_group_search_form').trigger('submit');
		});
	}
}
<?php } ?>
//-->
</script>