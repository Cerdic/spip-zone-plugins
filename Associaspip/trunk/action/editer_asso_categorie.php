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

function action_editer_asso_categorie_dist() {

    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_categorie = $securiser_action();
    $erreur = '';
    $champs = array(
	'libelle' => _request('libelle'),
	'valeur' => _request('valeur'),
	'duree' => association_recuperer_montant('duree'),
	'prix_cotisation' => association_recuperer_montant('prix_cotisation'),
	'commentaire' => _request('commentaire'),
    );
    if ($id_categorie) { // modification
	sql_updateq('spip_asso_categories', $champs, "id_categorie=$id_categorie");
    } else { // ajout
	$id_categorie = sql_insertq('spip_asso_categories', $champs);
	if (!$id_categorie)
	    $erreur = _T('asso:erreur_sgbdr');
    }

    return array($id_categorie, $erreur);
}

?>