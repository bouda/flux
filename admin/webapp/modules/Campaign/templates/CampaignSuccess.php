<?php
	/* @var $user Flux\User */
	$campaign = $this->getContext()->getRequest()->getAttribute("campaign", array());
	$clients = $this->getContext()->getRequest()->getAttribute("clients", array());
	$offers = $this->getContext()->getRequest()->getAttribute("offers", array());
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<div class="page-header">
	<!-- Actions -->
	<div class="pull-right">
		<div class="visible-sm visible-xs">
			<div class="btn-group">
  				<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Actions <span class="caret"></span></button>
				<ul class="dropdown-menu dropdown-menu-right" role="menu">
					<li><a data-toggle="modal" data-target="#edit_modal" href="/campaign/campaign-pane-edit?_id=<?php echo $campaign->getId() ?>">edit campaign</a></li>
					<li class="divider"></li>
					<li><a data-toggle="modal" data-target="#instruction_modal" href="/campaign/campaign-pane-instruction?_id=<?php echo $campaign->getId() ?>">view instructions</a></li>
					<li class="divider"></li>
					<li><a data-toggle="modal" id="btn_delete_sm" data-target="#delete_modal" href="#"><span class="text-danger">delete</span></a></li>
				</ul>
			</div>
		</div>
		<div class="hidden-sm hidden-xs">
			<div class="btn-group" role="group">
				<a class="btn btn-info" data-toggle="modal" data-target="#edit_modal" href="/campaign/campaign-pane-edit?_id=<?php echo $campaign->getId() ?>">edit campaign</a>
			</div>
			<div class="btn-group" role="group">
				<a class="btn btn-info" data-toggle="modal" data-target="#instruction_modal" href="/campaign/campaign-pane-instruction?_id=<?php echo $campaign->getId() ?>">view instructions</a>
			</div>
			<a data-toggle="modal" id="btn_delete" data-target="#delete_modal" class="btn btn-danger" href="#">delete</a>
		</div>
	</div>
	<h1><img class="img-thumbnail" src="/images/traffic-sources/<?php echo $campaign->getTrafficSource()->getTrafficSourceIcon() != '' ? $campaign->getTrafficSource()->getTrafficSourceIcon() : 'unknown' ?>_48.png" border="0" /> Campaign for <?php echo $campaign->getOffer()->getOfferName() ?> <small><?php echo $campaign->getClient()->getClientName() ?></small></h1>
</div>
<!-- Add breadcrumbs -->
<ol class="breadcrumb">
	<li><a href="/campaign/campaign-search">Campaigns</a></li>
	<li><a href="/offer/offer?_id=<?php echo $campaign->getOffer()->getOfferId() ?>"><?php echo $campaign->getOffer()->getOfferName() ?></a></li>
	<li class="active">Campaign #<?php echo $campaign->getKey() ?></li>
</ol>

