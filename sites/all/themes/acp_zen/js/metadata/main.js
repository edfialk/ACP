

	var ACPContext = {

		baseUrl: "http://ows9.csiss.gmu.edu:9008/acpweb/",
		feedsViewUrl: "GetFeedsView",
		papersViewUrl: "GetPapersView",

		getSensorTags: function(limit){
			limit = limit || 100;
			// debugger;
			return jQuery.getJSON(this.baseUrl + this.feedsViewUrl+"?limit="+limit+"&getdistvaluefield=sensor_tag");
		}
	}



