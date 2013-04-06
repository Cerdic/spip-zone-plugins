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

function exec_ressources() {
	if (!autoriser('voir_ressources', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('association_modules');
		$id_ressource = association_passeparam_id('ressource');
		if ($id_ressource) { // la presence de ce parametre interdit la prise en compte d'autres (a annuler donc si presents dans la requete)
			$statut = $type = '';
		} else { // on peut prendre en compte les filtres ; on recupere les parametres :
 			$statut = _request('statut'); // statut de la ressource
 			$type = _request('type'); // identifiant du type (categorie) de ressource
		}
		echo association_navigation_onglets('titre_onglet_prets', 'ressources');
		// INTRO : presentation du module
		echo "\n<p>"._T('asso:ressources_info')."</p>\n";
		// preparation des listes associees aux statuts
		$s_ico = $s_css = array();
		$s_query = sql_select('DISTINCT statut', 'spip_asso_ressources'); // liste des statuts utilises
		while ($data = sql_fetch($s_query)) {
			if (is_numeric($data['statut'])) { // utilisation des 3 nouveaux statuts numeriques (gestion de quantites/exemplaires)
				if ($data['statut']>0) { // ex: 'ok' (disponible ou libre)
					$s_ico[$data['statut']] = 'verte';
					$s_css[$data['statut']] = 'valide hproduct';
				} elseif ($data['statut']<0) { // ex: 'suspendu' (plus en pret)
					$s_ico[$data['statut']] = 'orange';
					$s_css[$data['statut']] = 'prospect hproduct';
				} else { // ex: 'reserve' (temporairement indisponible)
					$s_ico[$data['statut']] = 'rouge';
					$s_css[$data['statut']] = 'cv hproduct';
				}
			} else switch($data['statut']) { // utilisation des anciens 4+ statuts textuels (etat de reservation)
				case 'ok':
					$s_ico[$data['statut']] = 'verte';
					$s_css[$data['statut']] = 'valide hproduct';
					break;
				case 'reserve':
					$s_ico[$data['statut']] = 'rouge';
					$s_css[$data['statut']] = 'cv hproduct';
					break;
				case 'suspendu':
					$s_ico[$data['statut']] = 'orange';
					$s_css[$data['statut']] = 'prospect hproduct';
					break;
				case 'sorti':
				case '':
				case NULL:
					$s_ico[$data['statut']] = 'poubelle';
					$s_css[$data['statut']] = 'sorti hproduct';
					break;
			}
		}
		$s_sql = array(
			'ok' => "statut='ok' OR ROUND(statut,0)>0",
			'suspendu' => "statut='suspendu' OR ROUND(statut,0)<0",
			'reserve' => "statut='suspendu' OR ROUND(statut,0)<0",
			'sorti' => "statut IN ('sorti','',NULL)",
		);
		// TOTAUX : nombre de ressources par statut
		echo association_tablinfos_effectifs('ressources', array(
			'valide' => array('', sql_countsel('spip_asso_ressources', $s_sql['ok'] ), association_formater_puce('', 'verte', 'ressources_libelle_statut_ok'), ),
			'prospect' => array('', sql_countsel('spip_asso_ressources', $s_sql['suspendu']), association_formater_puce('', 'orange', 'ressources_libelle_statut_suspendu'), ),
			'cv' => array('', sql_countsel('spip_asso_ressources', $s_sql['reserve']), association_formater_puce('', 'rouge', 'ressources_libelle_statut_reserve'), ),
			'sorti' => array('', sql_countsel('spip_asso_ressources', $s_sql['sorti']), association_formater_puce('', 'poubelle', 'ressources_libelle_statut_sorti'), ),
		));
		// TOTAUX : montants des locations sur l'annee en cours
		$recettes = sql_getfetsel('SUM(duree*prix_unitaire) AS somme_recettes', 'spip_asso_prets', "DATE_FORMAT('date_sortie', '%Y')=DATE_FORMAT(NOW(), '%Y') ");
		$depences = sql_getfetsel('SUM(prix_acquisition) AS somme_depences', 'spip_asso_ressources', "DATE_FORMAT('date_acquisition', '%Y')=DATE_FORMAT(NOW(), '%Y') ");
		echo association_tablinfos_montants('ressources', $recettes, $depenses);
		// datation et raccourcis
		echo association_navigation_raccourcis(array(
			array('ressources_nav_ajouter', 'ajout-24.png', array('edit_ressource'), array('gerer_ressources', 'association') ),
		), 5);
		debut_cadre_association('pret-24.gif', 'ressources_titre_liste_ressources');
		// Filtres
		$filtre = '';
		foreach (array('ok', 'suspendu', 'reserve', 'sorti') as $type) {
			$s = ($type==$statut) ? " selected='selected'" : '';
			$p = association_langue("ressources_libelle_statut_$type");
			$filtre .= "<option value='$type'$s>$p</option>\n";
		}
		$filtre = '<select name="statut" onchange="form.submit()">\n<option value="">' ._T('asso:entete_tous') ."</option>\n$filtre\n</select>";

		echo association_form_filtres(array(), 'ressources', array('statut' => $filtre));
		// affichage du tableau
		echo association_bloc_listehtml2('asso_ressources',
			sql_select('*', 'spip_asso_ressources', $s_sql[$statut],'',  'id_ressource'),
			array(
				'id_ressource' => array('asso:entete_id', 'entier'),
				'statut' => array('', 'puce', $s_ico, ''), // quantity? availability?
				'date_acquisition' => array('asso:entete_date', 'date', 'dtstart', 'mois_annee'),
				'intitule' => array('asso:entete_article', 'texte', '', 'n'),
				'code' => array('asso:entete_code', 'code', 'x-spip_asso_ressources'),
				'pu' => array('asso:ressources_entete_montant', 'prix', 'rent'),
				'ud' => array('asso:entete_duree', 'duree', 1),
				'prix_caution' => array('asso:ressources_entete_caution', 'prix', 'guarantee'),
			), // entetes et formats des donnees
			autoriser('editer_ressources', 'association') ? array(
				array('suppr', 'ressource', 'id=$$'),
				array('edit', 'ressource', 'id=$$'),
				array('list', 'prets', 'id=$$'),
			) : array(), // boutons d'action
			'id_ressource', // champ portant la cle des lignes et des boutons
			$s_css, 'statut', $id_ressource
		);
		fin_page_association();
	}
}

?>