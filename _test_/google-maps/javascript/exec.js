function googlekey()
{
	$.ajax({
		type: "GET",
		url: "../plugins/google-maps/googlekey.php",
		success: function(key){
			var newkey=prompt("Google Maps Key",key);
			if(newkey && newkey!=key){
				$.ajax({
					type: "POST",
					url: "../plugins/google-maps/googlekey.php",
					data: {key:newkey}
				});
			}
		},
		error: function(oxhr,serror,ex){
			var newkey=prompt("Google Maps Key","");
			$.ajax({
				type: "POST",
				url: "../plugins/google-maps/googlekey.php",
				data: {key:newkey}
			});
		}
	});
}

function updatetag()
{
	var tag='<gmap';
	if($('#align').val()!='none')
	{	tag+='|'+$('#align').val();	}
	if($('#height').val()!='')
	{	tag+='|height='+$('#height').val();	}
	if($('#width').val()!='')
	{	tag+='|width='+$('#width').val();	}
	if($('#zoom').val()!='')
	{	tag+='|zoom='+$('#zoom').val();	}
	if($('#type').val()!='')
	{	tag+='|type='+$('#type').val();	}
	if($('#ptstype1')[0].checked)
	{	tag+='|pts=tous';	}
	else if($('#ptstype3')[0].checked)
	{	tag+='|rubrique=xx';	}
	else
	{
		var pts=$('#gmap_insert').html().match(/\|pts=[0-9,]*/g);
		if(pts)
		{	tag+=pts[0];	}
	}
	tag+='>';
	$('#balisegmap').val(tag);
}

//Charge la biblio GMaps de Google
google.load("maps", "2");

//Chemin des icones
var iconOn = dir_plugin + "images/markerOn.png";
var iconOff = dir_plugin + "images/markerOff.png";
//Marker actif
var marker2 = null;
//Id du marker actif
var id2 = null;
//Contenu du textarea (sert en cas d'annulation de fermeture)
var contenthtml = "";
//Code HTML de l'éditeur (dans la bulle)
var edithtml = "<textarea id=\"gmapeditor\" style=\"width:300px;height:100px\"></textarea><br />" +
			"<button onclick=\"boutonok();\"><img src=\"" + dir_plugin + "images/disk.png\" />&nbsp;" + bouton_ok + "</button>&nbsp;" +
			"<button onclick=\"boutoncancel();\"><img src=\"" + dir_plugin + "images/arrow_undo.png\" />&nbsp;" + bouton_cancel + "</button>&nbsp;" +
			"<button onclick=\"boutondel();\"><img src=\"" + dir_plugin + "images/delete.png\" />&nbsp;" + bouton_delete + "</button>&nbsp;" +
			"<br />&nbsp;<br />&nbsp;";

function initialize() {
	//Initialisation de la carte
	var map = new google.maps.Map2(document.getElementById("map"));
	map.disableDoubleClickZoom();
	map.disableContinuousZoom();
	map.enableGoogleBar();
	map.addMapType(G_PHYSICAL_MAP);
	map.addControl(new GSmallMapControl());
	map.addControl(new GMenuMapTypeControl());
	map.addControl(new GOverviewMapControl());
	map.enableScrollWheelZoom();
	$('#gmap_insert').html('&lt;gmap&gt;');
	map.setCenter(new google.maps.LatLng(0,0), 4);

	//Chargement des points
	$.ajax({
		type: "GET",
		url: "../spip.php?page=gmapspoints",
		dataType: "json",
		error: function(oXHR,sMsg,ex){
			alert(sMsg);
		},
		success: function(json){
			var latmax=0;
			var latmin=0;
			var lngmax=0;
			var lngmin=0;
			for(var i=0;i<json.gmappoints.length;i++)
			{	gr_addPoint(map,new google.maps.LatLng(json.gmappoints[i].lat,json.gmappoints[i].lng),json.gmappoints[i].id,false);
				if(latmax==0 || latmax<json.gmappoints[i].lat){	latmax=json.gmappoints[i].lat;	}
				if(latmin==0 || latmin>json.gmappoints[i].lat){	latmin=json.gmappoints[i].lat;	}
				if(lngmax==0 || lngmax<json.gmappoints[i].lng){	lngmax=json.gmappoints[i].lng;	}
				if(lngmin==0 || lngmin>json.gmappoints[i].lng){	lngmin=json.gmappoints[i].lng;	}
			}
			var lat=latmin/1+(latmax-latmin)/2;
			var lng=lngmin/1+(lngmax-lngmin)/2;
			map.setCenter(new google.maps.LatLng(lat,lng), 4);
		}
	});

	//Ajout d'un nouveau point
	GEvent.addListener(map,"click", function(overlay,latlng) {
		if(typeof(latlng)!="undefined"){
			$.ajax({type: "POST",
				url: "../spip.php?page=gmapspoint",
				data:{id:0,lat:latlng.lat(),lng:latlng.lng()},
				success:function(id){
					gr_addPoint(map,latlng,id,true);
				}
			});
		}
	});

	//Changement du type de carte
	GEvent.addListener(map,"maptypechanged", function(){
		switch(map.getCurrentMapType())
		{
			case G_PHYSICAL_MAP	 : $('#type').val("physical");	break;
			case G_HYBRID_MAP	 : $('#type').val("hybrid");	break;
			case G_SATELLITE_MAP : $('#type').val("satellite");	break;
			case G_NORMAL_MAP	 : $('#type').val("normal");	break;
		}
		updatetag();
	});

	//Changement du Zoom
	GEvent.addListener(map,"zoomend", function(oldLevel,newLevel){
		$('#zoom').val(newLevel);
		updatetag();
	});

}

