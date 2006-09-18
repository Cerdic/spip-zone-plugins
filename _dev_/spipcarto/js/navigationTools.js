
/*
Scripts for SVG only webmapping application navigation tools
Copyright (C) <2004>  <Andreas Neumann>
Version 1.03, 2005-02-22
neumann@karto.baug.ethz.ch
http://www.carto.net/
http://www.carto.net/neumann/

Credits: numerous people on svgdevelopers@yahoogroups.com

This ECMA script library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library (http://www.carto.net/papers/svg/navigationTools/lesser_gpl.txt); if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

----

current version: 1.02

version history:
1.0 initial version
1.01 added cleanUp() method to map and dragObj objects, some fixes in the documentation
1.02 fixed problems with double clicks, the second click of a double click is now ignored, using the evt.detail property
1.03 introduced timestamp and nrLayerToLoad array for dynamic loading (getUrl)

original document site: http://www.carto.net/papers/svg/navigationTools/
Please contact the author in case you want to use code or ideas commercially.
If you use this code, please include this copyright header, the included full
LGPL 2.1 text and read the terms provided in the LGPL 2.1 license
(http://www.gnu.org/copyleft/lesser.txt)

-------------------------------

Please report bugs and send improvements to neumann@karto.baug.ethz.ch
If you use these scripts, please link to the original (http://www.carto.net/papers/svg/navigationTools/)
somewhere in the source-code-comment or the "about" of your project and give credits, thanks!

*/


/* slider.js */
//slider properties
function slider(x1,y1,value1,x2,y2,value2,startVal,sliderGroupId,sliderColor,visSliderWidth,invisSliderWidth,sliderSymb,functionToCall,mouseMoveBool) {
	this.x1 = x1;
	this.y1 = y1;
	this.value1 = value1;
	this.x2 = x2;
	this.y2 = y2;
	this.value2 = value2;
	this.startVal = startVal;
	this.value = startVal;
	this.sliderGroupId = sliderGroupId;
	this.sliderGroup = document.getElementById(this.sliderGroupId);
	this.sliderColor = sliderColor;
	this.visSliderWidth = visSliderWidth;
	this.invisSliderWidth = invisSliderWidth;
	this.sliderSymb = sliderSymb;
	this.functionToCall = functionToCall;
	this.mouseMoveBool = mouseMoveBool;
	this.length = toPolarDist((this.x2 - this.x1),(this.y2 - this.y1));
	this.direction = toPolarDir((this.x2 - this.x1),(this.y2 - this.y1));
	this.createSlider();
	this.slideStatus = 0;
	this.ctm = getTransformToRootElement(this.sliderGroup).inverse();
}

//create slider
slider.prototype.createSlider = function() {
	var mySliderLine = document.createElementNS(svgNS,"line");
	mySliderLine.setAttributeNS(null,"x1",this.x1);
	mySliderLine.setAttributeNS(null,"y1",this.y1);
	mySliderLine.setAttributeNS(null,"x2",this.x2);
	mySliderLine.setAttributeNS(null,"y2",this.y2);
	mySliderLine.setAttributeNS(null,"stroke",this.sliderColor);
	mySliderLine.setAttributeNS(null,"stroke-width",this.invisSliderWidth);
	mySliderLine.setAttributeNS(null,"opacity","0");
	mySliderLine.setAttributeNS(null,"id",this.sliderGroupId+"_invisibleSliderLine");
	mySliderLine.addEventListener("mousedown",this,false);
	this.sliderGroup.appendChild(mySliderLine);
	mySliderLine = document.createElementNS(svgNS,"line");
	mySliderLine.setAttributeNS(null,"x1",this.x1);
	mySliderLine.setAttributeNS(null,"y1",this.y1);
	mySliderLine.setAttributeNS(null,"x2",this.x2);
	mySliderLine.setAttributeNS(null,"y2",this.y2);
	mySliderLine.setAttributeNS(null,"stroke",this.sliderColor);
	mySliderLine.setAttributeNS(null,"stroke-width",this.visSliderWidth);
	mySliderLine.setAttributeNS(null,"id",this.sliderGroupId+"_visibleSliderLine");
	mySliderLine.setAttributeNS(null,"pointer-events","none");
	this.sliderGroup.appendChild(mySliderLine);
	mySliderSymb = document.createElementNS(svgNS,"use");
	mySliderSymb.setAttributeNS(xlinkNS,"xlink:href","#"+this.sliderSymb);
	var myStartDistance = this.length - ((this.value2 - this.startVal) / (this.value2 - this.value1)) * this.length;
	var myPosX = this.x1 + toRectX(this.direction,myStartDistance);
	var myPosY = this.y1 + toRectY(this.direction,myStartDistance);
	var myTransformString = "translate("+myPosX+","+myPosY+") rotate(" + Math.round(this.direction / Math.PI * 180) + ")";
	mySliderSymb.setAttributeNS(null,"transform",myTransformString);
	mySliderSymb.setAttributeNS(null,"id",this.sliderGroupId+"_sliderSymbol");
	this.sliderGroup.appendChild(mySliderSymb);
}

