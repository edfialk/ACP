var Metadata = Metadata || {};
var console = console || {log: function(){}};

jQuery(function($){

	Metadata.config = Drupal.settings.acp_contextual || {
		baseUrl: "http://ows9.csiss.gmu.edu:9008/acpweb/",
		feedsUrl: "GetFeedsView",
		papersUrl: "GetPapersView",
		requestUrl: "/sites/all/modules/contextual_metadata/request.php",
		papersFields: "paper_id,doi,title,abstract,pyear,payload",
		feedsFields: "feed_id,link,title,pubdate,description,payload"
	};

	console.log('metadata config: %O', Metadata.config);

	Metadata.baseUrl = Metadata.config.baseUrl;
	Metadata.feedsUrl = Metadata.config.feedsUrl;
	Metadata.papersUrl = Metadata.config.papersUrl;
	Metadata.requestUrl = Metadata.config.requestUrl;

	/**
	 * Send request to GMU ACP Service (through ACP back-end)
	 * @param  {object} options with keys: ['sensor', 'param', 'app', 'type', 'limit', 'field', 'sortBy', 'sortDir']
	 * including options.field sends a request for options.field_tags (i.e. sensor_tags, app_tags, etc.)
	 * @return {jquery xhr request}
	 */
	Metadata.request = function(options){
		options = options || {};

		if (typeof options.type == 'undefined') options.type = 'feeds';
		if (typeof options.limit == 'undefined') options.limit = 10;
		if (typeof options.start == 'undefined') options.start = 0;
		if (typeof options.page == 'undefined') options.page = 1;

		var filter = '[{%22operator%22%3A%22=%22%2C%22value%22%3A%22111%22%2C%22property%22%3A%22ms%22}';

		if (options.sensor !== 'all' && options.sensor !== '' && options.sensor !== undefined && options.sensor !== null){
			filter += ',{%22operator%22%3A%22=%22%2C%22value%22%3A%22%27'+encodeURI(options.sensor)+'%27%22%2C%22property%22%3A%22sensor_tag%22}';
		}
		if (options.param !== 'all' && options.param != '' && options.param !== undefined && options.param !== null){
			filter += ',{%22operator%22%3A%22=%22%2C%22value%22%3A%22%27'+encodeURI(options.param)+'%27%22%2C%22property%22%3A%22param_tag%22}';
		}
		if (options.app !== 'all' && options.app != '' && options.app !== undefined && options.app !== null){
			filter += ',{%22operator%22%3A%22=%22%2C%22value%22%3A%22%27'+encodeURI(options.app)+'%27%22%2C%22property%22%3A%22app_tag%22}';
		}

		filter += ']';

		var gmuUrl = this.baseUrl + ( options.type == "feeds" ? this.feedsUrl : this.papersUrl ) + "?start="+options.start+"&page="+options.page+"&limit="+options.limit;

		if (typeof options.sortby !== 'undefined' && typeof options.sortdir !== 'undefined'){
			gmuUrl += "&sort=[{%22property%22:%22"+options.sortby+"%22,%22direction%22:%22"+options.sortdir+"%22}]";
		}

		if (options.field){
			gmuUrl += "&getdistvaluefield="+options.field;
		}else{
			gmuUrl += "&filter=" + filter;
		}

		if (options.type == 'feeds'){
			gmuUrl += '&outfields='+this.config.feedsFields;
		}else if (options.type == 'papers'){
			gmuUrl += '&outfields='+this.config.papersFields;
		}

		// gmuUrl += "&outfields=title,link,author,";

		console.log('metadata request to: ' + gmuUrl);

		return $.getJSON(this.requestUrl + "?url="+encodeURIComponent(gmuUrl));
	};

	/**
	 * get domain-fixed, encoding fixed html string
	 * @param  {object} paper
	 * @return {html} abstract
	 */
	Metadata.validate = function(string){
		var $string = $('<div>'+string+'</div>');

		//ieeexplore embeds relative-url images inside formula tags inside titles...unbelievable.
		//no idea how many there might be to convert to html, so have to replace with absolute-url img tag
		var $formula = $string.find('formula');
		if ($formula.length > 0 && $('img', $formula).length > 0){
			var img = $string.find('formula img').attr('src');
			$string.find('formula').replaceWith('<img src="http://ieeexplore.ieee.org/'+img+'">');
			string = $string.html();
		}

		return string;

	};

});