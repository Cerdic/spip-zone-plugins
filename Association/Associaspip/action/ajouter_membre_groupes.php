<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_ajouter_membre_groupes() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	
	$id_groupes = _request('id_groupes');
	
	$insert_data = array();
	if (is_array($id_groupes)) {
		foreach ($id_groupes as $id_groupe) {
			$insert_data[]=array('id_groupe' => $id_groupe, 'id_auteur' => $id_auteur);
		}
	}

	if (count($insert_data)) {
		sql_insertq_multi('spip_asso_groupes_liaisons', $insert_data);
	}

	return;
}
?>
