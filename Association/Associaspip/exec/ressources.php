<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/navigation_modules');

function exec_ressources()
{
	if (!autoriser('associer', 'ressources')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		onglets_association('titre_onglet_prets');
		// INTRO : presentation du module
		echo '<p>'._T('asso:ressources_info').'</p>';
		// TOTAUX : nombre de ressources par statut
		$liste_libelles['valide'] = '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-verte.gif" alt="" /> '. _T('asso:ressources_libelle_statut_ok') ;
		$liste_effectifs['valide'] = sql_countsel('spip_asso_ressources', "statut='ok' OR ROUND(statut,0)>0");
		$liste_libelles['prospect'] = '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-orange.gif" alt="" /> '. _T('asso:ressources_libelle_statut_suspendu') ;
		$liste_effectifs['prospect'] = sql_countsel('spip_asso_ressources', "statut='suspendu' OR ROUND(statut,0)<0");
		$liste_libelles['cv'] = '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-rouge.gif" alt="" /> '. _T('asso:ressources_libelle_statut_reserve') ;
		$liste_effectifs['cv'] = sql_countsel('spip_asso_ressources', "statut IN ('reserve',0)");
		$liste_libelles['sorti'] = '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-poubelle.gif" alt="" /> '. _T('asso:ressources_libelle_statut_sorti') ;
		$liste_effectifs['sorti'] = sql_countsel('spip_asso_ressources', "statut IN ('sorti','',NULL)");
		echo totauxinfos_effectifs('ressources', $liste_libelles, $liste_effectifs);
/* mdr : cela n'a de sens que si les ressources se pretent toutes sur la meme unite...
		// STATS sur tous les prets
		echo totauxinfos_stats('prets', 'prets', array('entete_duree'=>'duree',), "DATE_FORMAT(date_sortie, '%Y')=DATE_FORMAT(NOW(), '%Y')");
rdm */
		// TOTAUX : montants des locations sur l'annee en cours
		$recettes = sql_getfetsel('SUM(duree*prix_unitaire) AS somme_recettes', 'spip_asso_prets', "DATE_FORMAT('date_sortie', '%Y')=DATE_FORMAT(NOW(), '%Y') ");
		$depences = sql_getfetsel('SUM(prix_acquisition) AS somme_depences', 'spip_asso_ressources', "DATE_FORMAT('date_acquisition', '%Y')=DATE_FORMAT(NOW(), '%Y') ");
		echo totauxinfos_montants('ressources', $recettes, $depenses);
		// datation et raccourcis
		icones_association(array(), array(
			'ressources_nav_ajouter' => array('ajout-24.png', 'edit_ressource'),
		) );
		debut_cadre_association('pret-24.gif', 'ressources_titre_liste_ressources');
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_ressources'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'._T('asso:entete_id').'</th>';
		echo '<th>&nbsp;</th>';
		echo '<th>'._T('asso:ressources_entete_intitule').'</th>';
		echo '<th>'._T('asso:entete_code').'</th>';
		echo '<th>'._T('asso:ressources_entete_montant').'</th>';
		echo '<th>'._T('asso:ressources_entete_caution').'</th>';
		echo '<th colspan="3" class="actions">'._T('asso:entete_action').'</th>';
		echo "</tr>\n</thead><tbody>";
		$query = sql_select('*', 'spip_asso_ressources', '','',  'id_ressource') ;
		while ($data = sql_fetch($query)) {
			if (is_numeric($data['statut'])) { /* utilisation des 3 nouveaux statuts numeriques (gestion de quantites/exemplaires) */
				if ($data['statut']>0) { // ex: 'ok' (disponible ou libre)
					$puce = 'verte';
					$css = 'valide';
				} elseif ($data['statut']<0) { // ex: 'suspendu' (plus en pret)
					$puce = 'orange';
					$css = 'prospect';
				} else { // ex: 'reserve' (temporairement indisponible)
					$puce = 'rouge';
					$css = 'cv';
				}
			} else switch($data['statut']){ /* utilisation des anciens 4+ statuts textuels (etat de reservation) */
				case 'ok':
					$puce = 'verte';
					$css = 'valide';
					break;
				case 'reserve':
					$puce = 'rouge';
					$css = 'cv';
					break;
				case 'suspendu':
					$puce = 'orange';
					$css = 'prospect';
					break;
				case 'sorti':
				case '':
				case NULL:
					$puce = 'poubelle';
					$css = 'sorti';
					break;
			}
			echo "<tr class='$css'>";
			echo '<td class="integer">'.$data['id_ressource'].'</td>';
			echo '<td class="actions">'. association_bouton('','puce-'.$puce.'.gif', '', '', 'title="'.$data['statut'].'"') .'</td>';
			echo '<td class="text">'.$data['intitule'].'</td>';
			echo '<td class="text">'.$data['code'].'</td>';
			echo '<td class="decimal">'.association_prixfr($data['pu']).' / '.association_dureefr(1,$data['ud']).'</td>';
			echo '<td class="decimal">'.association_prixfr($data['prix_caution']).'</td>';
			echo '<td class="action">', association_bouton('ressources_nav_supprimer', 'suppr-12.gif', 'suppr_ressource', 'id='.$data['id_ressource']), '</td>';
			echo '<td class="action">', association_bouton('ressources_nav_editer', 'edit-12.gif', 'edit_ressource', 'id='.$data['id_ressource']), '</td>';
			echo '<td class="action">', association_bouton('prets_nav_gerer', 'voir-12.png', 'prets', 'id='.$data['id_ressource']), '</td>';
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		fin_page_association();
	}
}

?>