<?php
// Original From SPIP-Listes-V :: Id: spiplistes_pipeline_affiche_milieu.php paladin@quesaco.org

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_api');
include_spip('inc/layer');

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

// bloc appele' en pipeline par spiplistes_affiche_milieu()
// si pas adresse mail, alerte
// sinon, affiche bloc des abonnements
function spiplistes_auteur_abonnement () {

	$id_auteur = intval(_request('id_auteur'));
	if($id_auteur > 0) {
		
		//if($row = sql_fetsel("email,statut", "spip_auteurs", "id_auteur=".sql_quote($id_auteur)." LIMIT 1")) {
		if ($row = spiplistes_auteurs_auteur_select ('email,statut'
													 , 'id_auteur='.sql_quote($id_auteur)
													 )
			) {
			
			if($row['statut'] == '5poubelle')
			{
				// le compte est supprime'. Desabonner de tout
				spiplistes_abonnements_auteur_desabonner($id_auteur);
			}
			else
			{
				if(strlen($auteur_email = $row['email']) > 3) {
					$result = spiplistes_auteur_abonnement_details($id_auteur, $row['statut'], $auteur_email);
				}
				else {
					$result =	''
						. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png'
											 , true
											 , ''
											 , _T('spiplistes:abonnements_aux_courriers')
											 )
						. '<p class="verdana2">'
						. _T('spiplistes:Adresse_email_obligatoire')
						. '</p>'
						. fin_cadre_relief(true)
						;
				}
			}
		}
	}
	return($result);
}

