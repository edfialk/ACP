(function ($, Drupal, window, document, undefined) {

Drupal.behaviors.login_menu_behavior = {
	attach: function(context, settings) {
		$('#user-login-link', context).click(function(e){
			e.preventDefault();
			$('#user-login-content', context).toggleClass('hidden');
			window.setTimeout(function(){
				$('#edit-name', context).focus();
			}, 200);
		});
	}
}

})(jQuery, Drupal, this, this.document);
