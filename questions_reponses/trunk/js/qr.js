$(document).ready(function(){
	$('dl.qr > dt').addClass("close").click(function(){
		$(this).toggleClass("close").next().toggle('fast');
		return false;
	}).next().hide();
});