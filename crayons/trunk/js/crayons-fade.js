
// Gestion du Yellow Fade (fonctionnalite optionnelle)
function easeInOut(minValue,maxValue,totalSteps,actualStep,powr) {
	var delta = maxValue - minValue;
	var stepp = minValue+(Math.pow(((1 / totalSteps)*actualStep),powr)*delta);
	return Math.ceil(stepp)
};

function doBGFade(elem,startRGB,endRGB,finalColor,steps,intervals,powr) {
	if (elem.bgFadeInt) window.clearInterval(elem.bgFadeInt);
	var actStep = 0;
	elem.bgFadeInt = window.setInterval(
		function() {
			elem.style.backgroundColor = "rgb("+
				easeInOut(startRGB[0],endRGB[0],steps,actStep,powr)+","+
				easeInOut(startRGB[1],endRGB[1],steps,actStep,powr)+","+
				easeInOut(startRGB[2],endRGB[2],steps,actStep,powr)+")";
			actStep++;
			if (actStep > steps) {
			elem.style.backgroundColor = finalColor;
			window.clearInterval(elem.bgFadeInt);
			}
		}
		,intervals)
};

function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft;
		curtop = obj.offsetTop;
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		}
	}
	return [curleft,curtop];
};

// demarrage crayons-fade
jQuery(document).ready(function() {
	if (configCrayons.cfg.yellow_fade) {
		// Activer le Yellow Fade pour les elements editables
		jQuery(document).on('mouseenter', '.crayon-autorise', function(){
			doBGFade(this,[255,255,180],[255,255,255],'transparent',40,20,4);
		});
	}
});
