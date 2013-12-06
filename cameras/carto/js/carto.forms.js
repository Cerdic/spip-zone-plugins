carto.Form = {
	
	inputAsSlider: function(input_id, max_value){
		var id = input_id;
		var max = max_value;
		var val = $('#'+id).val();

		$('#'+id).hide();
		$('#'+id).after('<div id="'+id+'_slider"></div>');
		$('#'+id+'_slider').slider({
		    min: 0,
		    max: max,
		    value: val,
		    slide: function( event, ui ) {
				var pc = Math.floor(ui.value/max*100);
		        $('#'+id).val(  ui.value );
				$(this).children('.ui-progressbar-value').html("&nbsp;&nbsp;"+ui.value);
			    $('#'+id).change();
				$(this).progressbar({ value: pc });
		    }
		});
		$('#'+id+'_slider').progressbar({ value: val/max*100 })
			.children('.ui-progressbar-value')
			    .html("&nbsp;&nbsp;"+val)
			    .css("display", "block")
			    .css("text-align:center", "block");

		$('#'+id).keyup(function() {
	        $(this).change();
	    });
	},
	
	autocomplete: function(input_id, values){
		$('#'+input_id).autocomplete({
			minLength: 0, delay:0,
			source: values
		});
		$('#'+input_id).focus( function(){ $(this).autocomplete('search'); });
	},
	
	latLongInput : function(){
		// champ_position (fusion champ_lat + champ_long)
		var hasLatLon = ( ($('#champ_lat').val() != "") && ($('#champ_lon').val() != "") );
		
		$('#champ_lat').parent().before(
			 '<li class="editer saisie_input">'
			+'<label>Position</label><div class="groupedButtons" style="float:left">'
				+'<a href="#" class="uiButton" id="bouton_pointer">'+carto.Lang.bouton_pointer_sur_carte+'</a>'
				+'<a href="#" class="uiButton'+(!hasLatLon?' disabled':'')+'" id="bouton_deplacer">'+carto.Lang.bouton_deplacer_camera+'</a>'
			+'</div>'
			+'&nbsp;<a href="#" class="uiButton" id="bouton_saisie_coordonnes" style="float:right">'+carto.Lang.bouton_saisie_manuelle+'</a>'
			+'</li>'
		);
		
		$('#champ_lat').parent().next().andSelf()
			.wrapAll('<div class="carto_champs_profonds" id="saisie_coordonnes"/>');
		$("#saisie_coordonnes").toggle();
		$('#bouton_saisie_coordonnes').click(function(e) { 
			e.preventDefault();
			$(this).toggleClass('uiButtonSelected');
			$("#saisie_coordonnes").slideToggle();
		});
	},
	
	onglets : function(){
		// onglets
		var panes = $('.formulaire_editer fieldset');
		panes.first().before('<ul id="onglets"></ul>');
		panes.each(function(i) {
			$('#onglets').append(
				'<li id="onglet-'+i+'">'
					+$(this).find('legend').first().text()
				+'</li>');
			
			$('#onglet-'+i).click(function(e){
				$('.formulaire_editer fieldset, .formulaire_editer fieldset:hidden')
					.eq(i).show().siblings('fieldset, fieldset:hidden').hide();
				$('#onglets li').removeClass('onglet_courant');
				$('#onglets li').eq(i).addClass('onglet_courant');
			});
		
			$(this).find('legend').first().hide();
			$(this).hide();
		});
		// init
		panes.first().show();
		$('#onglet-0').toggleClass('onglet_courant');
	
	}
}