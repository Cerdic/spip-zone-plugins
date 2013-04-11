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

function action_editer_asso_groupe_dist() {
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_groupe = $securiser_action();
    $erreur = '';
    $champs = array(
	'nom' => _request('nom'),
	'commentaire' => _request('commentaire'),
	'affichage' => association_recuperer_entier('affichage'),
    );
    include_spip('base/association');
    if ($id_groupe) { // modification
	sql_updateq('spip_asso_groupes', $champs, "id_groupe=$id_groupe");
    } else { // ajout
	$id_groupe = sql_insertq('spip_asso_groupes', $champs);
	if (!$id_groupe)
	    $erreur = _T('asso:erreur_sgbdr');
    }

    return array($id_groupe, $erreur);
}

?>