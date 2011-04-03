function loadPictos(map, urlPictos, idmap, msg, pins) {
  var bbox = map.getExtent();
  bbox.transform(map.projection, map.displayProjection);
  var markerMgr = eval('markers'+idmap);
  var zoom = map.getZoom();
  if(zoom>=2) {
  	zoom = zoom - 2;
  }
	if (jQuery("#attente" + idmap).is(":hidden")) {
		jQuery("#attente" + idmap).show();
	}
	jQuery.ajax({
		url: urlPictos,
		dataType: "json",
		data: 'minlat=' + bbox.bottom + '&maxlat=' + bbox.top + '&minlon=' + bbox.left + '&maxlon=' + bbox.right,
		success: function(data) {
			var nb = data.pictos.length;
			for (var i=0;i<nb;i++) {
				if (jQuery.inArray(data.pictos[i].id, map.pictos) >= 0)
					continue;
				addPicto(data.pictos[i], markerMgr, map, pins);
				map.pictos.push(data.pictos[i].id);
			}
		},
		complete: function() {
			jQuery("#attente" + idmap).hide();
		}
	});
}

function img2icon(i, dp, dpW, dpH, pins) {
  if (typeof i == "undefined") {
    var url = dp;
    var imgW = dpW;
    var imgH = dpH;
  }
  else {
    var url = i.image;
    var imgW = parseInt(i.width);
    var imgH = parseInt(i.height);
  }
  var size = new OpenLayers.Size(imgW,imgH);
  if (pins)
  	var dh = - size.h;
  else
  	var dh = - size.h/2;
  var offset = function(size) { return new OpenLayers.Pixel(-size.w/2, dh); };
  var icon = new OpenLayers.Icon(
    url,
    size,
    null,
    offset
  );
  return icon;
}

function addPicto(item, markerLayer, map, pins) {
	if ((typeof(item.lat) == "undefined")||(typeof(item.lon) == "undefined")) return;
	else {
		var popup = null;
		var point = new OpenLayers.LonLat(parseFloat(item.lon), parseFloat(item.lat));
    var icon = img2icon(item.icon, defPicto, defPictoWidth, defPictoHeight, pins);
    var iconUrl = icon.url;
    var overUrl = img2icon(item.over, defPictoHover, defPictoHoverWidth, defPictoHoverHeight, pins).url;
    var html = '<div class="titre"><a href="' + item.url + '">' + item.title + '</a></div>';
		var marker = new OpenLayers.Marker(point.fromDataToDisplay(), icon);
		jQuery(marker.icon.imageDiv).attr("title", item.title);
		marker.events.register("click", marker, function click(evt) {
      if (popup == null) {
        popup = new OpenLayers.Popup.FramedCloud("popup" + item.id, point.fromDataToDisplay(), undefined, html + '<div style="display: block; width: 100%; height: 100%; background: transparent url(plugins/gis/img_pack/attente.gif) center center no-repeat;"></div>', undefined, true);
        popup.autoSize = true;
        popup.maxSize = new OpenLayers.Size(map.size.w/2 + 40, map.size.h/2 + 40);
        map.addPopup(popup);
        jQuery.ajax({
        	url: item.urlajax,
        	success: function(data) {
        		popup.setContentHTML(html + data);
        	},
        	complete: function() {
        		var h = jQuery("#popup" + item.id).height();
        		h -= jQuery(".titre", "#popup" + item.id).height();
        		h -= jQuery(".date", "#popup" + item.id).height();
        		h -= 60;
        		jQuery(".bulle", "#popup" + item.id).height(h);        		
        		popup.updateSize();
        		popup.panIntoView();
        	}
        });
      } else {
        popup.toggle();
      }
      OpenLayers.Event.stop(evt);
    });
		marker.events.register("mouseover", marker, function onmouseover(evt) {
      jQuery(marker.icon.imageDiv).css("cursor", "pointer");
      marker.setUrl(overUrl);
      OpenLayers.Event.stop(evt);
    });
 		marker.events.register("mouseout", marker, function onmouseout(evt) {
      jQuery(marker.icon.imageDiv).css("cursor", "");
      marker.setUrl(iconUrl);
      OpenLayers.Event.stop(evt);
    });
		markerLayer.addMarker(marker);
	}	
}