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

function exec_ressources() {
	if (!autoriser('associer', 'ressources')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('inc/navigation_modules');
		$id_ressource = association_passeparam_id('ressource');
		onglets_association('titre_onglet_prets', 'ressources');
		// INTRO : presentation du module
		echo '<p>'._T('asso:ressources_info').'</p>';
		// TOTAUX : nombre de ressources par statut
		echo association_totauxinfos_effectifs('ressources', array(
			'valide' => array('', sql_countsel('spip_asso_ressources', "statut='ok' OR ROUND(statut,0)>0"), association_formater_puce('', 'verte', 'ressources_libelle_statut_ok'), ),
			'prospect' => array('', sql_countsel('spip_asso_ressources', "statut='suspendu' OR ROUND(statut,0)<0"), association_formater_puce('', 'orange', 'ressources_libelle_statut_suspendu'), ),
			'cv' => array('', sql_countsel('spip_asso_ressources', "statut IN ('reserve',0)"), association_formater_puce('', 'rouge', 'ressources_libelle_statut_reserve'), ),
			'sorti' => array('', sql_countsel('spip_asso_ressources', "statut IN ('sorti','',NULL)"), association_formater_puce('', 'poubelle', 'ressources_libelle_statut_sorti'), ),
		));
/* mdr : cela n'a de sens que si les ressources se pretent toutes sur la meme unite...
		// STATS sur tous les prets
		echo association_totauxinfos_stats('prets', 'prets', array('entete_duree'=>'duree',), "DATE_FORMAT(date_sortie, '%Y')=DATE_FORMAT(NOW(), '%Y')");
rdm */
		// TOTAUX : montants des locations sur l'annee en cours
		$recettes = sql_getfetsel('SUM(duree*prix_unitaire) AS somme_recettes', 'spip_asso_prets', "DATE_FORMAT('date_sortie', '%Y')=DATE_FORMAT(NOW(), '%Y') ");
		$depences = sql_getfetsel('SUM(prix_acquisition) AS somme_depences', 'spip_asso_ressources', "DATE_FORMAT('date_acquisition', '%Y')=DATE_FORMAT(NOW(), '%Y') ");
		echo association_totauxinfos_montants('ressources', $recettes, $depenses);
		// datation et raccourcis
		raccourcis_association(array(), array(
			'ressources_nav_ajouter' => array('ajout-24.png', 'edit_ressource'),
		) );
		debut_cadre_association('pret-24.gif', 'ressources_titre_liste_ressources');
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
		// Filtres
		/// annees d'acquisition ?
		/// statut de disponibilite ?
		// affichage du tableau
		echo association_bloc_listehtml(
			array('*', 'spip_asso_ressources', '','',  'id_ressource'), // requete
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
			array(
				array('suppr', 'ressource', 'id=$$'),
				array('edit', 'ressource', 'id=$$'),
				array('act', 'prets_nav_gerer', 'voir-12.png', 'prets', 'id=$$'),
			), // boutons d'action
			'id_ressource', // champ portant la cle des lignes et des boutons
			$s_css, 'statut', $id_ressource
		);
		fin_page_association();
	}
}

?>