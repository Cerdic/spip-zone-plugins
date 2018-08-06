jQuery(function($){
	function init_faq() {
		$('dl.faq > dt').addClass("close").click(function(){
			$(this).toggleClass("close").next().toggle('fast');
			return false;
		}).next().hide();
	}
	init_faq();
	// Relancer lors de rechargements ajax
	if (window.jQuery){
		$(function(){
			onAjaxLoad(init_faq);
		});
	}
	
});