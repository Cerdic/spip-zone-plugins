<?php
// Original From SPIP-Listes-V :: Id: spiplistes_pipeline_affiche_milieu.php paladin@quesaco.org

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api');

// pipeline (plugin.xml)
function spiplistes_affiche_milieu ($flux) {
	switch($flux['args']['exec']) {
		case 'auteurs_edit':
		case 'auteur_infos':
			$flux['data'] .= spiplistes_auteur_abonnement();
			break;
		default:
			break;
	}
	return ($flux);
}

// bloc appelé en pipeline par spiplistes_affiche_milieu()
function spiplistes_auteur_abonnement () {

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	$id_auteur = intval(_request('id_auteur'));

	$flag_editable = ($id_auteur > 0) 
		&& (
			(($connect_statut == '0minirezo') && ($connect_toutes_rubriques))
			|| ($connect_id_auteur == $id_auteur)
			);

	if($flag_editable) {
		$sql_query = "SELECT email,statut FROM spip_auteurs WHERE id_auteur=$id_auteur LIMIT 1";
		$sql_result = spip_query($sql_query);
		if($row = spip_fetch_array($sql_result)) {
			if(strlen($auteur_email = $row['email']) > 3) {
				return(spiplistes_auteur_abonnement_details ($flag_editable, $id_auteur, $row['statut']));
			}
			else {
				return(	""
					. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_listes-24.png", true, "", _T('spiplistes:Abonnements_aux_courriers'))
					. "<p class='verdana2'>"
					. _T('spiplistes:Adresse_email_obligatoire')
					. "</p>"
					. fin_cadre_relief(true)
					);
			}
		}
	}
}

