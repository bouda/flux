<?php
	/* @var $user Flux\User */
	$campaign = $this->getContext()->getRequest()->getAttribute("campaign", array());
	$clients = $this->getContext()->getRequest()->getAttribute("clients", array());
	$offers = $this->getContext()->getRequest()->getAttribute("offers", array());
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	<h4 class="modal-title">Edit Campaign #<?php echo $campaign->getId() ?></h4>
</div>
<form action="/api" id="campaign_form" method="PUT">
	<input type="hidden" name="func" value="/campaign/campaign" />
	<input type="hidden" name="_id" value="<?php echo $campaign->getId() ?>" />
	<div class="modal-body">
	    <!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#basic" role="tab" data-toggle="tab">Basic Settings</a></li>
			<li role="presentation" class=""><a href="#redirects" role="tab" data-toggle="tab">Redirects</a></li>
			<li role="presentation" class=""><a href="#whitelisted_ips" role="tab" data-toggle="tab">Whitelisted IPs</a></li>
		</ul>
		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane fade in active" id="basic">
			    <div class="help-block">These are the main settings for this campaign.</div>
        		<div class="form-group">
        			<label class="control-label hidden-xs" for="description">Description</label>
        			<textarea id="description" name="description" class="form-control" placeholder="Enter Descriptive Name"><?php echo $campaign->getDescription(); ?></textarea>
        		</div>
        		
        		<div class="form-group">
        			<label class="control-label hidden-xs" for="status">Status</label>
        			<select class="form-control" name="status" id="status" placeholder="Status">
        				<option value="<?php echo \Flux\Campaign::CAMPAIGN_STATUS_ACTIVE ?>"<?php echo $campaign->getStatus() == \Flux\Campaign::CAMPAIGN_STATUS_ACTIVE ? ' selected="selected"' : ''; ?>>Active</option>
        				<option value="<?php echo \Flux\Campaign::CAMPAIGN_STATUS_INACTIVE ?>"<?php echo $campaign->getStatus() == \Flux\Campaign::CAMPAIGN_STATUS_INACTIVE ? ' selected="selected"' : ''; ?>>Inactive</option>
        			</select>
        		</div>		  
        	
        		<div class="form-group">
        			<label class="control-label hidden-xs" for="client_id">Publisher Client</label>
        			<select class="form-control" name="client[client_id]" id="client_id" placeholder="Publisher Client">
        				<?php
        					/* @var $client \Flux\Client */
        					foreach ($clients AS $client) { 
        				?>
        					<option value="<?php echo $client->getId(); ?>"<?php echo $campaign->getClient()->getClientId() == $client->getId() ? ' selected="selected"' : ''; ?>><?php echo $client->getName() ?></option>
        				<?php } ?>
        			</select>
        		</div>
        	
        		<div class="form-group">
        			<label class="control-label hidden-xs" for="offer_id">Offer</label>
        			<select class="form-control" name="offer[offer_id]" id="offer_id" placeholder="Offer">
        				<?php
        					/* @var $offer \Flux\Offer */ 
        					foreach ($offers AS $offer) { 
        				?>
        					<option value="<?php echo $offer->getId(); ?>"<?php echo $campaign->getOffer()->getOfferId() == $offer->getId() ? ' selected="selected"' : ''; ?> data-data="<?php echo htmlentities(json_encode(array('url' => $offer->getFormattedRedirectUrl()))) ?>"><?php echo $offer->getName() ?></option>
        				<?php } ?>
        			</select>
        		</div>
        	</div>
        	<div role="tabpanel" class="tab-pane fade in" id="redirects">
        	    <div class="help-block">These urls define how traffic comes into this campaign and where it goes</div>
        	    <div class="form-group">
        			<label class="control-label hidden-xs" for="redirect_link">Redirect Link</label>
        			<textarea name="redirect_link" rows="3" id="redirect_link" class="form-control" placeholder="enter a url to redirect traffic to (like http://www.metalhiplawsuits.us/index.php?_id=#_id#)..."><?php echo $campaign->getRedirectLink() ?></textarea>
        		</div>
        		
        		<hr />
        		
        		<div class="alert alert-info">
        			Use the link below in Adwords to direct traffic to this campaign.  You can customize this link on the instructions tab.
        		</div>
        		
        		<div class="form-group">
        			<label class="control-label hidden-xs" for="redirect_link">Adwords Url</label>
        			<textarea id="adwords_url" class="form-control" placeholder="<?php echo (defined("MO_REALTIME_URL") ? MO_REALTIME_URL : "") . '/r?' . \Flux\DataField::DATA_FIELD_REF_CAMPAIGN_KEY . '=' . $campaign->getId() ?>&__clear=1"><?php echo (defined("MO_REALTIME_URL") ? MO_REALTIME_URL : "") . '/r?' . \Flux\DataField::DATA_FIELD_REF_CAMPAIGN_KEY . '=' . $campaign->getId() ?>&__clear=1</textarea>
        		</div>
        	</div>
        	<div role="tabpanel" class="tab-pane fade in" id="whitelisted_ips">
        	    <div class="help-block">If you want to limit incoming traffic, you can specify a list of whitelisted IPs</div>
        	    <div class="form-group">
        			<label class="control-label hidden-xs" for="redirect_link">Whitelisted IPs</label>
        			<input type="text" name="whitelist_ips[]" id="whitelist_ips" class="form-control" placeholder="enter IPs to whitelist" value="<?php echo implode(",", $campaign->getWhitelistIps()) ?>" />
        		</div>
        	</div>
    	</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<input type="submit" name="__save" class="btn btn-primary" value="Save Campaign" />
	</div>
</form>
	
<script>
//<!--
$(document).ready(function() {
    $('#whitelist_ips').selectize({
    	delimiter: ',',
		persist: false,
		allowEmptyOption: true,
		create: function(input) {
			return {
				value: input,
				text: input
			}
		}
    });
	
	$('#client_id,#status').selectize();

	$('#campaign_form').form(function(data) {
		if (data.record) {
			$.rad.notify('Campaign Updated', 'The campaign has been updated successfully');
		}
	},{keep_form: 1});

	$('#offer_id').selectize().on('change', function() {
		$.each($offer_selectize[0].selectize.options, function(i, item) {
			if (item.value == $offer_selectize[0].selectize.getValue()) {
				$('#redirect_link').val(item.url);
			}
		});
	});
});
//-->
</script>