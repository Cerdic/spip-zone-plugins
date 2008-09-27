//***********
// draw the map controls and logo 
// with different filetypes, for 
// compatibility reasons.
// also, give these controls their functions.
//***********
var images_folder = "png";
var images_extension = "png";
// Microsoft spoils everyone's fun again. Since IE doesn't support pngs,
// Things will have to look a bit different.
if(navigator.appName == "Microsoft Internet Explorer"){
	images_folder = "ie";
	images_extension = "gif";
}

//***********
// First Map Custom Control
//***********
function mapTypeControl() {
}
mapTypeControl.prototype = new GControl();
mapTypeControl.prototype.initialize = function(map) {
	var container = document.createElement("div");
    		
	var mapTypeMap = document.createElement("div");
	GEvent.addDomListener(mapTypeMap, "click", function() {
		map.setMapType(G_NORMAL_MAP);
	});
	var typeMapImg = document.createElement("img");
	typeMapImg.src = URLbase + "/img_pack/" + images_folder + "/ctlMap." + images_extension;
	typeMapImg.style.cursor = "pointer";
	typeMapImg.style.position = "absolute";
	typeMapImg.style.left= "0px";
	mapTypeMap.appendChild(typeMapImg);
      		
	var mapTypeSat = document.createElement("div");
	GEvent.addDomListener(mapTypeSat, "click", function() {
		map.setMapType(G_SATELLITE_MAP);
	});
	var typeSatImg = document.createElement("img");
	typeSatImg.src = URLbase + "/img_pack/" + images_folder + "/ctlSat." + images_extension;
	typeSatImg.style.cursor = "pointer";
	typeSatImg.style.position = "absolute";
	typeSatImg.style.left= "49px";
	mapTypeSat.appendChild(typeSatImg);
      		
	var mapTypeHyb = document.createElement("div");
	GEvent.addDomListener(mapTypeHyb, "click", function() {
		map.setMapType(G_HYBRID_MAP);
	});
	var typeHybImg = document.createElement("img");
	typeHybImg.src = URLbase + "/img_pack/" + images_folder + "/ctlHyb." + images_extension;
	typeHybImg.style.cursor = "pointer";
	typeHybImg.style.position = "absolute";
	typeHybImg.style.left= "115px";
	mapTypeHyb.appendChild(typeHybImg);
	
	var mapTypePhy = document.createElement("div");
	GEvent.addDomListener(mapTypePhy, "click", function() {
		map.setMapType(G_PHYSICAL_MAP);
	});
	var typePhyImg = document.createElement("img");
	typePhyImg.src = URLbase + "/img_pack/" + images_folder + "/ctlPhy." + images_extension;
	typePhyImg.style.cursor = "pointer";
	typePhyImg.style.position = "absolute";
	typePhyImg.style.left= "172px";
	mapTypePhy.appendChild(typePhyImg);
      		
	container.appendChild(mapTypeMap);
	container.appendChild(mapTypeSat);
	container.appendChild(mapTypeHyb);
	container.appendChild(mapTypePhy);
    		
	map.getContainer().appendChild(container);
	return container;
}
mapTypeControl.prototype.getDefaultPosition = function() {
	return new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(10, 0));
}
//***********
// Second Map Custom Control
//***********
function mapZoomControl() {
}
mapZoomControl.prototype = new GControl();
mapZoomControl.prototype.initialize = function(map) {
	var container = document.createElement("div");
	container.id = "mapZoomControl";
	var zoomIn = document.createElement("div");
	zoomIn.style.position = "absolute";
	zoomIn.style.top= "0px";
	GEvent.addDomListener(zoomIn, "click", function() {
		this.parentNode.childNodes[18 - map.getZoom()].firstChild.src = URLbase + "/img_pack/" + images_folder + "/ctlZoomNotch." + images_extension;
		map.zoomIn();
		this.parentNode.childNodes[18 - map.getZoom()].firstChild.src = URLbase + "/img_pack/" + images_folder + "/ctlZoomSel." + images_extension;
	});
	var zoomInImg = document.createElement("img");
	zoomInImg.src = URLbase + "/img_pack/" + images_folder + "/ctlZoomIn." + images_extension;
	zoomInImg.style.cursor = "pointer";
	zoomIn.appendChild(zoomInImg);
	container.appendChild(zoomIn);
      		
	var notchImg = new Array();
	var notchDiv = new Array();
	for(var i=0; i < 18; i++){
		notchDiv[i] = document.createElement("div");
		GEvent.addDomListener(notchDiv[i], "click", function() {
			this.parentNode.childNodes[18 - map.getZoom()].firstChild.src = URLbase + "/img_pack/" + images_folder + "/ctlZoomNotch." + images_extension;
			map.setZoom(17 - this.zoom);
			this.firstChild.src = URLbase + "/img_pack/" + images_folder + "/ctlZoomSel." + images_extension;
		});
		notchDiv[i].zoom = i;
		notchDiv[i].style.position = "absolute";
		notchDiv[i].style.top= 9 + (i*9) + "px";
		notchImg[i] = document.createElement("img");
		notchImg[i].src = URLbase + "/img_pack/" + images_folder + "/ctlZoomNotch." + images_extension;
		notchImg[i].style.cursor = "pointer";
		notchDiv[i].appendChild(notchImg[i]);
		container.appendChild(notchDiv[i]);
	}
    		
	var zoomOut = document.createElement("div");
	zoomOut.style.position = "absolute";
	zoomOut.style.top= "179px";
	GEvent.addDomListener(zoomOut, "click", function() {
		this.parentNode.childNodes[18 - map.getZoom()].firstChild.src = URLbase + "/img_pack/" + images_folder + "/ctlZoomNotch." + images_extension;
		map.zoomOut();
		this.parentNode.childNodes[18 - map.getZoom()].firstChild.src = URLbase + "/img_pack/" + images_folder + "/ctlZoomSel." + images_extension;
	});
	var zoomOutImg = document.createElement("img");
	zoomOutImg.src = URLbase + "/img_pack/" + images_folder + "/ctlZoomOut." + images_extension;
	zoomOutImg.style.cursor = "pointer";
	zoomOut.appendChild(zoomOutImg);
	container.appendChild(zoomOut);
      		
	map.getContainer().appendChild(container);
	return container;
	}
