<?php

drupal_add_css('http://cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css', 'external');
// drupal_add_css('http://cdn.datatables.net/plug-ins/725b2a2115b/integration/bootstrap/3/dataTables.bootstrap.css', 'external');
drupal_add_js('http://cdn.datatables.net/1.10.2/js/jquery.dataTables.js', 'external');
// drupal_add_js(drupal_get_path('theme', 'acp_zen') . '/js/dataTables.fixedColumns.js', array('group' => JS_THEME));
// drupal_add_js('http://cdn.datatables.net/plug-ins/725b2a2115b/integration/bootstrap/3/dataTables.bootstrap.js', 'external');
// drupal_add_js('https://www.google.com/jsapi', 'external');
// drupal_add_js(drupal_get_path('theme', 'acp_zen') . '/js/tablequerywrapper.js', array('group' => JS_THEME));
drupal_add_js(drupal_get_path('theme', 'acp_zen') . '/js/datatable.js', array('group' => JS_THEME));

?>

<div id="datatable-wrapper">
	<div id="datatable-options" class="col-sm-2">
		<form role="form">
			<div class="form-group params">
				<legend>Parameter</legend>
				<div class="checkbox">
					<label><input type='checkbox' value='2'>CH4</label>
				</div>
				<div class="checkbox">
					<label><input type='checkbox' value='3'>CO</label>
				</div>
				<div class="checkbox">
					<label><input type='checkbox' value='4'>CO2</label>
				</div>
				<div class="checkbox">
					<label><input type='checkbox' value='5'>NO2</label>
				</div>
				<div class="checkbox">
					<label><input type='checkbox' value='6'>O3</label>
				</div>
				<div class="checkbox">
					<label><input type='checkbox' value='7'>SO2</label>
				</div>
			</div>
			<div class="form-group services">
				<legend>Service</legend>
				<div class="checkbox">
					<label><input type="checkbox" name="service" value="11">FTP</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" name="service" value="12">HTTP</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" name="service" value="13">OpeNDAP</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" name="service" value="14">WCS</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" name="service" value="15">WMS</label>
				</div>
			</div>
			<div class="form-group temporal">
				<legend>Temporal Resolution</legend>
				<div class="radio">
					<label><input type='radio' name="temporal" value='every' checked>All</label>
				</div>
				<div class="radio">
					<label><input type='radio' name="temporal" value='6 minute'>6 minute</label>
				</div>
				<div class="radio">
					<label><input type='radio' name="temporal" value='Twice per day (daytime and nighttime)'>Twice per day (daytime and nighttime)</label>
				</div>
				<div class="radio">
					<label><input type='radio' name="temporal" value='daily'>Daily</label>
				</div>
				<div class="radio">
					<label><input type='radio' name="temporal" value='Once per 8 days (daytime and nighttime)'>Once per 8 days (daytime and nighttime)</label>
				</div>
				<div class="radio">
					<label><input type='radio' name="temporal" value='monthly'>Monthly</label>
				</div>
				<a href="#" class="showMore">More</a>
			</div>
			<div class="form-group spatial">
				<legend>Spatial Resolution</legend>
				<div class="radio">
					<label><input type='radio' name="spatial" value="every" checked>All</label>
				</div>
				<div class="radio">
					<label><input type='radio' name="spatial" value="0.25 x 0.25 deg">0.25 x 0.25 deg</label>
				</div>
				<div class="radio">
					<label><input type='radio' name="spatial" value="1 x 1 deg">1 x 1 deg</label>
				</div>
				<div class="radio">
					<label><input type='radio' name="spatial" value="1.25 x 1 deg">1.25 x 1 deg</label>
				</div>
				<div class="radio">
					<label><input type='radio' name="spatial" value="1.25 x 1.25 deg">1.25 x 1.25 deg</label>
				</div>
				<div class="radio">
					<label><input type='radio' name="spatial" value="50 km x 50 km">50 km x 50 km</label>
				</div>
				<div class="radio">
					<label><input type='radio' name="spatial" value="110 km x 110 km">110 km x 110 km</label>
				</div>
				<a href="#" class="showMore">More</a>
			</div>
		</form>
	</div>
	<div id="datatable-display" class="col-sm-10">
		<div class="alerts"></div>
		<div class="intro">Please choose some options on the left to retrieve records.</div>
		<div class="table-container">
			<table id='table' class='table-striped cell-border'>
				<thead>
					<tr>
						<th>Dataset Name</th>
						<th>Data Provider</th>
						<th>CH4</th>
						<th>CO</th>
						<th>CO2</th>
						<th>NO2</th>
						<th>O3</th>
						<th>SO2</th>
						<th>Temporal Resolution</th>
						<th>Spatial Resolution</th>
						<th>Catalog</th>
						<th>FTP</th>
						<th>HTTP</th>
						<th>OpeNDAP</th>
						<th>WCS</th>
						<th>WMS</th>
						<th>Notes</th>
						<th>Metadata Source</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	<div class="clearfix"></div>
</div>