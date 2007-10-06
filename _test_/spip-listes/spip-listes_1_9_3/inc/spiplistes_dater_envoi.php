<?php
// Orginal From SPIP-Listes-V :: Id: spiplistes_dater_envoi.php paladin@quesaco.org
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate: 2007-10-01 08:58:08 +0200 (lun., 01 oct. 2007) $

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/texte');
include_spip('inc/actions');
include_spip('inc/date');

function spiplistes_dater_envoi($id, $flag, $statut, $date_debut_envoi, $btn_nom_valider, $enveloppe_formulaire = true)
{
	global $spip_lang_left, $spip_lang_right, $options;
	
	if($statut=='vide') {
		return(false);
	}
	
	$date_valide = (!empty($date_debut_envoi) && ($date_debut_envoi != _SPIPLISTES_ZERO_TIME_DATE));

	if ($flag && (
			(($statut == 'encour') || ($statut == 'redac') || ($statut == 'ready'))	// courrier ?
			|| ( // liste valide ?
				($statut == _SPIPLISTES_PRIVATE_LIST) || ($statut == _SPIPLISTES_PUBLIC_LIST) || ($statut == _SPIPLISTES_MONTHLY_LIST)
				) 
		&& ($options == 'avancees'))
		) {

		if(!$date_valide) {
			// propose une date par défaut
			$date_debut_envoi = __mysql_date_time(time());
		}

		if (ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})( ([0-9]{2}):([0-9]{2}))?", $date_debut_envoi, $regs)) {
			$annee = $regs[1];
			$mois = $regs[2];
			$jour = $regs[3];
			$heure = $regs[5];
			$minute = $regs[6];
		}
		
		$js = "size='1' class='fondl' onchange=\"findObj_forcer('valider_date').style.visibility='visible';\"";
		
		$invite =  "<strong><span class='verdana1' style='text-transform: uppercase;'>"
			. _T('spiplistes:Date_expedition')
			. ' : </span> '
			. (!$date_valide ? "<span style='color:gray'>"._T('spiplistes:Date_non_precisee')."</span>" : majuscules(affdate($date_debut_envoi)))
			.  "</strong>"
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
				. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER,'id_courrier='.$id)."' method='post' style='margin: 5px; margin-$spip_lang_left: 20px;'>\n"
				. $masque
				. (
					($date_valide)
					? "<input type='submit' name='$btn_nom_valider' value=\""._T('bouton_changer')."\" class='fondo visible_au_chargement' id='valider_date'/>"
					: "<input type='submit' name='$btn_nom_valider' value=\""._T('bouton_valider')."\" class='fondo' id='valider_date'/>"
					)
				. "</form>"
				;
		}

		$result = block_parfois_visible('daterblock', $invite, $masque, 'text-align: left');
	} 
	else {
		$result = ""
			. "<div style='text-align:center;'>"
			. "<strong><span class='verdana1'>"
			. (($statut == 'encour')
				? _T('spiplistes:Courrier_en_traitement')
				: _T('spiplistes:Date_expedition')
				)
			. "</span> "
			. ($date_valide
				? affdate_heure($date_debut_envoi)
				: _T('spiplistes:Des_validation')
				)
			. "</strong>"
			. "</div>"
			;
	}
	if(!empty($result)) {
		$result =  "<div style='margin-top:1ex;clear:right;'>" . debut_cadre_couleur('',true) . $result .  fin_cadre_couleur(true) ."</div>\n";
	}
	return ($result);
}

?>
