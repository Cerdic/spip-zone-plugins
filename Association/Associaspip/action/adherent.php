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

function action_adherent() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	$categorie=$_POST['categorie'];
	$validite=$_POST['validite'];
	$commentaire=$_POST['commentaire'];
	$statut_interne=$_POST['statut_interne'];

	adherent_update($id_auteur, $commentaire, $categorie, $statut_interne, $validite);
}

function adherent_update($id_auteur, $commentaire, $categorie, $statut_interne, $validite)
{
	include_spip('base/association');
	sql_updateq('spip_asso_membres', 
				   array("commentaire"=> $commentaire,
					 "validite"=> $validite,
					 "categorie"=> $categorie,
					 "statut_interne"=> $statut_interne),
				   "id_auteur=$id_auteur");
}
?>
