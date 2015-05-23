$(document).ready(function(){
	$('.renseignements').hide();
	$('.resume-titre').click(function() {
		$(this).next().slideToggle('fast');
	});
});