// bloc des abonnements
function spiplistes_auteur_abonnement_details ($id_auteur, $auteur_statut, $email) {
	
	include_spip("inc/spiplistes_api");
	include_spip("inc/spiplistes_api_presentation");

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	$result = "";

	$flag_editable = (
		(($connect_statut == '0minirezo') && $connect_toutes_rubriques)
		|| ($connect_id_auteur == $id_auteur)
		);

	if($flag_editable) {
		
		// recupere la liste des abonnements disponibles
		$sql_where = spiplistes_listes_sql_where_or(_SPIPLISTES_LISTES_STATUTS_PERIODIQUES)
			. " OR statut=".sql_quote(_SPIPLISTES_LIST_PUBLIC);
			
		// les auteurs ont droit aux listes privees (internes)
		if(($auteur_statut == '1comite') || ($auteur_statut == '0minirezo')) {
			$sql_where .= " OR statut=".sql_quote(_SPIPLISTES_LIST_PRIVATE);
		}

		$sql_result = sql_select(
			array('id_liste','titre','texte','date','statut')
			, "spip_listes"
			, $sql_where
			, ''
			, array("titre ASC")
			);

		$nb_listes_dispo = sql_count($sql_result);
//spiplistes_log(gettype($nb_listes_dispo).":".$nb_listes_dispo);

		// si liste disponible, affiche formulaire
		if($sql_result && $nb_listes_dispo) {
			
			// recupere la liste des listes
			$listes = array();
			while($row = sql_fetch($sql_result)) {
				$texte = propre($row['texte']);
				if(strlen($legend = textebrut($texte)) > 40) {
					$texte = couper($texte, 40);
				}
				$texte = strip_tags($texte, '<strong>');
				$listes[] = array(
					'id_liste' => intval($row['id_liste'])
					, 'titre' => $row['titre']
					, 'texte' => $texte
					, 'date' => $row['date']
					, 'statut' => $row['statut']
					, 'legend' => $legend
				);
			} // end while
			
			// si retour de formulaire, ajoute/retire les abonnements
			if(_request('btn_abonnements_valider')) {
				$abos_set = _request('abos_set');
				$abo_ajoute = array();
				// liste des abonnements de id_auteur
				$auteur_abos_current_list = spiplistes_abonnements_listes_auteur($id_auteur);
				// ajoute/retire les abonnements desires
				if(count($abos_set)) {
					// Abonnements ?
					foreach($abos_set as $value) {
						if(!in_array($value, $auteur_abos_current_list)) {
							$abo_ajoute[] = $value;
						}
					}
					if(count($abo_ajoute)) {
						spiplistes_abonnements_ajouter($id_auteur, $abo_ajoute);
					}
					// Desabonnements ?
					foreach($auteur_abos_current_list as $value) {
						if(!in_array($value, $abos_set)) {
							spiplistes_abonnements_auteur_desabonner($id_auteur, $value);
						}
					}
				}
				// desabonne de tout
				else {
					if(spiplistes_abonnements_auteur_desabonner($id_auteur, "toutes") === false) {
						$result .= spiplistes_boite_alerte(_T('spiplistes:Erreur_sur_la_base'), true);
					}
				}
			} // end if
			
			// si retour de formulaire, modifie le format de reception
			if($abo_format = _request('abo_format')) {
				spiplistes_format_abo_modifier($id_auteur, $abo_format);
			}
			
			// recupere le format d'abonnement de id_auteur
			$abo_format = spiplistes_format_abo_demande($id_auteur);		
		
			// recupere la liste ou auteur est abonne
			$auteur_abos_current_list = spiplistes_abonnements_listes_auteur($id_auteur);
			
			$bloc_visible = _request('btn_abonnements_valider');
			
			$debut_block = ($bloc_visible ? "spiplistes_debut_block_visible" : "spiplistes_debut_block_invisible");
			
			$result .= ""
				. "<!-- formulaire abonnement spiplistes -->\n" 
				. "<a name='abonnement'></a>\n"
				. debut_cadre_enfonce(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_listes-24.png"
					, true, '', _T('spiplistes:listes_de_diffusion_'))
				. spiplistes_bouton_block_depliable(
					_T('spiplistes:abonnements_aux_courriers')
					, $bloc_visible
					, "abos_block")
				. "<div class='verdana2'>"
				;
			if($n = count($auteur_abos_current_list)) {
				$result .= $n."/"
					. spiplistes_nb_listes_str_get ($nb_listes_dispo)
					. ". "
					. _T('spiplistes:format_de_reception')." : "
						.	(
							(in_array($abo_format, array('html', 'texte')))
							? _T('spiplistes:'.$abo_format)
							: "&lt;"._T('spiplistes:aucun')."&gt;"
							)
					;
			} else {
				$result .= _T('spiplistes:Sans_abonnement');
			}
			$result .= ""
				. "</div>\n"
				. $debut_block("abos_block")
				. "<form action='".generer_url_ecrire("auteur_infos", "id_auteur=$id_auteur")."' method='post' style='margin-bottom:0;' name='abos_formulaire'>\n"
				. debut_cadre_formulaire("", true)
				. "\n<p class='verdana2' style='margin-top:0;margin-bottom:0;'>"
				.	(
						($abo_format!='html' && $abo_format!='texte')
						? _T('spiplistes:Alert_abonnement_sans_format')
						: _T('spiplistes:vous_etes_abonne_aux_listes_selectionnees_').":"
					)
				. "</p>\n"
				//
				. "<!-- liste des abonnements -->\n"
				. "<ul class='liste-listes'>\n"
				;
			foreach($listes as $key=>$value) {
				$id_liste = $value['id_liste'];
				$auteur_est_abonne = in_array($id_liste, $auteur_abos_current_list);
				$checked = $auteur_est_abonne ? "checked='checked'" : "";
				$label = $auteur_est_abonne ? "Arreter_abonnement_a" : "Abonner_a";
				$label = _T("spiplistes:".$label)." ".$value['titre'];
				$prochain_envoi = 
					($value['date'] > 0)
					? _T('spiplistes:Prochain_envoi_').": <span style='font-weight:bold;'>".affdate_heure($value['date'])."</span>"
					: _T('spiplistes:envoi_non_programme')
					;
				$result .= ""
					. "<li>\n"
					. "<label>\n"
					. "<input name='abos_set[]' type='checkbox' value='$id_liste' title=\"$label\" $checked />\n"
					. spiplistes_bullet_titre_liste('puce', $value['statut'], '', true)
					. "<span title=\"".$value['legend']."\">\n"
					. "<span class='titre'>".typo($value['titre'])."</span> \n"
					. "<span class='description'>".typo($value['texte'])." </span>\n"
					. "<span class='periodicite'>($prochain_envoi)</span>\n"
					. "</span>\n"
					. "</label></li>\n"
					;
			}
			$result .= ""
				. "</ul>\n"
				. "<!-- fin liste des abonnements -->\n"
				. fin_cadre_formulaire(true)
				//
				// selection du format de reception
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
			if(spiplistes_format_valide($abo_format) && ($abo_format!="non")) {
				$result .= ""
					. debut_cadre_formulaire("margin-top:1ex", true)
					. "<ul class='liste-format-desabo'>\n"
					. "<li>\n"
					. spiplistes_form_input_radio('abo_format', 'non', _T('spiplistes:Suspendre_abonnements')
						, false, true, false)
					. "</li>\n"
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