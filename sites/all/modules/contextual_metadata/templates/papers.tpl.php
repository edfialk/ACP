<?php

$content = $data['#content'];
// $url = $content['url'];
$sensor = $content['sensorTag'];
$param = $content['paramTag'];
$app = $content['appTag'];
$requestUrl = $content['requestUrl'];
$baseUrl = $content['baseUrl'];
$papersUrl = $content['papersUrl'];
$sortby = $content['sortby'];
$sortdir = $content['sortdir'];
$id = rand(0,10000);

?>

<div id='<?php print $id; ?>' class='papers metapanel'>
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
				<li><a href='#' data-value='pyear'>Date</a></li>
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
	<hr>
	<div class='loading'>
		<div class="spinner">
			<div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div>
		</div>
	</div>
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
			$results.html('')

			limit = parseInt($('.dropdown', $limit).attr('data-value'));

			var req = Metadata.request({
				type: 'papers',
				param: $('#param').val(),
				sensor: $('#sensor').val(),
				app: $('#app').val(),
				page: page,
				limit: limit,
				start: start,
				sortby: $('.dropdown', $sortby).attr('data-value'),
				sortdir: $('.dropdown', $sortdir).attr('data-value')
			}).done(function(json){
				if (json.status == 'error'){
					if (json.message == "couldn't connect to host" || json.message == "connect() timed out!"){
						$results.html("Couldn't connect to GMU, please try again in a few minutes. <a href='"+baseUrl+filter+"'>GMU Request</a>");
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
					try{
						//suddenly this is json for some entries instead of all xml...ok...
						var payload = JSON.parse(row.payload),
							author = payload["dc:creator"],
							title = Metadata.validate(payload["dc:title"]),
							abstract = Metadata.validate(payload["prism:teaser"]),
							paper_id = payload.eid,
							link = "";

						if (payload.link && Array.isArray(payload.link)){
							payload.link.forEach(function(l, i){
								if (l["@ref"] == "scidir"){
									link = l["@href"];
								}
							});
						}

					}catch(e){
						var $payload = $(row.payload),
							$mdurl = $payload.find('mdurl'),
							$author = $payload.find('authors'),
							cdataregexp = /<!--\[CDATA\[(.*)\]\]-->/,
							link = cdataregexp.exec($mdurl.html())[1] || '';
							author = cdataregexp.exec($author.html())[1],
							title = Metadata.validate(row.title),
							abstract = Metadata.validate(row.abstract),
							paper_id = row.paper_id;
					}

					var $paper = $("<div id='paper_"+paper_id+"_"+id+"' class='paper'/>"),
						$head = $("<h4/>").appendTo($paper),
						$title = $("<a href='"+link+"' data-target='"+paper_id+"_"+id+"'>"+title+"</a>").appendTo($head),
						$body = $("<div id='body_"+paper_id+"_"+id+"' class='collapse'/>").appendTo($paper);

					// console.log($payload);

					if (author != ''){
						$head.append("<small> - "+author+"</small>");
					}
					if (row.pyear){
						$head.append("<small> - "+row.pyear+"</small>");
					}
					if (link != ''){
						$body.append("<p><a href='"+link+"'>"+link+"</a></p>");
					}
					if (abstract != ''){
						$body.append("<div class='abstract'>"+abstract+"</div>");
					}

					$title.click(function(e){
						if (e.which == '2'){ //middle click
							return;
						}
						e.preventDefault();
						e.stopPropagation();
						var target = $(e.target).attr('data-target');
						// $('#body_'+target).toggleClass('collapse');
						// $('#paper_'+target).toggleClass('active');
						$(this).parent().next().toggleClass('collapse');
						$(this).parent().parent().toggleClass('active');

					});

					$results.append($paper);
					$results.append("<hr/>");
				});
			}).fail(function(){
				$results.html('<div class="alert alert-warning">Server has returned an error.</div>');
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
