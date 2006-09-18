var currentIter = 0;
var lastIter = 0;
var maxIter = 0;
var slideShowElement = "";
var slideShowData = new Array();
var slideShowInit = 1;
var slideShowDelay = 9000;
var articleLink = "";

function initSlideShow(element, data) {
	slideShowElement = element;
	slideShowData = data;
	element.style.display="block";
	
	articleLink = document.createElement('a');
	articleLink.className = 'global';
	element.appendChild(articleLink);
	articleLink.href = "";
	
	maxIter = data.length;
	for(i=0;i<data.length;i++)
	{
		var currentImg = document.createElement('img');
		currentImg.setAttribute('id','slideElement' + parseInt(i));
		currentImg.style.position="absolute";
		currentImg.style.left="0px";
		currentImg.style.top="0px";
		currentImg.style.margin="0px";
		currentImg.style.border="0px";
		currentImg.src=data[i][0];
	
		articleLink.appendChild(currentImg);
		currentImg.currentOpacity = new fx.Opacity(currentImg, {duration: 400});
		currentImg.currentOpacity.setOpacity(0);
	}
	
	currentImg.currentOpacity = new fx.Opacity(currentImg, {duration: 400});
	currentImg.currentOpacity.setOpacity(0);
	
	var slideInfoZone = document.createElement('div');
	slideInfoZone.setAttribute('id','slideInfoZone');
	slideInfoZone.combo = new fx.Combo(slideInfoZone);
	slideInfoZone.combo.o.setOpacity(0);
	articleLink.appendChild(slideInfoZone);
	
	doSlideShow();
}

function nextSlideShow() {
	//soundManager.play('select');
	lastIter = currentIter;
	currentIter++;
	if (currentIter >= maxIter)
	{
		currentIter = 0;
		lastIter = maxIter - 1;
	}
	doSlideShow();
	slideShowInit = 0;
}

function doSlideShow() {
	//alert(currentIter);
	if (slideShowInit == 1)
	{
		setTimeout(nextSlideShow,10);
	} else { 
		if (currentIter != 0) {
			$('slideElement' + parseInt(currentIter)).currentOpacity.options.onComplete = function() {
				$('slideElement' + parseInt(lastIter)).currentOpacity.setOpacity(0);
			}
			$('slideElement' + parseInt(currentIter)).currentOpacity.custom(0, 1);
		} else {
			$('slideElement' + parseInt(currentIter)).currentOpacity.setOpacity(1);
			$('slideElement' + parseInt(lastIter)).currentOpacity.custom(1, 0);
		}
		setTimeout(showInfoSlideShow,1000);
		setTimeout(hideInfoSlideShow,slideShowDelay-1000);
		setTimeout(nextSlideShow,slideShowDelay);
	}	
}

function showInfoSlideShow() {
	articleLink.removeChild($('slideInfoZone'));
	var slideInfoZone = document.createElement('div');
	slideInfoZone.setAttribute('id','slideInfoZone');
	slideInfoZone.combo = new fx.Combo(slideInfoZone);
	slideInfoZone.combo.o.setOpacity(0);
	var slideInfoZoneTitle = document.createElement('h2');
	slideInfoZoneTitle.innerHTML = slideShowData[currentIter][2]
	slideInfoZone.appendChild(slideInfoZoneTitle);
	var slideInfoZoneDescription = document.createElement('p');
	slideInfoZoneDescription.innerHTML = slideShowData[currentIter][3]
	slideInfoZone.appendChild(slideInfoZoneDescription);
	articleLink.appendChild(slideInfoZone);
	
	articleLink.href = slideShowData[currentIter][1];
	
	slideInfoZone.combo.o.custom(0, 0.7);
	slideInfoZone.combo.h.custom(0, slideInfoZone.combo.h.el.offsetHeight);
}

function hideInfoSlideShow() {
	$('slideInfoZone').combo.o.custom(0.7, 0);
	//$('slideInfoZone').combo.h.custom(slideInfoZone.combo.h.el.offsetHeight, 0);
}