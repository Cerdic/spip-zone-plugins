<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_adherents() {
	sinon_interdire_acces(autoriser('voir_membres', 'association', 0)); // on s'assure qu'il n'y ai pas d'id associe a la demande d'autorisation ici car on les consulte tous
	include_spip('association_modules');
/// INITIALISATIONS
	$id = association_passeparam_id('auteur');
	$args_url = array();
	if (!$id) { // Pas de ID : les autres filtres sont actifs
		$args_sql = array();
		list($statut_interne, $critere) = association_passeparam_statut('interne', 'defaut');
		if ($statut_interne != 'defaut') {
			$args_url[] = "statut_interne=$statut_interne";
			$args_sql[] = "m.$critere";
		} else {
			$args_sql[] = "$critere";
		}
		$id_categorie = association_recuperer_entier('categorie');
		if ($id_categorie) {
			$args_url[] = "categorie=$id_categorie";
			$args_sql[] = "m.id_categorie=$id_categorie";
		}
		$id_groupe = association_recuperer_entier('groupe');
		if ($id_groupe) {
			$args_url[] = "groupe=$id_groupe";
			$args_sql[] = "g.id_groupe=$id_groupe ";
			$jointure_groupe = ' LEFT JOIN spip_asso_fonctions AS g ON m.id_auteur=g.id_auteur ';
		} else {
			$jointure_groupe = '';
		}
		$lettre = _request('lettre');
		if ($lettre) {
			$args_url[] = "lettre=$lettre";
			$args_sql[] = "UPPER(m.nom_famille) LIKE UPPER('$lettre%') "; // le 1er UPPER (plutot que LOWER puisque les lettres sont mises et passees en majuscule) sur le champ est requis car LIKE est sensible a la casse... le 2nd UPPER est pour contrer les requetes entrees manuellement... (remarque, avec MySQL 5 et SQL Server, on aurait pu avoir simplement "nom_famille LIKE '$lettre%' COLLATE UTF_GENERAL_CI" ou mieux ailleurs : "nom_famille ILIKE '$lettre%'" mais c'est pas forcement portable)
		}
		$critere = implode(' AND ', $args_sql);
		$suffixe_pdf = "membres_$statut_interne".'_';
		$suffixe_pdf .= $lettre?str_replace(array('/', '\\', '?', '%', '*', ':', '|', '"', '<', '>', '.', ' ', '(', ')', ';', '&', '#', '[', ']', ), '~', utf8_decode($lettre) ):'tous'; // caracteres problematiques : http://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
		$suffixe_pdf .= "_$id_categorie"."_$id_groupe";
	} else { // si le filtre ID est actif, on ignore les autres filtres
		$critere = "m.id_auteur=$id";
		$id_groupe = 0;
		$id_categorie = 0;
		$lettre = ' ';
		$suffixe_pdf = "membre$id";
		$jointure_groupe = '';
	}
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('titre_onglet_membres', 'adherents');
/// AFFICHAGES_LATERAUX : TOTAUX : effectifs par statuts
	$membres = $GLOBALS['association_liste_des_statuts'];
	array_shift($membres); // on sort les anciens membres
	$liste_decomptes = array();
	foreach ($membres as $statut) {
		$classe_css = $GLOBALS['association_styles_des_statuts'][$statut];
		$liste_decomptes[$classe_css] = array( 'adherent_liste_nombre_'.$statut, sql_countsel('spip_asso_membres', "statut_interne='$statut'"), );
	}
	echo association_tablinfos_effectifs('adherents', $liste_decomptes);
/// AFFICHAGES_LATERAUX : TOTAUX : montants des cotisations durant l'annee en cours
	list($id_periode, $critere_periode) = association_passeparam_periode('operation', 'asso_comptes', 0); // annee/exercice actuelle/courant
	$data = sql_fetsel('SUM(recette) AS somme_recettes, SUM(depense) AS somme_depenses', 'spip_asso_comptes', "$critere_periode AND imputation=".sql_quote($GLOBALS['association_metas']['pc_cotisations']) );
	echo association_tablinfos_montants('cotisations_'.$id_periode, $data['somme_recettes'], $data['somme_depenses']);
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('gerer_les_groupes', 'annonce.gif', array('groupes'), array('voir_groupes', 'association', 100) ), // l'id groupe passe en parametre est a 100 car ce sont les groupes utilisateurs et non les autorisations qu'on liste
		array('menu2_titre_mailing', 'mail-24.png', array('mailing', ((intval($id_groupe)>99)?"&filtre_id_groupe=$id_groupe":'').($statut_interne?"&filtre_statut_interne=$statut_interne":'')), array('relancer_membres', 'association') ),
		array('synchronise_asso_membres', 'reload-32.png', array('synchroniser_asso_membres'), array('gerer_membres', 'association') ),
	), 2);
