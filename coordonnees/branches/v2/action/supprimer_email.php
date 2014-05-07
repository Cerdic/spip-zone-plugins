<?php
/**
 * Action : supprimer un email
 *
 * @plugin     Coordonnees
 * @copyright  2014
 * @author     Marcimat / Ateliers CYM
 * @licence    GNU/GPL
 * @package    SPIP\Coordonnees\Action
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Supprimer un email
 * arg : id_email
 * exemple : #URL_ACTION_AUTEUR{supprimer_email, #ID_EMAIL, #SELF}
 */

function action_supprimer_email_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_email = intval($arg);

	if ($id_email>0 AND autoriser('supprimer', 'email', $id_email)) {
		sql_delete('spip_emails', "id_email=" . sql_quote($id_email));
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_email/$id_email'");
	}
}

?>