function spiplistes_auteur_abonnement_details ($flag_editable, $id_auteur, $auteur_statut) {
	
	include_spip("inc/spiplistes_api");

	global $connect_statut;
	
	$result = "";

	if($flag_editable) {
		
		// récupère la liste des abonnements disponibles
		$sql_where = " statut='"._SPIPLISTES_PUBLIC_LIST."' OR statut='"._SPIPLISTES_MONTHLY_LIST."'" .
			((($auteur_statut == '1comite') || ($auteur_statut == '0minirezo')) 
			? " OR statut='"._SPIPLISTES_PRIVATE_LIST."'"
			: "");
		$sql_query = "SELECT id_liste,titre,texte,date,statut FROM spip_listes WHERE $sql_where";

		$sql_result = spip_query($sql_query);
		
		// si liste disponible, affiche formulaire
		if($sql_result && spip_num_rows($sql_result)) {
			
			// récupère la liste des listes
			$listes = array();
			while($row = spip_fetch_array($sql_result)) {
				$listes[] = array(
					'id_liste' => $row['id_liste']
					, 'titre' => $row['titre']
					, 'texte' => $row['texte']
					, 'date' => $row['date']
					, 'statut' => $row['statut']
				);
			} // end while
			
			// si retour de formulaire, ajoute/retire les abonnements
			if(_request('btn_abonnements_valider')) {
				$abos_set = _request('abos_set');
				$abo_ajoute = array();
				$abo_retire = array();
				$auteur_current_list = array(); // liste des abonnements de id_auteur
				$sql_query = "SELECT id_liste FROM spip_auteurs_listes WHERE id_auteur=$id_auteur";
				$sql_result = spip_query($sql_query);
				while ($row = spip_fetch_array($sql_result)) {
					$auteur_current_list[] = $row['id_liste'];
				}
				// ajoute/retire les abonnements désirés
				if(count($abos_set)) {
					// Abonnements ?
					foreach($abos_set as $value) {
						if(!in_array($value, $auteur_current_list)) {
							$abo_ajoute[] = $value;
						}
					}
					if(count($abo_ajoute)) {
						$sql_query = "";
						foreach($abo_ajoute as $value) {
							$sql_query .= " ($id_auteur, $value),";
						}
						$sql_query = rtrim($sql_query, ",");
						$sql_query = "INSERT INTO spip_auteurs_listes (id_auteur, id_liste) VALUES ".$sql_query;
						if(!spip_query($sql_query)) {
							$result .= __boite_alerte(_T('spiplistes:Erreur_sur_la_base'), true);
						}
					}
					// Désabonnements ?
					foreach($auteur_current_list as $value) {
						if(!in_array($value, $abos_set)) {
							$abo_retire[] = $value;
						}
					}
					if(count($abo_retire)) {
						foreach($abo_retire as $value) {
							$sql_query = "DELETE FROM spip_auteurs_listes WHERE id_auteur=$id_auteur AND id_liste="._q($value);
							if(!spip_query($sql_query)) {
								$result .= __boite_alerte(_T('spiplistes:Erreur_sur_la_base'), true);
							}
						}
					}
				}
				// désabonne de tout
				else {
					$sql_query ="DELETE FROM spip_auteurs_listes WHERE id_auteur=$id_auteur";
					if(!spip_query($sql_query)) {
						$result .= __boite_alerte(_T('spiplistes:Erreur_sur_la_base'), true);
					}
				}
			} // end if
			
			// si retour de formulaire, modifie le format de réception
			if($abo_format = _request('abo_format')) {
				if(!spiplistes_format_est_correct($abo_format)) {
					$abo_format = "";
				}
				else {
					$sql_query = "SELECT COUNT(id_auteur) AS c FROM spip_auteurs_elargis WHERE id_auteur=$id_auteur LIMIT 1";
					if(($row = spip_fetch_array(spip_query($sql_query)))
						&& $row['c'] ) {
						$sql_query = "UPDATE spip_auteurs_elargis SET `spip_listes_format`="._q($abo_format)." WHERE id_auteur=$id_auteur";
					}
					else {
						$sql_query = "INSERT INTO spip_auteurs_elargis (id_auteur,`spip_listes_format`) 
							VALUES ($id_auteur,"._q($abo_format).")";
					}
					if(!spip_query($sql_query)) {
						$result .= __boite_alerte(_T('spiplistes:Erreur_sur_la_base'), true);
					}
				}
			}
			
			// récupère le format d'abonnement de id_auteur
			$abo_format = "";
			$sql_query = "SELECT `spip_listes_format` FROM spip_auteurs_elargis WHERE id_auteur=$id_auteur";
			if($sql_result = spip_query($sql_query)) {
				$row = spip_fetch_array($sql_result);
				$abo_format = $row['spip_listes_format'];
				if(!spiplistes_format_est_correct($abo_format)) {
					$abo_format = "";
				}
			}
			
			// récupère la liste où auteur est abonné
			$sql_query = "SELECT id_liste FROM spip_auteurs_listes WHERE id_auteur=$id_auteur";
			$sql_result = spip_query($sql_query);
			$auteur_current_list = array(); 
			while ($row = spip_fetch_array($sql_result)) {
				$auteur_current_list[] = $row['id_liste'];
			}
			
			if(_request('btn_abonnements_valider')) {
				$bouton_block = "bouton_block_visible";
				$debut_block = "debut_block_visible";
			}
			else {
				$bouton_block = "bouton_block_invisible";
				$debut_block = "debut_block_invisible";
			}
			
			$result .= ""
				. "<!-- formulaire abonnement spiplistes -->\n" 
				. "<a name='abonnement'></a>\n"
				. debut_cadre_enfonce(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_listes-24.png", true, "", $bouton_block("abos_block")._T('spiplistes:Abonnements_aux_courriers').__plugin_aide("abo_fiche_auteur"))
				. $debut_block("abos_block")
				. "<form action='".generer_url_ecrire("auteur_infos", "id_auteur=$id_auteur")."' method='post' style='margin-bottom:0;' name='abos_formulaire'>\n"
				. debut_cadre_formulaire("", true)
				. "\n<p class='verdana2' style='margin-top:0;margin-bottom:0;'>"
				.	(
						($abo_format!='html' && $abo_format!='texte')
						? _T('spiplistes:Alert_abonnement_sans_format')
						: _T('spiplistes:Vous_etes_abonne_aux_listes_selectionnees').":"
					)
				. "</p>\n"
				//
				. "<!-- liste des abonnements -->\n"
				. "<ul class='liste-listes'>\n"
				;
			foreach($listes as $key=>$value) {
				$id_liste = $value['id_liste'];
				$checked = in_array($id_liste, $auteur_current_list) ? "checked='checked'" : "";
				$label = in_array($id_liste, $auteur_current_list) ? "spiplistes:Arreter_abonnement_a" : "spiplistes:Abonner_a";
				$label = _T($label)." ".$value['titre'];
				$prochain_envoi = 
					($value['date'] != _SPIPLISTES_ZERO_TIME_DATE)
					? _T('spiplistes:Prochain_envoi_').": <span style='font-weight:bold;'>".affdate_heure($value['date'])."</span>"
					: _T('spiplistes:envoi_non_programme')
					;
				$result .= ""
					. "<li>\n"
					. "<label>\n"
					. "<input name='abos_set[]' type='checkbox' value='$id_liste' title=\"$label\" $checked />\n"
					. "<img src='".spiplistes_items_get_item("puce", $value['statut'])."'"
						. " alt=\"".spiplistes_items_get_item("alt", $value['statut'])."\" border='0' />\n"
					. "<span class='titre'>".propre($value['titre'])."</span> \n"
					. "<span class='description'>".propre($value['texte'])." </span>\n"
					. "<span class='periodicite'>($prochain_envoi)</span>\n"
					. "</label></li>\n"
					;
			}
			$result .= ""
				. "</ul>\n"
				. "<!-- fin liste des abonnements -->\n"
				. fin_cadre_formulaire(true)
				//
				// sélection du format de réception
				. debut_cadre_formulaire("margin-top:1ex", true)
				. ((empty($abo_format) || ($abo_format=="non")) 
					? "<p>"._T('spiplistes:Format_obligatoire_pour_diffusion')."</p>" : "" )
				. _T('spiplistes:format_de_reception')
				. "<ul class='liste-format'>\n"
				;
			$checked = ($abo_format=="html" ? "checked='checked'" : "");
			$result .= ""
				. "<li style='width:50%;float:left;'>\n"
				. " <input type='radio' name='abo_format' value='html' id='format_rcpt_html' title='"._T('spiplistes:html')."' $checked />"
				. " <label for='format_rcpt_html'>"._T('spiplistes:version_html')."</label></li>\n"
				;
			$checked = ($abo_format=="texte" ? "checked='checked'" : "");
			$result .= ""
				. "<li>\n"
				. " <input type='radio' name='abo_format' value='texte' id='format_rcpt_texte' title='"._T('spiplistes:texte')."' $checked />"
				. " <label for='format_rcpt_texte'>"._T('spiplistes:version_texte')."</label></li>\n"
				. "</ul>\n"
				. fin_cadre_formulaire(true)
				;
			if(spiplistes_format_est_correct($abo_format) && ($abo_format!="non")) {
				$result .= ""
					. debut_cadre_formulaire("margin-top:1ex", true)
					. "<ul class='liste-format-desabo'>\n"
					. "<li>\n"
					. " <input type='radio' name='abo_format' value='non' id='format_rcpt_non' title='"._T('spiplistes:Suspendre_abonnements')."' />"
					. " <label for='format_rcpt_non'>"._T('spiplistes:Suspendre_abonnements')."</label></li>\n"
					. "</ul>\n"
					. fin_cadre_formulaire(true)
					;
			}
			$result .= ""
				//
				. "<div style='text-align:right;margin-top:1ex;'><input type='submit' name='btn_abonnements_valider' "
					. " value='"._T('spiplistes:Valider_abonnement')."' class='fondo' /></div>\n"
				. "</form>\n"
				. fin_block()
				. fin_cadre_enfonce(true)
				;
		}
	}
	
	return($result);
}

?>