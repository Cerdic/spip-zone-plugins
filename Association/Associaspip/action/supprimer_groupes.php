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


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_groupes() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_groupe= $securiser_action();

	/* supprimer toutes les entrees de ce groupe dans la table de liaison */
	sql_delete('spip_asso_groupes_liaisons', "id_groupe=$id_groupe");

	sql_delete('spip_asso_groupes', "id_groupe=$id_groupe");	
}

?>
