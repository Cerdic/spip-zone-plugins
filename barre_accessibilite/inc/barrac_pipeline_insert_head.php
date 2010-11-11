<?php 
	
	// inc/barrac_pipeline_insert_head.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of BarrAc.
	
	BarrAc is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	BarrAc is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with BarrAc; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de BarrAc. 
	
	BarrAc est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiee par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	BarrAc est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU 
	pour plus de details. 
	
	Vous devez avoir recu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/utils');
include_spip('inc/plugin_globales_lib');
include_spip('inc/barrac_api_icones');

// pipeline (plugin.xml)
// Insere les css de la barre d'accessibilite dans l'espace public
function barrac_insert_head ($flux) {

	$barrac_js_declarations = $barrac_js_var_init = $barrac_js_events_manager = 
		$barrac_js_cookie_init_case = $barrac_js_bouton_switch =
		$barrac_js_code_result = $barrac_flux_complements = "";

	$config_default = unserialize(_BARRAC_DEFAULT_VALUES_ARRAY);
	
	// charger la config barrac
	$config = __plugin_lire_key_in_serialized_meta('config', _BARRAC_META_PREFERENCES);
	
	// corrige les manques eventuels
	foreach($config_default as $key=>$value) {
		if(!isset($config[$key])) $config[$key] = $config_default[$key];
	}
	foreach($config as $key=>$value) {
		$$key = $value;
	}

	// Combien de boutons actifs ?
	$boutons_actifs_count = barrac_boutons_actifs_count($config);

	// pas de boutons ? 
	if(!$boutons_actifs_count) return($flux);
	
	// valider la position
	$config['barrac_position_barre'] = barrac_confirmer_valeur_var(
		$config['barrac_position_barre']
		, unserialize(_BARRAC_POSITIONS_ARRAY)
		, _BARRAC_POSITION_DEFAULT
	);
	list($ii, $jj) = explode("_", $config['barrac_position_barre']);
	$style_barre = "$ii:0px;$jj:0px;";
	
	if($config['barrac_position_fixed'] == 'oui') {
		// sera corrige en dynamique par affichage_final pour IE qui ne comprend pas fixed
		$style_barre .= " position:fixed;"; 
	}
	
	// valider la marge
	$config['barrac_marge_entre_boutons'] = intval($config['barrac_marge_entre_boutons']);
	$config['barrac_marge_entre_boutons'] = 
		(($config['barrac_marge_entre_boutons'] >= 0) && ($config['barrac_marge_entre_boutons'] <= _BARRAC_ICONE_MARGE_MAX))
		? $config['barrac_marge_entre_boutons']
		: _BARRAC_ICONE_MARGE_DEFAULT
		;

	// valider la taille
	$config['barrac_taille_bouton'] = intval($config['barrac_taille_bouton']);
	$config['barrac_taille_bouton'] = 
		(($config['barrac_taille_bouton'] >= 0) && ($config['barrac_taille_bouton'] <= _BARRAC_ICONE_TAILLE_MAX))
		? $config['barrac_taille_bouton']
		: _BARRAC_ICONE_TAILLE_DEFAULT
		;

	// valider la presentation 
	$config['barrac_presentation_barre'] = barrac_confirmer_valeur_var($config['barrac_presentation_barre']
		, array(_BARRAC_PRESENTATION_HORIZONTAL, _BARRAC_PRESENTATION_VERTICAL)
		, _BARRAC_PRESENTATION_DEFAULT);

	// definir taille de la barre
	$ii = barrac_barre_largest_side_size($config, $boutons_actifs_count);

	switch($config['barrac_presentation_barre']) {
		case _BARRAC_PRESENTATION_HORIZONTAL:
			$width = $ii;
			$height = $config['barrac_taille_bouton'];
			$padding = "margin-left:".$config['barrac_marge_entre_boutons']."px;";
			$style_item = " float:left; margin-right:".$config['barrac_marge_entre_boutons']."px;";
			break;
		case _BARRAC_PRESENTATION_VERTICAL:
			$width = $config['barrac_taille_bouton'];
			$height = $ii;
			$padding = "margin-top:".$config['barrac_marge_entre_boutons']."px;";
			$style_item = " margin-bottom:".$config['barrac_marge_entre_boutons']."px;";
			break;		
	}
	$style_barre .= "width:".$width."px;height:".$height."px;".$padding;

	// bouton action grossir taille caracteres
	if($config[_BARRAC_ACTION_GROSSIR]=='oui') {
		if(($config['barrac_grossir_global'] == "non") 
			&& ($ii = barac_compacte_css_link ('barrac_grossir_css', $config['barrac_grossir_cssfile'], 'nav_caracteres_grossir'))
			) {
			$barrac_flux_complements .= $ii;
			$reaction_evenement_parent = " $('#barrac_grossir_css').attr('disabled',false);";
			$reaction_evenement_frere = " $('#barrac_grossir_css').attr('disabled',true);";
		}
		else {
			$barrac_js_declarations .= ""
				. " var barrac_grossir = {'fontSize': 0};"
				;
			$barrac_js_var_init .= ""
				. " barrac_grossir['fontSize'] = barrac_init_string($('body').css('fontSize'));"
				;
			$reaction_evenement_parent = ""
				. " $('body').css('fontSize','".$config['barrac_grossir_taille']."%');";
			$reaction_evenement_frere = ""
				. " $.each( barrac_grossir, function(i, n){ $('body').css(i, n); });";
		}
		$reaction_evenement_parent .= barrac_javascript_cookie(_BARRAC_ACTION_GROSSIR, 'oui');
		$reaction_evenement_frere .= barrac_javascript_cookie(_BARRAC_ACTION_GROSSIR, 'non');
		$barrac_js_events_manager .= 
			barrac_js_events_manager (_BARRAC_ACTION_GROSSIR, _BARRAC_ACTION_REDUIRE, $reaction_evenement_parent, $reaction_evenement_frere);
		$barrac_js_cookie_init_case .= barrac_js_cookie_init_case(_BARRAC_ACTION_GROSSIR);
		$barrac_js_bouton_switch .= barrac_js_bouton_switch (_BARRAC_ACTION_GROSSIR, _BARRAC_ACTION_REDUIRE);
	}
	
	// bouton action espacement des liens
	if($config[_BARRAC_ACTION_ESPACER]=='oui') {
		if(($barrac_espacer_global == "non") 
			&& ($ii = barac_compacte_css_link ('barrac_espacer_css', $config['barrac_espacer_cssfile'], 'nav_espacer_liens'))
			){
			$barrac_flux_complements .= $ii;
			$reaction_evenement_parent = " $('#barrac_espacer_css').attr('disabled',false);";
			$reaction_evenement_frere = "	$('#barrac_espacer_css').attr('disabled',true);";
		}
		else {
			$barrac_js_declarations .= ""
				. " var barrac_espacer = {'paddingLeft': 0, 'paddingRight': 0};"
				;
			$barrac_js_var_init .= ""
				. " barrac_espacer['paddingLeft'] = barrac_init_string($('//p/a').css('paddingLeft'));"
				. " barrac_espacer['paddingRight'] = barrac_init_string($('//p/a').css('paddingRight'));"
				;
			$reaction_evenement_parent = ""
				. " $('//p/a').css({ paddingLeft: '".$config['barrac_espacer_taille']."', paddingRight: '".$config['barrac_espacer_taille']."' });";
			$reaction_evenement_frere = ""
				. " $.each( barrac_espacer, function(i, n){ $('//p/a').css(i, n); });";
		}
		$reaction_evenement_parent .= barrac_javascript_cookie(_BARRAC_ACTION_ESPACER, 'oui');
		$reaction_evenement_frere .= barrac_javascript_cookie(_BARRAC_ACTION_ESPACER, 'non');
		$barrac_js_events_manager .= 
			barrac_js_events_manager (_BARRAC_ACTION_ESPACER, _BARRAC_ACTION_RAPPROCHER, $reaction_evenement_parent, $reaction_evenement_frere);
		$barrac_js_cookie_init_case .= barrac_js_cookie_init_case(_BARRAC_ACTION_ESPACER);
		$barrac_js_bouton_switch .= barrac_js_bouton_switch (_BARRAC_ACTION_ESPACER, _BARRAC_ACTION_ESPACER);
	}
	
	// bouton action encadrement des paragraphes
	if($config[_BARRAC_ACTION_ENCADRER]=='oui') {
		if(($config['barrac_encadrer_global'] == "non")
			&& ($ii = barac_compacte_css_link ('barrac_encadrer_css', $config['barrac_encadrer_cssfile'], 'nav_encadrer_liens'))
			) {
			$barrac_flux_complements .= $ii;
			$reaction_evenement_parent = " $('#barrac_encadrer_css').attr('disabled',false);";
			$reaction_evenement_frere = " $('#barrac_encadrer_css').attr('disabled',true);";
		}
		else {
			$barrac_js_declarations .= ""
				. " var barrac_encadrer = {'titre': {'padding':'', 'border':''}, 'texte': {'padding':'', 'border':''}};"
				;
			$barrac_js_var_init .= ""
				. " barrac_encadrer['titre']['padding'] = barrac_init_string($('.titre').css('padding'));"
				. " barrac_encadrer['titre']['border'] = barrac_init_string($('.titre').css('border'));"
				. " barrac_encadrer['texte']['padding'] = barrac_init_string($('.texte').css('padding'));"
				. " barrac_encadrer['texte']['border'] = barrac_init_string($('.texte').css('border'));"
				;
			$reaction_evenement_parent = ""
				. " $('.titre, .texte').css({padding: '".$config['barrac_encadrer_padding']."', border: '".$config['barrac_encadrer_taille']." solid ".$config['barrac_encadrer_couleur']."'});";
			$reaction_evenement_frere = ""
				. " $.each( barrac_encadrer['titre'], function(i, n){ $('.titre').css(i, n); });"
				. " $.each( barrac_encadrer['texte'], function(i, n){ $('.texte').css(i, n); });";
		}
		$reaction_evenement_parent .= barrac_javascript_cookie(_BARRAC_ACTION_ENCADRER, 'oui');
		$reaction_evenement_frere .= barrac_javascript_cookie(_BARRAC_ACTION_ENCADRER, 'non');
		$barrac_js_events_manager .= 
			barrac_js_events_manager (_BARRAC_ACTION_ENCADRER, _BARRAC_ACTION_DECADRER, $reaction_evenement_parent, $reaction_evenement_frere);
		$barrac_js_cookie_init_case .= barrac_js_cookie_init_case(_BARRAC_ACTION_ENCADRER);
		$barrac_js_bouton_switch .= barrac_js_bouton_switch (_BARRAC_ACTION_ENCADRER, _BARRAC_ACTION_DECADRER);
	}

	// bouton action inversion des couleurs
	if($config[_BARRAC_ACTION_INVERSER]=='oui') {
		if(($config['barrac_inverser_global'] == "non") 
			&& ($ii = barac_compacte_css_link ('barrac_inverser_css', $config['barrac_inverser_cssfile'], 'nav_inverser_liens'))
			) {
			$barrac_flux_complements .= $ii;
			$reaction_evenement_parent = " $('#barrac_inverser_css').attr('disabled',false);";
			$reaction_evenement_frere = " $('#barrac_inverser_css').attr('disabled',true);";
		}
		else {
				// les filtres ne fonctionnent que pour MSIE (html si >= 6)
				$reaction_evenement_parent = "
					$('body').css('filter', 'Invert()');";
				$reaction_evenement_frere = "
					$('body').css('filter', '');";
		}
		if(!empty($reaction_evenement_parent)) {
			$reaction_evenement_parent .= barrac_javascript_cookie(_BARRAC_ACTION_INVERSER, 'oui');
			$reaction_evenement_frere .= barrac_javascript_cookie(_BARRAC_ACTION_INVERSER, 'non');
			$barrac_js_events_manager .= 
				barrac_js_events_manager (_BARRAC_ACTION_INVERSER, _BARRAC_ACTION_REPLACER, $reaction_evenement_parent, $reaction_evenement_frere);
			$barrac_js_cookie_init_case .= barrac_js_cookie_init_case(_BARRAC_ACTION_INVERSER);
			$barrac_js_bouton_switch .= barrac_js_bouton_switch (_BARRAC_ACTION_INVERSER, _BARRAC_ACTION_REPLACER);
		}
	}

	if(!empty($barrac_js_events_manager)) {
		$barrac_js_code_result = "
"
.	$barrac_js_declarations
.	" 
	function barrac_init_intval(v) { return(!v ? 0 : v); }
	function barrac_init_string(s) { return(!s ? '' : s); }
	function barrac_cookieCreate (name, value, days) {
		var expires = '';
		if (days) {
			 var date = new Date();
			 date.setTime(date.getTime() + (days*24*60*60*1000));
			 expires = '; expires=' + date.toGMTString();
		}
		document.cookie = name + '=' + value + expires + '; path=/';
		return (true);
	}

	$(document).ready(function() {
		
		var barrac_montre_toi = true;
		
		if('" . $barrac_mobile_no_display . "'=='oui') {
			/* alert(jQuery.fn.jquery); */
			if(navigator.userAgent.match(/webkit/i)) {
					barrac_montre_toi = false;
			}
		}
		
		if(barrac_montre_toi) {
			$('#barrac_boutons').css('display','block');
		
"
.	$barrac_js_var_init
.	"
		if($.browser.msie) { 
			$.extend ({ 
				key_reset: function(ctrlKey, keyCode) { return (ctrlKey && ( keyCode == 106)); }
			}); 
		}
		else if($.browser.mozilla) {
			$.extend ({ 
				key_reset: function(ctrlKey, keyCode) { return (ctrlKey && ( keyCode == 96)); }
			}); 
		}
		else if($.browser.opera) {
			$.extend ({ 
				key_reset: function(ctrlKey, keyCode) { return (keyCode == 42); }
			}); 
		}
		
		$(document).keyup( function(evenement) { 
			var keyCode = window.event ? evenement.keyCode : evenement.which;
			var ctrlKey = evenement.ctrlKey;
			if($.key_reset(ctrlKey, keyCode)) {
				"
				. $barrac_js_bouton_switch
				. "
			}			
			return (false);
		});

		" 
. $barrac_js_events_manager
. "
		if(document.cookie!=''){
			cookies = document.cookie.split('; ');
			var cc;
			for(var ii = 0; ii < cookies.length; ii++) {
				cc = cookies[ii].split('=');
				statut = cc[1];
				switch(cc[0]) {
					"
. $barrac_js_cookie_init_case
. "
				}
			}
		}
		} /* fin if(barrac_montre_toi) */
		else {
			$('#barrac_boutons').css('display','none');
		}
	}); /* fin ready() */
";

	// corriger la transparence des fonds en png pour IE > 5.5 < 7.0
	// pour IE 5.5, le filtre ne fonctionne pas ! Laisser sales icones !
	$barrac_js_ie_complements = "
<!--[if (gte IE 6)&(lt IE 7)]>
<script type='text/javascript'>
	$.fn.extend({
		barrac_png_image_swap: function() {
			var png_src = '';
			png_src = $(this).css('backgroundImage');
			$(this).css('backgroundImage', 'none');
			png_src = png_src.substr(4);
			png_src = png_src.substr(0, png_src.length - 1);
			var filtre = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + png_src + ')';
			$(this).css('filter', filtre);
		}
	});
	$(document).ready(function() {
		$('#barrac_boutons').children('.item').each(function(){ 
			$(this).barrac_png_image_swap();
		});
		$('#barrac_boutons a').each(function() {
			$(this).barrac_png_image_swap();
		});
	});
</script>
<![endif]-->
";
		}
	
	// les feuilles CSS de barrac
	$barrac_flux_styles = "
<!-- barrac CSS -->
<style type='text/css' media='screen'>
#barrac_boutons, #barrac_boutons *
, #barrac_boutons .item a:link, #barrac_boutons .item a:visited, #barrac_boutons .item a:hover, #barrac_boutons .item a:active {
	display:block;
	margin:0; padding:0;	border:0;
}
#barrac_boutons {
	position: absolute;
	width:42px; /* calcule par insert_head */
	height:42px; /* calcule par insert_head */
	z-index: 32000;
	list-style: none;
}
#barrac_boutons .item, #barrac_boutons .item a {
	display:block;
	width:24px;
	height:24px;
	background: url(none) no-repeat center center;
	overflow: hidden;
}
#barrac_boutons .item div { }

