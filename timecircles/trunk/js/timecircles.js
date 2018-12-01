/**
	* TimeCircles v1.5.3.15 / SPIP 3.1+
	* by Wim Barelds - https://wimbarelds.nl/
	* brought to SPIP by Loiseau2nuit (2018)
	* https://contrib.spip.net/Loiseau2nuit
	* Licence: MIT
	* 
	* Calling functions brought by lib/timecircles.js
	* with some little default settings.
	* Copy this file in your own '/squelettes/js' 
	* directory to tweak countdowns & timers 
	*
	* See & tweak for yourself custom examples: 
	* http://git.wimbarelds.nl/TimeCircles/ 
	**/
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
	
	// Start and stop are methods applied on the public TimeCircles instance
	$(".startTimer").click(function() {
		$(".CountDownTimer").TimeCircles().start();
	});
	$(".stopTimer").click(function() {
		$(".CountDownTimer").TimeCircles().stop();
	});
	
	// Fade in and fade out are examples of how chaining can be done with TimeCircles
	$(".fadeIn").click(function() {
		$(".PageOpenTimer").fadeIn();
	});
	$(".fadeOut").click(function() {
		$(".PageOpenTimer").fadeOut();
	});
});
