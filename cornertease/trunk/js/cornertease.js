function cornertease_depli(){
	$("#cornertease_article").fadeIn('slow');
	$("#cornertease_conteneur").animate({ 
        width: '250px',
        height: '320px'
      }, 500, function(){
		 	$("#cornertease_conteneur").removeClass('cornertease_closed');
			$("#cornertease_conteneur").addClass('cornertease_open');
			$("#cornertease_repli").show();
			$("#cornertease_conteneur").unbind('click');
			$("#cornertease_repli").bind('click', cornertease_repli);
		 } );
}

function cornertease_repli(){
	$("#cornertease_article").fadeOut();
	$("#cornertease_conteneur").animate({ 
        width: '54px',
        height: '54px'
      }, 500, function(){
		 	$("#cornertease_repli").hide();
			$("#cornertease_conteneur").addClass('cornertease_closed');
			$("#cornertease_conteneur").removeClass('cornertease_open');
			$("#cornertease_conteneur").bind('click', cornertease_depli);
			$("#cornertease_repli").unbind('click');
		 } );
}

$(document).ready(function(){
	cornertease_cont = '<div id="cornertease_conteneur" class="cornertease_closed"><div id="cornertease_article">'+cornertease_cont+'<div id="cornertease_repli"></div></div><div class="cornertease_clear"></div></div>';
	$("body").append(cornertease_cont);
	var cornertease_test_dur = false;
	switch(cornertease_aff){
		case 'each':
			if(!$.cookie('cornertease_session')){ 
				cornertease_depli(); 
				$.cookie('cornertease_session', true, { expires: null});
				cornertease_test_dur = true;
			}
 			else{
				$("#cornertease_conteneur").bind('click', cornertease_depli);
				/*plié*/
			}
			break;
		case 'first':
			if(!$.cookie('cornertease_cookie')){ 
				cornertease_depli();
				$.cookie('cornertease_cookie', true, { expires: 365});
				cornertease_test_dur = true;
			 }
 			else{
				$("#cornertease_conteneur").bind('click', cornertease_depli);
				/*plié*/
			}
			break;
		case 'allways':
			cornertease_depli();
			cornertease_test_dur = true;
			break;
		case 'never':
			$("#cornertease_conteneur").bind('click', cornertease_depli);
			break;
			/*plié*/
	}
	if(cornertease_dur && cornertease_test_dur){ var cornertease_time = cornertease_dur*1000; setTimeout("cornertease_repli()",cornertease_time); }
});