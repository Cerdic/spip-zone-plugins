<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_adherents()
{
	if (!autoriser('voir_membres', 'association', 0)) {  // on s'assure qu'il n'y ai pas d'id associe a la demande d'autorisation sur voir_membres car on les consulte tous
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('inc/navigation_modules');
		list($statut_interne, $critere) = association_passeparam_statut('interne', 'defaut');
		$lettre = _request('lettre');
		onglets_association('titre_onglet_membres', 'adherents');
		// TOTAUX : effectifs par statuts
		$membres = $GLOBALS['association_liste_des_statuts'];
		array_shift($membres); // on sort les anciens membres
		$liste_decomptes = array();
		foreach ($membres as $statut) {
			$classe_css = $GLOBALS['association_styles_des_statuts'][$statut];
			$liste_decomptes[$classe_css] = array( 'adherent_liste_nombre_'.$statut, sql_countsel('spip_asso_membres', "statut_interne='$statut'"), );
		}
		echo association_totauxinfos_effectifs('adherents', $liste_decomptes);
		// TOTAUX : montants des cotisations durant l'annee en cours
		$annee = date('Y'); // dans la requete SQL est : DATE_FORMAT(NOW(), '%Y') ou YEAR(NOW())
		$data = sql_fetsel('SUM(recette) AS somme_recettes, SUM(depense) AS somme_depenses', 'spip_asso_comptes', "DATE_FORMAT('date', '%Y')=$annee AND imputation=".sql_quote($GLOBALS['association_metas']['pc_cotisations']) );
		echo association_totauxinfos_montants('cotisations_'.$annee, $data['somme_recettes'], $data['somme_depenses']);
		// datation et raccourcis
		raccourcis_association(array(), array(
			'gerer_les_groupes' => array('annonce.gif', 'groupes', array('voir_groupes', 'association', 100) ), // l'id groupe passe en parametre est a 100 car ce sont les groupes definis par l'utilisateur et non ceux des autorisation qu'on liste dans cette page
			'menu2_titre_relances_cotisations' => array('relance-24.png', 'edit_relances', array('relancer_membres', 'association') ),
			'synchronise_asso_membre_lien' => array('reload-32.png', 'synchroniser_asso_membres', array('synchroniser_membres', 'association') ),
		));
		if ( test_plugin_actif('FPDF') && test_plugin_actif('COORDONNEES') && autoriser('relancer_membres', 'association') ) { // etiquettes
			echo debut_cadre_enfonce('',true);
			echo recuperer_fond('prive/editer/imprimer_etiquettes');
			echo fin_cadre_enfonce(true);
		}
		//Filtres ID et groupe : si le filtre id est actif, on ignore le filtre groupe
		$id = association_passeparam_id('auteur');
		if (!$id) {
			$id = _T('asso:adherent_libelle_id_auteur');
			$id_groupe = association_recuperer_entier('groupe');
		} else {
			$critere = "a.id_auteur=$id";
			$id_groupe = 0;
		}
		// on appelle ici la fonction qui calcule le code du formulaire/tableau de membres pour pouvoir recuperer la liste des membres affiches a transmettre pour la generation du pdf
		list($where_adherents, $jointure_adherents, $code_liste_membres) = adherents_liste($lettre, $critere, $statut_interne, $id_groupe);
		$champsExclus = array();
		if ( !$GLOBALS['association_metas']['civilite'] )
			$champsExclus[] = 'sexe';
		if ( !$GLOBALS['association_metas']['prenom'] )
			$champsExclus[] = 'prenom';
		if ( !$GLOBALS['association_metas']['id_asso'] )
			$champsExclus[] = 'id_asso';
		echo association_bloc_listepdf('membre', array('where_adherents'=>$where_adherents, 'jointure_adherents'=>$jointure_adherents, 'statut_interne'=>$statut_interne), 'adherent_libelle_', $champsExclus, true);
		debut_cadre_association('annonce.gif', 'adherent_titre_liste_actifs');
		// FILTRES
		filtres_association(array(
			'lettre' => array($lettre, 'asso_membres', 'nom_famille', 'adherents', ),
			'id' => $id,
			'groupe' => $id_groupe, // ne pas proposer que si on affiche les groupes : on peut vouloir filtrer par groupe sans pour autant les afficher
			'statut'=> $statut_interne,
		), 'adherents' );
		// Affichage de la liste
		echo $code_liste_membres;
		fin_page_association();
	}
}

/**
 * liste des adherents
 *
 * @param string $lettre
 *   Filtre lettre
 * @param string $critere
 *   SQL de restriction selon statut
 * @param string $statut_interne
 *   Filtre statut interne
 * @param int $id_groupe
 *   Filtre groupe
 * @return array
 *   Liste du :
 *   - critere de requete SQL en fonction des filtres actifs,
 *   - possible jonction SQL sur la table des groupes,
 *   - code HTML du tableau affichant la liste des membres en fonction des
 *     filtres actifs et de la configuration (champs affiches ou pas)
 */
