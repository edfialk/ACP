<?php

$content = $data['#content'];
$url = $content['url'];

?>

<div id='sensor_tags'>
	Loading...
</div>

<script>
	(function($){

		var requestUrl = '<?php print $url; ?>';
		var req = jQuery.getJSON(requestUrl);
		var $out = jQuery('#sensor_tags');
		req.done(function(json){
			$out.html('');
			json.rows.forEach(function(row, i, rows){
				$out.append('<p>'+row.sensor_tag+'</p>');
			});
		});

	}(jQuery));

</script>
