<?php
/* Plugin timecircles (SPIP 3.1+)
 * (c) 2009-2018 Wim Barelds
 * packaged for SPIP by Loiseau2nuit
 *
 * Add beautiful jquery powered timers to your 
 * website with simple short models
 * 
 * Licence: MIT
 * https://opensource.org/licenses/mit-license.php 
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * load timecircles' css
 **/
function timecircles_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" type="text/css" media="all" href="'.find_in_path('css/timecircles.css').'" />';
	return $flux;
}

/**
 * load timecircles' js in the admin area
 **/
function timecircles_header_prive($flux){
	$flux = timecircles_insert_head_css($flux);
	$flux = timecircles_insert_head($flux);
	return $flux;
} 

/**
 * load timecircles' js on the website
 **/
function timecircles_insert_head($flux){
	$flux .= '<script src="'.find_in_path('js/timecircles.js').'" type="text/javascript"></script>'
	.<<<EOF
<script type="text/javascript">
<!--
jQuery(document).ready(function(){
	$(".DateCountdown").TimeCircles();
	$(".CountDownTimer").TimeCircles({ time: { Days: { show: false }, Hours: { show: false } }});
	$(".PageOpenTimer").TimeCircles();
	var updateTime = function(){
		var date = $("#date").val();
		var time = $("#time").val();
		var datetime = date + ' ' + time + ':00';
		$(".DateCountdown").data('date', datetime).TimeCircles().start();
	}
	$("#date").change(updateTime).keyup(updateTime);
	$("#time").change(updateTime).keyup(updateTime);
	/* Start and stop are methods applied on the public TimeCircles instance */
	$(".startTimer").click(function() {
		$(".CountDownTimer").TimeCircles().start();
	});
	$(".stopTimer").click(function() {
		$(".CountDownTimer").TimeCircles().stop();
	});
	/* Fade in and fade out are examples of how chaining can be done with TimeCircles */
	$(".fadeIn").click(function() {
		$(".PageOpenTimer").fadeIn();
	});
	$(".fadeOut").click(function() {
		$(".PageOpenTimer").fadeOut();
	});
	$(window).on('resize', function(){
    $('.DateCountdown').TimeCircles().rebuild();
		$('.CountDownTimer').TimeCircles().rebuild();
		$('.PageOpenTimer').TimeCircles().rebuild();
	});
});
-->		
</script>
EOF;

	return $flux;
}
