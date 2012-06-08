$(document).ready(function(){
	$('a.chargez_couleur').click(function(){
		var codehexa = $('strong.codehex').html();
		$('input.palette').attr('value',codehexa).focus();
		return false;
	});
});
