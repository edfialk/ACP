(function($){

  if (typeof console == "undefined") {
    window.console = {
      log: function () {}
    };
  }

//  https://spreadsheets.google.com/feeds/cells/0ArbpQl2Q0oGRdG5lTGl2LUtTOHduczJaZC1CNy0tRUE/od6/private/full  <-- can get formulas but it's private!

	// var spreadsheetKey = '0Aiz9YvG1OtuddGRQdUJKMXJoVTRfaURURW01V1BJOGc'; //<--- dev
  var spreadsheetKey = '0ArbpQl2Q0oGRdG5lTGl2LUtTOHduczJaZC1CNy0tRUE'; //<-- official
  var dataSourceUrl = 'https://docs.google.com/spreadsheet/pub?key='+spreadsheetKey+'&pub=1';
  // var jsonUrl = 'https://spreadsheets.google.com/feeds/list/'+spreadsheetKey+'/od6/public/values?alt=json';
  var jsonUrl = 'http://54.191.97.139/goog.php';
  var $table, data, $req, options, filters, $container, $intro;
  var ready = false;

  $(document).ready(function(){
    init();
  });

	function init(){

    $intro = $('.intro');
    $container = $('.table-container');

    $table = $('#table').DataTable({
      'ajax' : {
        url: jsonUrl,
      },
      'columnDefs': [
        {
          "targets" : [10],
          "visible" : false,
          "searchable" : false
        },
        {
          "targets" : [2,3,4,5,6,7,8,9,10,11,12,13,14,15],
          "className": "dt-center"
        }
      ],
      scrollX : true,
      stateSave: true,
      pageLength: 5,
      lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
      language: {
        loadingRecords: 'No matching records found.'
      }
    });

    $table.on('xhr.dt', function(e, settings, json){
      ready = true;
    });

    $('#datatable-options input').on('change', function(){
      if (ready){
        $intro.hide();
        $table.draw();
      }else{
        $('.dataTables_empty').html('<div class="spinner"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div><div>Just a moment, loading records...</div>');
        $table.on('xhr.dt', function(){
          $intro.hide();
          $table.draw();
        });
      }
    });

    //this is the table filtering function run on each row, return true to show the row, false to hide it
    $.fn.dataTableExt.afnFiltering.push(
      function( oSettings, aData, iDataIndex ) {
        var show = false; //default to hide row

        $('.params input:checked').each(function(){
          if ($(this).prop('checked') && aData[this.value] !== ''){
            show = true;
          }
        });

        $(".services input:checked").each(function(){
          show &= (aData[this.value] !== '');
        });

        var temporal = $("input[name='temporal']:checked").val();
        if (temporal != 'every'){
          show &= (aData[8] == temporal);
        }

        var spatial = $("input[name='spatial']:checked").val();
        if (spatial != 'every'){
          show &= (aData[9] == spatial);
        }

        return show;
      }
    );
	}

})(jQuery);
