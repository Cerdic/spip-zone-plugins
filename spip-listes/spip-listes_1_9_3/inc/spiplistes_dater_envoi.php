<?php
/**
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/texte');
include_spip('inc/actions');
include_spip('inc/date');
include_spip('inc/spiplistes_api');

function spiplistes_dater_envoi (
	$type_objet, $id_objet, $statut_objet
	, $flag_autorise
	, $titre_boite
	, $date_debut_envoi
	, $btn_nom_valider, $enveloppe_formulaire = true
) {
	global $spip_lang_left, $spip_lang_right;
	
	if($statut_objet=='vide') {
		return(false);
	}
	
	$date_valide = spiplistes_date_heure_valide($date_debut_envoi);

	$courrier_editable = 
		($type_objet == 'courrier')
		&&	(
			($statut_objet == _SPIPLISTES_COURRIER_STATUT_REDAC) 
			|| ($statut_objet == _SPIPLISTES_COURRIER_STATUT_ENCOURS)
		)
		;
	$liste_editable = 
		($type_objet == 'liste')
		&& in_array($statut_objet, explode(";", _SPIPLISTES_LISTES_STATUTS_OK))
		;
		
	if($flag_autorise && ($courrier_editable || $liste_editable)) {

		if(!$date_valide) {
			// propose date maintenant par défaut
			$date_debut_envoi = normaliser_date(time());
			$date_valide = spiplistes_date_heure_valide($date_debut_envoi);
		}

		list($annee, $mois, $jour, $heure, $minute, $seconde) = $date_valide;
		
		$js = "size='1' class='fondl'";
		
		if($enveloppe_formulaire)
			$js .= "onchange=\"findObj_forcer('valider_date').style.visibility='visible';\"";
		
		$invite = ""
			. "<span class='verdana1 titre-boite-date'>"
			. "<span class='titre'>" . $titre_boite . "</span>"
			. ": "
			.	(
				(!$date_valide)
				? "<span class='gray'>" . _T('spiplistes:date_non_precisee') . "</span>" 
				: "<span class='date'>" . affdate_heure($date_debut_envoi) . "</span>"
				)
			.  "</span>\n"
			;
		
		$masque = 
			afficher_jour($jour, "name='jour' $js", true)
			. afficher_mois($mois, "name='mois' $js", true)
			. afficher_annee($annee, "name='annee' $js")
			. " - "
			. afficher_heure($heure, "name='heure' $js")
			. afficher_minute($minute, "name='minute' $js")
			. "&nbsp;\n"
			;

		if($enveloppe_formulaire) {
			$masque = ""
				. "<!-- dater_envoi form -->\n"
				. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER,'id_$type_objet='.$id_objet)."' method='post' style='margin: 5px; margin-$spip_lang_left: 20px;'>\n"
				. $masque
				. "<input type='submit' name='$btn_nom_valider' id='valider_date' "
					.	(
						($date_valide)
						? "value=\""._T('bouton_changer')."\" class='fondo visible_au_chargement'"
						: "value=\""._T('bouton_valider')."\" class='fondo'"
						)
				. "/>"
				. "</form>\n"
				;
		}

		$result = block_parfois_visible('daterblock', $invite, $masque, 'text-align: left');
	} 
	else {
		$result = ""
			. "<div style='text-align:center;'>"
			. "<span class='verdana1 titre-boite-date'><span class='titre'>"
			.	(
				($statut_objet == 'encour')
				? _T('spiplistes:courrier_en_cours_')
				: _T('spiplistes:date_expedition_')
				)
			. "</span>: <span class='date'>"
			.	(
				($date_valide)
				? affdate_heure($date_debut_envoi)
				: _T('spiplistes:attente_validation')
				)
			. "</span></span>"
			. "</div>\n"
			;
	}
	if(!empty($result)) {
		$result = ""
			. "<div style='margin-top:1ex;clear:right;'>" 
			. debut_cadre_couleur('',true) 
			. $result 
			. fin_cadre_couleur(true) 
			. "</div>\n"
			;
	}
	return ($result);
}

?>