#barrac_boutons a:link, #barrac_boutons a:visited, #barrac_boutons a:hover, #barrac_boutons a:active {
	text-decoration: none;
}
</style>
<!--[if IE]>
<style type='text/css' media='screen'>
#barrac_boutons {
	width: auto !important;/**/
}
#barrac_boutons .item {
	cursor: hand; 
	margin:0; padding:0; border:0; font-size:1pt; line-height:1pt;
}
</style>
<![endif]-->
";/**/
	
	// place la barre des boutons
	$barrac_flux_styles .= "
<style type='text/css'>
#barrac_boutons {" . $style_barre . "}
#barrac_boutons .item { background-image: url(" . barrac_icone_fond() . ");width:" . $config['barrac_taille_bouton'] . "px;height:" . $config['barrac_taille_bouton'] . "px;$style_item }
#barrac_boutons .item a { width:" . $config['barrac_taille_bouton'] . "px;height:" . $config['barrac_taille_bouton'] . "px; }
</style>
";

/* */
$barrac_js_code_result = "
<!-- barrac JS -->
<script type='text/javascript'>
//<![CDATA[
" 
. preg_replace('=[[:space:]]+=', ' ', barrac_compacte_js($barrac_js_code_result)) 
//. $barrac_js_code_result
. "
//]]>
</script>
";

	// assembler le tout
	$barrac_flux_complements .= $barrac_js_code_result . $barrac_js_ie_complements;
	
	// compresser
	$barrac_flux_styles = barrac_compacte_css($barrac_flux_styles);

	$flux .= $barrac_flux_styles . $barrac_flux_complements;

	return ($flux);
}


