carto.Editer_camera_prive = function(carte, opts){
		
	init();
	
	function init(){
		$("#champ_direction, #champ_type, #champ_angle, #champ_lat, #champ_lon, "
			+"#champ_apparence, #champ_op_type").attr("autocomplete", "off");
		miseEnForme();
		propagationChangements();
	}
	
	function miseEnForme(){
		carto.Form.inputAsSlider("champ_direction", 360);
		carto.Form.inputAsSlider("champ_angle", 180);
		if(opts && opts.operateurs) carto.Form.autocomplete("champ_op_name", opts.operateurs);
		carto.Form.latLongInput();
		//carto.Form.onglets();
		
		carte.onFeatureUpdate = onMapFeatureUpdate;
	}
	
	function propagationChangements(){
		$('#champ_type').change(function() { carte.setFeatureAttribute('type', $(this).val()); });
		$('#champ_direction').change(function() { carte.setFeatureAttribute('direction', $(this).val()); });
		$('#champ_angle').change(function() { carte.setFeatureAttribute('angle', $(this).val()); });
		
		$('#champ_lat').keyup(function(){
			 carte.setFeatureAttribute('latlon', {lat:$('#champ_lat').val(), lng:$('#champ_lon').val()});
	    });
		$('#champ_lon').keyup(function(){
			 carte.setFeatureAttribute('latlon', {lat:$('#champ_lat').val(), lng:$('#champ_lon').val()});
	    })
	
	
		//TODO: mieux gérer le passage d'un etat à l'autre, actuellement obligé d'activer/desactiver les boutons entre eux
	
		// latlon
		$('#bouton_deplacer').click( function(e){
			e.preventDefault();
			if( !$(this).hasClass('disabled') ){
				
				if( $(this).hasClass('uiButtonSelected') ){
					carte.setMapMode();
					$('#'+carte.getContainerId()).css({'cursor':'default'});
				}else{
					carte.setMapMode('drag');
					$('#'+carte.getContainerId()).css({'cursor':'default'});
				}
				$(this).toggleClass('uiButtonSelected');
				$(this).siblings().removeClass('uiButtonSelected');
			}
		});
		
		$('#bouton_pointer').click(function(e){
			e.preventDefault();
			if( !$(this).hasClass('disabled') ){
				
				if( $(this).hasClass('uiButtonSelected') ){
					carte.setMapMode();
					$('#'+carte.getContainerId()).css({'cursor':'default'});
				}else{	
					carte.setMapMode('point');
					//$('#bouton_deplacer').removeClass('disabled');
					$('#'+carte.getContainerId()).css({'cursor':'crosshair'});
				}
				
				$(this).toggleClass('uiButtonSelected');
				$(this).siblings().removeClass('uiButtonSelected');
			}
		});
		
		
		$('#champ_apparence').change(function() { carte.setFeatureAttribute('apparence', $(this).val()); });
		$('#champ_op_type').change(function() { carte.setFeatureAttribute('op_type', $(this).val()); });
		
		
		
		//setMapMode
	}
	
	function onMapFeatureUpdate(attr, val){
		//console.log( "formulaire : Event from map : "+attr+":"+val.toSource() );
		
		switch(attr){
			case "latlon":
				$("#champ_lat").val(val.lat);
				$("#champ_lon").val(val.lng);
				$('#bouton_deplacer').removeClass('disabled');
				break;
			
			default:break;
		}
	}
	
	function majFormSpip(carte){
		var cameraActive = carte.getSelectedFeature();

		if(cameraActive == null){
			alert("camera active pas trouvée");
		}else{
			//carte.cameraActive.attributes["point"] = pos.lon+","+pos.lat;
			var pos = cameraActive.attributes["point"];//.split(',');

			//var pos = cameraActive.geometry.getBounds().getCenterLonLat();
			$('#champ_lat').val( pos.lat );
			$('#champ_lon').val( pos.lon );
			//onFeatureEdit(feature);

			//
			if (carte.getSelectedFeature() != null && $('#bouton_deplacer').hasClass('disabled') ){
				$('#bouton_deplacer').removeClass('disabled');
			}
		}
	}

	function getFormValues(){
		return {
			lon : $('#champ_lon').val(),
		    lat : $('#champ_lat').val(), 
			type: $('#champ_type').val(),
			direction: $('#champ_direction').val(),
			angle: $('#champ_angle').val()
		}
	}
}