/// AFFICHAGES_LATERAUX : Forms-PDF
	if ( autoriser('exporter_membres', 'association') ) { // etiquettes
		echo association_form_etiquettes($critere, $jointure_adherents, $suffixe_pdf);
	}
	if ( autoriser('exporter_membres', 'association') ) { // tableau des membres
		$champsExclus = array();
		if ( !$GLOBALS['association_metas']['civilite'] )
			$champsExclus[] = 'sexe';
		if ( !$GLOBALS['association_metas']['prenom'] )
			$champsExclus[] = 'prenom';
		if ( !$GLOBALS['association_metas']['id_asso'] )
			$champsExclus[] = 'id_asso';
		echo association_form_listepdf('membre', array('where_adherents'=>$critere, 'jointure_adherents'=>$jointure_adherents, 'statut_interne'=>$statut_interne, 'suffixe'=>$suffixe_pdf), 'adherent_libelle_', $champsExclus, TRUE);
	}
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('annonce.gif', 'adherent_titre_liste_actifs');
/// AFFICHAGES_CENTRAUX : FILTRES
	$filtre_categorie = '<select name="categorie" onchange="form.submit()">';
	$filtre_categorie .= '<option value="" ';
	$filtre_categorie .= (($id_categorie=='%' || !$id_categorie)?' selected="selected"':'');
	$filtre_categorie .= '>'. _T('asso:entete_tous') .'</option>';
	$sql = sql_select(
		'id_categorie, valeur, libelle',
		'spip_asso_categories',
		sql_in_select('id_categorie', 'id_categorie', 'spip_asso_membres', '', 'id_categorie'), // uniquement les categories utilisees
		'', 'valeur');
	while ($categorie = sql_fetch($sql)) {
		$filtre_categorie .= '<option value="'.$categorie['id_categorie'].'"';
		$filtre_categorie .= ($id_categorie==$categorie['id_categorie']?' selected="selected"':'');
//		$filtre_categorie .= '>'.$categorie['valeur'].' - '.$categorie['libelle'].'</option>'; // long ; comme pour les comptes (ref - intitule)
		$filtre_categorie .= '>'.$categorie['valeur'].'</option>'; // court (ou pas) : comme pour les groupes
	}
	echo association_form_filtres(array(
		'lettre' => array($lettre, 'asso_membres', 'nom_famille', generer_url_ecrire('adherents', implode('&',$args_url)) ),
		'id' => $id,
		'groupe' => $id_groupe, // ne pas proposer que si on affiche les groupes : on peut vouloir filtrer par groupe sans pour autant les afficher
		'statut'=> $statut_interne,
	), 'adherents', array(
		'categorie' => $filtre_categorie,
	));
/// AFFICHAGES_CENTRAUX : TABLEAU
	echo adherents_liste($critere, $statut_interne, $args_url, $jointure_groupe);
	fin_page_association();
}


/**
 * liste des adherents
 *   Filtre statut interne (pour la selection d'action...)
 * @param string $critere
 *   SQL de restriction selon les filtres
 * @param array $args_url
 *   Liste de parametres d'URL pour la pagination
 * @param string $jointure
 *   SQL de jointure pour la liaison aux groupes
 * @return string
 *   Code HTML du tableau affichant la liste des membres en fonction des
 * filtres actifs et de la configuration (champs affiches ou pas)
 */