/******************************************************************************/

function barac_compacte_css_link ($id, $href, $title) {
	if(!empty($href) && ($href = find_in_path($href)) && ($href = compacte($href, 'css'))) {
		return("<link id='$id' href='".$href."' rel='alternate stylesheet'" . " title='"._T(_BARRAC_LANG.$title)."' type='text/css' />\n");
	}
	return(false);
}

function barrac_js_events_manager ($action_parent, $action_frere, $reaction_parent, $reaction_frere) {
	return(
			"
		$('#"._BARRAC_PREFIX."_item_".$action_parent."').click( function() { 
			$('#"._BARRAC_PREFIX."_item_".$action_frere."').show();
			$('#"._BARRAC_PREFIX."_item_".$action_parent."').hide();" . $reaction_parent
			. "
		});
		$('#"._BARRAC_PREFIX."_item_".$action_frere."').click( function() { 
			$('#"._BARRAC_PREFIX."_item_".$action_parent."').show();
			$('#"._BARRAC_PREFIX."_item_".$action_frere."').hide();" . $reaction_frere
			. "
		});
		");
}

function barrac_js_bouton_switch ($action_parent, $action_frere) {
	return("
		if($('#"._BARRAC_PREFIX."_item_".$action_parent."').css('display') == 'none') {
			$('#"._BARRAC_PREFIX."_item_".$action_frere."').click();
		}
	");
}

function barrac_js_cookie_init_case ($action_parent) {
	return(	"
					case '" . _BARRAC_PREFIX . "_" . $action_parent . "':
						if(statut == 'oui') { $('#" . _BARRAC_PREFIX . "_item_" . $action_parent . "').click(); } 
						break;
				"
	);
}

function barrac_javascript_cookie ($action_parent, $value) {
	return(	"
			barrac_cookieCreate('" . _BARRAC_PREFIX . "_" . $action_parent . "', '".$value."', 365);"
	);
}

/** barrac_confirmer_valeur_var () 
	Verifier si la variable contient une valeur acceptable.
	Renvoyer si ok, sinon renvoyer valeur par defaut.
*/
function barrac_confirmer_valeur_var ($var, $acceptables, $defaut) {
	return ((!empty($var) && in_array($var, $acceptables)) ? $var : $defaut);
}
