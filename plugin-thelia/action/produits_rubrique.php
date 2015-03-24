<?php
function produits_rubrique_update($id_rubrique){
	//supprimer les enregistrements de cet rubrique
	$query = "DELETE FROM spip_produits_rubriques WHERE id_rubrique=" . _q($id_rubrique);
	$result = spip_query($query);

	$query = "DELETE FROM spip_rubriquesthelia_rubriques WHERE id_rubrique=" . _q($id_rubrique);
	$result = spip_query($query);

	//ajouter les associations produits-rubriques de cet rubrique
	foreach ($_POST as $clef => $valeur){
		if (strpos($clef, "produit-")===0){
			$id_produit = substr($clef, 8);
			spip_query("INSERT INTO spip_produits_rubriques (id_rubrique,id_produit) VALUES (" . _q($id_rubrique) . "," . _q($id_produit) . ")");
		}
	}

	//ajouter les associations rubriquesthelia-rubriques de cet rubrique
	foreach ($_POST as $clef => $valeur){
		if (strpos($clef, "rubriquethelia-")===0){
			$id_rubriquethelia = substr($clef, 15);
			spip_query("INSERT INTO spip_rubriquesthelia_rubriques (id_rubrique,id_rubriquethelia) VALUES (" . _q($id_rubrique) . "," . _q($id_rubriquethelia) . ")");
		}
	}

	return array($id_rubrique);
}

function action_produits_rubrique(){

	global $auteur_session;
	$arg = _request('arg');
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = _request('redirect');

	if (!include_spip("inc/securiser_action"))
		include_spip("inc/actions");
	if (verifier_action_auteur("produits_rubrique-$arg", $hash, $id_auteur)==TRUE){
		$arg = explode("-", $arg);
		$id_rubrique = $arg[0];
		if (intval($id_rubrique) && autoriser('modifier', 'rubrique', $id_rubrique)){
			list($id_rubrique) = produits_rubrique_update($id_rubrique);
			//if ($redirect) $redirect = parametre_url($redirect,"id_rubrique",$id_rubrique);
		}
	}

	if ($redirect)
		redirige_par_entete(str_replace("&amp;", "&", urldecode($redirect)));

}