<!-- Page Content -->
<div class="help-block">Get a bird's eye view of this offer and how it is performing below.</div>
<br/>
<!-- content -->
<div class="row">
	<!-- main col right -->
	<div class="col-sm-8">
		<div class="panel panel-default">
			<div class="panel-heading"><h4>Click Traffic</h4></div>
			<div class="panel-body">
				<div id="click_by_hour_div">
            		<!--Divs that will hold each control and chart-->
            		<div id="click_by_hour_chart_div" style="width:100%;height:250px">
            			<div class="text-muted text-center">
            				<span class="fa fa-spinner fa-spin"></span>
            				Loading report data...
            			</div>
            		</div>
            		<div id="click_by_hour_filter_div" style="width:100%;height:50px"></div>
                </div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading"><h4>Conversion Traffic</h4></div>
			<div class="panel-body">
				<div id="conversion_by_hour_div">
            		<!--Divs that will hold each control and chart-->
            		<div id="conversion_by_hour_chart_div" style="width:100%;height:250px">
            			<div class="text-muted text-center">
            				<span class="fa fa-spinner fa-spin"></span>
            				Loading report data...
            			</div>
            		</div>
            		<div id="conversion_by_hour_filter_div" style="width:100%;height:50px"></div>
                </div>
			</div>
		</div>
	</div>
	
	<!-- main col right -->
	<div class="col-sm-4">
	    <?php if ($campaign->getOffer()->getOffer()->getRedirectType() != \Flux\Offer::REDIRECT_TYPE_POST) { ?>
		<div class="panel panel-default text-center">
			<img id="offer_thumbnail_img" class="page_thumbnail" src="http://api.page2images.com/directlink?p2i_device=6&p2i_screen=1024x768&p2i_size=300x300&p2i_key=<?php echo defined('MO_PAGE2IMAGES_API') ? MO_PAGE2IMAGES_API : '108709d8d7ae991c' ?>&p2i_url=<?php echo urlencode($campaign->getRedirectLink()) ?>" border="0" alt="Loading thumbnail..." data-url="<?php echo $campaign->getRedirectLink() ?>" />
			<p />
			<div>
				<a class="btn btn-info" href="<?php echo (defined("MO_REALTIME_URL") ? MO_REALTIME_URL : "") . '/r?' . \Flux\DataField::DATA_FIELD_REF_CAMPAIGN_KEY . '=' . $campaign->getId() ?>" target="_blank">Preview Landing Page</a>
				<br /><small><?php echo (defined("MO_REALTIME_URL") ? MO_REALTIME_URL : "") . '/r?' . \Flux\DataField::DATA_FIELD_REF_CAMPAIGN_KEY . '=' . $campaign->getId() ?></small>
			</div>
		</div>
		<p />
		<?php } ?>
		<div class="panel panel-default text-center">
			<div class="panel-heading">
				<h4>Today's Stats</h4>
			</div>
			<div class="panel-body">
			<h4><?php echo number_format($campaign->getDailyClicks(), 0, null, ',') ?> Clicks</h4>
			<h4><?php echo number_format($campaign->getDailyConversions(), 0, null, ',') ?> Conversions</h4>
			</div>
		</div>
		<p />
		<div class="panel panel-default">
			<div class="panel-heading"><h4>Quick Links</h4></div>
			<div class="panel">
				<div class="list-group">
					<a href="/offer/offer?_id=<?php echo $campaign->getOffer()->getOfferId() ?>" class="list-group-item"><?php echo $campaign->getOffer()->getOfferName() ?></a>
					<a href="/client/client?_id=<?php echo $campaign->getClient()->getClientId() ?>" class="list-group-item"><?php echo $campaign->getClient()->getClientName() ?></a>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- edit modal -->
<div class="modal fade" id="edit_modal"><div class="modal-lg modal-dialog"><div class="modal-content"></div></div></div>
<!-- instruction modal -->
<div class="modal fade" id="instruction_modal"><div class="modal-lg modal-dialog"><div class="modal-content"></div></div></div>

<script>
//<!--
google.load("visualization", "1", {packages:["corechart"]});

$(document).ready(function() {
	$('#btn_delete,#btn_delete_sm').click(function() {
		if (confirm('Are you sure you want to delete this campaign and completely remove it from the system?')) {
			$.rad.del('/api', { func: '/campaign/campaign/<?php echo $campaign->getId() ?>' }, function(data) {
				$.rad.notify('Campaign Removed', 'This campaign has been removed from the system.');
				window.location.replace('/campaign/campaign-search');
			});
		}
	});

	drawClickByHourChart();
   	drawConversionByHourChart();
});

