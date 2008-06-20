<?php 

// inc/spiplistes_pipeline_header_prive.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/*
	
	Nota: plugin.xml en cache.
		si modif plugin.xml, il faut r�activer le plugin (config/plugin: d�sactiver/activer)
	
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

function spiplistes_header_prive ($flux) {

	$exec = _request('exec');

	$flux .= ""
		. "\n\n<!-- PLUGIN SPIPLISTES GADGETS v.: ".__plugin_real_version_get(_SPIPLISTES_PREFIX)." -->\n"
		. "<script src='".url_absolue(find_in_path('javascript/spiplistes_gadgets.js'))."' type='text/javascript'></script>\n"
		. "
<!-- SpipListes CSS -->
<style type='text/css'>
<!--
.spiplistes fieldset { border:1px solid black; }
.spiplistes .message-alerte { color:red; font-weight:bold; }
.spiplistes .liste-moderateurs
, .spiplistes .liste-listes { list-style: none;margin:0;padding:0;width:100%;height:auto;display:table; }
.spiplistes .liste-moderateurs li 
, .spiplistes .liste-moderateurs li .statut-nom
, .spiplistes .liste-moderateurs li .supprim { margin:0;padding:0 0 0 0.5ex;display:table-cell;line-height:1.6em;width:50%; }
.spiplistes .liste-moderateurs li  { display:table-row;width:100% }
.spiplistes .liste-moderateurs li .nom { padding-left:2ex }
.spiplistes .liste-moderateurs li .supprim { font-weight:bold;text-align:right;padding:0 1ex }
-->
</style>
"
	;

	if(in_array($exec, array(
		_SPIPLISTES_EXEC_ABONNES_LISTE
		, _SPIPLISTES_EXEC_COURRIER_EDIT
		, _SPIPLISTES_EXEC_COURRIERS_LISTE
		, _SPIPLISTES_EXEC_LISTES_LISTE
		, _SPIPLISTES_EXEC_MAINTENANCE
		, _SPIPLISTES_EXEC_CONFIGURE
		, 'auteur_infos' // liste-listes
		)
		)
	) {
		
		$flux .= "\n\n<!-- PLUGIN SPIPLISTES v.: ".__plugin_real_version_get(_SPIPLISTES_PREFIX)." -->\n"
					. "<link rel='stylesheet' href='"._DIR_PLUGIN_SPIPLISTES."spiplistes_style_prive.css' type='text/css' media='all' />\n"
					;

		switch($exec) {
			case _SPIPLISTES_EXEC_COURRIER_EDIT:
				$flux .= ""
					. "<script type=\"text/javascript\" src=\"" ._DIR_PLUGIN_SPIPLISTES . "javascript/spiplistes_courrier_edit.js\"></script>\n"
/*
 le datepicker ne sert plus dans cette version.					
					. "<link rel='stylesheet' href='".url_absolue(find_in_path('img_pack/date_picker.css'))."' type='text/css' media='all' />\n"
					. "<script src='".url_absolue(find_in_path('javascript/datepicker.js'))."' type='text/javascript'></script>\n"
 A priori, ce bout de code ne sert plus
					. "<script src='".url_absolue(find_in_path('javascript/jquery-dom.js'))."' type='text/javascript'></script>\n"
*/					
					. "<meta http-equiv='expires' content='0'>\n"
					. "<meta http-equiv='pragma' content='no-cache' />\n"
					. "<meta http-equiv='cache-control' content='no-cache' />\n"
					;
				break;
			case _SPIPLISTES_EXEC_COURRIERS_LISTE:
				break;
			case _SPIPLISTES_EXEC_CONFIGURE:
				$flux .= "
<!-- SpipListes JS -->
<script type='text/JavaScript'>
<!--
	jQuery.fn.toggle_options = function(div_options) {
			if($(this).attr('checked')) {
				$(div_options).show();
			}
			else {
				$(div_options).hide();
			}
	};
	$(document).ready(function(){
		$('#opt-lien-en-tete-courrier').click( function() { $(this).toggle_options('#div-lien-en-tete-courrier') } );
		$('#opt-ajout-tampon-editeur').click( function() { $(this).toggle_options('#div-ajout-tampon-editeur') } );
		$('#view-spiplistes-log').toggle(function(){
			/* demander le journal systeme */
			$.post('" . generer_url_action('spiplistes_lire_console', '') . "', function(data) {
				$('#view-spiplistes-log-box').append(data);
			});
			$(this).html(\""._T('spiplistes:masquer_les_journaux_SPIPLISTES')."\");
		},function(){
			$('#view-spiplistes-log-box').empty();
			$(this).html(\""._T('spiplistes:Voir_les_journaux_SPIPLISTES')."\");
		});
	});
//-->
</script>
";
				break;
		}

		$flux .="<!-- / PLUGIN SPIPLISTES -->\n\n";

	}
	return ($flux);
}

?>