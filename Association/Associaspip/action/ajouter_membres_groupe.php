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

/* cette action est appelée soit la page d'edition d'un groupe et dans ce cas on recupere l'id_groupe avec securiser_action */
/* soit depuis la page action_adherents et on a potentiellement plusieurs id_groupe a recuperer dans un tableau */
function action_ajouter_membres_groupe() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_groupe = $securiser_action();
	
	$id_auteurs = _request('id_auteurs');
	$id_auteurs = (isset($id_auteurs)) ? $id_auteurs:array();
	
	$id_groupes = _request('id_groupes');
	
	/* si on a un tableau d'id_groupes, on recupere dedans les id_groupes */
	if (is_array($id_groupes)) {
		foreach ($id_groupes as $id_groupe) {
			insert_membres($id_groupe, $id_auteurs); 
		}
	} else { /* pas de tableau, l'id_groupe est celui donné par securiser_action */
		insert_membres($id_groupe, $id_auteurs);
	}
	return;
}

function insert_membres($id_groupe, $id_auteurs)
{
	// l'interface d'ajout de membres depuis la liste des adherents permet d'ajouter a un groupe des membres qui en font deja partie
	// il faut donc filter les ajouts pour en exclure ceux qui y sont deja sinon c'est erreur SQL et les ajouts ne se font pas
	$membres = array();
	$query = sql_select('id_auteur', 'spip_asso_groupes_liaisons', 'id_groupe='.$id_groupe);
	while ($row = sql_fetch($query)) {
		$membres[] = $row['id_auteur'];
	}

	$insert_data = array();
	foreach ($id_auteurs as $id_auteur) {
		if (!in_array($id_auteur, $membres)){
			$insert_data[]=array('id_groupe' => $id_groupe, 'id_auteur' => $id_auteur);
		}
	}

	if (count($insert_data)) {
		sql_insertq_multi('spip_asso_groupes_liaisons', $insert_data);
	}
}
?>
