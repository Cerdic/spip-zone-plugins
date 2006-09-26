/*
Scripts for SVG only webmapping application navigation tools
Copyright (C) <2004>  <Andreas Neumann>
Version 1.02, 2005-02-21
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

//constructor: holds data on map and initializes various references
function map(mapName,maxWidth,minWidth,zoomFact,nrDecimals,units,showCoords,coordXId,coordYId,dynamicLayers,digiLayers,activeDigiLayer) {
	this.mapName = mapName; //id of svg element containing the map geometry
	this.mapSVG = document.getElementById(this.mapName); //reference to nested SVG element holding the map-graphics
	this.mainMapGroup = document.getElementById(this.mapName+"Group"); //group within mainmap - to be transformed when panning manually
	this.dynamicLayers = dynamicLayers; //an associative array holding ids of values that are loaded from the web server dynamically (.getUrl())
	this.nrLayerToLoad = 0; //statusVariable to indicate how many layers are still to load
	this.maxWidth = maxWidth; //max map width
	this.minWidth = minWidth; //min map width, after zooming in
	this.zoomFact = zoomFact; //ratio to zoom in or out in relation to previous viewBox
	this.digiLayers = digiLayers; //references to digiLayers (for digitizing tools)
	this.activeDigiLayer = activeDigiLayer; //active Digi Layer, key is final group id where geometry should be copied to after digitizing
	this.pixXOffset = parseFloat(this.mapSVG.getAttributeNS(null,"x")); //offset from left margin of outer viewBox
	this.pixYOffset = parseFloat(this.mapSVG.getAttributeNS(null,"y")); //offset from top margin of outer viewBox
	var viewBoxArray = this.mapSVG.getAttributeNS(null,"viewBox").split(" ");
	this.curxOrig = parseFloat(viewBoxArray[0]); //holds the current xOrig
	this.curyOrig = parseFloat(viewBoxArray[1]); //holds the current yOrig
	this.curWidth = parseFloat(viewBoxArray[2]); //holds the current map width
	this.curHeight = parseFloat(viewBoxArray[3]); //holds the current map height
	this.pixWidth = parseFloat(this.mapSVG.getAttributeNS(null,"width")); //holds width of the map in pixel coordinates
	this.pixHeight = parseFloat(this.mapSVG.getAttributeNS(null,"height")); //holds height of the map in pixel coordinates
	this.pixSize = this.curWidth / this.pixWidth; //size of a screen pixel in map units
	this.zoomVal = this.maxWidth / this.curWidth * 100; //zoomVal in relation to initial zoom
	this.nrDecimals = nrDecimals; //nr of decimal places to be displayed for show coordinates or accuracy when working with digitizing
	this.navStatus = "false"; //to reset status in navigation
	this.units = units; //holds a string with map units, e.g. "m", alternatively String.fromCharCode(176) for degrees
	this.showCoords = showCoords;
	//initialize coordinate display if showCoords == true
	if (this.showCoords == true) {
		//add event listener for coordinate display
		this.mapSVG.addEventListener("mousemove",this,false);
		if (typeof(coordXId) == "string") {
			this.coordXText = document.getElementById(coordXId).firstChild;
		}
		else {
			alert("Error: coordXId needs to be an id of type string");
		}
		if (typeof(coordYId) == "string") {
			this.coordYText = document.getElementById(coordYId).firstChild;
		}
		else {
			alert("Error: coordYId needs to be an id of type string");
		}
	}
	//create background-element to receive events for showing coordinates
	var myBackgroundRect = document.createElementNS(svgNS,"rect");
	myBackgroundRect.setAttributeNS(null,"x",this.curxOrig);
	myBackgroundRect.setAttributeNS(null,"y",this.curyOrig);
	myBackgroundRect.setAttributeNS(null,"width",this.curWidth);
	myBackgroundRect.setAttributeNS(null,"height",this.curHeight);
	myBackgroundRect.setAttributeNS(null,"fill","white");
	myBackgroundRect.setAttributeNS(null,"stroke","none");
	myBackgroundRect.setAttributeNS(null,"id","mapBackgroundRect");
	this.mainMapGroup.insertBefore(myBackgroundRect,this.mainMapGroup.firstChild);
}

//resets viewBox of main map after zooming and panning
map.prototype.newViewBox = function(refRectId) {
	this.checkAndRemoveTempLayer();
	var myRefRect = document.getElementById(refRectId);
	this.curxOrig = parseFloat(myRefRect.getAttributeNS(null,"x"));
	this.curyOrig = parseFloat(myRefRect.getAttributeNS(null,"y"));
	this.curWidth = parseFloat(myRefRect.getAttributeNS(null,"width"));
	this.curHeight = parseFloat(myRefRect.getAttributeNS(null,"height"));
	var myViewBoxString = this.curxOrig + " " + this.curyOrig + " " + this.curWidth + " " + this.curHeight;
	this.pixSize = this.curWidth / this.pixWidth;
	this.zoomVal = this.maxWidth / this.curWidth * 100;
	this.mapSVG.setAttributeNS(null,"viewBox",myViewBoxString);
	myZoomSlider.setValue(this.curWidth);
	loadProjectSpecific();
}

map.prototype.setNewViewBox = function(xmin,ymin,xmax,ymax) {
	//check if within constraints
	if (xmin < myRefMapDragger.constrXmin) {
		xmin = myRefMapDragger.constrXmin;
	}
	if (xmax > myRefMapDragger.constrXmax) {
		xmax = myRefMapDragger.constrXmin;
	}
	if (ymin < (myRefMapDragger.constrYmax * -1)) {
		ymin = myRefMapDragger.constrYmax * -1;
	}
	if (ymax > (myRefMapDragger.constrYmin * -1)) {
		ymax = myRefMapDragger.constrYmin * -1;
	}
	
	var origWidth = xmax - xmin;
	var origHeight = ymax - ymin;
	var myRatio = this.curWidth/this.curHeight;
	
	if (origWidth / origHeight > myRatio) { //case rect is more wide than ratio
		var newWidth = origWidth;
		var newHeight = origWidth * 1 / myRatio;
		ymin = (ymax + (newHeight - origHeight) / 2) * -1;
	}
	else {
		var newHeight = origHeight;
		var newWidth = newHeight * myRatio;
		xmin = xmin - (newWidth - origWidth) / 2;
		ymin = ymax * -1;
	}
	//check if within constraints
	if (xmin < myRefMapDragger.constrXmin) {
		xmin = myRefMapDragger.constrXmin;
	}
	if (ymin < myRefMapDragger.constrYmin) {
		ymin = myRefMapDragger.constrYmin;
	}
	if ((xmin + newWidth) > myRefMapDragger.constrXmax) {
		xmin = myRefMapDragger.constrXmax - newWidth;
	}
	if ((ymin + newHeight) > myRefMapDragger.constrYmax) {
		ymin = myRefMapDragger.constrYmax - newHeight;
	}		
	myRefMapDragger.newView(xmin,ymin,newWidth,newHeight);
	this.newViewBox(myRefMapDragger.dragId);
}

//handles events associated with navigation
map.prototype.handleEvent = function(evt) {
	var callerId = evt.currentTarget.getAttributeNS(null,"id");
	if (callerId.match(/\bzoomBgRectManual/)) {
		this.zoomManDragRect(evt);
	}
	if (callerId.match(/\bzoomBgRectRecenter/)) {
		this.recenterFinally(evt);
	}
	if (callerId.match(/\bbgPanManual/)) {
		this.panManualFinally(evt);
	}
	if (callerId == "mainMap" && evt.type == "mousemove") {
		this.showCoordinates(evt);
	}
}

//calcs coordinates; relies on myMapApp to handle different window sizes and resizing of windows
map.prototype.calcCoord = function(coordx,coordy) {
	var coords = myMapApp.calcCoord(coordx,coordy);
	var mapCoords = new Array();
	mapCoords["x"] = this.curxOrig + (coords["x"] - this.pixXOffset) * this.pixSize;
	mapCoords["y"] = (this.curyOrig + (coords["y"] - this.pixYOffset) * this.pixSize);
	return mapCoords;
}

//displays x and y coordinates in two separate text elements
map.prototype.showCoordinates = function(evt) {
	var mapCoords = this.calcCoord(evt.clientX,evt.clientY);
	this.coordXText.nodeValue = "X: " + formatNumberString(mapCoords["x"].toFixed(this.nrDecimals)) + this.units;
	this.coordYText.nodeValue = "Y: " + formatNumberString((mapCoords["y"] * -1).toFixed(this.nrDecimals)) + this.units;
}

//chekcs for and removes temporary rectangle objects
map.prototype.checkAndRemoveTempLayer = function() {
	if (this.navStatus != "false") {
		if (this.navStatus == "zoomManual") {
			this.mainMapGroup.removeChild(document.getElementById("zoomBgRectManual"));	
		}
		if (this.navStatus == "recenter") {
			this.mainMapGroup.removeChild(document.getElementById("zoomBgRectRecenter"));	
		}
		if (this.navStatus.match(/\bpanmanual/)) {
			this.mainMapGroup.removeChild(document.getElementById("bgPanManual"));	
		}
		this.navStatus = "false";
	}
}

//starts manual zooming mode
map.prototype.zoomManual = function(evt) {
	if (Math.round(myMainMap.curWidth) > myMainMap.minWidth && evt.detail == 1) {
		this.checkAndRemoveTempLayer();
		this.navStatus = "zoomManual";
		var zoomBgRect = document.createElementNS(svgNS,"rect");
		zoomBgRect.setAttributeNS(null,"x",this.curxOrig);
		zoomBgRect.setAttributeNS(null,"y",this.curyOrig);
		zoomBgRect.setAttributeNS(null,"width",this.curWidth);
		zoomBgRect.setAttributeNS(null,"height",this.curHeight);
		zoomBgRect.setAttributeNS(null,"fill","white");
		zoomBgRect.setAttributeNS(null,"fill-opacity","0.3");
		zoomBgRect.setAttributeNS(null,"stroke","none");
		zoomBgRect.setAttributeNS(null,"id","zoomBgRectManual");
		zoomBgRect.addEventListener("mousedown",this, false);
		zoomBgRect.addEventListener("mousemove",this, false);
		zoomBgRect.addEventListener("mouseup",this, false);
		zoomBgRect.addEventListener("mouseout",this,false);
		this.mainMapGroup.appendChild(zoomBgRect);
		statusChange("Click and drag rectangle (upper left to lower right) for new map extent.");
	}
}

//manages manual zooming by drawing a rectangle
map.prototype.zoomManDragRect = function(evt) {
	var mapCoords = this.calcCoord(evt.clientX,evt.clientY);
	var myX = mapCoords["x"];
	var myY = mapCoords["y"];
	var myYXFact = this.curHeight / this.curWidth;
	if (evt.type == "mousedown") {
		this.manZoomActive = 1;
		this.zoomRect = document.createElementNS(svgNS,"rect");
		var myLineWidth = this.curWidth * 0.003;
		this.zoomRect.setAttributeNS(null,"id","zoomRect");
		this.zoomRect.setAttributeNS(null,"style","fill:darksalmon;fill-opacity:0.5;stroke:dimgray;stroke-width:"+myLineWidth+";stroke-dasharray:"+(myLineWidth*3)+","+myLineWidth);
		this.zoomRect.setAttributeNS(null,"pointer-events","none");
		this.zoomRect.setAttributeNS(null,"x",myX);
		this.zoomRect.setAttributeNS(null,"y",myY);
		this.zoomRect.setAttributeNS(null,"width",this.minWidth);
		this.zoomRect.setAttributeNS(null,"height",this.minWidth * myYXFact);
		this.mainMapGroup.appendChild(this.zoomRect);
		this.zoomRectOrigX = myX;
		this.zoomRectOrigY = myY;
	}
	if (evt.type == "mousemove" && this.manZoomActive == 1) {
		var myZoomWidth = myX - this.zoomRectOrigX;
		if (myZoomWidth < 0) {
			if (Math.abs(myZoomWidth) < this.minWidth) {
				this.zoomRect.setAttributeNS(null,"x",this.zoomRectOrigX - this.minWidth);
				this.zoomRect.setAttributeNS(null,"y",this.zoomRectOrigY - this.minWidth * myYXFact);
				this.zoomRect.setAttributeNS(null,"width",this.minWidth);
				this.zoomRect.setAttributeNS(null,"height",this.minWidth * myYXFact);
			}
			else {
				this.zoomRect.setAttributeNS(null,"x",myX);
				this.zoomRect.setAttributeNS(null,"y",this.zoomRectOrigY - Math.abs(myZoomWidth) * myYXFact);
				this.zoomRect.setAttributeNS(null,"width",Math.abs(myZoomWidth));
				this.zoomRect.setAttributeNS(null,"height",Math.abs(myZoomWidth) * myYXFact);			
			}
		}
		else {
			this.zoomRect.setAttributeNS(null,"x",this.zoomRectOrigX);
			this.zoomRect.setAttributeNS(null,"y",this.zoomRectOrigY);
			if (myZoomWidth < this.minWidth) {
				this.zoomRect.setAttributeNS(null,"width",this.minWidth);
				this.zoomRect.setAttributeNS(null,"height",this.minWidth * myYXFact);		
			}
			else {
				this.zoomRect.setAttributeNS(null,"width",myZoomWidth);
				this.zoomRect.setAttributeNS(null,"height",myZoomWidth * myYXFact);
			}
		}
	}
	if ((evt.type == "mouseup" || evt.type == "mouseout") && this.manZoomActive == 1) {
		this.manZoomActive = 0;
		this.navStatus = "false";
		if (parseFloat(this.zoomRect.getAttributeNS(null,"width")) > this.curWidth * 0.02) {
			myRefMapDragger.newView(parseFloat(this.zoomRect.getAttributeNS(null,"x")),parseFloat(this.zoomRect.getAttributeNS(null,"y")),parseFloat(this.zoomRect.getAttributeNS(null,"width")),parseFloat(this.zoomRect.getAttributeNS(null,"height")));
			this.newViewBox(myRefMapDragger.dragId);
		}
		this.mainMapGroup.removeChild(this.zoomRect);
		this.mainMapGroup.removeChild(document.getElementById("zoomBgRectManual"));
		statusChange("map ready");
	}
}

//initializes recentering mode
map.prototype.recenter = function(evt) {
	if (evt.detail == 1) {
		this.checkAndRemoveTempLayer();
		this.navStatus = "recenter";
		var zoomBgRect = document.createElementNS(svgNS,"rect");
		zoomBgRect.setAttributeNS(null,"x",this.curxOrig);
		zoomBgRect.setAttributeNS(null,"y",this.curyOrig);
		zoomBgRect.setAttributeNS(null,"width",this.curWidth);
		zoomBgRect.setAttributeNS(null,"height",this.curHeight);
		zoomBgRect.setAttributeNS(null,"fill","white");
		zoomBgRect.setAttributeNS(null,"fill-opacity","0.3");
		zoomBgRect.setAttributeNS(null,"stroke","none");
		zoomBgRect.setAttributeNS(null,"id","zoomBgRectRecenter");
		zoomBgRect.addEventListener("click",this, false);
		this.mainMapGroup.appendChild(zoomBgRect);
		statusChange("Click in map to define new map center.");
	}
}

//finishes recentering after mouse-click
map.prototype.recenterFinally = function(evt) {
	if (evt.type == "click") {
		var mapCoords = this.calcCoord(evt.clientX,evt.clientY);
		var myX = mapCoords["x"];
		var myY = mapCoords["y"];
		var myNewX = myX - this.curWidth / 2;
		var myNewY = myY - this.curHeight / 2;
		
		//check if within constraints
		if (myNewX < myRefMapDragger.constrXmin) {
			myNewX = myRefMapDragger.constrXmin;
		}
		if (myNewY < myRefMapDragger.constrYmin) {
			myNewY = myRefMapDragger.constrYmin;
		}
		if ((myNewX + this.curWidth) > myRefMapDragger.constrXmax) {
			myNewX = myRefMapDragger.constrXmax - this.curWidth;
		}
		if ((myNewY + this.curHeight) > myRefMapDragger.constrYmax) {
			myNewY = myRefMapDragger.constrYmax - this.curHeight;
		}
		myRefMapDragger.newView(myNewX,myNewY,this.curWidth,this.curHeight);
		this.navStatus = "false";
		this.newViewBox(myRefMapDragger.dragId);
			
		this.mainMapGroup.removeChild(document.getElementById("zoomBgRectRecenter"));
		statusChange("map ready");
	}
}

//initializes manual panning
map.prototype.panManual = function(evt) {
	if (evt.detail == 1) {
		this.checkAndRemoveTempLayer();
		this.navStatus = "panmanual";
		//draw a temporary over whole map to avoid interference with existing events
		var myBackgroundRect = document.getElementById("mapBackgroundRect");
		var panManRect = document.createElementNS(svgNS,"rect");
		panManRect.setAttributeNS(null,"x",myBackgroundRect.getAttributeNS(null,"x"));
		panManRect.setAttributeNS(null,"y",myBackgroundRect.getAttributeNS(null,"y"));
		panManRect.setAttributeNS(null,"width",myBackgroundRect.getAttributeNS(null,"width"));
		panManRect.setAttributeNS(null,"height",myBackgroundRect.getAttributeNS(null,"height"));
		panManRect.setAttributeNS(null,"fill","white");
		panManRect.setAttributeNS(null,"fill-opacity","0.3");
		panManRect.setAttributeNS(null,"stroke","none");
		panManRect.setAttributeNS(null,"id","bgPanManual");
		panManRect.addEventListener("mousedown",this, false);
		panManRect.addEventListener("mousemove",this, false);
		panManRect.addEventListener("mouseup",this, false);
		panManRect.addEventListener("mouseout",this, false);
		this.mainMapGroup.appendChild(panManRect);
		statusChange("Mouse down and move to pan the map, up to load current view.");
	}
}

//manages and finishes manual panning
map.prototype.panManualFinally = function(evt) {
	if (evt.type == "mousedown") {
		this.navStatus = "panmanualActive";
		this.panCoords = this.calcCoord(evt.clientX,evt.clientY);
	}
	if (evt.type == "mousemove" && this.navStatus == "panmanualActive") {
		var mapCoords = this.calcCoord(evt.clientX,evt.clientY);
		var diffX = this.panCoords["x"] - mapCoords["x"];
		var diffY = this.panCoords["y"] - mapCoords["y"];
		var myNewX = this.curxOrig + diffX;
		var myNewY = this.curyOrig + diffY;
		//check if within constraints
		if (myNewX < myRefMapDragger.constrXmin) {
			var myNewXTemp = myRefMapDragger.constrXmin;
			diffX = diffX + (myNewXTemp - myNewX);
			myNewX = myNewXTemp;
		}
		if (myNewY < myRefMapDragger.constrYmin) {
			var myNewYTemp = myRefMapDragger.constrYmin;
			diffY = diffY + (myNewYTemp - myNewY);
			myNewY = myNewYTemp;
		}
		if ((myNewX + this.curWidth) > myRefMapDragger.constrXmax) {
			var myNewXTemp = myRefMapDragger.constrXmax - this.curWidth;
			diffX = diffX + (myNewXTemp - myNewX);
			myNewX = myNewXTemp;
		}
		if ((myNewY + this.curHeight) > myRefMapDragger.constrYmax) {
			var myNewYTemp = myRefMapDragger.constrYmax - this.curHeight;
			diffY = diffY + (myNewYTemp - myNewY);
			myNewY = myNewYTemp;
		}		
		var transformString = "translate("+(diffX * -1) +","+(diffY * -1)+")";
		this.mainMapGroup.setAttributeNS(null,"transform",transformString);
		myRefMapDragger.newView(myNewX,myNewY,this.curWidth,this.curHeight);
	}
	if ((evt.type == "mouseup" || evt.type == "mouseout") && this.navStatus == "panmanualActive") {
		this.navStatus = "false";
		this.mainMapGroup.setAttributeNS(null,"transform","translate(0,0)");
		this.newViewBox(myRefMapDragger.dragId);			
		this.mainMapGroup.removeChild(document.getElementById("bgPanManual"));
		statusChange("map ready");	
	}
}

//remove all temporarily added elements and event listeners
map.prototype.cleanUp = function() {
			//remove background rect
			var oldBackground = document.getElementById("mapBackgroundRect");

			oldBackground.parentNode.removeChild(oldBackground);
			//remove eventlisteners
			if (this.showCoords == true) {
				//add event listener for coordinate display
				this.mapSVG.removeEventListener("mousemove",this,false);
			}

}

//make an element (rectangle) draggable within constraints
function dragObj(dragId,referenceMap,myDragSymbol,dragSymbThreshold,showCoords,coordXId,coordYId,mainMapObj) {
	this.dragId = dragId;
	this.myDragger = document.getElementById(this.dragId);
	this.myRefMap = document.getElementById(referenceMap);
	this.myDragSymbol = document.getElementById(myDragSymbol);	
	this.dragSymbThreshold = dragSymbThreshold;
	var viewBox = this.myRefMap.getAttributeNS(null,"viewBox").split(" ");
	this.constrXmin = parseFloat(viewBox[0]);
	this.constrYmin = parseFloat(viewBox[1]);
	this.constrXmax = this.constrXmin + parseFloat(viewBox[2]);
	this.constrYmax = this.constrYmin + parseFloat(viewBox[3]);
	this.refMapX = parseFloat(this.myRefMap.getAttributeNS(null,"x"));
	this.refMapY = parseFloat(this.myRefMap.getAttributeNS(null,"y"));
	this.refMapWidth = parseFloat(this.myRefMap.getAttributeNS(null,"width"));
	this.pixSize = (this.constrXmax - this.constrXmin) / this.refMapWidth;
	this.mainMapObj = mainMapObj;
	//initialize coordinate display if showCoords == true
	this.showCoords = showCoords;
	if (this.showCoords == true) {
		//add event listener for coordinate display
		this.myRefMap.addEventListener("mousemove",this,false);
		if (typeof(coordXId) == "string") {
			this.coordXText = document.getElementById(coordXId).firstChild;
		}
		else {
			alert("Error: coordXId needs to be an id of type string");
		}
		if (typeof(coordYId) == "string") {
			this.coordYText = document.getElementById(coordYId).firstChild;
		}
		else {
			alert("Error: coordYId needs to be an id of type string");
		}
	}
	this.status = false;
}

dragObj.prototype.calcCoord = function(coordx,coordy) {
	var coords = myMapApp.calcCoord(coordx,coordy);
	var mapCoords = new Array();
	mapCoords["x"] = this.constrXmin + (coords["x"] - this.refMapX) * this.pixSize;
	mapCoords["y"] = this.constrYmin + ((coords["y"]) - this.refMapY) * this.pixSize;
	return mapCoords;
}

dragObj.prototype.handleEvent = function(evt) {
	if (evt.type == "mousemove") {
		var mapCoords = this.calcCoord(evt.clientX,evt.clientY);
		this.coordXText.nodeValue = "X: " + formatNumberString(mapCoords["x"].toFixed(this.mainMapObj.nrDecimals)) + this.mainMapObj.units;
		this.coordYText.nodeValue = "Y: " + formatNumberString((mapCoords["y"] * -1).toFixed(this.mainMapObj.nrDecimals)) + this.mainMapObj.units;
	}
}

dragObj.prototype.newView = function(x,y,width,height) {
	this.myDragger.setAttributeNS(null,"x",x);
	this.myDragger.setAttributeNS(null,"y",y);
	this.myDragger.setAttributeNS(null,"width",width);
	this.myDragger.setAttributeNS(null,"height",height);
	this.myDragSymbol.setAttributeNS(null,"x",(x + width/2));
	this.myDragSymbol.setAttributeNS(null,"y",(y + height/2));
	if (width < this.dragSymbThreshold) {
		this.myDragSymbol.setAttributeNS(null,"visibility","visible");
	}
	else {
		this.myDragSymbol.setAttributeNS(null,"visibility","hidden");	
	}
}

dragObj.prototype.resizeDragger = function(status,sliderGroupName,width) {
	var myX = parseFloat(this.myDragger.getAttributeNS(null,"x"));
	var myY = parseFloat(this.myDragger.getAttributeNS(null,"y"));
	var myWidth = parseFloat(this.myDragger.getAttributeNS(null,"width"));
	var myHeight = parseFloat(this.myDragger.getAttributeNS(null,"height"));
	var myCenterX = myX + myWidth / 2;
	var myCenterY = myY + myHeight / 2;
	var myRatio = myHeight / myWidth;
	var toMoveX = myCenterX - width / 2;
	var toMoveY = myCenterY - width * myRatio / 2;
	if (toMoveX < this.constrXmin) {
		toMoveX = this.constrXmin;
	}
	if ((toMoveX + width) > this.constrXmax) {
		toMoveX = this.constrXmax - width;
	}
	if (toMoveY < this.constrYmin) {
		toMoveY = this.constrYmin;
	}
	if ((toMoveY + width * myRatio) > this.constrYmax) {
		toMoveY = this.constrYmax - width * myRatio;
	}
	this.newView(toMoveX,toMoveY,width,width * myRatio);
	if (status == "release") {
		this.mainMapObj.newViewBox(this.dragId);
	}
}

dragObj.prototype.drag = function(evt) {
	if (evt.type == "mousedown") {
		this.status = true;
	}
	if ((evt.type == "mousemove" || evt.type == "mousedown") && this.status == true) {
		var coords = this.calcCoord(evt.clientX,evt.clientY);
		var newEvtX = coords["x"];
		var newEvtY = coords["y"];
		var myX = parseFloat(this.myDragger.getAttributeNS(null,"x"));
		var myY = parseFloat(this.myDragger.getAttributeNS(null,"y"));
		var myWidth = parseFloat(this.myDragger.getAttributeNS(null,"width"));
		var myHeight = parseFloat(this.myDragger.getAttributeNS(null,"height"));
		var toMoveX = newEvtX - myWidth / 2;
		var toMoveY = newEvtY - myHeight / 2;
		if (toMoveX < this.constrXmin) {
			toMoveX = this.constrXmin;
		}
		if ((toMoveX + myWidth) > this.constrXmax) {
			toMoveX = this.constrXmax - myWidth;
		}
		if (toMoveY < this.constrYmin) {
			toMoveY = this.constrYmin;
		}
		if ((toMoveY + myHeight) > this.constrYmax) {
			toMoveY = this.constrYmax - myHeight;
		}
		this.newView(toMoveX,toMoveY,myWidth,myHeight);
	}
	if ((evt.type == "mouseup" || evt.type == "mouseout") && this.status == true) {
		this.status = false;
		if (evt.detail == 1) { //second click is ignored
			this.mainMapObj.newViewBox('dragRectForRefMap')
		}
	}
}

dragObj.prototype.zoom = function(inOrOut) {
	var myOldX = this.myDragger.getAttributeNS(null,"x");
	var myOldY = this.myDragger.getAttributeNS(null,"y");
	var myOldWidth = this.myDragger.getAttributeNS(null,"width");
	var myOldHeight = this.myDragger.getAttributeNS(null,"height");
	switch (inOrOut) {
		case "in":
			var myNewX = parseFloat(myOldX) + myOldWidth / 2 - (myOldWidth * this.mainMapObj.zoomFact * 0.5);
			var myNewY = parseFloat(myOldY) + myOldHeight / 2 - (myOldHeight * this.mainMapObj.zoomFact * 0.5);
			var myNewWidth = myOldWidth * this.mainMapObj.zoomFact;
			var myNewHeight = myOldHeight * this.mainMapObj.zoomFact;
			if (myNewWidth < this.mainMapObj.minWidth) {
				var myYXFact = this.mainMapObj.curHeight / this.mainMapObj.curWidth;
				myNewWidth = this.mainMapObj.minWidth;
				myNewHeight = myNewWidth * myYXFact;
				myNewX = parseFloat(myOldX) + myOldWidth / 2 - (myNewWidth * 0.5);
				myNewY = parseFloat(myOldY) + myOldHeight / 2 - (myNewHeight * 0.5);
			}
			break;
		case "out":
			var myNewX = parseFloat(myOldX) + myOldWidth / 2 - (myOldWidth * (1 + this.mainMapObj.zoomFact) * 0.5);
			var myNewY = parseFloat(myOldY) + myOldHeight / 2 - (myOldHeight * (1 + this.mainMapObj.zoomFact) * 0.5);
			var myNewWidth = myOldWidth * (1 + this.mainMapObj.zoomFact);
			var myNewHeight = myOldHeight * (1 + this.mainMapObj.zoomFact);
			break;
		default:
			var myNewX = this.constrXmin;
			var myNewY = this.constrYmin;
			var myNewWidth = this.constrXmax - this.constrXmin;
			var myNewHeight = this.constrYmax - this.constrYmin;
			break;
	}	
	//check if within constraints
	if (myNewWidth > (this.constrXmax - this.constrXmin)) {
		myNewWidth = this.constrXmax - this.constrXmin;
	}
	if (myNewHeight > (this.constrYmax - this.constrYmin)) {
		myNewHeight = this.constrYmax - this.constrYmin;
	}
	if (myNewX < this.constrXmin) {
		myNewX = this.constrXmin;
	}
	if (myNewY < this.constrYmin) {
		myNewY = this.constrYmin;
	}
	if ((myNewX + myNewWidth) > this.constrXmax) {
		myNewX = this.constrXmax - myNewWidth;
	}
	if ((myNewY + myNewHeight) > this.constrYmax) {
		myNewY = this.constrYmax - myNewHeight;
	}
	//alert(myNewX+", "+myNewY+", "+myNewWidth+", "+myNewHeight);
	this.newView(myNewX,myNewY,myNewWidth,myNewHeight);
	this.mainMapObj.newViewBox(this.dragId);
}

dragObj.prototype.pan = function (myX,myY,howmuch) {
	//get values from draggable rectangle
	var xulcorner = parseFloat(this.myDragger.getAttributeNS(null,"x"));
	var yulcorner = parseFloat(this.myDragger.getAttributeNS(null,"y"));
	var width = parseFloat(this.myDragger.getAttributeNS(null,"width"));
	var height = parseFloat(this.myDragger.getAttributeNS(null,"height"));

	//set values of draggable rectangle
	var rectXulcorner = xulcorner + howmuch * width * myX;
	var rectYulcorner = yulcorner + howmuch * height * myY;
	//check if within constraints
	if (rectXulcorner < this.constrXmin) {
		rectXulcorner = this.constrXmin;
	}
	if (rectYulcorner < this.constrYmin) {
		rectYulcorner = this.constrYmin;
	}
	if ((rectXulcorner + width) > this.constrXmax) {
		rectXulcorner = this.constrXmax - width;
	}
	if ((rectYulcorner + height) > this.constrYmax) {
		rectYulcorner = this.constrYmax - height;
	}
	this.newView(rectXulcorner,rectYulcorner,width,height);

	//set viewport of main map
	if ((xulcorner != rectXulcorner) || (yulcorner != rectYulcorner)) {
		this.mainMapObj.newViewBox(this.dragId);
	}

	statusChange("map ready ...");
}

//remove all temporarily used elements and event listeners
dragObj.prototype.cleanUp = function() {
			//remove eventlisteners
			if (this.showCoords == true) {
				//add event listener for coordinate display
				this.myRefMap.removeEventListener("mousemove",this,false);
			}

}

//magnifier glass mouse-over effects
function magnify(evt,scaleFact,inOrOut) {
	if (inOrOut == "in") {
		if (Math.round(myMainMap.curWidth) > myMainMap.minWidth) {
			statusChange("click to zoom in ");
			scaleObject(evt,scaleFact);
		}
		else {
			statusChange("maximum zoom factor reached! cannot zoom in any more!");
		}
	}
	if (inOrOut == "out") {
		if (Math.round(myMainMap.curWidth) < myMainMap.maxWidth) {
			statusChange("click to zoom out");
			scaleObject(evt,scaleFact);
		}
		else {
			statusChange("Minimum zoom factor reached. Cannot zoom out any more.");
		}
	}
	if (inOrOut == "manual") {
		if (Math.round(myMainMap.curWidth) > myMainMap.minWidth) {
			statusChange("Click and Drag Rectangle in main map.");
			scaleObject(evt,scaleFact);
		}
		else {
			statusChange("Maximum zoom factor reached. Cannot zoom in any more.");
		}
	}
	if (inOrOut == "full") {
		if (Math.round(myMainMap.curWidth) < myMainMap.maxWidth) {
			statusChange("click to set full view");
			scaleObject(evt,scaleFact);
		}
		else {
			statusChange("full view already reached");
		}
	}
	if (inOrOut == "panmanual") {
			statusChange("click to start panning map");
			scaleObject(evt,scaleFact);	
	}
	if (inOrOut == "recenter") {
			statusChange('Click to define new Map center');
			scaleObject(evt,scaleFact);
	}	
	if (scaleFact == 1) {
		statusChange("map ready");
		scaleObject(evt,scaleFact);
	}
}

function zoomIt(evt,inOrOut) {
	if (evt.detail == 1) { //only react on first click, double click: second click is ignored
		if (inOrOut == "in") {
			if (Math.round(myMainMap.curWidth) > myMainMap.minWidth) {
				myRefMapDragger.zoom("in");
			}
			else {
				statusChange("Maximum zoom factor reached. Cannot zoom in any more.");
			}
		}
		if (inOrOut == "out") {
			if (Math.round(myMainMap.curWidth) < myMainMap.maxWidth) {
				myRefMapDragger.zoom("out");
			}
			else {
				statusChange("Minimum zoom factor reached. Cannot zoom out any more.");
			}
		}
		if (inOrOut == "full") {
			//if (Math.round(myMainMap.curWidth) < myMainMap.maxWidth) {
				myRefMapDragger.zoom("full");
			/*}
			else {
				statusChange("Full view already reached.");
			}*/
		}
	}
}

//alert map extent
function showExtent() {
	with(myMainMap) {
		alert("Xmin="+curxOrig.toFixed(nrDecimals)+units+"; Xmax="+(curxOrig + curWidth).toFixed(nrDecimals)+units+"\nYmin="+((curyOrig + curHeight) * -1).toFixed(nrDecimals) +units+"; Ymax="+(curyOrig*-1).toFixed(nrDecimals)+units+"\nWidth="+curWidth.toFixed(nrDecimals)+units+"; Height="+curHeight.toFixed(nrDecimals)+units);
	}
}