<?php
$content = $data['#content'];
?>

<div class='context-opts metapanel container'>
	<div class='loading'>
		<div class="spinner">
			<div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div>
		</div>
	</div>
	<div class='form-wrap hidden'>
		<form class='form-horizontal' role='form'>
			<div class="form-group">
				<label for="sensor" class="col-sm-2 control-label">Sensor</label>
				<div class="col-sm-10">
					<select id="sensor" class="form-control">
						<option value='all'>All</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="param" class="col-sm-2 control-label">Parameter</label>
				<div class="col-sm-10">
					<select id="param" class="form-control">
						<option value='all'>All</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="app" class="col-sm-2 control-label">App</label>
				<div class="col-sm-10">
					<select id="app" class="form-control">
						<option value='all'>All</option>
					</select>
				</div>
			</div>
		</form>
	</div>
</div>

<script>

// BASE_URL.FEEDS_URL."?limit=100&getdistvaluefield=app_tag";

jQuery(function($){

	$('.context-opts select').change(function(){
		$(Metadata).trigger('refresh');
	});

	var appsReq = Metadata.request({
		type: "feeds",
		field: "app_tag",
		limit: 100
	}).done(function(json){
		// console.log("app_tags: %o", json);
		if (json.success == true){
			var $select = $('#app');
			json.rows.forEach(function(row){
				$select.append("<option value='"+row.app_tag+"'>"+row.app_tag+"</a>");
			});
		}
	}).fail(function(json){
		console.log('opts fail');
	});

	var sensorReq = Metadata.request({
		type: "feeds",
		field: "sensor_tag",
		limit: 100
	}).done(function(json){
		// console.log("sensor_tags: %o", json);
		if (json.success == true){
			var $select = $('#sensor');
			json.rows.forEach(function(row){
				$select.append("<option value='"+row.sensor_tag+"'>"+row.sensor_tag+"</a>");
			});
		}
	});

	var paramReq = Metadata.request({
		type: "feeds",
		field: "param_tag",
		limit: 100
	}).done(function(json){
		// console.log("param_tags: %o", json);
		if (json.success == true){
			var $select = $('#param');
			json.rows.forEach(function(row){
				$select.append("<option value='"+row.param_tag+"'>"+row.param_tag+"</a>");
			});
		}
	});

	$.when(appsReq, sensorReq, paramReq).then(function(){
		$('.context-opts .form-wrap').removeClass('hidden');
		$('.context-opts .loading').addClass('hidden');
	});

});

</script>