//remove all slider elements
slider.prototype.removeSlider = function() {
    var mySliderSymb = document.getElementById(this.sliderGroup+"_sliderSymbol");
	this.sliderGroup.removeChild(mySliderSymb);
    var mySliderLine = document.getElementById(this.sliderGroup+"_visibleSliderLine");
	this.sliderGroup.removeChild(mySliderLine);
    var mySliderLine = document.getElementById(this.sliderGroup+"_invisibleSliderLine");
	this.sliderGroup.removeChild(mySliderLine);
}

//handle events
slider.prototype.handleEvent = function(evt) {
	this.drag(evt);
}

//drag slider
slider.prototype.drag = function(evt) {
	var svgroot = document.documentElement;
	if (evt.type == "mousedown" || (evt.type == "mousemove" && this.slideStatus == 1)) {
		var myCoords = myMapApp.calcCoord(evt.clientX,evt.clientY);
		//undo the effect of transformations
		var mySVGPoint = svgroot.createSVGPoint();
		mySVGPoint.x = myCoords["x"];
		mySVGPoint.y = myCoords["y"];
		mySVGPoint = mySVGPoint.matrixTransform(this.ctm);
		myCoords["x"] = mySVGPoint.x;
		myCoords["y"] = mySVGPoint.y;
	
		//draw normal line for first vertex
		var ax = this.x2 - this.x1;
		var ay = this.y2 - this.y1;
		//normal vector 1
		var px1 = parseFloat(this.x1) + ay * -1;
		var py1 = parseFloat(this.y1) + ax;
		//normal vector 2
		var px2 = parseFloat(this.x2) + ay * -1;
		var py2 = parseFloat(this.y2) + ax;
				
		if (leftOfTest(myCoords["x"],myCoords["y"],this.x1,this.y1,px1,py1) == 0 && leftOfTest(myCoords["x"],myCoords["y"],this.x2,this.y2,px2,py2) == 1) {
			if (evt.type == "mousedown" && evt.detail == 1) {
				this.slideStatus = 1;
				svgroot.addEventListener("mousemove",this,false);
				svgroot.addEventListener("mouseup",this,false);
			}
			myNewPos = intersect2lines(this.x1,this.y1,this.x2,this.y2,myCoords["x"],myCoords["y"],myCoords["x"] + ay * -1,myCoords["y"] + ax);
			var myPercentage = toPolarDist(myNewPos['x'] - this.x1,myNewPos['y'] - this.y1) / this.length;
			this.value = this.value1 + myPercentage * (this.value2 - this.value1);
		}
		else {
			var myNewPos = new Array();
			if (leftOfTest(myCoords["x"],myCoords["y"],this.x1,this.y1,px1,py1) == 0 && leftOfTest(myCoords["x"],myCoords["y"],this.x2,this.y2,px2,py2) == 0) {
				//more than max
				this.value = this.value2;
				myNewPos['x'] = this.x2;
				myNewPos['y'] = this.y2;
			}
			if (leftOfTest(myCoords["x"],myCoords["y"],this.x1,this.y1,px1,py1) == 1 && leftOfTest(myCoords["x"],myCoords["y"],this.x2,this.y2,px2,py2) == 1) {
				//less than min
				this.value = this.value1;
				myNewPos['x'] = this.x1;
				myNewPos['y'] = this.y1;
			}
		}
		var myTransformString = "translate("+myNewPos['x']+","+myNewPos['y']+") rotate(" + Math.round(this.direction / Math.PI * 180) + ")";
		document.getElementById(this.sliderGroupId+"_sliderSymbol").setAttributeNS(null,"transform",myTransformString);
		this.getValue();
	}
	if (evt.type == "mouseup" && evt.detail == 1) {
		if (this.slideStatus == 1) {
			this.slideStatus = 2;
			svgroot.removeEventListener("mousemove",this,false);
			svgroot.removeEventListener("mouseup",this,false);
			this.getValue();
		}
		this.slideStatus = 0;
	}
}

