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
	$id_asso = _request('id_asso');

	$modifs = array("commentaire"=> $commentaire,
					"validite"=> $validite,
					"categorie"=> $categorie,
					"statut_interne"=> $statut_interne,
					"nom_famille" => $nom_famille,
					"fonction" => $fonction,
					'email' => _request('email'),
					'adresse' => _request('adresse'),
					'code_postal' => _request('code_postal'),
					'ville' => _request('ville'),
					'telephone' => _request('telephone'),
					'mobile' => _request('mobile'),
				);

	/* pour ne pas ecraser les champs quand ils sont desactives */
	if ($GLOBALS['association_metas']['civilite']=="on") $modifs["sexe"] = $sexe;
	if ($GLOBALS['association_metas']['prenom']=="on") $modifs["prenom"] = $prenom;
	if ($GLOBALS['association_metas']['id_asso']=="on") $modifs["id_asso"] = $id_asso;

	sql_updateq('spip_asso_membres', $modifs, "id_auteur=$id_auteur");

	return (array($id_auteur,''));
}
?>
