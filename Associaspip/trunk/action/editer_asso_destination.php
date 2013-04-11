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

function action_editer_asso_destination_dist() {
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_destination = $securiser_action();
    $erreur = '';
    $champs = array(
	'intitule' => _request('intitule'),
	'commentaire' => _request('commentaire'),
    );
    include_spip('base/association');
    if ($id_destination) { // modification
	sql_updateq('spip_asso_destination', $champs, "id_destination=$id_destination");
    } else { // ajout
	$id_destination = sql_insertq('spip_asso_destination', $champs);
	if (!$id_destination)
	    $erreur = _T('asso:erreur_sgbdr');
    }

    return array($id_destination, $erreur);
}

?>