//Création d'un point
function gr_addPoint(map,center,id,newpoint){
	if(typeof newpoint == "undefined") { newpoint=true;	}
	var marker = new GMarker(center, {draggable: true});

	//Déplacement d'un point
	GEvent.addListener(marker, "dragstart", function() {
		map.closeInfoWindow();
	});

	GEvent.addListener(marker, "dragend", function() {
		var xy = marker.getLatLng();
		$.ajax({type: "POST",
			url: "../spip.php?page=gmapspoint",
			data:{id:id,lat:xy.lat(),lng:xy.lng()}
		});
	});

	//Sélection d'un point
	GEvent.addListener(marker, "click", function() {
		marker2=marker;
		id2=id;
		var tag = $('#gmap_insert').html();
		tag = tag.replace(">","&gt;");	//Corrige un bug sous Safari
		var on = false;
		//Il n'y a pas de présélection ?
		if(tag.indexOf("|pts=")==-1)
		{
			tag = tag.replace("&gt;","|pts=" + id + "&gt;");
			on = true;
		}	//Il n'appartient pas à la présélection
		else if(tag.indexOf("|pts="+id+"&gt;")==-1
		&& tag.indexOf("|pts="+id+",")==-1
		&& tag.indexOf(","+id+",")==-1
		&& tag.indexOf(","+id+"&gt;")==-1)
		{
			tag = tag.replace("&gt;","," + id + "&gt;");
			on = true;
		}	//Alors c'est qu'il est là !
		else
		{
			tag = tag.replace("|pts="+id+"&gt;","&gt;");
			tag = tag.replace("|pts="+id+",","|pts=");
			tag = tag.replace(","+id+"&gt;","&gt;");
			tag = tag.replace(","+id+",",",");
			on = false;
		}

		//Change l'image
		if(on){ marker.setImage(iconOn);	}
		else {	marker.setImage(iconOff);	}

		//Update du texte
		$('#gmap_insert').html(tag);
		updatetag();
	});

	//Edition d'un point
	GEvent.addListener(marker, "dblclick", function(){
		marker2=marker;
		id2=id;
		marker.openInfoWindowHtml(edithtml);
	});

	GEvent.addListener(marker, "infowindowopen", function(){
		$('#gmapeditor').html('aze');
		if(contenthtml!="")
		{	$('#gmapeditor').val(contenthtml);
			nicEdit = new nicEditor({
				buttonList : ['bold','italic','underline','strikeThrough','ol','ul','image','link','unlink']
			}).panelInstance('gmapeditor');
		}else{
			$.ajax({
				type: "GET",
				url: "../spip.php?page=gmapspoints&edit=true&pts[]="+id,
				dataType: "json",
				error: function(oXHR,sMsg,ex){
					alert(sMsg + " " + ex);
				},
				success: function(json){
					$('#gmapeditor').val(json.gmappoints[0].html);
					nicEdit = new nicEditor({
						buttonList : ['bold','italic','underline','strikeThrough','ol','ul','image','link','unlink']
					}).panelInstance('gmapeditor');
					contenthtml="heeeelp, dont close me !!!";
				}
			});
		}
	});

	GEvent.addListener(marker, "infowindowbeforeclose", function(){
		if(contenthtml!="" && !confirm(alert_cancel))
		{	var contenthtml2 = nicEdit.nicInstances[0].getContent();
			contenthtml="";
			marker.openInfoWindowHtml(edithtml);
			contenthtml=contenthtml2;
		}
		else
		{	contenthtml="";	}
	});

	boutonok=function(){
		var content = nicEdit.nicInstances[0].getContent();
		contenthtml="";
		$.ajax({type: "POST",
			url: "../spip.php?page=gmapspoint",
			data:{id:id2,html:content},
			success:function(){
				nicEdit.removeInstance('gmapeditor');
				nicEdit = null;
				map.closeInfoWindow();
			}
		});
	}

	boutoncancel=function(){
		map.closeInfoWindow();
	}

	boutondel=function(){
		if(confirm(alert_delete))
		{	var tag = $('#gmap_insert').html();
			tag = tag.replace("|pts="+id2+"&gt;","&gt;");
			tag = tag.replace("|pts="+id2+",","|pts=");
			tag = tag.replace(","+id2+"&gt;","&gt;");
			tag = tag.replace(","+id2+",",",");
			$('#gmap_insert').html(tag);
			updatetag();
			$.ajax({type: "POST",
				url: "../spip.php?page=gmapspoint",
				data:{id:-1,lat:id2,lng:id2},
				success: function(){
					contenthtml="";
					marker2.hide();
					map.closeInfoWindow();
				}
			});
		}
	}

	map.addOverlay(marker);

	//Si c'est un nouveau point, on le séléctionne
	if(newpoint){
		var tag = $('#gmap_insert').html();
		if(tag.indexOf("|pts=")==-1)
		{	tag = tag.replace("&gt;","|pts=" + id + "&gt;");
			on = true;
		} else if(tag.indexOf("|pts="+id+"&gt;")==-1
		&& tag.indexOf("|pts="+id+",")==-1
		&& tag.indexOf(","+id+",")==-1
		&& tag.indexOf(","+id+"&gt;")==-1){
			tag = tag.replace("&gt;","," + id + "&gt;");
			on = true;
		}
		marker.setImage(iconOn);
		$('#gmap_insert').html(tag);
		updatetag();
	}

	return id;
}

//On load, initialize le bouzin !
google.setOnLoadCallback(initialize);