function adherents_liste($critere, $statut_interne, $args_url, $jointure) {
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	include_spip('inc/filtres_images_mini');
	$limit = intval(_request('debut')) . "," . _ASSOCIASPIP_LIMITE_SOUSPAGE;
	$query = sql_select('m.id_auteur AS id_auteur, a.email AS email, m.sexe, m.nom_famille, m.prenom, m.id_asso, a.statut AS statut, m.date_validite, m.statut_interne, m.id_categorie, a.bio AS bio',"spip_asso_membres AS m LEFT JOIN spip_auteurs AS a ON m.id_auteur=a.id_auteur $jointure", $critere, '', 'm.nom_famille, m.prenom, m.date_validite', $limit);
//	echo "<div style='background:yellow; color:red;'>SQL WHERE : $critere</div>"; // query check
	$tbd = '';
/// AFFICHAGES_CENTRAUX : TABLEAU
	while ($data = sql_fetch($query)) {
		$id_auteur = $data['id_auteur'];
		$class = $GLOBALS['association_styles_des_statuts'][$data['statut_interne']];
		$logo = $chercher_logo($id_auteur, 'id_auteur');
		if ($logo) {
			$logo = image_reduire($logo[0], 60);
		}else{
			$logo = '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'membre-60.gif"  width="60" />' ;
		}
		if (empty($data['email'])) {
			$mail = $data['nom_famille'];
		} else {
			$mail = '<a href="mailto:'.$data['email'].'">'.$data['nom_famille'].'</a>';
		}
		$statut = $data['statut'];
		if (!$statut OR $statut=='nouveau')
			$statut = $data['bio'];
		switch ( substr($GLOBALS['spip_version_branche'],0,1) ) { // $GLOBALS['spip_version_base'] non... $GLOBALS['meta']['version_installee'] non... $GLOBALS['spip_version_branche'] oui... trouve dans info_maj_spip() dans "inc/presentation.php"
			case 2: // SPIP 2.x : icones dans "prive/images/"
				switch($statut)	{
					case '0minirezo':
						$icone = 'admin-12.gif';
						break;
					case '1comite':
						$icone = 'redac-12.gif';
						break;
					case '5poubelle': // ?!?
						$icone = 'poubelle.gif';
						break;
					case '6forum':
					default : // autres cas
						$icone = 'visit-12.gif';
						break;
				}
				break;
			case 3: // SPIP 3.x : les icones sont dans "prive/themes/spip/images/"
				switch($statut)	{
					case '0minirezo':
					case '1comite':
					case '5poubelle': // ?!?
					case '6forum':
						$icone = 'auteur-'.$statut.'-16.png';
						break;
					default : // autres cas
						$icone = 'auteur-16.png';
						break;
				}
				break;
			default: // !?!
				$icone = 'rien.gif';
				break;
		}
		$tbd .= "<tr class='$class'>\n";
		if ($GLOBALS['association_metas']['aff_id_auteur'])
			$tbd .= '<td class="integer">'
			. $id_auteur.'</td>';
		if ($GLOBALS['association_metas']['aff_photo'])
			$tbd .= '<td class="photo logo centre">'.$logo.'</td>';
		if ($GLOBALS['association_metas']['aff_civilite'] && $GLOBALS['association_metas']['civilite'])
			$tbd .= '<td class="honorific-prefix">'.$data['sexe'].'</td>';
		$tbd .= '<td class="family-name">'
		.$mail.'</td>';
		if ($GLOBALS['association_metas']['aff_prenom'] && $GLOBALS['association_metas']['prenom'])
			$tbd .= '<td class="given-name">'.$data['prenom'].'</td>';
		if ($GLOBALS['association_metas']['aff_groupes']) {
			$tbd .= '<td class="organisation-unit">';
			$query_groupes = sql_select('g.nom as nom_groupe, g.id_groupe as id_groupe', 'spip_asso_groupes g LEFT JOIN spip_asso_fonctions l ON g.id_groupe=l.id_groupe', 'l.id_auteur='.$id_auteur);
			if ($row_groupes = sql_fetch($query_groupes)) {
				$tbd .= '<a href="'. generer_url_ecrire('membres_groupe', 'id='.$row_groupes['id_groupe']) .'">'.$row_groupes['nom_groupe'].'</a>';
				while ($row_groupes = sql_fetch($query_groupes)) {
					$tbd .= ', <a href="'.generer_url_ecrire('membres_groupe', 'id='.$row_groupes['id_groupe']).'">'.$row_groupes['nom_groupe'].'</a>';
				}
			}
			$tbd .= '</td>';
		}
		if ($GLOBALS['association_metas']['aff_id_asso'] && $GLOBALS['association_metas']['id_asso'])
			$tbd .= '<td class="text">'.$data['id_asso'].'</td>';
		if ($GLOBALS['association_metas']['aff_categorie']) {
			$tbd .= '<td class="text">'.
			( $data['id_categorie']
				? sql_getfetsel('valeur', 'spip_asso_categories', "id_categorie=$data[id_categorie]" )
				: $data['categorie']
			) .'</td>';
		}
		if ($GLOBALS['association_metas']['aff_validite'])
			$tbd .= '<td class="date">'. association_formater_date($data['date_validite'], 'dtend') .'</td>';
		$tbd .= '<td class="action">'
		.'<a href="'. generer_url_ecrire('auteur_infos', "id_auteur=$id_auteur") .'">'. http_img_pack($icone,'','', _T('asso:adherent_label_modifier_visiteur') ) .'</a></td>';
		if (autoriser('editer_membres', 'association')) {
			$tbd .= association_bouton_paye('ajout_cotisation','id='.$id_auteur)
			. association_bouton_edit('adherent', "id=$id_auteur");
		}
		$tbd .= association_bouton_list('adherent','id='.$id_auteur)
		. association_bouton_coch('id_auteurs', $id_auteur)
		. "</tr>\n";
	}
	if (!$tbd)
		return _T('asso:recherche_reponse0');
	$thd = '<tr class="row_first">';
	if ($GLOBALS['association_metas']['aff_id_auteur'])
		$thd .= '<th>'. _T('asso:entete_id') .'</th>';
	if ($GLOBALS['association_metas']['aff_photo'])
		$thd .= '<th>'._T('asso:adherent_libelle_photo') .'</th>';
	if ($GLOBALS['association_metas']['aff_civilite'] && $GLOBALS['association_metas']['civilite'])
		$thd .= '<th>'. _T('asso:adherent_libelle_sexe') .'</th>';
	$thd .= '<th>'. _T('asso:adherent_libelle_nom_famille') .'</th>';
	if ($GLOBALS['association_metas']['aff_prenom'] && $GLOBALS['association_metas']['prenom'])
		$thd .= '<th>'. _T('asso:adherent_libelle_prenom') .'</th>';
	if ($GLOBALS['association_metas']['aff_groupes'])
		$thd .= '<th>'. _T('asso:adherent_libelle_groupes') .'</th>';
	if ($GLOBALS['association_metas']['aff_id_asso'] && $GLOBALS['association_metas']['id_asso'])
		$thd .= '<th>'. _T('asso:adherent_libelle_id_asso') .'</th>';
	if ($GLOBALS['association_metas']['aff_categorie'])
		$thd .= '<th>'. _T('asso:adherent_libelle_categorie') .'</th>';
	if ($GLOBALS['association_metas']['aff_validite'])
		$thd .= '<th>'. _T('asso:adherent_libelle_validite') .'</th>';
	$thd .= '<th colspan="'
	  . (autoriser('editer_membres', 'association')?4:2)
	  .'" class="actions">'._T('asso:entete_actions')
	  ."</th>\n"
	  . '<th><input title="'._T('asso:selectionner_tout')
	  .'" type="checkbox" id="selectionnerTous" onclick="var currentVal = this.checked; var checkboxList = document.getElementsByName(\'id_auteurs[]\'); for (var i in checkboxList) {checkboxList[i].checked=currentVal;}" /></th>'
	. "</tr>\n";
	$res = "<table width='100%' class='asso_tablo' id='liste_adherents'>\n";
	$res .= $thd.$tbd.$thd. "</table>\n";
/// AFFICHAGES_CENTRAUX : PAGINATION
	$res .= "<table width='100%' class='asso_tablo_filtres'><tr>\n";
	if (autoriser('editer_membres', 'association', 100)) {
		$nav .= "<td align='right' class='formulaire'>\n";
		if ($tbd) {
			if (autoriser('editer_membres', 'association')) {
				$nav .= '<select name="action_adherents"><option value="">'._T('asso:choisir_action')."</option>\n<option value='desactive'>"
				.($statut_interne=='sorti' ? _T('asso:reactiver_adherent') : _T('asso:desactiver_adherent'))
				."</option>\n<option value='delete'>"._T('asso:supprimer_adherent')."</option>\n";
			}
			if (autoriser('editer_groupes', 'association', 100)) {
				$nav .= sql_countsel('spip_asso_groupes', '') ? '<option value="grouper">'._T('asso:rejoindre_groupe').'</option><option value="degrouper">'._T('asso:quitter_un_groupe')."</option>\n" : '';
			}
			$nav .= '</select><input type="submit" value="'._T('asso:bouton_confirmer').'" />';
		}
		$nav .= '<input type="hidden" name="statut_courant" value="'.$statut_interne.'" />'
		.  '</td>';
	}
	$res .= association_form_souspage(array('spip_asso_membres', $critere), 'adherents', $args_url, $nav);
// FIN
	return generer_form_ecrire('action_adherents', $res);
}

?>