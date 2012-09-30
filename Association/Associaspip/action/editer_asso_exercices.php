<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 * @copyright Copyright (c) 201108 Marcel Bolla
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
    return;

function action_editer_asso_exercices_dist()
{
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_exercice = $securiser_action();
    $erreur = '';
    $champs = array(
	'intitule' => _request('intitule'),
	'commentaire' => _request('commentaire'),
	'debut' => association_recuperer_date('debut'),
	'fin' => association_recuperer_date('fin'),
    );
    include_spip('base/association');
    if ($id_exercice) { // modification
	sql_updateq('spip_asso_exercices', $champs, "id_exercice=$id_exercice");
    } else { // ajout
	$id_exercice = sql_insertq('spip_asso_exercices', $champs);
	if (!$id_exercice)
	    $erreur = _T('asso:erreur_sgbdr');
    }

    return array($id_exercice, $erreur);
}

?>