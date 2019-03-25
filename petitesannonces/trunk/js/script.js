jQuery(function($){
	
	// un accordeon et son interrupteur
// 	$('.accordeon').css({display:'none'});
	$('.interrupteur').click(function(){
		$(this).toggleClass('on');
		$(this).next('.accordeon').toggleClass('ouvert');
		return false;
	});
	
});