$(function() {
	$('form').submit(function(event) {
		$(this).find('input[type="submit"], button[type="submit"]').addClass('js-sending').attr('disabled', 'disabled');
	});
});
