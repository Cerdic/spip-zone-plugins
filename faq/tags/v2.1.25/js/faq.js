$(document).ready(function(){
	$('dl.faq > dt').addClass("close").click(function(){
		$(this).toggleClass("close").next().toggle('fast');
		return false;
	}).next().hide();
});