function drawClickByHourChart() {
	$.rad.get("/api", { "func": "/report/graph-click-by-hour", "date_range": "<?php echo \Mojavi\Form\DateRangeForm::DATE_RANGE_LAST_24_HOURS ?>", "tz": "<?php echo $this->getContext()->getUser()->getUserDetails()->getTimezone() ?>", "group_type": 2, "campaign_id_array": "<?php echo $campaign->getId() ?>" }, function(data) {
		if (data.record && data.record.cols && data.record.cols.length > 1) {
			var dashboard = new google.visualization.Dashboard(document.getElementById('click_by_hour_chart_div'));
			var datatable = new google.visualization.DataTable({ cols: data.record.cols, rows: data.record.rows });	
			var dataview = new google.visualization.DataView(datatable);	
			var series = data.record.series;
			
			var columns = [];
			for (var i = 0; i < datatable.getNumberOfColumns(); i++) {
				columns.push(i);
			}
		
			var $options = {
				animation:{ duration: 250, easing: 'out' },
				hAxis: {
					gridlines: {color: '#eaeaea', count: -1, units: { days: {format: ["MMM dd"]}, hours: {format: ["h a", "ha"]}}},
					minorGridlines: {color: '#f4f4f4', count: -1, units: { days: {format: ["MMM dd"]}, hours: {format: ["h a", "ha"]}}},
					textStyle: { color: '#737373', fontSize: 11 },
				},
				legend: { textStyle: { color: '#737373', fontSize: 11 }},
				vAxis: { 
					gridlines: {color: '#eaeaea', count: 4},
					minorGridlines: {color: '#f4f4f4', count: 1},
					textStyle: { color: '#737373', fontSize: 11 }
				},
				series: data.record.series,
				chartArea:{ left:'8%', top: '8%', width: '70%', height:'80%' }
			};
			// Create a pie chart, passing some options
	        var chart = new google.visualization.ChartWrapper({
	          chartType: 'LineChart',
	          containerId: 'click_by_hour_chart_div',
	          options: $options
	        });
	        
	        var chart_range_control = new google.visualization.ControlWrapper({
	        	controlType: 'ChartRangeFilter',
	            containerId: 'click_by_hour_filter_div',
	            options: {
	              	filterColumnLabel: 'Hour',
		            ui: { chartType: 'LineChart', chartOptions: { chartArea: {left:'8%',width: '70%'}, hAxis: { gridlines: {color: '#eaeaea', count: 30}, minorGridlines: {color: '#f4f4f4', count: 1}, baselineColor: 'none', textStyle: { color: '#737373', fontSize: 11 }}}, minRangeSize: 86400000 /* 1 day */ }
	            },
	            state: { range: { start: new Date(<?php echo date('Y', strtotime('today')) ?>, <?php echo date('m', strtotime('today'))-1 ?>, <?php echo date('d', strtotime('today')) ?>), end: new Date(<?php echo date('Y', strtotime('tomorrow')) ?>, <?php echo date('m', strtotime('tomorrow'))-1 ?>, <?php echo date('d', strtotime('tomorrow')) ?>) }}
			});

	        dashboard.bind(chart_range_control, chart);
	        dashboard.draw(dataview);

	        google.visualization.events.addListener(chart, 'select', function () {
				var sel = dashboard.getSelection();
				// if selection length is 0, we deselected an element
				if (sel.length > 0) {
					// if row is undefined, we clicked on the legend
					if (typeof sel[0].row === 'undefined' || sel[0].row === null) {
						var col = sel[0].column;
						if (columns[col] == col) {
							// hide the data series
							columns[col] = {
								label: datatable.getColumnLabel(col),
								type: datatable.getColumnType(col),
								calc: function () {
									return null;
								}
							};
		
							// grey out the legend entry
							series[col - 1].color = '#eaeaea';
						}
						else {
							// show the data series
							columns[col] = col;
							series[col - 1].color = series[col - 1].orig_color;
						}
						dataview.setColumns(columns);
						dashboard.draw(dataview);
					}
				}
			});
		} else {
			$('#click_by_hour_chart_div').html('<div class="alert alert-warning"><h3 class="text-warning text-center"><span class="glyphicon glyphicon-retweet"></span> We\'re sorry, there is no data to display on this graph yet</h3></div>');
		}
	});
}

