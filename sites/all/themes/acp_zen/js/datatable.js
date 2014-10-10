(function($){

  if (typeof console == "undefined") {
    window.console = {
      log: function () {}
    };
  }

	// var dataSourceUrl = 'https://spreadsheets.google.com/tq?key=rh_6pF1K_XsruwVr_doofvw&pub=1';
	// var dataSourceUrl = 'https://spreadsheets.google.com/tq?key=tneLiv-KS8wns2Zd-B7--EA&amp;sheet=od6';
  // var spreadsheetKey = 'tneLiv-KS8wns2Zd-B7--EA';
  var spreadsheetKey = '0Aiz9YvG1OtuddGRQdUJKMXJoVTRfaURURW01V1BJOGc'; //<---
	var dataSourceUrl = 'https://docs.google.com/spreadsheet/pub?key='+spreadsheetKey+'&pub=1';
  var jsonUrl = 'https://spreadsheets.google.com/feeds/list/'+spreadsheetKey+'/od6/public/values?alt=json';
	var $table, options, filters, $container, $intro;
  var ready = false;

  $(document).ready(function(){
    init();
  });

	function init(){
    $intro = $('.intro');
    $container = $('.table-container');
    // .hide();
    $table = $('#table').DataTable({
      'ajax' : {
        url: jsonUrl,
        dataSrc: 'feed.entry'
      },
      'columnDefs': [
        {
          "targets" : [10],
          "visible" : false,
          "searchable" : false
        }
      ],
      'columns': [
        { data: 'gsx$datasetname.$t' },
        { data: 'gsx$dataprovider.$t' },
        { data: 'gsx$ch4.$t', className: 'dt-center' },
        { data: 'gsx$co.$t', className: 'dt-center' },
        { data: 'gsx$co2.$t', className: 'dt-center' },
        { data: 'gsx$no2.$t', className: 'dt-center' },
        { data: 'gsx$o3.$t', className: 'dt-center' },
        { data: 'gsx$so2.$t', className: 'dt-center' },
        { data: 'gsx$temporalresolution.$t', className: 'dt-center' },
        { data: 'gsx$spatialresolution.$t', className: 'dt-center' },
        { data: 'gsx$catalog.$t', className: 'dt-center', visible: false },
        { data: 'gsx$ftp.$t', className: 'dt-center' },
        { data: 'gsx$http.$t', className: 'dt-center' },
        { data: 'gsx$opendap.$t', className: 'dt-center' },
        { data: 'gsx$wcs.$t', className: 'dt-center' },
        { data: 'gsx$wms.$t', className: 'dt-center' },
        { data: 'gsx$notes.$t' },
        { data: 'gsx$metadatasource.$t' },
      ],
      scrollX : true,
      stateSave: true,
      pageLength: 5,
      lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
      language: {
        // loadingRecords: '<div class="spinner"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div> Loading...'
        loadingRecords: 'No matching records found.'
      }
    });

    $table.on('xhr.dt', function(e, settings, json){
      ready = true;
    });

    $('#datatable-options input').on('change', function(){
      if (ready){
        $intro.hide();
        // $container.show();
        $table.draw();
      }else{
        // $('#table').dataTable().fnSettings().oLanguage.sloadingRecords = "testing...";
        $('.dataTables_empty').html('<div class="spinner"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div><div>Just a moment, loading records...</div>');
        $table.on('xhr.dt', function(){
          $intro.hide();
          // $container.show();
          $table.draw();
        });
      }
    });
    // new $.fn.DataTable.FixedColumns( $table, { "leftColumns": 1 } );

    $.fn.dataTableExt.afnFiltering.push(
      function( oSettings, aData, iDataIndex ) {
        // var show = $params.length === 0; //default to show this row if no active filters
        var show = false;

        $('.params input:checked').each(function(){
          // if ($(this).prop('checked')){
          //   show &= aData[this.value] !== '';
          // }else{
          //   show &= aData[this.value] === '';
          // }
          if ($(this).prop('checked') && aData[this.value] !== ''){
            show = true;
          }
          // show &= $(this).prop('checked') && aData[this.value] !== '';
          // if ($(this).attr('checked')){
          //   if (aData[this.value])
          // }
          // if (aData[this.value] === ''){
          //   show = false;
          // }
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
