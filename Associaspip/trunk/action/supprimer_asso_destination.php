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

function action_supprimer_asso_destination_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_destination = $securiser_action();
	sql_delete('spip_asso_destination', "id_destination=$id_destination"); // supprimer la destination comptable
	$operations_affectees = sql_allfetsel('id_compte', 'spip_asso_destination_op', "id_destination=$id_destination"); //!\ ecriture raccourcie en esperant ne pas en recuperer une flopee qui deborderait de la memorie...
	if ( count($operations_affectees) ) { // tsss,,, c'etait utilise !
		sql_delete('spip_asso_destination_op', "id_destination=$id_destination"); // supprimer les ventilations attachees
		if (intval($GLOBALS['association_metas']['destinations'])>1) { // pour bien faire, il aurait fallu virer les autres ventilations (memes: id_compte, date, recette|depense 0) pour reventiler l'operation... : sql_delete('spip_asso_destination_op', sql_in('id_compte',$operations_affectees));
			sql_update('spip_asso_comptes', array('vu'=>0), sql_in('id_compte',$operations_affectees) ); // ...mais on laisse l'utilisateur gerer (en re-editant), apres l'avoir cependant on signale... //!\ a parfaire : s'assurer quand meme qu'il y a encore des ventilations (memes: id_compte, date, recette|depense 0) non ?
		}
	}
}

?>