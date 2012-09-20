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

function action_editer_asso_ressources()
{
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_ressource=$securiser_action();
    $erreur = '';
    $code= _request('code');
    $date_achat = association_recuperer_date('date_acquisition');
    $prix_achat = association_recuperer_montant('prix_acquisition');
    $quantite = floatval(_request('quantite'));
    $statut = $quantite ? (_request('suspendu')?"-$quantite":$quantite) : _request('statut');
    $champs = array(
	'date_acquisition' => $date_achat,
	'code' => $code,
	'intitule' => _request('intitule'),
	'prix_caution' => association_recuperer_montant('prix_caution'),
	'pu' => association_recuperer_montant('pu'),
	'ud' => _request('ud'),
	'statut' => $statut,
	'commentaire' => _request('commentaire'),
    );
    include_spip('base/association');
    $id_compte = association_recuperer_entier('id_compte');
    $journal = _request('journal');
    include_spip('inc/association_comptabilite');
    include_spip('inc/modifier'); // on passe par modifier_contenu pour que la modification soit envoyee aux plugins et que Champs Extras 2 la recupere
    if ($id_ressource) {// c'est une modification
	// on modifie les operations comptables associees a l'acquisition
	$erreur = association_modifier_operation_comptable($date_achat, 0, $prix_achat, '['. _T('asso:titre_num', array('titre'=>_T('local:ressource'),'num'=>"'$code' &times;&nbsp;$statut") ) ."->ressource$id_ressource] ", $GLOBALS['association_metas']['pc_ressources'], $journal, $id_ressource, $id_compte);
	// on modifie les informations relatives a la ressource
	modifier_contenu(
	    'asso_membre', // table a modifier
	    $id_ressource, // identifiant
	    '', // parametres
	    $champs // champs a modifier
	);
    } else { // c'est un ajout
	$id_ressource = sql_insertq('spip_asso_ressources', $champs );
	if (!$id_ressource) { // la suite serait aleatoire sans cette cle...
	    $erreur = _T('asso:erreur_sgbdr');
	} else { // on ajoute les operations comptables associees a l'acquisition
	    association_ajouter_operation_comptable($date_achat, 0, $prix_achat, '['. _T('asso:titre_num', array('titre'=>_T('local:ressource'),'num'=>"'$code' &times;&nbsp;$statut") ) ."->ressource$id_ressource] ", $GLOBALS['association_metas']['pc_ressources'], $journal, $id_ressource);
	    modifier_contenu('asso_ressources', $id_ressource, '', array());
	}
    }

    return array($id_ressource, $erreur);
}

?>