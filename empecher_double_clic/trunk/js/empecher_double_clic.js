$(function() {
	$('form').submit(function() {
		$(this).addClass('js-sending')
			.find('input[type="submit"], button[type="submit"]')
			.addClass('js-button-sending')
			.on('click',function (e) {e.preventDefault();});
	});
});
