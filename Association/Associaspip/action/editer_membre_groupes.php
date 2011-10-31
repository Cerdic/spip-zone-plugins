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

function action_editer_membre_groupes() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	
	$fonctions = _request('fonctions');
	$fonctions = (isset($fonctions)) ? $fonctions:array();
	
	$insert_data = array();
	foreach ($fonctions as $id_groupe => $fonction) {
		sql_updateq('spip_asso_groupes_liaisons', array('fonction' => $fonction), 'id_groupe='.$id_groupe.' AND id_auteur='.$id_auteur);
	}
	
	return;
}
?>
