$(document).ready(function(){
	$('dl.plan').each(
		function(i){
			$(this).toggleClass('plan on');
		}
	);
	
	$('dt.plan').mouseover(
		function(){
			var x = $(this).children(".point").css("left");
			var largeur = $(this).children(".point").css("width");
			var xbis = parseInt(x.replace(/px/,'')) + parseInt(largeur.replace(/px/,'')) + 10;
			var y = $(this).children(".point").css("top");
			$(this).next().each(
				function(i){
					$(this).show();
					$(this).css("left", xbis+'px');
					$(this).css("top", y);
				}
			);
		}
	);
	
	$('dt.plan').mouseout(
		function(){
			$(this).next().each(
				function(i){
					$(this).hide();
				}
			);
		}
	);
});