<?php

$content = $data['#content'];
$sensor = $content['sensorTag'];
$param = $content['paramTag'];
$app = $content['appTag'];
$requestUrl = $content['requestUrl'];
$baseUrl = $content['baseUrl'];
$feedsUrl = $content['feedsUrl'];
$sortby = $content['sortby'];
$sortdir = $content['sortdir'];
$id = rand(0,10000);
?>

<div id='<?php print $id; ?>' class='feeds metapanel'>
	<div class='sortwrap'>
		<span class='limit'>
			<a class='dropdown' data-toggle='dropdown' href='#' data-value='10'>10<span class='caret'></span></a>
			<ul class='dropdown-menu' role='menu'>
				<li><a href='#' data-value='5'>5</a></li>
				<li><a href='#' data-value='10'>10</a></li>
				<li><a href='#' data-value='25'>25</a></li>
				<li><a href='#' data-value='50'>50</a></li>
			</ul>
		</span> results per page.
		Sort by
		<div class='sortby'>
			<a class='dropdown' data-toggle='dropdown' href='#' data-value='<?php print $sortby; ?>'><?php print $sortby; ?><span class='caret'></span></a>
			<ul class='dropdown-menu' role='menu'>
				<li><a href='#' data-value='title'>Title</a></li>
				<li><a href='#' data-value='pubdate'>Date</a></li>
			</ul>
		</div>
		<div class='sortdir'>
			<a class='dropdown' data-toggle='dropdown' href='#' data-value='<?php print $sortdir; ?>'><?php print $sortdir; ?><span class='caret'></span></a>
			<ul class='dropdown-menu' role='menu'>
				<li><a href='#' data-value='asc'>ASC</a></li>
				<li><a href='#' data-value='desc'>DESC</a></li>
			</ul>
		</div>
	</div>
	<div class='loading'>
		<div class="spinner">
			<div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div>
		</div>
	</div>
	<hr>
	<div class='results'></div>
	<div class='pages'>
		<span class='status'></span>
		<a href='#' class='prev'>Prev</a>
		<a href='#' class='next'>Next</a>
	</div>
</div>

<script>

	jQuery(function($){

		var id = '<?php print $id; ?>',
			$out = $('#'+id),
			$results = $('.results', $out),
			$loading = $('.loading', $out),
			$sortby = $('.sortby', $out),
			$sortdir = $('.sortdir', $out),
			$pages = $('.pages', $out),
			$limit = $('.limit', $out),
			page = 1,
			start = 0,
			limit = parseInt($('.dropdown', $limit).attr('data-value')),
			numResults;

		var go = function(){
			$loading.removeClass('hidden');
			$results.html('');

			limit = parseInt($('.dropdown', $limit).attr('data-value'));

			var req = Metadata.request({
				type: 'feeds',
				param: $('#param').val(),
				sensor: $('#sensor').val(),
				app: $('#app').val(),
				start: start,
				page: page,
				limit: limit,
				sortby: $('.dropdown', $sortby).attr('data-value'),
				sortdir: $('.dropdown', $sortdir).attr('data-value')
			}).done(function(json){
				if (json.status == 'error'){
					if (json.message == "couldn't connect to host" || json.message == "connect() timed out!"){
						$results.html("Couldn't connect to GMU, please try again in a few minutes.");
					}else{
						$results.html(json.message);
					}
					return;
				}
				$loading.addClass('hidden');
				if (json.rows.length == 0){
					$results.append("<div>No results.</div>");
					$('.status', $pages).html('');
					$('a', $pages).addClass('hidden');
				}else{
					numResults = json.results;
					$('.status', $pages).html('Showing results ' + (start+1) + " - " + (start + parseInt(limit)) + " of " + numResults);
					$('a', $pages).removeClass('hidden');
				}
				json.rows.forEach(function(row, i, rows){
					var $payload = $(row.payload),
						$author = $payload.find('author').find('name'),
						author = $author.text(),
						$feed = $("<div id='feed_"+row.feed_id+"_"+id+"' class='metafeed'/>"),
						$head = $("<h4/>").appendTo($feed),
						link = row.link,
						title = $("<div>"+row.title+"</div>").text(),
						$title = $("<a href='"+row.link+"' data-target='"+row.feed_id+"_"+id+"'>"+title+"</a>").appendTo($head),
						$body = $("<div id='body_"+row.feed_id+"_"+id+"' class='collapse'/>").appendTo($feed),
						description = $("<div>"+row.description+"</div>").text();

					if (author != "" && author != "unknown"){
						$head.append("<small> - "+author+"</small>");
					}
					if (row.pubdate){
						$head.append("<small> - "+row.pubdate+"</small>");
					}
					if (link != undefined){
						$body.append("<p><a href='"+link+"'>"+link+"</a></p>");
					}
					if (description != ''){
						$body.append("<div class='abstract'>"+description+"</div>");
					}

					$title.click(function(e){
						if (e.which == '2'){
							return;
						}
						e.preventDefault();
						e.stopPropagation();
						var target = $(e.target).attr('data-target');
						// $('#body_'+target).toggleClass('collapse');
						// $('#feed_'+target).toggleClass('active');
						$(this).parent().next().toggleClass('collapse');
						$(this).parent().parent().toggleClass('active');
					});

					$results.append($feed).append("<hr/>");
				});
			}).fail(function(xhr, status, err){
				$results.html('<div class="alert alert-warning">Server has returned an error.</div>');
				console.log('feeds error: '+err.message+', %O', err);
			}).always(function(){
				if ($('#content').outerHeight() > $('#main').height() || $(document).width() < 480){
		            $('#page').removeClass('heightfix');
		        	$('#main').removeClass('heightfix');
	        	}else if ($('#content').outerHeight() < $('#main').height()){
	        		$('#page').addClass('heightfix');
	        		$('#main').addClass('heightfix');
	        	}
			});
		};

		go();
		$(Metadata).on('refresh', go);

		$('.sortwrap .dropdown-menu', $out).click(function(e){
			e.preventDefault();
			var by = $(e.target).attr('data-value');
			$(this).prev().html(by + "<span class='caret'></span>").attr('data-value', by);
			go();
		});
		$('.pages a', $out).click(function(e){
			e.preventDefault();
			var dir = $(e.target).attr('class');
			if (dir == 'prev' && start > 0){
				start -= limit;
				if (start < 0) start = 0;
				go();
			}else if (dir == 'next' && start + limit < numResults ){
				start += limit;
				go();
			}
		});

	});

</script>
