(function($){

  if (typeof console == "undefined") {
    window.console = {
      log: function () {}
    };
  }

	// var spreadsheetKey = '0Aiz9YvG1OtuddGRQdUJKMXJoVTRfaURURW01V1BJOGc'; //<--- dev
  var spreadsheetKey = '0ArbpQl2Q0oGRdG5lTGl2LUtTOHduczJaZC1CNy0tRUE'; //<-- official
  var dataSourceUrl = 'https://docs.google.com/spreadsheet/pub?key='+spreadsheetKey+'&pub=1';
  var jsonUrl = 'https://spreadsheets.google.com/feeds/list/'+spreadsheetKey+'/od6/public/values?alt=json';
  var $table, data, $req, options, filters, $container, $intro;
  var ready = false;

  $(document).ready(function(){
    init();
  });

	function init(){

    var $req = $.getJSON(jsonUrl).done(function(json){
      data = [];
      json = json.feed.entry;
      json.shift(); //remove sub-header row in spreadsheet, which is treated as 1st row here
      json.forEach(function(val){
        //this must match table column order, google puts every cell value in $t key IF the value isn't empty
        var ret = [];
        val.gsx$datasetname ? ret.push(val.gsx$datasetname.$t) : ret.push('');
        val.gsx$dataprovider ? ret.push(val.gsx$dataprovider.$t) : ret.push('');
        val.gsx$atmosphericcompositionvariablespprofileccolumn ? ret.push(val.gsx$atmosphericcompositionvariablespprofileccolumn.$t) : ret.push(''); //CH4
        val.gsx$_cpzh4 ? ret.push(val.gsx$_cpzh4.$t) : ret.push(''); //CO
        val.gsx$_cre1l ? ret.push(val.gsx$_cre1l.$t) : ret.push(''); //CO2
        val.gsx$_chk2m ? ret.push(val.gsx$_chk2m.$t) : ret.push(''); //NO2
        val.gsx$_ciyn3 ? ret.push(val.gsx$_ciyn3.$t) : ret.push(''); //O3
        val.gsx$_ckd7g ? ret.push(val.gsx$_ckd7g.$t) : ret.push(''); //SO2
        val.gsx$temporalresolution ? ret.push(val.gsx$temporalresolution.$t) : ret.push('');
        val.gsx$spatialresolution ? ret.push( val.gsx$spatialresolution.$t) : ret.push('');
        // val.gsx$catalog ? ret.push(.$t) : ret.push('');
        val.gsx$dataaccess ? ret.push(val.gsx$dataaccess.$t) : ret.push('');
        val.gsx$_cvlqs ? ret.push(val.gsx$_cvlqs.$t) : ret.push(''); //http
        val.gsx$_cx0b9 ? ret.push(val.gsx$_cx0b9.$t) : ret.push(''); //OPeNDAP
        val.gsx$_d9ney ? ret.push(val.gsx$_d9ney.$t) : ret.push(''); //WCS
        val.gsx$_db1zf ? ret.push(val.gsx$_db1zf.$t) : ret.push(''); //WMS
        val.gsx$notes ? ret.push(val.gsx$notes.$t) : ret.push('');
        val.gsx$metadatasource ? ret.push(val.gsx$metadatasource.$t) : ret.push('');

        data.push(ret);
      });
      // console.log(data);
    });

    $intro = $('.intro');
    $container = $('.table-container');
    var columns = [
      { data: 'gsx$datasetname.$t' },
      { data: 'gsx$dataprovider.$t' },
      { data: 'gsx$atmosphericcompositionvariablespprofileccolumn.$t', className: 'dt-center' },
      { data: 'gsx$_cpzh4.$t', className: 'dt-center' },
      { data: 'gsx$_cre1l.$t', className: 'dt-center' }, //CO2
      { data: 'gsx$_chk2m.$t', className: 'dt-center' }, //no2
      { data: 'gsx$_ciyn3.$t', className: 'dt-center' }, //O3
      { data: 'gsx$_ckd7g.$t', className: 'dt-center' }, //SO2
      { data: 'gsx$temporalresolution.$t', className: 'dt-center' },
      { data: 'gsx$spatialresolution.$t', className: 'dt-center' },
      { data: 'gsx$catalog.$t', className: 'dt-center', visible: false },
      { data: 'gsx$dataaccess.$t', className: 'dt-center' },
      { data: 'gsx$_cvlqs.$t', className: 'dt-center' }, //http
      { data: 'gsx$_cx0b9.$t', className: 'dt-center' },
      { data: 'gsx$_d9ney.$t', className: 'dt-center' },
      { data: 'gsx$_db1zf.$t', className: 'dt-center' },
      { data: 'gsx$notes.$t' },
      { data: 'gsx$metadatasource.$t' }
    ];
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
      'columns': columns,
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
        if (iDataIndex == 0){
          return false; //2nd row in spreadsheet is a sub-header, but google api treats it as a real row, so we always filter it
        }
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
