<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');   // for spip presentation functions
include_spip('inc/config');   		// for spip presentation functions
include_spip('inc/layer');          // for spip layer functions
include_spip('inc/utils');          // for _request function
include_spip('inc/plugin');         // xml function

include_spip('inc/date');


//ajoute un div de selection archive oui/non
function inc_depublication_dist($id_article) {
	
	global $spip_lang_left, $spip_lang_right, $options;
	global $connect_statut, $options,$connect_id_auteur, $couleur_claire ;


	//ne fait rien si le plugin n'est pas initialisé ie n'a pas de version
	if (!isset($GLOBALS['meta']['depublication_version'])) {
		return "";
		exit;
	}
	

	// on récupère la date de dépublication de l'article
	
	
	$result = spip_query('select depublication from spip_articles_depublication where id_article='.$id_article);
	$row = spip_fetch_array($result);
	if ($row['depublication']) {
		$date = $row['depublication'];
	} else {
		$date = '';
	}
	
	
	if (ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})( ([0-9]{2}):([0-9]{2}))?", $date, $regs)) {
		$annee = $regs[1];
		$mois = $regs[2];
		$jour = $regs[3];
		$heures = $regs[5];
		$minutes = $regs[6];
	}
	
	
	$out = "<div id='depublication-$id_article'>";
	$out .= "<a name='depublication'></a>";
	if (_request('edit')||_request('neweven'))
		$bouton = bouton_block_visible("depublication");
	else
		$bouton = bouton_block_invisible("depublication");
	
	
	if ($date != '') {
		$date = majuscules(affdate_heure($date));
	} else {
		$date = 'Pas de date fixée pour l\'instant';
	}
	$out .= debut_cadre_enfonce("../"._DIR_PLUGIN_DEPUBLICATION."images/depublication-24.png", true, "", $bouton."DEPUBLICATION : ".$date);

	$out .= debut_block_invisible("depublication");
	
	$out .= "Veuillez sélectionner la date de dépublication de votre article";
	
	$js = "size='1' class='fondl'
		onchange=\"findObj_forcer('valider_depublication').style.visibility='visible';\"";

	$masque = 
		  afficher_jour($jour, "name='jour' $js", true)
		. afficher_mois($mois, "name='mois' $js", true)
		. afficher_annee($annee, "name='annee' $js")
		. (' - '
			. afficher_heure($heures, "name='heures' $js")
		      . afficher_minute($minutes, "name='minutes' $js"))
		  . "&nbsp;\n";

		$res = "<div style='margin: 5px; margin-$spip_lang_left: 20px;'>"
		.  ajax_action_post("depublication", 
					$id_article,
					$script,
					"id=".$id_article,
					$masque,
					_T('bouton_changer'),
				       " class='fondo visible_au_chargement' id='valider_depublication'", "","")
		.  "</div>";

		$out .= $res;
	
	
	$out .= fin_block("depublication");

	$out .= fin_cadre_enfonce(true);
	
	
	$out .= "</div><br/>";
	
	//$flux .="<div style=\"height: 5px;\"></div>";
	//$flux .= fin_cadre_formulaire();
	
	// retourne le flux mis à jour
	return ajax_action_greffe("depublication-".$id_article, $out);
}

?>
