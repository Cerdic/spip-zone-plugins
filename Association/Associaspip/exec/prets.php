<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_prets()
{
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'activites', $id_ressource)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_ressource = intval(_request('id'));
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:prets_titre_liste_reservations')) ;
		association_onglets(_T('asso:titre_onglet_prets'));
		echo debut_gauche('',true);
		echo debut_boite_info(true);
		$ressource = sql_fetsel('*', 'spip_asso_ressources', "id_ressource=$id_ressource" ) ;
		$unite = $data['ud'];
		$infos['ressources_libelle_code'] = $data['code'];
		if (is_numeric($data['statut'])) { /* utilisation des 3 nouveaux statuts numeriques (gestion de quantites/exemplaires) */
			if ($data['statut']>0) {
				$puce = 'verte';
				$type = 'ok';
			} elseif ($data['statut']<0) {
				$puce = 'orange';
				$type = 'suspendu';
			} else {
				$puce = 'rouge';
				$type = 'reserve';
			}
		} else {
			switch($data['statut']){ /* utilisation des anciens 4+ statuts textuels (etat de reservation) */
				case 'ok':
					$puce = 'verte';
					break;
				case 'reserve':
					$puce = 'rouge';
					break;
				case 'suspendu':
					$puce = 'orange';
					break;
				case 'sorti':
				case '':
				case NULL:
					$puce = 'poubelle';
					break;
			}
			$type = $data['statut'];
		}
		$infos['statut'] =  '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-'.$puce.'.gif" title="'.$data['statut'].'" alt="" /> '. _T("asso:ressources_libelle_statut_$type");
		$stats = sql_fetsel('AVG(duree) AS moyenne, STDDEV(duree) AS variance', 'spip_asso_prets', "id_ressource=$id_ressource");
		$infos['duree_emprunt_moyenne'] = association_dureefr($stats['moyenne'],$unite);
		$infos['duree_emprunt_ecart_type'] = association_dureefr(sqrt($stats['variance']),$data['unite']);
		echo totauxinfos_intro($data['intitule'] , 'ressource', $id_ressource, $infos );
		// TOTAUX : nombres d'emprunts de la ressource depuis le debut
		$liste_libelles = $liste_effectifs = array();
		$liste_libelles['pair'] = _T('asso:prets_restitues'); // restitues, termines, anciens, ...
		$liste_libelles['impair'] = _T('asso:prets_en_cours'); // dus, en attente, en cours, nouveaux, ...
		$liste_effectifs['pair'] = sql_countsel('spip_asso_prets', "id_ressource=$id_ressource AND date_retour>=date_sortie");
		$liste_effectifs['impair'] = sql_countsel('spip_asso_prets', "id_ressource=$id_ressource AND date_retour<date_sortie");
		// TOTAUX : montants generes par les umprunts de la ressources
		$quantiteFacturee = sql_fetsel('SUM(duree) AS totale', 'spip_asso_prets', "id_ressource=$id_ressource");
		echo totauxinfos_sommes(_T('asso:emprunts'), $data['pu']*$quantiteFacturee['totale'], NULL);
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = ($data['statut']=='ok' || $data['statut']>0) ? association_icone(_T('asso:prets_nav_ajouter'), generer_url_ecrire('edit_pret','id_ressource='.$id_ressource.'&id_pret='), 'creer-12.gif') : '';
		$res .= association_icone(_T('asso:bouton_retour'), generer_url_ecrire('ressources'), 'retour-24.png');
		echo bloc_des_raccourcis($res);
		echo debut_droite('',true);
		echo debut_cadre_relief('', false, '', $titre =_T('asso:prets_titre_liste_reservations'));
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_prets'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'._T('asso:entete_id').'</th>';
		echo '<th>'._T('asso:prets_entete_date_sortie').'</th>';
		echo '<th>'._T('asso:entete_nom').'</th>';
		echo '<th>'._T('asso:prets_entete_duree').'</th>';
		echo '<th>'._T('asso:prets_entete_date_retour').'</th>';
		echo '<th colspan="2" class="actions">'._T('asso:entete_action').'</th>';
		echo "</tr>\n</thead><tbody>";
		$query = sql_select('*', 'spip_asso_prets', "id_ressource=$id_ressource", '', 'date_sortie DESC' );
		while ($data = sql_fetch($query)) {
			echo '<tr class="'.($data['date_retour']<$data['date_sortie']?'pair':'impair').'">';
			echo '<td class="integer">'.$data['id_pret'].'</td>';
			echo '<td class="date">'. association_datefr($data['date_sortie'], 'dtstart') .'</td>';
			$id_emprunteur = intval($data['id_emprunteur']);
			$auteur = sql_fetsel('*', 'spip_asso_membres', "id_auteur=$id_emprunteur");
			echo '<td class="n">'.association_calculer_nom_membre($auteur['sexe'], $auteur['prenom'], $auteur['nom_famille'],'span');
			echo '</td><td class="date">' .association_dureefr($data['duree'],$unite) .'</td>';
			echo '<td class="date">'. ($data['date_retour']<$data['date_sortie'] ? '&nbsp' : association_datefr($data['date_retour'],'dtend') ) .'</td>';
			echo '<td class="actions">'. association_bouton('prets_nav_annuler', 'poubelle-12.gif', 'action_prets', 'id_pret='.$data['id_pret'].'&id_ressource='.$id_ressource) .'</td>';
			echo '<td class="actions">' . association_bouton('prets_nav_editer', 'edit-12.gif', 'edit_pret', 'id_pret='.$data['id_pret']) . '</td>';
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		echo fin_cadre_relief();
		echo fin_page_association();
	}
}

?>