function adherents_liste($lettre, $critere, $statut_interne, $id_groupe)
{
	if ($lettre)
		$critere .= " AND UPPER(nom_famille) LIKE UPPER('$lettre%') "; // le 1er UPPER (plutot que LOWER puisque les lettres sont mises et passees en majuscule) sur le champ est requis car LIKE est sensible a la casse... le 2nd UPPER est pour contrer les requetes entrees manuellement... (remarque, avec MySQL 5 et SQL Server, on aurait pu avoir simplement "nom_famille LIKE '$lettre%' COLLATE UTF_GENERAL_CI" ou mieux ailleurs : "nom_famille ILIKE '$lettre%'" mais c'est pas forcement portable)
	if ($id_groupe) {
		$critere .= " AND c.id_groupe=$id_groupe ";
		$jointure_groupe = ' LEFT JOIN spip_asso_groupes_liaisons c ON a.id_auteur=c.id_auteur ';
	} else {
		$jointure_groupe = '';
	}
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	include_spip('inc/filtres_images_mini');
	$query = sql_select('a.id_auteur AS id_auteur, b.email AS email, a.sexe, a.nom_famille, a.prenom, a.id_asso, b.statut AS statut, a.validite, a.statut_interne, a.categorie, b.bio AS bio','spip_asso_membres' .  " a LEFT JOIN spip_auteurs b ON a.id_auteur=b.id_auteur $jointure_groupe", $critere, '', 'nom_famille, prenom, validite', sql_asso1page() );
	$auteurs = '';
	while ($data = sql_fetch($query)) {
		$id_auteur = $data['id_auteur'];
		$class = $GLOBALS['association_styles_des_statuts'][$data['statut_interne']];
		$logo = $chercher_logo($id_auteur, 'id_auteur');
		if ($logo) {
			$logo = image_reduire($logo[0], 60);
		}else{
			$logo = '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'membre-60.gif"  width="10"/>' ;
		}
		if (empty($data['email'])) {
			$mail = $data['nom_famille'];
		} else {
			$mail = '<a href="mailto:'.$data['email'].'">'.$data['nom_famille'].'</a>';
		}
		$statut = $data['statut'];
		if (!$statut OR $statut=='nouveau')
			$statut = $data['bio'];
		switch($statut)	{
			case '0minirezo':
				$icone = 'admin-12.gif';
				break;
			case '1comite':
				$icone = 'redac-12.gif';
				break;
			case '5poubelle':
				$icone = 'poubelle.gif';
				break;
			case '6forum':
			default :
				$icone = 'visit-12.gif';
				break;
		}
		$icone = !$icone ? strlen($statut) :  http_img_pack($icone,'','', _T('asso:adherent_label_modifier_visiteur'));
		$auteurs .= "<tr class='$class'>\n";
		if ($GLOBALS['association_metas']['aff_id_auteur']=='on') {
			$auteurs .= '<td class="integer">'
			. $id_auteur.'</td>';
		}
		if ($GLOBALS['association_metas']['aff_photo']) {
			$auteurs .= '<td class="logo centre">'.$logo.'</td>';
		}
		if ($GLOBALS['association_metas']['aff_civilite']=='on' && $GLOBALS['association_metas']['civilite'])
			$auteurs .= '<td class="honorific-prefix">'.$data['sexe'].'</td>';
		$auteurs .= '<td class="family-name">'
		.$mail.'</td>';
		if ($GLOBALS['association_metas']['aff_prenom'] && $GLOBALS['association_metas']['prenom'])
				$auteurs .= '<td class="given-name">'.$data['prenom'].'</td>';
		if ($GLOBALS['association_metas']['aff_groupes']) {
			$auteurs .= '<td class="organisation-unit">';
			$query_groupes = sql_select('g.nom as nom_groupe, g.id_groupe as id_groupe', 'spip_asso_groupes g LEFT JOIN spip_asso_groupes_liaisons l ON g.id_groupe=l.id_groupe', 'l.id_auteur='.$id_auteur);
			if ($row_groupes = sql_fetch($query_groupes)) {
				$auteurs .= '<a href="'. generer_url_ecrire('membres_groupe', 'id='.$row_groupes['id_groupe']) .'">'.$row_groupes['nom_groupe'].'</a>';
				while ($row_groupes = sql_fetch($query_groupes)) {
					$auteurs .= ', <a href="'.generer_url_ecrire('membres_groupe', 'id='.$row_groupes['id_groupe']).'">'.$row_groupes['nom_groupe'].'</a>';
				}
			}
			$auteurs .= '</td>';
		}
		if ($GLOBALS['association_metas']['aff_id_asso'] && $GLOBALS['association_metas']['id_asso']) {
			$auteurs .= '<td class="text">'.$data['id_asso'].'</td>';
		}
		if ($GLOBALS['association_metas']['aff_categorie']) {
			$auteurs .= '<td class="text">'. affiche_categorie($data['categorie']) .'</td>';
		}
		if ($GLOBALS['association_metas']['aff_validite']) {
			$auteurs .= '<td class="date">';
			if ($data['validite']==''){
				$auteurs .= '&nbsp;';
			} else {
				$auteurs .= '<abbr class="dtend" title="'.$data['validite'].'">'. association_formater_date($data['validite']) .'</td>';
			}
			$auteurs .= '</td>';
		}
		$auteurs .= '<td class="action">'
		. '<a href="'. generer_url_ecrire('auteur_infos','id_auteur='.$id_auteur) .'">'.$icone.'</a></td>';
		if (autoriser('editer_membres', 'association')) {
			$auteurs .= association_bouton_act('adherent_label_ajouter_cotisation', 'cotis-12.gif', 'ajout_cotisation','id='.$id_auteur)
			. association_bouton_edit('adherent','id='.$id_auteur);
		}
		$auteurs .= association_bouton_act('adherent_label_voir_membre', 'voir-12.png', 'adherent','id='.$id_auteur)
		. association_bouton_coch('id_auteurs', $id_auteur)
		. "</tr>\n";
	}
	$res = "<table width='100%' class='asso_tablo' id='liste_adherents'>\n"
	. "<thead>\n<tr>";
	if ($GLOBALS['association_metas']['aff_id_auteur']) {
		$res .= '<th>'._T('asso:entete_id').'</th>';
	}
	if ($GLOBALS['association_metas']['aff_photo']) {
		$res .= '<th>'._T('asso:adherent_libelle_photo').'</th>';
	}
	if ($GLOBALS['association_metas']['aff_civilite'] && $GLOBALS['association_metas']['civilite'])
		$res .= '<th>'._T('asso:adherent_libelle_sexe').'</th>';
	$res .= '<th>'._T('asso:adherent_libelle_nom_famille').'</th>';
	if ($GLOBALS['association_metas']['aff_prenom'] && $GLOBALS['association_metas']['prenom'])
		$res .= '<th>'._T('asso:adherent_libelle_prenom').'</th>';
	if ($GLOBALS['association_metas']['aff_groupes']) {
		$res .= '<th>'._T('asso:adherent_libelle_groupes').'</th>';
	}
	if ($GLOBALS['association_metas']['aff_id_asso'] && $GLOBALS['association_metas']['id_asso']) {
		$res .= '<th>'._T('asso:adherent_libelle_id_asso').'</th>';
	}
	if ($GLOBALS['association_metas']['aff_categorie']) {
		$res .= '<th>'._T('asso:adherent_libelle_categorie').'</th>';
	}
	if ($GLOBALS['association_metas']['aff_validite']) {
		$res .= '<th>'._T('asso:adherent_libelle_validite').'</th>';
	}
	$res .= '<th colspan="'. (autoriser('editer_membres', 'association')?4:2) .'" class="actions">'._T('asso:entete_actions').'</th>'
	. '<th><input title="'._T('asso:selectionner_tout').'" type="checkbox" id="selectionnerTous" onclick="var currentVal = this.checked; var checkboxList = document.getElementsByName(\'id_auteurs[]\'); for (var i in checkboxList){checkboxList[i].checked=currentVal;}" /></th>'
	. "</tr>\n</thead><tbody>"
	. $auteurs
	. "</tbody>\n</table>\n";
	// SOUS-PAGINATION
	$res .= "<table width='100%' class='asso_tablo_filtres'><tr>\n";
	$res .= association_selectionner_souspage(array('spip_asso_membres', $critere), 'adherents', 'lettre='.$lettre.'&statut_interne='.$statut_interne, false);
	if (autoriser('editer_membres', 'association', 100)) {
		$res .= "</td><td align='right' class='formulaire'><form>\n";
		if ($auteurs) {
			if (autoriser('editer_membres', 'association')) {
				$res .=  '<select name="action_adherents"><option value="" selected="">'._T('asso:choisir_action').'</option><option value="desactive">'
				.($statut_interne=='sorti' ? _T('asso:reactiver_adherent') : _T('asso:desactiver_adherent'))
				.'</option><option value="delete">'._T('asso:supprimer_adherent').'</option>';
			}
			if (autoriser('editer_groupes', 'association', 100)) {
				$res .=sql_countsel('spip_asso_groupes', '') ? '<option value="grouper">'._T('asso:rejoindre_groupe').'</option><option value="degrouper">'._T('asso:quitter_un_groupe').'</option>' : '';
			}
			$res .='</select><input type="submit" value="'._T('asso:bouton_confirmer').'" />';
		}
		$res .= '<input type="hidden" name="statut_courant" value="'.$statut_interne.'" />'
		.  '</form></td>';
	}
	$res .= '</tr></table>';
	return 	array($critere, $jointure_groupe, generer_form_ecrire('action_adherents', $res));
}

/**
 * Affiche le code d'une categorie a partir de son ID
 *
 * @param int $c
 *   spip_asso_categories.id_categorie
 * @return string
 *   spip_asso_categories.valeur
 */
function affiche_categorie($c)
{
  return is_numeric($c)
    ? sql_getfetsel('valeur', 'spip_asso_categories', "id_categorie=$c")
    : $c;
}

?>