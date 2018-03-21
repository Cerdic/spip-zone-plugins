<?php
/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2016                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function formulaires_illustrer_document_automatique_charger_dist($id_document) {
	include_spip('inc/documents');
	$valeurs = sql_fetsel('id_document,mode,id_vignette,extension,media', 'spip_documents',
		'id_document=' . intval($id_document));
	if (!$valeurs /*OR in_array($valeurs['extension'],array('jpg','gif','png'))*/) {
		return array('editable' => false, 'id' => $id_document);
	}

	$valeurs['id'] = $id_document;
	$valeurs['_hidden'] = "<input name='id_document' value='$id_document' type='hidden' />";
	$valeurs['mode'] = 'vignette'; // pour les id dans le dom
	$vignette = sql_fetsel('fichier,largeur,hauteur,id_document', 'spip_documents',
		'id_document=' . $valeurs['id_vignette']);
	$valeurs['vignette'] = get_spip_doc($vignette['fichier']);
	$valeurs['hauteur'] = $vignette['hauteur'];
	$valeurs['largeur'] = $vignette['largeur'];
	$valeurs['id_vignette'] = $vignette['id_document'];
	$valeurs['_pipeline'] = array('editer_contenu_objet', array('type' => 'illustrer_document_automatique', 'id' => $id_document));

//print ("fc charger : id_document ".$valeurs['id']."ou".$id_document." id_vignette : ".$valeurs['id_vignette']."<br><br>");

	return $valeurs;
}

function formulaires_illustrer_document_automatique_verifier_dist($id_document) {
	$erreurs = array();
	if (_request('supprimer')) {

	} else {

// retour ==> effacer ?
/*	$id_vignette = sql_getfetsel('id_vignette', 'spip_documents', 'id_document=' . intval($id_document));
	$verifier = charger_fonction('verifier', 'formulaires/joindre_document');
		$erreurs = $verifier($id_vignette, 0, '', 'vignette');
		*/
// FIN retour ==> effacer ?
	}

	return $erreurs;
}

function formulaires_illustrer_document_automatique_traiter_dist($id_document) {

/*
 * Ajouter un document (au format $_FILES)
 *
 * https://code.spip.net/@ajouter_un_document
 *
 * @param int $id_document
 *   document a remplacer, ou pour une vignette, l'id_document de maman
 *   0 ou 'new' pour une insertion
 * @param array $file
 *   proprietes au format $_FILE etendu :
 *     string tmp_name : source sur le serveur
 *     string name : nom du fichier envoye
 *     bool titrer : donner ou non un titre a partir du nom du fichier
 *     bool distant : pour utiliser une source distante sur internet
 *     string mode : vignette|image|documents|choix
 * @param string $objet
 *   objet auquel associer le document
 * @param int $id_objet
 *   id_objet
 * @param string $mode
 *   mode par defaut si pas precise pour le document
 * @return array|bool|int|mixed|string|unknown
 * 	 si int : l'id_document ajouté (opération réussie)
 *   si string : une erreur s'est produit, la chaine est le message d'erreur
 *  
 */
//action_ajouter_un_document_dist($id_document, $file, $objet, $id_objet, $mode) 






//refuser_traiter_formulaire_ajax();



//	$id_vignette = sql_getfetsel('id_vignette', 'spip_documents', 'id_document=' . intval($id_document));



/*  -------------vignettes pdf auto--------------- */

$file_temp=vignette_pdf2jpg($id_document);


/* Propriétes au format $_FILE étendu : https://zone.spip.org/trac/spip-zone/browser/_core_/plugins/medias/action/ajouter_documents.php#L71
	 *   - string tmp_name : source sur le serveur
	 *   - string name : nom du fichier envoye
	 *   - bool titrer : donner ou non un titre a partir du nom du fichier
	 *   - bool distant : pour utiliser une source distante sur internet
	 *   - string mode : vignette|image|documents|choix*/

$name=str_replace("../IMG/tmp/","",$file_temp); // un peu crad

// verifeir sir pas deja une image avec ce nom
$present = sql_getfetsel('fichier', 'spip_documents', 'fichier LIKE "%'.$name.'"');
//echo "$present";
// =>> A FINIR


//print("file_temp".$file_temp."<br>");
//print("name".$name."<br>");


$file[]=array("tmp_name"=>"$file_temp",
					"name"=>"$name",
					"titrer"=>"false",
					"mode"=>"vignette");

//print_r($file);


/*1 - AJOUTER VIGNETTE*/
$ajouter_documents = charger_fonction('ajouter_documents', 'action');
$ajoute = $ajouter_documents("new", $file, 'document', $id_document,'mode'); //
//print_r($ajoute);
/*  -------------FIN vignettes pdf auto--------------- */


/*2 - ENREGISTRE l'id_vignette dans le pdf source */
		if (is_numeric(reset($ajoute))
			and $id_vignette = reset($ajoute)
		) {
			include_spip('action/editer_document');
			document_modifier($id_document, array("id_vignette" => $id_vignette, 'mode' => 'document'));
			$res['message_ok'] = _T('medias:document_installe_succes');
//					$res['redirect'] = "?exec=document_edit&id_document=".$id_document;
			
		} else {
			$res['message_erreur'] = reset($ajoute);
		}
	

	return $res;

}
