<?php

$content = $data['#content'];
$url = $content['url'];
$id = rand(0,10000);

?>

<!-- <div data-toggle='collapse' data-target='<?php print $id; ?>opts'>Show Filters</div> -->
<!-- <div id='<?php print $id; ?>opts' class='collapse options'> -->
	<!-- <form class='form-inline'> -->

	<!-- </form> -->
<!-- </div> -->
<div id='<?php print $id; ?>' class='papers metapanel'>
	Loading...
</div>

<script>

	(function($){

		var requestUrl = '<?php print $url; ?>';
		var req = jQuery.getJSON(requestUrl);
		var $out = jQuery('#<?php print $id; ?>');
		req.done(function(json){
			$out.html('');
			json.rows.forEach(function(row, i, rows){
				$out.append('<h4><a href="#" data-toggle="collapse" data-target="#'+row.paper_id+'">'+row.title+'</a></h4><p id="'+row.paper_id+'" class="collapse">'+row.abstract+'</p>');
			});
		});
		req.fail(function(){
			// console.log('papers fail');
		});
		req.always(function(){
			if ($('#content').outerHeight() > $('#main').height() || $(document).width() < 480){
	            $('#page').removeClass('heightfix');
	        	$('#main').removeClass('heightfix');
        	}else if ($('#content').outerHeight() < $('#main').height()){
        		$('#page').addClass('heightfix');
        		$('#main').addClass('heightfix');
        	}
		});

	}(jQuery));

</script>
