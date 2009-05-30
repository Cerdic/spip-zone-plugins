<?php 

	// inc/player_pipeline_header_prive.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	// CP-20080321

	
if (!defined("_ECRIRE_INC_VERSION")) return;

// pipeline (plugin.xml)
// Ajoute l'appel aux feuilles de style dans le header privé
function player_header_prive ($flux) {

	global $connect_statut
		, $connect_toutes_rubriques
		, $couleur_foncee
		, $couleur_claire
		;

	$exec = _request('exec');
	
	if(
		($exec == 'player_admin')
		&& ($connect_statut == '0minirezo')
		&& $connect_toutes_rubriques
		) {

		//		
		$flux .= ""
			. "

<style type='text/css'>
<!--
.player_flv_player {  margin:0 0 0.5em; padding:0; list-style: none; font-size:90%; }
.player_flv_player li { border:1px solid ".($couleur_claire ? $couleur_claire : 'edf3fe')."; display:inline; padding:0.5em 1ex; margin:0 3px 0 0; font-weight: bold; }
.player_flv_player .onglet_on { background-color: ".($couleur_claire ? $couleur_claire : '#edf3fe')."; color: ".($couleur_foncee ? $couleur_foncee : '#3874b0')."; }
.player_flv_player .onglet_off { background-color: ".($couleur_foncee ? $couleur_foncee : '#3874b0').";	color: #fff; }
#player_flv_options { list-style: none; margin:1em 0 0; padding:0; }
#player_flv_options li { margin:0 0 0.5em; }
.incolor input { width:8ex; border:1px solid black; padding:0 0.5ex;}
.colorpicker { border:1px solid red; text-align:center; margin:0.5em auto; height:auto; width: 200px; }
.colorpicker_bar { background-color:grey; text-align:right; padding-right:0.5ex; color:white; }
.colorpicker_close { display:block; width:1em; height: 1em; font-weight:bold; margin: 0 0 0 auto; }
.colorpicker_hide { height:auto; display:block; }
.hover { cursor:pointer; }
-->
</style>
<script type='text/javascript' src='"._DIR_PLUGIN_PLAYER_JAVASCRIPT."farbtastic/farbtastic.js'></script>
<link rel='stylesheet' href='"._DIR_PLUGIN_PLAYER_JAVASCRIPT."farbtastic/farbtastic.css' type='text/css' />
<script type='text/javascript'>
//<![CDATA[
	var colorpicker_is_active = false;
	$(document).ready(function() {
		$('.incolor').click(function() { 
			if(colorpicker_is_active) return(false);
			var color_dest = $(this).attr('title');
			$(this).addClass('colorpicker_hide');
			$(this).after('<div class=\'colorpicker\'><div class=\'colorpicker_bar\'><div class=\'colorpicker_close\'>X</div></div><div id=\'colorpicker\'></div></div>');
			$('#colorpicker').css({display:'block'}).farbtastic('#' + color_dest);
			colorpicker_is_active = true;
			$('.colorpicker_close').hover(function(){
				$(this).addClass('hover');
			},function(){
				$(this).removeClass('hover');
			});
			$('.colorpicker_close').click( function() { 
				$(this).removeClass('colorpicker_hide');
				$('.colorpicker').empty().remove();
				colorpicker_is_active = false;
			});
		});
		$('.flv_onglet').mouseover(function() { 
			$(this).addClass('onglet_on');
		});
		$('.flv_onglet').mouseout( function() { 
			if(!$(this).find('input').attr('checked')) {
				$(this).removeClass('onglet_on');
			}
		});
		$('.flv_onglet>label>input').click(function() {
			var current_class = $(this).parent().parent().attr('class');
			if(!(current_class.search(/onglet_off/) > 0)) {
				$('.flv_onglet').removeClass('onglet_on').removeClass('onglet_off');
				$(this).parent().parent().addClass('onglet_off');
				var selected_class = $(this).attr('id');
				var flv_obj = ['".implode('\',\'',array_keys(unserialize(_PLAYER_FLV_LECTEURS)))."'];
				for(ii in flv_obj) {
					if(flv_obj[ii] != selected_class) {
						$('li.'+flv_obj[ii]).hide();
					}
				}
				$('li.'+selected_class).show();
			}
		});
	});
//]]>
</script>
"
		;
	}
	
	return ($flux);
}

?>