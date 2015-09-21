(function($){

  if (typeof console == "undefined") {
    window.console = {
      log: function () {}
    };
  }

  var spreadsheetKey = '0ArbpQl2Q0oGRdG5lTGl2LUtTOHduczJaZC1CNy0tRUE'; //<-- official
  var dataSourceUrl = 'https://docs.google.com/spreadsheet/pub?key='+spreadsheetKey+'&pub=1';
  var jsonUrl = 'http://54.191.97.139/goog.php';
  var $table, $intro, $temporal, $spatial, temporals, spatials;
  var ready = false;

  $(document).ready(function(){
    init();
  });

	function init(){

    $intro = $('.intro');
    $temporal = $('.temporal');
    $spatial = $('.spatial');

    $table = $('#table').DataTable({
      'ajax' : function(data, callback, settings){
        var self = this;
        $.getJSON(jsonUrl, function(json){
          if (json.status == 'error'){
            $('.alerts').html("<div class='alert alert-danger'>The server returned an error: " + json.message + "</div>");
          }else if (json.status == 'warning'){
            var $alert = $("<div class='alert alert-warning alert-dismissable'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button></div>");
            json.message.forEach(function(msg){
              $alert.append("<p>"+msg+"</p>");
            });
            $('.alerts').html($alert);
          }

          temporals = json.temporals;
          temporals.sort();
          spatials = json.spatials;
          spatials.sort();

          callback(json);
          self.trigger('xhr.dt');
        }).fail(function(jqxhr, text, error){
            $('.alerts').html("<div class='alert alert-danger'>Request failed: " + text + "</div>");
        });
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

    $table.on('length.dt', function(e, settings, len){
      var event = new CustomEvent('resize');
      window.dispatchEvent(event);
    });

    var inputHandler = function(){
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
    }

    $('#datatable-options input').on('change', inputHandler);

    $('.showMore', $temporal).click(function(e){
      e.preventDefault();
      var $this = $(this);
      var go = function(){
        $this.text() == 'More' ? $this.html('Less') : $this.html('More');

        if ($('.extra', $temporal).length > 0){
          $('.extra', $temporal).toggleClass('hidden');
        }else{
          $('#datatable-options input').off('change');
          var current = [];
          $('input', $temporal).each(function(i, input){
            current.push(input.value.toLowerCase());
          });
          temporals.forEach(function(temporal){
            if (current.indexOf(temporal.toLowerCase()) == -1){
              var $opt = $("<div class='radio extra'><label><input type='radio' name='temporal' value='"+temporal+"'>"+temporal+"</label></div>");
              $this.before($opt);
            }
          });
          $('#datatable-options input').on('change', inputHandler);
        }
      }

      ready ? go() : $table.on('xhr.dt', go);

    });
    $('.showMore', $spatial).click(function(e){
      e.preventDefault();
      var $this = $(this);
      var go = function(){
        $this.text() == 'More' ? $this.html('Less') : $this.html('More');

        if ($('.extra', $spatial).length > 0){
          $('.extra', $spatial).toggleClass('hidden');
        }else{
          $('#datatable-options input').off('change');
          var current = [];
          $('input', $spatial).each(function(i, input){
            current.push(input.value.toLowerCase());
          });
          spatials.forEach(function(spatial){
            if (current.indexOf(spatial.toLowerCase()) == -1){
              var $opt = $("<div class='radio extra'><label><input type='radio' name='spatial' value='"+spatial+"'>"+spatial+"</label></div>");
              $this.before($opt);
            }
          });
          $('#datatable-options input').on('change', inputHandler);
        }
      }
      ready ? go() : $table.on('xhr.dt', go);
    });

    //this is the table filtering function run on each row, return true to show the row, false to hide it
    $.fn.dataTableExt.afnFiltering.push(
      function( oSettings, aData, iDataIndex ) {

        var show = false; //default to hide row
        var $params = $('.params input:checked');
        var $services = $('.services input:checked');

        $params.each(function(){
          if ($(this).prop('checked') && aData[this.value] !== ''){
            show = true;
          }
        });

        $services.each(function(){
          if ($params.length == 0){
            //if only services are checked, show all rows with any of those services
            if (aData[this.value] != ''){
              show = true;
            }
          }else{
            //if param is checked, filter param rows by checked services
            show &= (aData[this.value] !== '');
          }
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
