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

	$idom = 'nouv_admin_'.$admid;

    if (!$determiner_non_admins = charger_fonction('determiner_non_admins','inc',true))
        $determiner_non_admins = 'determiner_non_admins';

	$futurs = selectionner_admin_boucle($determiner_non_admins('acsadmins',$admid,'', "nom, statut"), $idom, $admid);

	// url completee par la fonction JS onkeypress_rechercher
	$url = generer_url_ecrire('acs_rechercher_admin', "idom=$idom&admid=$admid&nom=");

  // function acs_construire_selectionner_hierarchie($idom, $liste, $racine, $url, $name, $url_init='')
	return acs_construire_selectionner_hierarchie($idom, $futurs, _T('acs:admins'), $url, 'nouv_admin_'.$admid);
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

		$res .= '<a class="highlight off"'.
			' onclick="changerhighlight(this);findObj_forcer(\'nouv_admin_'.$admid.'\').value='.$id.';aff_selection('.$id.','.$args.');return false;"'.
		  ' ondbclick="findObj_forcer(\'nouv_admin_'.$admid.'\').value='.$id.'";findObj_forcer("acs_selection_admin").style.display="none";return false;"'.
		  '>'.typo(extraire_multi($row["nom"])).'</a> ';
	}

	return $res;
}

// fonction pompee sur http://doc.spip.org/@construire_selectionner_hierarchie
// possede des classes en plus de l'original permettant de styler la presentation
function acs_construire_selectionner_hierarchie($idom, $liste, $racine, $url, $name, $url_init='')
{
	global $spip_lang_right;

	$idom1 = $idom . "_champ_recherche";
	$idom2 = $idom . "_principal";
	$idom3 = $idom . "_selection";
	$idom4 = $idom . "_col_1";
	$idom5 = 'img_' . $idom4;
	$idom6 = $idom."_fonc";

	return "<div id='$idom'>"
	. "<a id='$idom6' style='visibility: hidden;'"
	. ($url_init ?  "\nhref='$url_init'" : '')
	. "></a>"
	. "<div class='recherche_rapide_parent'>"
	. http_img_pack("searching.gif", "*", "style='visibility: hidden;float:$spip_lang_right' id='$idom5'")
	. ""
	. "<input style='width: 100px;float:$spip_lang_right;' type='search' id='$idom1'"
	  // eliminer Return car il provoque la soumission (balise unique)
	  // ce serait encore mieux de ne le faire que s'il y a encore plusieurs
	  // resultats retournes par la recherche
	. "\nonkeypress=\"k=event.keyCode;if (k==13 || k==3){return false;}\""
	  // lancer la recherche apres le filtrage ci-dessus
	. "\nonkeyup=\"return onkey_rechercher(this.value,"
	  // la destination de la recherche
	. "'$idom4'"
#	. "this.parentNode.parentNode.parentNode.parentNode.nextSibling.firstChild.id"
	. ",'"
	  // l'url effectuant la recherche
	. $url
	. "',"
	  // le noeud contenant un gif anime
	  // . "'idom5'"
	. "this.parentNode.previousSibling.firstChild"
	. ",'"
	  // la valeur de l'attribut Name a remplir
	.  $name
	. "','"
	  // noeud invisible memorisant l'URL initiale (pour re-initialisation)
	. $idom6
	. "')\""
	. " />"
	. "\n</div>"
	. ($racine?"<div>$racine</div>":"")
  	. "<div id='$idom2' style='float:left;width:50%'>
  			<div id='$idom4' class='arial1'>$liste</div>
  		</div>
  		<div id='$idom3' style='float:left;width:50%'></div>
		</div>\n";
}
?>
