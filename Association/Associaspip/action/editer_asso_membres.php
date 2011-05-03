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

function action_editer_asso_membres() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();

	include_spip('base/association');

	$categorie = _request('categorie');
	$validite = _request('validite');
	$commentaire = _request('commentaire');
	$statut_interne = _request('statut_interne');
	$sexe = _request('sexe');
	$nom_famille = _request('nom_famille');
	$prenom = _request('prenom');
	$fonction = _request('fonction');

	sql_updateq('spip_asso_membres', 
				   array("commentaire"=> $commentaire,
					 "validite"=> $validite,
					 "categorie"=> $categorie,
					 "statut_interne"=> $statut_interne,
					 "sexe" => $sexe,
					 "nom_famille" => $nom_famille,
					 "prenom" => $prenom,
					 "fonction" => $fonction),
				   "id_auteur=$id_auteur");

	return (array($id_auteur,''));
}
?>
