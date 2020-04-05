$(function() {
	$('form').submit(function() {
		if(!$(this).hasClass('bouton_action_post')) {
			$(this).addClass('js-sending')
				.find('input[type="submit"], button[type="submit"]')
				.addClass('js-button-sending')
				.on('click', function(e) {e.preventDefault();});
		}
	});
});
