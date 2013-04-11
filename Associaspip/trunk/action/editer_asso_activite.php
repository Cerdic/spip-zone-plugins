<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
    return;

function action_editer_asso_activite_dist() {
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_activite = $securiser_action();
    $erreur = '';
    $date_paiement = association_recuperer_date('date_paiement');
    $participant = _request('nom');
    $id_auteur = association_recuperer_entier('id_auteur');
    if (!$participant AND $id_auteur) {
	$participant = association_formater_idnom($id_auteur, array('spip_asso_membres'), '');
    }
    $evenement = association_recuperer_entier('id_evenement');
    $prix_unite = association_recuperer_montant('prix_unitaire');
    $quantite = association_recuperer_montant('quantite');
    $modifs = array(
	'id_evenement' => $evenement,
	'nom' => _request('nom'),
	'id_auteur' => $id_auteur,
	'quantite' => $quantite,
	'prix_unitaire' => $prix_unite,
	'date_paiement' => $date_paiement,
	'date_inscription' => association_recuperer_date('date_inscription'),
	'commentaire' => _request('commentaire'),
    );
    include_spip('base/association');
    $id_compte = association_recuperer_entier('id_compte');
    $journal = _request('journal');
    $ref_activite = '['. _T('asso:titre_num', array('titre'=>_T('perso:evenement'),'num'=>$evenement) ) ."->activite$evenement] &mdash; ". ($id_auteur?"[$participant"."->membre$id_auteur]":$participant) ." :$quantite*$prix_unite" ;
    include_spip('inc/association_comptabilite');
    include_spip('inc/modifier'); // on passe par modifier_contenu pour que la modification soit envoyee aux plugins et que Champs Extras 2 la recupere
    if ($id_activite) { // c'est une modification
	// on modifie les operations comptables associees a la participation
	$erreur = association_modifier_operation_comptable($date_paiement, $quantite*$prix_unite, 0, $ref_activite, $GLOBALS['association_metas']['pc_activites'], $journal, $id_activite, $id_compte);
	// on modifie les informations relatives a la participation
	modifier_contenu(
	    'asso_activites', // table a modifier
	    $id_activite, // identifiant
	    '', // parametres
	    $modifs // champs a modifier
	);
    } else { // c'est un ajout
	// on enregistre l'inscription/participation a l'activite
	$id_activite = sql_insertq('spip_asso_activites', $modifs);
	if (!$id_activite) { // la suite serait aleatoire sans cette cle...
	    $erreur = _T('asso:erreur_sgbdr');
	} else { // on ajoute l'operation comptable associee a la participation
	    association_ajouter_operation_comptable($date_paiement, $quantite*$prix_unite, 0, $ref_activite, $GLOBALS['association_metas']['pc_activites'], $journal, $id_activite);
	    modifier_contenu('asso_activites', $id_activite, '', array());
	}
    }

    return array($id_activite, $erreur);
}

?>