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

function exec_dons() {
	sinon_interdire_acces(autoriser('voir_dons', 'association'));
	include_spip ('association_modules');
/// INITIALISATIONS
	$id_don = association_passeparam_id('don');
	list($id_periode, $critere_periode) = association_passeparam_annee('don', 'asso_dons', $id_don);
	if ($id_don) { // la presence de ce parametre interdit la prise en compte d'autres (a annuler donc si presents dans la requete)
		$type = '';
		$suffixe_pdf = "don$id_don";
		$critere_type = '';
	} else { // on peut prendre en compte les filtres ; on recupere les parametres :
 		$type = _request('type'); // type de don
 		$suffixe_pdf = "dons_$id_periode".'_'.($type?$type:'tous');
		$critere_type = $type?"$type AND ":'';
	}
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('titre_onglet_dons', 'dons');
/// AFFICHAGES_LATERAUX : TOTAUX : nombre de dons selon leur nature
	$liste_effectifs = array(
		'argent' => sql_countsel('spip_asso_dons', "argent<>0 AND colis='' AND  $critere_periode"),
		'colis' => sql_countsel('spip_asso_dons', "argent=0 AND colis<>'' AND  $critere_periode")
	);
	echo association_tablinfos_effectifs('dons', array(
		'pair' => array( 'dons_en_argent', $liste_effectifs['argent'], ),
		'prospect' => array('dons_en_nature', $liste_effectifs['colis'], ),
		'impair' => array('dons_mixtes', sql_countsel('spip_asso_dons', $critere_periode)-$liste_effectifs['argent']-$liste_effectifs['colis'] ),
	));
/// AFFICHAGES_LATERAUX : STATS sur les donnations de l'exercice
	echo association_tablinfos_stats('donnations', 'dons', array('dons_en_argent'=>'argent','dons_en_nature'=>'valeur',), $critere_periode);
/// AFFICHAGES_LATERAUX : TOTAUX : montants des dons et remboursements financiers
	$dons_financiers = sql_getfetsel('SUM(argent) AS somme_recettes', 'spip_asso_dons', "argent AND $critere_periode" );
	$remboursements = sql_getfetsel('SUM(argent) AS somme_reversees', 'spip_asso_dons', "argent AND contrepartie AND $critere_periode" );
	echo association_tablinfos_montants($id_periode, $dons_financiers, $remboursements);
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('ajouter_un_don', 'ajout-24.png', array('edit_don'), array('editer_dons', 'association') ),
	), 2);
/// AFFICHAGES_LATERAUX : Forms-PDF
	if ( autoriser('exporter_membres', 'association') ) { // etiquettes
		echo association_form_etiquettes(" $critere_type $critere_periode ", ' LEFT JOIN spip_asso_dons AS d ON m.id_auteur=d.id_auteur ', $suffixe_pdf);
	}
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('dons-24.gif', 'tous_les_dons');
/// AFFICHAGES_CENTRAUX : FILTRES
	$filtre_typedon = "<select name='type' onchange='form.submit()'>\n";
	$filtre_typedon .= '<option value="">' ._T('asso:entete_tous') ."</option>\n";
	$filtre_typedon .= '<option value="argent"'. ($type=='argent'?' selected="selected"':'') .'>'. _T('asso:dons_en_argent') ."</option>\n";
	$filtre_typedon .= '<option value="colis"'. ($type=='colis'?' selected="selected"':'') .'>'. _T('asso:dons_en_nature') ."</option>";
	$filtre_typedon .= '<option value="argent AND colis"'. (($type=='argent AND colis' OR $type=='colis AND argent')?' selected="selected"':'') .'>'. _T('asso:dons_mixtes') ."</option>\n";
	$filtre_typedon .= "</select>\n";
	echo association_form_filtres(array(
		'periode' => array($id_periode, 'asso_dons', 'don'),
#		'id' => $id_don,
	), 'dons', array(
		'type' => $filtre_typedon,
	));
/// AFFICHAGES_CENTRAUX : TABLEAU
	echo association_bloc_listehtml2('asso_dons',
		sql_select("*, CASE WHEN argent<>0 AND colis='' THEN 'argent' WHEN argent=0 AND colis<>''  THEN 'colis' ELSE 'mixte' END AS type_don ", 'spip_asso_dons', "$critere_type $critere_periode", '', 'date_don DESC'), // requete
		array(
			'id_don' => array('asso:entete_id', 'entier'),
			'date_don' => array('asso:entete_date', 'date', ''),
			'id_auteur' => array('asso:entete_nom', 'idnom', array('spip_asso_dons', 'nom', 'id_auteur'), 'membre'),
			'argent' => array('asso:argent', 'prix', 'donation cash'),
			'colis' => array('asso:colis', 'texte', 'propre'), // voir s'il est possible de mettre la valeur au survol
//			'valeur' => array('asso:valeur', 'prix', 'donation estimated'),
//			'contrepartie' => array('asso:contrepartie', 'texte', 'propre'),
//			'commentaire' => array('asso:entete_commentaire', 'texte', 'propre'),
		), // entetes et formats des donnees
		autoriser('editer_dons', 'association') ? array(
			array('suppr', 'don', 'id=$$'),
			array('edit', 'don', 'id=$$'),
		) : array(), // boutons d'action
		'id_don', // champ portant la cle des lignes et des boutons
		array('argent'=>'pair', 'colis'=>'prospect', 'mixte'=>'impair'), 'type_don', $id_don
	);
/// AFFICHAGES_CENTRAUX : PAGINATION
	echo association_form_souspage(array('spip_asso_dons', "$critere_type $critere_periode"), 'dons', ($GLOBALS['association_metas']['exercices']?'exercice':'annee')."=$id_periode".($type?"&type='$type'":'') );
	fin_page_association();
}

?>