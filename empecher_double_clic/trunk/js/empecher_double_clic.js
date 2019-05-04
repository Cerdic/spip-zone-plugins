$(function() {
	$('form').submit(function(event) {
		$(this).addClass('js-sending').find('input[type="submit"], button[type="submit"]').attr('disabled', 'disabled');
	});
});
