<?php

$content = $data['#content'];
$url = $content['url'];

?>

<div id='feeds'>
	Loading...
</div>

<script>
	(function($){

		var requestUrl = '<?php print $url; ?>';
		var req = jQuery.getJSON(requestUrl);
		var $out = jQuery('#feeds');
		req.done(function(json){
			$out.html('');
			json.rows.forEach(function(row, i, rows){
				$out.append('<h4><a href="'+row.link+'">'+row.title+'</a></h4>');
			});
		});

	}(jQuery));

</script>
