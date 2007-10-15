<?php
// spiplistes_menu_navigation.php
// Original From SPIP-Listes-V :: $Id: spiplistes_menu_navigation.php paladin@quesaco.org $

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

//	Ajout des gadgets


if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_spiplistes_menu_navigation () {

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	if($connect_statut == '0minirezo') {

		$gadgets_array = array(
			array(
				'href' => generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_EDIT,'new=oui&type=nl')
				, 'img_src' => _DIR_IMG_PACK."creer.gif"
				, 'img_bg' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_brouillon-24.png"
				, 'alt' => _T('spiplistes:Nouveau_courrier')
			)
			, array(
				'href' => generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_EDIT,'new=oui')
				, 'img_src' => _DIR_IMG_PACK."creer.gif"
				, 'img_bg' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."reply-to-all-24.gif"
				, 'alt' => _T('spiplistes:Nouvelle_liste_de_diffusion')
			)
		);
		
		$result = "<div style='width: 300px;'>\n";
		
		foreach($gadgets_array as $gadget) {
			$result .= ""
				. "<div style='width: 140px; float: left;'>\n"
				. "<table class='cellule-h-table' style='vertical-align: middle;' cellpadding='0'>\n"
				. "<tr><td>"
				. "<a href='{$gadget['href']}' class='cellule-h'>"
				. "<span class='cell-i'>"
				. "<img src='{$gadget['img_src']}' alt='{$gadget['alt']}' "
					. "style='background: transparent url({$gadget['img_bg']}) no-repeat scroll center;"
						. " -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial;'>\n"
				. "</span></a></td>\n"
				. "<td class='cellule-h-lien'>"
				. "<a href='{$gadget['href']}' class='cellule-h'>"
				. $gadget['alt']
				. "</a></td></tr></table>\n"
				. "</div>\n"
				;
		}
		
		$gadget .= "</div>\n";
		
		ajax_retour($result);
	}
}
?>