//this code is executed, after the slider is released
//you can use switch/if to detect which slider was used (use this.sliderGroup) for that
slider.prototype.getValue = function() {
	if (this.slideStatus == 1 && this.mouseMoveBool == true) {
		if (typeof(this.functionToCall) == "function") {
			this.functionToCall("change",this.sliderGroupId,this.value);
		}
		if (typeof(this.functionToCall) == "object") {
			this.functionToCall.getSliderVal("change",this.sliderGroupId,this.value);
		}
		if (typeof(this.functionToCall) == "string") {
			eval(this.functionToCall+"('change','"+this.sliderGroupId+"',"+this.value+")");
		}
	}
	if (this.slideStatus == 2) {
		if (typeof(this.functionToCall) == "function") {
			this.functionToCall("release",this.sliderGroupId,this.value);
		}
		if (typeof(this.functionToCall) == "object") {
			this.functionToCall.getSliderVal("release",this.sliderGroupId,this.value);
		}
		if (typeof(this.functionToCall) == "string") {
			eval(this.functionToCall+"('release','"+this.sliderGroupId+"',"+this.value+")");
		}
	}
}	

//this is to set the value from other scripts
slider.prototype.setValue = function(value) {
	var myPercAlLine = (value - this.value1) / (this.value2 - this.value1);
	this.value = myPercAlLine;
	var myPosX = this.x1 + toRectX(this.direction,this.length * myPercAlLine);
	var myPosY = this.y1 + toRectY(this.direction,this.length * myPercAlLine);
	var myTransformString = "translate("+myPosX+","+myPosY+") rotate(" + Math.round(this.direction / Math.PI * 180) + ")";
	document.getElementById(this.sliderGroupId+"_sliderSymbol").setAttributeNS(null,"transform",myTransformString);
}	
/* slider.js */




/* mapApp.js */	
//holds data on window size
function mapApp() {
	if (!document.documentElement.getScreenCTM) {
		//initialize ratio
		this.resetFactors();
		//add resize event to document element
		document.documentElement.addEventListener("SVGResize",this,false);
	}
}

mapApp.prototype.handleEvent = function(evt) {
	if (evt.type == "SVGResize") {
		this.resetFactors();
	}
}

mapApp.prototype.resetFactors = function() {
	if (!document.documentElement.getScreenCTM) {
		//case for viewers that don't support .getScreenCTM, such as ASV3
		//calculate ratio and offset values of app window
		var viewBoxArray = document.documentElement.getAttributeNS(null,"viewBox").split(" ");
		var myRatio = viewBoxArray[2]/viewBoxArray[3];
		if ((window.innerWidth/window.innerHeight) > myRatio) { //case window is more wide than myRatio
			this.scaleFactor = viewBoxArray[3] / window.innerHeight;
		}
		else { //case window is more tall than myRatio
			this.scaleFactor = viewBoxArray[2] / window.innerWidth;
		}
		this.offsetX = (window.innerWidth - viewBoxArray[2] * 1 / this.scaleFactor) / 2;
		this.offsetY = (window.innerHeight - viewBoxArray[3] * 1 / this.scaleFactor) / 2;
	}
}

mapApp.prototype.calcCoord = function(coordx,coordy) {
	var coords = new Array();
	if (!document.documentElement.getScreenCTM) {
		//case ASV3 a. Corel
		coords["x"] = (coordx  - this.offsetX) * this.scaleFactor;
		coords["y"] = (coordy - this.offsetY) * this.scaleFactor;
	}
	else {
		matrix=document.documentElement.getScreenCTM();
		coords["x"]= matrix.inverse().a*coordx+matrix.inverse().c*coordy+matrix.inverse().e;
		coords["y"]= matrix.inverse().b*coordx+matrix.inverse().d*coordy+matrix.inverse().f;
	}
	return coords;
}
/* mapApp.js */	
	
		
/* checkbox.js */
function checkBoxScript(evt,myLayer) { //checkBox for toggling layers an contextMenue
	var myLayerObj = document.getElementById(myLayer);
	var myCheckCrossObj = document.getElementById("checkCross"+myLayer);
	var myCheckCrossVisibility = myCheckCrossObj.getAttributeNS(null,"visibility");
	if (evt.type == "click" && evt.detail == 1) {
		if (myCheckCrossVisibility == "visible") {
			myLayerObj.setAttributeNS(null,"visibility","hidden");
			myCheckCrossObj.setAttributeNS(null,"visibility","hidden");
			//you can do if/else or switch statements to set different actions on activating a checkbox here
			//myLayer holds the currentLayer name
			/*if (myLayer == "DOQ") {
				getOrthoImage();
			}*/
		}
		else {
			myLayerObj.setAttributeNS(null,"visibility","visible");
			myCheckCrossObj.setAttributeNS(null,"visibility","visible");
		}
	}
}
/* checkbox.js */	
		
