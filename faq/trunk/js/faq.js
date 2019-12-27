jQuery(function($){
	function init_faq() {
		$('dl.faq > dt').addClass("item-faq-closed").click(function(){
			$(this).toggleClass("item-faq-closed").next().toggle('fast');
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