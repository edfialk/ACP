(function($){

	google.load('visualization', '1', {'packages' : ['table']});
	google.setOnLoadCallback(init);

	// var dataSourceUrl = 'https://spreadsheets.google.com/tq?key=rh_6pF1K_XsruwVr_doofvw&pub=1';
	// var dataSourceUrl = 'https://spreadsheets.google.com/tq?key=tneLiv-KS8wns2Zd-B7--EA&amp;sheet=od6';
  // var spreadsheetKey = 'tneLiv-KS8wns2Zd-B7--EA';
  var spreadsheetKey = '0Aiz9YvG1OtuddGRQdUJKMXJoVTRfaURURW01V1BJOGc'; //<---
	var dataSourceUrl = 'https://docs.google.com/spreadsheet/pub?key='+spreadsheetKey+'&pub=1';
  var jsonUrl = 'https://spreadsheets.google.com/feeds/list/'+spreadsheetKey+'/od6/public/values?alt=json-in-script&callback=?' ;
	var query, options, filters, container;

  getNumRows();

	function init(){
    query = new google.visualization.Query(dataSourceUrl);
    container = document.getElementById("table");
    options = {'pageSize': 10};
    filters = {
      params: {},
      spatial: 'every',
      temporal: 'every'
    };
    sendAndDraw();
    $('.params input').on('change', function(){
      if ($(this).prop('checked')){
        filters.params[this.value] = true;
      }else{
        delete filters.params[this.value];
      }
      sendAndDraw();
    });
    $('.spatial input').on('change', function(){
      filters.spatial = this.value;
      sendAndDraw();
    });
    $('.temporal input').on('change', function(){
      filters.temporal = this.value;
      sendAndDraw();
    });
	}

  function sendAndDraw() {
    $('#intro').hide();
    query.abort();
    var tableQueryWrapper = new TableQueryWrapper(query, container, options, filters);
    tableQueryWrapper.sendAndDraw();
  }

  function setOption(prop, value) {
    options[prop] = value;
    sendAndDraw();
  }

  function getNumRows(){
    $.getJSON(jsonUrl, function(data){
      var numrows = data.feed.entry.length;
      $('#totalrows').html(numrows + ' total rows.');
    });
  }

})(jQuery);
