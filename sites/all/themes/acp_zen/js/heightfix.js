(function($){

	$(function(){
        // workaround for webkit-bug http://stackoverflow.com/a/8468131/348841

		// Returns a function, that, as long as it continues to be invoked, will not
		// be triggered. The function will be called after it stops being called for
		// N milliseconds. If `immediate` is passed, trigger the function on the
		// leading edge, instead of the trailing.
		function debounce(func, wait, immediate) {
			var timeout;
			return function() {
				var context = this, args = arguments;
				var later = function() {
					timeout = null;
					if (!immediate) func.apply(context, args);
				};
				var callNow = immediate && !timeout;
				clearTimeout(timeout);
				timeout = setTimeout(later, wait);
				if (callNow) func.apply(context, args);
			};
		};

        var rz = debounce(function () {
        	if ($('#content').outerHeight() > $('#main').height() || $(document).width() < 480){
	            $('#page').removeClass('heightfix');
	        	$('#main').removeClass('heightfix');
        	}else if ($('#content').outerHeight() < $('#main').height()){
        		$('#page').addClass('heightfix');
        		$('#main').addClass('heightfix');
        	}
        }, 100);

        window.addEventListener('resize', rz);

        rz();
	});


})(jQuery);
