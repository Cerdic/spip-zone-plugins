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

include_spip ('inc/navigation_modules');

function exec_prets()
{
	if (!autoriser('associer', 'activites', $id_ressource)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_ressource = intval(_request('id'));
		onglets_association('titre_onglet_prets', 'ressources');
		$ressource = sql_fetsel('*', 'spip_asso_ressources', "id_ressource=$id_ressource" ) ;
		$unite = $ressource['ud']?$ressource['ud']:'D';
		$infos['ressources_libelle_code'] = $ressource['code'];
		$infos['ressources_libelle_caution'] = association_formater_prix($ressource['prix_caution']);
		if (is_numeric($ressource['statut'])) { // utilisation des 3 nouveaux statuts numeriques (gestion de quantites/exemplaires)
			if ($ressource['statut']>0) {
				$puce = 'verte';
				$type = 'ok';
			} elseif ($ressource['statut']<0) {
				$puce = 'orange';
				$type = 'suspendu';
			} else {
				$puce = 'rouge';
				$type = 'reserve';
			}
		} else {
			switch($ressource['statut']){ // utilisation des anciens 4+ statuts textuels (etat de reservation)
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
			$type = $ressource['statut'];
		}
		$infos['statut'] = association_formater_puce("ressources_libelle_statut_$type", $puce);
		echo association_totauxinfos_intro($ressource['intitule'], 'ressource', $id_ressource, $infos, 'asso', 'asso_ressource');
		// TOTAUX : nombres d'emprunts de la ressource depuis le debut
		echo association_totauxinfos_effectifs('prets', array(
			'pair' => array( 'prets_restitues', sql_countsel('spip_asso_prets', "id_ressource=$id_ressource AND date_retour>date_sortie"), ), // restitues, termines, anciens, ...
			'impair' => array( 'prets_encours', sql_countsel('spip_asso_prets', "id_ressource=$id_ressource AND date_retour<=date_sortie"), ), // dus, en attente, en cours, nouveaux, ...
		));
		// STATS sur la duree et le montant des emprunts
		echo association_totauxinfos_stats('prets', 'prets', array('entete_duree'=>'duree','entete_montant'=>'duree*prix_unitaire',), "id_ressource=$id_ressource");
		// TOTAUX : montants generes par les umprunts de la ressources
		$recettes = sql_getfetsel('SUM(duree*prix_unitaire) AS totale', 'spip_asso_prets', "id_ressource=$id_ressource");
		echo association_totauxinfos_montants('emprunts', $recettes, $ressource['prix_acquisition']); // /!\ les recettes sont calculees simplement (s'il y a un systeme de penalite pour retard, il faut s'adapter a la saisie pour que le module soit utile) ; les depenses ne prennent pas en compte les eventuels frais d'entretien ou de reparation de la ressource...
		// datation et raccourcis
		if ( (is_numeric($ressource['statut']) && $ressource['statut']>0) || $ressource['statut']=='ok' )
			$res['prets_nav_ajouter'] = array('creer-12.gif', array('edit_pret', "id_ressource=$id_ressource&id_pret="), );
		raccourcis_association('ressources', $res);
		debut_cadre_association('pret-24.gif', 'prets_titre_liste_reservations');
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_prets'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'._T('asso:entete_id').'</th>';
		echo '<th>'._T('asso:prets_entete_date_sortie').'</th>';
		echo '<th>'._T('asso:entete_nom').'</th>';
		echo '<th>'._T('asso:prets_entete_duree').'</th>';
		echo '<th>'._T('asso:prets_entete_date_retour').'</th>';
		echo '<th colspan="2" class="actions">'._T('asso:entete_actions').'</th>';
		echo "</tr>\n</thead><tbody>";
		$query = sql_select('*', 'spip_asso_prets', "id_ressource=$id_ressource", '', 'date_sortie DESC' );
		while ($data = sql_fetch($query)) {
			echo '<tr class="'.($data['date_retour']>$data['date_sortie']?'pair':'impair').'" id="'.$data['id_pret'].'">';
			echo '<td class="integer">'.$data['id_pret'].'</td>';
			echo '<td class="date">'. association_formater_date($data['date_sortie'], 'dtstart') .'</td>';
			$id_emprunteur = intval($data['id_emprunteur']);
			$auteur = sql_fetsel('*', 'spip_asso_membres', "id_auteur=$id_emprunteur");
			echo '<td class="n">'.association_calculer_nom_membre($auteur['sexe'], $auteur['prenom'], $auteur['nom_famille'],'span');
			echo '</td><td class="date">'.association_formater_duree($data['duree'],$unite) .'</td>';
			echo '<td class="date">'. ($data['date_retour']<$data['date_sortie'] ? '&nbsp' : association_formater_date($data['date_retour'],'dtend') ) .'</td>';
			echo association_bouton_suppr('pret', 'id_pret='.$data['id_pret'].'&id_ressource='.$id_ressource);
			echo association_bouton_edit('pret', 'id_pret='.$data['id_pret']);
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		fin_page_association();
	}
}

?>