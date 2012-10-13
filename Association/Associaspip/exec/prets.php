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

function exec_prets() {
	if (!autoriser('associer', 'activites')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		$id_pret = association_recuperer_entier('id_pret');
		list($annee, $critere_periode) = association_passeparam_annee('sortie', 'asso_prets', $id_pret);
		if ($id_pret) { // la presence de ce parametre interdit la prise en compte d'autres (a annuler donc si presents dans la requete)
			$id_ressource = sql_getfetsel('id_ressource', 'spip_asso_prets', "id_pret=$id_pret");
			$ressource = sql_fetsel('*', 'spip_asso_ressources', "id_ressource=$id_ressource");
			$statut = '';
		} else { // on peut prendre en compte les filtres ; on recupere les parametres de :
			list($id_ressource, $ressource) = association_passeparam_id('ressource', 'asso_ressources');
			$statut = association_passeparam_statut(); // etat de restitution du pret
		}
		onglets_association('titre_onglet_prets', 'ressources');
		$unite = $ressource['ud']?$ressource['ud']:'D';
		$infos['entete_code'] = association_formater_code($ressource['code'], 'x-spip_asso_ressources');
		$infos['ressources_entete_montant'] = association_formater_prix($ressource['pu'], 'rent');
		$infos['ressources_entete_caution'] = association_formater_prix($ressource['prix_caution'], 'guarantee');
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
			switch($ressource['statut']) { // utilisation des anciens 4+ statuts textuels (etat de reservation)
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
		$infos['statut'] = '<span class="'.(is_numeric($ressource['statut'])?'quanttity':'availability').'">'. association_formater_puce($ressource['statut'], $puce, "ressources_libelle_statut_$type") .'</span>';
		echo '<div class="hproduct">'. association_totauxinfos_intro('<span class="n">'.$ressource['intitule'].'</span>', 'ressource', $id_ressource, $infos, 'asso_ressource') .'</div>';
		// TOTAUX : nombres d'emprunts de la ressource depuis le debut
		$q_where = "id_ressource=$id_ressource AND $critere_periode ";
		echo association_totauxinfos_effectifs('prets', array(
			'pair' => array( 'prets_restitues', sql_countsel('spip_asso_prets', "$q_where AND date_retour<NOW() AND date_retour<>'0000-00-00T00:00:00' "), ), // restitues, termines, anciens, ...
			'impair' => array( 'prets_encours', sql_countsel('spip_asso_prets', "$q_where AND (date_retour>NOW() OR date_retour='0000-00-00T00:00:00' ) "), ), // dus, en attente, en cours, nouveaux, ...
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
		// FILTRES
		$filtre_statut = '<select name="statut" onchange="form.submit()">';
		$filtre_statut .= '<option value="">' ._T('asso:entete_tous') .'</option>';
		$filtre_statut .= '<option value="sortie"';
		$filtre_statut .= (intval($statut)<0||$statut=='sortie'?' selected="selected"':'');
		$filtre_statut .= '>'. _T('asso:prets_encours') .'</option>';
		$filtre_statut .= '<option value="retour"';
		$filtre_statut .= (intval($statut)>0||$statut=='retour'?' selected="selected"':'');
		$filtre_statut .= '>'. _T('asso:prets_restitues') .'</option>';
		$filtre_statut .= '</select>';
		filtres_association(array(
			'annee' => array($annee, 'asso_prets', 'sortie'),
		), 'prets', array(
			'' => "<input type='hidden' name='id' value='$id_ressource' />", // "prets&id=$id_ressource" a la place de 'prets' ne fonctionne pas...
			'statut' => $filtre_statut,
		));
		// TABLEAU
		switch ($statut) {
			case 'retour' :
				$q_where .= " AND date_retour<NOW() AND date_retour<>'0000-00-00T00:00:00'";
				break;
			case 'sortie' :
				$q_where .= " AND (date_retour>NOW() OR date_retour='0000-00-00T00:00:00')";
				break;
			default :
				break;
		}
		echo association_bloc_listehtml(
			array("*, CASE WHEN date_retour='0000-00-00T00:00:00' THEN 1 WHEN date_retour>NOW() THEN 1 ELSE 0 END AS statut_sortie ", 'spip_asso_prets', $q_where, '', 'date_sortie DESC'), // requete
			array(
				'id_pret' => array('asso:entete_id', 'entier'),
				'date_sortie' => array('asso:prets_entete_date_sortie', 'date', 'dtstart'),
				'id_auteur' => array('asso:entete_nom', 'idnom', array(), 'membre'),
				'duree' => array('asso:entete_duree', 'duree', $unite),
				'date_retour' => array('asso:prets_entete_date_retour', 'date', 'dtend'),
			), // entetes et formats des donnees
			array(
				array('suppr', 'pret', 'id=$$'),
				array('edit', 'pret', 'id=$$'),
			), // boutons d'action
			'id_pret', // champ portant la cle des lignes et des boutons
			array('pair', 'impair'), 'statut_sortie', $id_pret
		);
		echo association_selectionner_souspage(array('spip_asso_prets', $q_where), 'prets', "id=$id_ressource&annee=$annee".($etat?"&etat='$etat'":'') );
		fin_page_association();
	}
}

?>