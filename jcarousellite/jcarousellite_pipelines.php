<?php


function jcarousellite_insert_head($flux){
	$flux .= '<script type="text/javascript" src="'.find_in_path('js/jcarousellite.js').'"></script>';
	$flux .= '<script type="text/javascript">
		(function ($) {
	$(document).ready(function(){
			/*
			* 	Modèle <jcarousel_meme_rubrique>
			*/
			$(".SliderArticlesMemeRubrique .jCarouselLite").jCarouselLite({
				 btnNext: ".articleSlider .next",
			    btnPrev: ".articleSlider .prev",
			    circular: false
			});
			
				});
				
			}(jQuery));
				
			</script>';

	return $flux;
}

function jcarousellite_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/jcarousellite.css').'" />';

	return $flux;
}

?>