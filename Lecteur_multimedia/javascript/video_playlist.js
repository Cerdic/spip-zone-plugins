var myListener = new Object();

/**
 * Initialisation
 */
myListener.onInit = function()
{

};
/**
 * onClick event on the video
 */
myListener.onClick = function()
{
	var total = document.getElementById("info_click").innerHTML;
	document.getElementById("info_click").innerHTML = Number(total)+1;
};
/**
 * onKeyUp event on the video
 */
myListener.onKeyUp = function(pKey)
{
	document.getElementById("info_key").innerHTML = pKey;
};
/**
 * onComplete event
 */
myListener.onFinished = function()
{
	video_next();
};
/**
 * Update
 */
myListener.onUpdate = function()
{
	/*
	document.getElementById("info_playing").innerHTML = this.isPlaying;
	document.getElementById("info_url").innerHTML = this.url;
	document.getElementById("info_volume").innerHTML = this.volume;
	document.getElementById("info_position").innerHTML = this.position;
	document.getElementById("info_duration").innerHTML = this.duration;
	// prevoir un preload de la prochaine video
	document.getElementById("info_buffer").innerHTML = this.bufferLength + "/" + this.bufferTime;
	document.getElementById("info_bytes").innerHTML = this.bytesLoaded + "/" + this.bytesTotal + " (" + this.bytesPercent + "%)";
	*/
	var timer = this.bytesPercent ;

	$("#vloading").css({width:Math.round(timer) +"%"});

	var isPlaying = (this.isPlaying == "true");
	isVideoPlaying = "true" ;
	document.getElementById("playerplay").style.display = (isPlaying)?"none":"block";
	document.getElementById("playerpause").style.display = (isPlaying)?"block":"none";

	var timer2 = this.position / this.duration * 100 ;
	$("#vposition").css({width:Math.round(timer2) +"%"});
};

function getFlashObject()
{
	return document.getElementById("myFlash");
}