mapZoomControl.prototype.getDefaultPosition = function() {
	return new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(24, 100));
}
//***********
// Third Map Custom Control
//***********
function mapMoveControl() {
}
mapMoveControl.prototype = new GControl();
mapMoveControl.prototype.initialize = function(map) {
	var container = document.createElement("div");
    		
	var mapMoveMap = document.createElement("div");
    		
	var mapMoveImgUL = document.createElement("img");
	mapMoveImgUL.src = URLbase + "/img_pack/" + images_folder + "/ctlTopLeft." + images_extension;
	mapMoveImgUL.style.position = "absolute";
	mapMoveImgUL.style.left= "7px";
	mapMoveMap.appendChild(mapMoveImgUL);
      		
	var butomUpDiv = document.createElement("div");
	butomUpDiv.style.position = "absolute";
	butomUpDiv.style.left= "24px";
	GEvent.addDomListener(butomUpDiv, "click", function() {
		pan(north);
	});
	var butomUpImg = document.createElement("img");
	butomUpImg.src = URLbase + "/img_pack/" + images_folder + "/ctlTop." + images_extension;
	butomUpImg.style.cursor = "pointer";
	butomUpDiv.appendChild(butomUpImg);
	mapMoveMap.appendChild(butomUpDiv);
      		
	var mapMoveImgUR = document.createElement("img");
	mapMoveImgUR.src = URLbase + "/img_pack/" + images_folder + "/ctlTopRight." + images_extension;
	mapMoveImgUR.style.position = "absolute";
	mapMoveImgUR.style.left= "43px";
	mapMoveMap.appendChild(mapMoveImgUR);
      		
	var butomRightDiv = document.createElement("div");
	butomRightDiv.style.position = "absolute";
	butomRightDiv.style.left= "43px";
	butomRightDiv.style.top= "17px";
	GEvent.addDomListener(butomRightDiv, "click", function() {
		pan(east);
	});
	var butomRightImg = document.createElement("img");
	butomRightImg.src = URLbase + "/img_pack/" + images_folder + "/ctlRight." + images_extension;
	butomRightImg.style.cursor = "pointer";
	butomRightDiv.appendChild(butomRightImg);
	mapMoveMap.appendChild(butomRightDiv);
      		
	var butomCenterDiv = document.createElement("div");
	butomCenterDiv.style.position = "absolute";
	butomCenterDiv.style.left= "24px";
	butomCenterDiv.style.top= "17px";
	GEvent.addDomListener(butomCenterDiv, "click", function() {
		map.panTo(center);
	});
	var butomCenterImg = document.createElement("img");
	butomCenterImg.src = URLbase + "/img_pack/" + images_folder + "/ctlCenter." + images_extension;
	butomCenterImg.style.cursor = "pointer";
	butomCenterDiv.appendChild(butomCenterImg);
	mapMoveMap.appendChild(butomCenterDiv);
      		
	var butomLeftDiv = document.createElement("div");
	butomLeftDiv.style.position = "absolute";
	butomLeftDiv.style.left= "7px";
	butomLeftDiv.style.top= "17px";
	GEvent.addDomListener(butomLeftDiv, "click", function() {
		pan(west);
	});
	var butomLeftImg = document.createElement("img");
	butomLeftImg.src = URLbase + "/img_pack/" + images_folder + "/ctlLeft." + images_extension;
	butomLeftImg.style.cursor = "pointer";
	butomLeftDiv.appendChild(butomLeftImg);
	mapMoveMap.appendChild(butomLeftDiv);
      		
	var mapMoveImgDL = document.createElement("img");
	mapMoveImgDL.src = URLbase + "/img_pack/" + images_folder + "/ctlBotLeft." + images_extension;
	mapMoveImgDL.style.position = "absolute";
	mapMoveImgDL.style.top= "36px";
	mapMoveImgDL.style.left= "7px";
	mapMoveMap.appendChild(mapMoveImgDL);
      		
	var butomDownDiv = document.createElement("div");
	butomDownDiv.style.position = "absolute";
	butomDownDiv.style.left= "24px";
	butomDownDiv.style.top= "36px";
	GEvent.addDomListener(butomDownDiv, "click", function() {
		pan(south);
	});
	var butomDownImg = document.createElement("img");
	butomDownImg.src = URLbase + "/img_pack/" + images_folder + "/ctlBot." + images_extension;
	butomDownImg.style.cursor = "pointer";
	butomDownDiv.appendChild(butomDownImg);
	mapMoveMap.appendChild(butomDownDiv);
      		
	var mapMoveImgDR = document.createElement("img");
	mapMoveImgDR.src = URLbase + "/img_pack/" + images_folder + "/ctlBotRight." + images_extension;
	mapMoveImgDR.style.position = "absolute";
	mapMoveImgDR.style.left= "43px";
	mapMoveImgDR.style.top= "36px";
	mapMoveMap.appendChild(mapMoveImgDR);
      		
	container.appendChild(mapMoveMap);
    		
	map.getContainer().appendChild(container);
	return container;
}
mapMoveControl.prototype.getDefaultPosition = function() {
	return new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(0, 30));
}
//	map control functions
var north="north";
var east="east";
var south="south";
var west="west";
		
