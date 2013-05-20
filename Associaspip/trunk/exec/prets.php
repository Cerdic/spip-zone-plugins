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

function exec_prets() {
	sinon_interdire_acces(autoriser('voir_prets', 'association'));
	include_spip('association_modules');
/// INITIALISATIONS
	$id_pret = association_recuperer_entier('id_pret');
	list($id_periode, $critere_periode) = association_passeparam_periode('sortie', 'asso_prets', $id_pret);
	if ($id_pret) { // la presence de ce parametre interdit la prise en compte d'autres (a annuler donc si presents dans la requete)
		$id_ressource = intval(sql_getfetsel('id_ressource', 'spip_asso_prets', "id_pret=$id_pret"));
		$ressource = sql_fetsel('*', 'spip_asso_ressources', "id_ressource=$id_ressource");
		$statut = '';
		$suffixe_pdf = "pret$id_pret";
	} else { // on peut prendre en compte les filtres ; on recupere les parametres de :
		$r = association_controle_id('ressource', 'asso_ressources');
		if (!$r) return;
		list($id_ressource, $ressource) = $r;
		$statut = association_passeparam_statut(); // etat de restitution du pret
		$suffixe_pdf = "prets_$id_ressource"."_$id_periode".'_'.($statut?$statut:'tous');
	}
	$where = "id_ressource=$id_ressource AND $critere_periode";
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('titre_onglet_prets', 'ressources');
/// AFFICHAGES_LATERAUX : TOTAUX : effectifs par statuts
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
	} else { // utilisation des anciens 4+ statuts textuels (etat de reservation)
		switch($ressource['statut']) {
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
	echo '<div class="hproduct">'. association_tablinfos_intro('<span class="n">'.$ressource['intitule'].'</span>', 'ressource', $id_ressource, $infos, 'asso_ressource') .'</div>';
/// AFFICHAGES_LATERAUX : TOTAUX : nombres d'emprunts de la ressource pour la periode
	echo association_tablinfos_effectifs('prets', array(
		'pair' => array( 'prets_restitues', sql_countsel('spip_asso_prets', "$where AND date_retour<NOW() AND date_retour<>'0000-00-00T00:00:00' "), ), // restitues, termines, anciens, ...
		'impair' => array( 'prets_encours', sql_countsel('spip_asso_prets', "$where AND (date_retour>NOW() OR date_retour='0000-00-00T00:00:00' ) "), ), // dus, en attente, en cours, nouveaux, ...
	));
/// AFFICHAGES_LATERAUX : STATS sur la duree et le montant des emprunts pendant la periode
	echo association_tablinfos_stats('prets', 'prets', array('entete_duree'=>'duree','entete_montant'=>'duree*prix_unitaire',), $where);
/// AFFICHAGES_LATERAUX : TOTAUX : montants generes par les umprunts de la ressources depuis le debut
	echo association_tablinfos_montants('emprunts', sql_getfetsel('SUM(duree*prix_unitaire) AS totale', 'spip_asso_prets', "id_ressource=$id_ressource"), $ressource['prix_acquisition']); // /!\ les recettes sont calculees simplement (s'il y a un systeme de penalite pour retard, il faut s'adapter a la saisie pour que le module soit utile) ; les depenses ne prennent pas en compte les eventuels frais d'entretien ou de reparation de la ressource...
/// AFFICHAGES_LATERAUX : RACCOURCIS
	$res[] = array('ressources_titre_liste_ressources', 'grille-24.png', array('ressources', "id=$id_ressource"), array('voir_ressources', 'association'));
	if ( (is_numeric($ressource['statut']) && $ressource['statut']>0) || $ressource['statut']=='ok' ) // ressource disponible a la/le location/pret
		$res [] = array('prets_nav_ajouter', 'creer-12.gif', array('edit_pret', "id_ressource=$id_ressource&id_pret=0"), array('editer_prets', 'association'));
	echo association_navigation_raccourcis($res, 15);
	if ( autoriser('exporter_membres', 'association') ) { // etiquettes
		echo association_form_etiquettes($where, ' LEFT JOIN spip_asso_prets AS p ON m.id_auteur=p.id_auteur ', $suffixe_pdf); //!\ reorganiser le code pour prendre en compte le statut/etat de restitution
	}
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('pret-24.gif', 'prets_titre_liste_reservations');
/// AFFICHAGES_CENTRAUX : FILTRES
	$filtre_statut = "<select name='statut' onchange='form.submit()'>\n";
	$filtre_statut .= '<option value="">' ._T('asso:entete_tous') ."</option>\n";
	$filtre_statut .= '<option value="sortie"';
	$filtre_statut .= (intval($statut)<0||$statut=='sortie'?' selected="selected"':'');
	$filtre_statut .= '>'. _T('asso:prets_encours') ."</option>\n";
	$filtre_statut .= '<option value="retour"';
	$filtre_statut .= (intval($statut)>0||$statut=='retour'?' selected="selected"':'');
	$filtre_statut .= '>'. _T('asso:prets_restitues') ."</option>\n";
	$filtre_statut .= "</select>\n";
	echo association_form_filtres(array(
		'periode' => array($id_periode, 'asso_prets', 'sortie')),
		'prets', array( // "prets&id=$id_ressource" a la place de 'prets' ne fonctionne pas...
		'' => "<input type='hidden' name='id' value='$id_ressource' />",
		'statut' => $filtre_statut,
	));
/// AFFICHAGES_CENTRAUX : TABLEAU
	switch ($statut) {
		case 'retour' :
			$where .= " AND date_retour<NOW() AND date_retour<>'0000-00-00T00:00:00'";
			break;
		case 'sortie' :
			$where .= " AND (date_retour>NOW() OR date_retour='0000-00-00T00:00:00')";
			break;
		default :
			break;
	}
	echo association_bloc_listehtml2('asso_prets',
		sql_select("*, CASE WHEN date_retour='0000-00-00T00:00:00' THEN 1 WHEN date_retour>NOW() THEN 1 ELSE 0 END AS statut_sortie ", 'spip_asso_prets', $where, '', 'date_sortie DESC'), // requete
		array(
			'id_pret' => array('asso:entete_id', 'entier'),
			'date_sortie' => array('asso:prets_entete_date_sortie', 'date', 'dtstart'),
			'id_auteur' => array('asso:entete_nom', 'idnom', array(), 'membre'),
			'duree' => array('asso:entete_duree', 'duree', intval($unite)),
			'date_retour' => array('asso:prets_entete_date_retour', 'date', 'dtend'),
		), // entetes et formats des donnees
		autoriser('editer_prets', 'association') ? array(
			array('suppr', 'pret', 'id=$$'),
			array('edit', 'pret', 'id=$$'),
		) : array(), // boutons d'action
		'id_pret', // champ portant la cle des lignes et des boutons
		array('pair', 'impair'), 'statut_sortie', $id_pret
	);
/// AFFICHAGES_CENTRAUX : PAGINATION
	echo association_form_souspage(array('spip_asso_prets', $where), 'prets', "id=$id_ressource&".($GLOBALS['association_metas']['exercices']?'exercice':'annee')."=$id_periode".($statut?"&statut='$statut'":'') );
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>