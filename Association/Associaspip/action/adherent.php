<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James                     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_adherent() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	$categorie=$_POST['categorie'];
	$validite=$_POST['validite'];
	$commentaire=$_POST['commentaire'];
	$statut_interne=$_POST['statut_interne'];

	$id_asso = ($GLOBALS['association_metas']['indexation']=="id_asso") ? intval($_POST['id_asso']) : 0;

	adherent_update($id_auteur, $id_asso, $commentaire, $categorie, $statut_interne, $validite);
}

function adherent_update($id_auteur, $id_asso, $commentaire, $categorie, $statut_interne, $validite)
{
	include_spip('base/association');
	sql_updateq(_ASSOCIATION_AUTEURS_ELARGIS, 
				   array("id_asso"=> $id_asso,
					 "commentaire"=> $commentaire,
					 "validite"=> $validite,
					 "categorie"=> $categorie,
					 "statut_interne"=> $statut_interne),
				   "id_auteur=$id_auteur");
}
?>
