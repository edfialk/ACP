<?php

$content = $data['#content'];
$url = $content['url'];

?>

<div id='app_tags'>
	Loading...
</div>

<script>
	(function($){

		var requestUrl = '<?php print $url; ?>';
		var req = jQuery.getJSON(requestUrl);
		var $out = jQuery('#app_tags');
		console.log($out);
		req.done(function(json){
			$out.html('');
			json.rows.forEach(function(row, i, rows){
				$out.append('<p>'+row.app_tag+'</p>');
			});
		});

	}(jQuery));

</script>