function pan(dir){
	var center = map.getCenter();
	var bounds = map.getBounds();
	var northEast = bounds.getNorthEast();
	var southWest = bounds.getSouthWest();
	if(dir==north) map.panTo(new GLatLng(northEast.lat(), center.x));
	if(dir==east)  map.panTo(new GLatLng(center.y, northEast.lng()));
	if(dir==south) map.panTo(new GLatLng(southWest.lat(), center.x));
	if(dir==west)  map.panTo(new GLatLng(center.y, southWest.lng()));
}
//***********
// Fake Map Custom Control
// for rounded corners
//***********
function cornerControl() {
}
cornerControl.prototype = new GControl();
cornerControl.prototype.initialize = function(map) {
	var size = map.getSize();
	var div = document.createElement("div");
	div.style.position = "absolute";
	var img1 = document.createElement("img");
	img1.src =  URLbase + "/img_pack/" + images_folder + "/corner01." + images_extension;
	img1.style.position = "absolute";
	img1.style.left= "0px";
	div.appendChild(img1);
	var img2 = document.createElement("img");
	img2.src = URLbase + "/img_pack/" + images_folder + "/corner02." + images_extension;
	img2.style.position = "absolute";
	img2.style.left = (size.width - 15) + "px";
	div.appendChild(img2);
	var img3 = document.createElement("img");
	img3.src = URLbase + "/img_pack/" + images_folder + "/corner03." + images_extension;
	img3.style.position = "absolute";
	img3.style.left = (size.width - 15) + "px";
	img3.style.top = (size.height - 15) + "px";
	div.appendChild(img3);
	var img4 = document.createElement("img");
	img4.src = URLbase + "/img_pack/" + images_folder + "/corner04." + images_extension;
	img4.style.position = "absolute";
	img4.style.left = "0px";
	img4.style.top = (size.height - 15) + "px";
	div.appendChild(img4);
	
	map.getContainer().appendChild(div);
	return div;
}
cornerControl.prototype.getDefaultPosition = function() {
	return new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(0, 0));
}
//***********
// Adress Custom Control
//***********
function mapAddressControl() {
}
mapAddressControl.prototype = new GControl();
mapAddressControl.prototype.initialize = function(map) {
	var container = document.createElement("div");
	var imgAddress = document.createElement("img");
	GEvent.addDomListener(imgAddress, "click", function() {
		$("#menuses").toggle();
	});
	imgAddress.src = URLbase + "/img_pack/" + images_folder + "/libro." + images_extension;
	imgAddress.style.cursor = "pointer";
	imgAddress.style.position = "absolute";
	imgAddress.style.left= "314px";
	
	var formContainer = document.createElement("div");
	formContainer.style.display = "none";
	formContainer.id = "menuses";
	
	var left = document.createElement("img");
	left.src = URLbase + "/img_pack/" + images_folder + "/left." + images_extension;
	left.style.position = "absolute";
	left.style.top= "10px";
	left.style.left= "0px";
	formContainer.appendChild(left);
	
	var center = document.createElement("img");
	center.src = URLbase + "/img_pack/" + images_folder + "/center." + images_extension;
	center.style.width = "290px";
	center.style.height = "35px";
	center.style.position = "absolute";
	center.style.top= "10px";
	center.style.left= "7px";
	formContainer.appendChild(center);
	
	var input = document.createElement("input");
	input.name="address";
	input.id="address";
	input.type = "text";
	input.value = "Plaza Juda; Levi, Cordoba, Spain";
	input.size = "30";
	input.style.position = "absolute";
	input.style.top= "15px";
	input.style.left= "8px";
	formContainer.appendChild(input);
	var boton = document.createElement("img");
	boton.src = URLbase + "/img_pack/" + images_folder + "/go." + images_extension;
	boton.style.cursor = "pointer";
	boton.style.position = "absolute";
	boton.style.top= "15px";
	boton.style.left= "265px";
	GEvent.addDomListener(boton, "click", function() {
		var geocoder = new GClientGeocoder();
		var address = document.getElementById("address").value;
		if (geocoder) {
       		geocoder.getLatLng(
				address,
         		function(point) {
            		if (!point) {
             			alert(address + " not found");
            		} else {
              			map.setCenter(point, 13);
              			var marker = new GMarker(point);
              			formMap.addOverlay(marker);
           				marker.openInfoWindowHtml(address);
						$('#form_lat').val(point.y);
						$('#form_long').val(point.x);
           			}
       			}
       		);
   		}
	});
	formContainer.appendChild(boton);
	
	var right = document.createElement("img");
	right.src = URLbase + "/img_pack/" + images_folder + "/right." + images_extension;
	right.style.position = "absolute";
	right.style.top= "10px";
	right.style.left= "297px";
	formContainer.appendChild(right);
	
	container.appendChild(imgAddress);
	container.appendChild(formContainer);

	map.getContainer().appendChild(container);
	return container;
}
mapAddressControl.prototype.getDefaultPosition = function() {
	return new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(90, 290));
}