function drawConversionByHourChart() {
	$.rad.get("/api", { "func": "/report/graph-conversion-by-hour", "date_range": "<?php echo \Mojavi\Form\DateRangeForm::DATE_RANGE_LAST_24_HOURS ?>", "tz": "<?php echo $this->getContext()->getUser()->getUserDetails()->getTimezone() ?>", "group_type": 2, "campaign_id_array": "<?php echo $campaign->getId() ?>" }, function(data) {
		if (data.record && data.record.cols && data.record.cols.length > 1) {
			var dashboard = new google.visualization.Dashboard(document.getElementById('conversion_by_hour_chart_div'));
			var datatable = new google.visualization.DataTable({ cols: data.record.cols, rows: data.record.rows });	
			var dataview = new google.visualization.DataView(datatable);	
			var series = data.record.series;
			
			var columns = [];
			for (var i = 0; i < datatable.getNumberOfColumns(); i++) {
				columns.push(i);
			}
		
			var $options = {
				animation:{ duration: 250, easing: 'out' },
				hAxis: {
					gridlines: {color: '#eaeaea', count: -1, units: { days: {format: ["MMM dd"]}, hours: {format: ["h a", "ha"]}}},
					minorGridlines: {color: '#f4f4f4', count: -1, units: { days: {format: ["MMM dd"]}, hours: {format: ["h a", "ha"]}}},
					textStyle: { color: '#737373', fontSize: 11 },
				},
				legend: { textStyle: { color: '#737373', fontSize: 11 }},
				vAxis: { 
					gridlines: {color: '#eaeaea', count: 4},
					minorGridlines: {color: '#f4f4f4', count: 1},
					textStyle: { color: '#737373', fontSize: 11 }
				},
				series: data.record.series,
				chartArea:{ left:'8%', top: '8%', width: '70%', height:'80%' }
			};
			// Create a pie chart, passing some options
	        var chart = new google.visualization.ChartWrapper({
	          chartType: 'LineChart',
	          containerId: 'click_by_hour_chart_div',
	          options: $options
	        });
	        
	        var chart_range_control = new google.visualization.ControlWrapper({
	        	controlType: 'ChartRangeFilter',
	            containerId: 'click_by_hour_filter_div',
	            options: {
	              	filterColumnLabel: 'Hour',
		            ui: { chartType: 'LineChart', chartOptions: { chartArea: {left:'8%',width: '70%'}, hAxis: { gridlines: {color: '#eaeaea', count: 30}, minorGridlines: {color: '#f4f4f4', count: 1}, baselineColor: 'none', textStyle: { color: '#737373', fontSize: 11 }}}, minRangeSize: 86400000 /* 1 day */ }
	            },
	            state: { range: { start: new Date(<?php echo date('Y', strtotime('today')) ?>, <?php echo date('m', strtotime('today'))-1 ?>, <?php echo date('d', strtotime('today')) ?>), end: new Date(<?php echo date('Y', strtotime('tomorrow')) ?>, <?php echo date('m', strtotime('tomorrow'))-1 ?>, <?php echo date('d', strtotime('tomorrow')) ?>) }}
			});

	        dashboard.bind(chart_range_control, chart);
	        dashboard.draw(dataview);

	        google.visualization.events.addListener(chart, 'select', function () {
				var sel = dashboard.getSelection();
				// if selection length is 0, we deselected an element
				if (sel.length > 0) {
					// if row is undefined, we clicked on the legend
					if (typeof sel[0].row === 'undefined' || sel[0].row === null) {
						var col = sel[0].column;
						if (columns[col] == col) {
							// hide the data series
							columns[col] = {
								label: datatable.getColumnLabel(col),
								type: datatable.getColumnType(col),
								calc: function () {
									return null;
								}
							};
		
							// grey out the legend entry
							series[col - 1].color = '#eaeaea';
						}
						else {
							// show the data series
							columns[col] = col;
							series[col - 1].color = series[col - 1].orig_color;
						}
						dataview.setColumns(columns);
						dashboard.draw(dataview);
					}
				}
			});
		} else {
			$('#conversion_by_hour_chart_div').html('<div class="alert alert-warning"><h3 class="text-warning text-center"><span class="glyphicon glyphicon-retweet"></span> We\'re sorry, there is no data to display on this graph yet</h3></div>');
		}
	});
}
//-->
</script>