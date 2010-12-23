/* ZOOMER */

jQuery(document).ready(function(){
	
	function zooming(){
	if ($('.zoomer img').length) {
	$('.zoomer img').addimagezoom({
		zoomrange: [3, 20],
		magnifiersize: [300,300],
		magnifierpos: 'right'
		//explic:'pouet'
	})
	.parent('.zoomer').hover(
	//plus joli on bascule le title sur le alt	
	function () {
		var originalTitle = $(this).attr('title');
		if ($(this).attr('title').length) {
		$(this).find('img').attr("alt", originalTitle)
		$(this).attr("title", '')
		.find('img').attr("title", '');
		var tellme = $(".tellzoom").txt();
		alert("grrr"+tellme);
	
		}
      	}
      	).click(
      	function () {
      		var titre = $(this).find('img').attr('alt');
		$(this).attr("title", titre);
    	}
      	);
	
	$('.zoomer span').hide(); 

	}
	}
	zooming();
	onAjaxLoad(zooming);
});
