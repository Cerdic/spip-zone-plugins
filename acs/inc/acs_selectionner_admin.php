<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acs_editer_admins');
include_spip('inc/selectionner');

//
// Affiche un mini-navigateur ajax sur les admins
//

// http://doc.spip.org/@inc_selectionner_auteur_dist
function inc_acs_selectionner_admin_dist($admid)
{
	global $spip_lang_right, $couleur_foncee;

	$idom = 'nouv_admin';

    if (!$determiner_non_admins = charger_fonction('determiner_non_admins','inc',true))
        $determiner_non_admins = 'determiner_non_admins';

	$futurs = selectionner_admin_boucle($determiner_non_admins('acsadmins',$admid,'', "nom, statut"), $idom, $admid);

	// url completee par la fonction JS onkeypress_rechercher
	$url = generer_url_ecrire('acs_rechercher_admin', "idom=$idom&nom=");

  // http://doc.spip.org/@construire_selectionner_hierarchie (spip 1.9208)
  //function construire_selectionner_hierarchie($idom, $liste, $racine, $url, $name, $url_init='')
	return construire_selectionner_hierarchie($idom, $futurs, _T('acs:admins'), $url, 'nouv_admin_'.$admid);
}

// http://doc.spip.org/@selectionner_auteur_boucle
function selectionner_admin_boucle($query, $idom, $admid)
{
	global  $spip_lang_left;

	$info = generer_url_ecrire('informer_auteur', "id=");
	$args = "'$idom" . "_selection', '$info', event";
	$res = '';

	while ($row = spip_fetch_array($query)) {

		$id = $row["id_auteur"];

		// attention, les <a></a> doivent etre au premier niveau
		// et se suivrent pour que changerhighligth fonctionne
		// De plus, leur zone doit avoir une balise et une seule
		// autour de la valeur pertinente pour que aff_selection
		// fonctionne (faudrait concentrer tout ca).

		$res .= "<a class='highlight off'"
		. "\nonclick=\"changerhighlight(this);"
		. "findObj_forcer('nouv_admin_$admid').value="
		. $id
		. "; aff_selection($id,$args); return false;"
		. "\"\nondbclick=\""
		. "findObj_forcer('nouv_admin_$admid').value="
		. $id
		. ";findObj_forcer('acs_selection_admin').style.display="
		. "'none'; return false"
		. "\"><b>"
		. typo(extraire_multi($row["nom"]))
		. "</b></a> ";
	}

	return $res;
}
?>
