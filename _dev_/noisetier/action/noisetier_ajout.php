<?php

function action_noisetier_ajout_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	$page = _request('page');
	$zone = _request('zone');
	if ($arg=='ajout_texte') $id_noisette = noisetier_ajout_texte($page, $zone);
	if ($arg=='ajout_noisette') {
		$url_noisette = _request('url_noisette');
		$id_noisette = noisetier_ajout_noisette($page, $zone, $url_noisette);
	}
	
	$redirect = str_replace("&amp;","&",urldecode(_request('redirect')));
	if ($redirect==NULL) $redirect="";
	if ($redirect) $redirect = parametre_url($redirect,"noisette_visible",$id_noisette);
	if ($redirect) $redirect = ancre_url($redirect,"noisette-$id_noisette");
	if ($redirect)
		redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
}

function noisetier_ajout_texte($page, $zone, $exclue='') {
	include_spip('base/abstract_sql');
	include_spip('inc/filtres');
	$titre = addslashes(corriger_caracteres(_T('noisetier:titre_nouveau_texte')));
	$descriptif = addslashes(corriger_caracteres(_T('noisetier:descriptif_nouveau_texte')));
	$position = 1;
	$query = "SELECT MAX(position) AS positionmax FROM spip_noisettes WHERE zone='$zone'";
	$res = spip_query($query);
	if ($row = spip_fetch_array($res)) $position = $row['positionmax']+1;
	$id_noisette = spip_abstract_insert("spip_noisettes","(page,exclue,zone,position,titre,descriptif,type)","('$page','$exclue','$zone','$position','$titre','$descriptif','texte')");
	return $id_noisette;
}

function noisetier_ajout_noisette($page, $zone, $url_noisette, $exclue='') {
	include_spip('base/abstract_sql');
	include_spip('inc/filtres');
	// Ajout de plugins au début de l'url car le script action est effectué à la racine du site et l'url contient alors un ../ de trop au début.
	$contenu_noisette = file_get_contents ('plugins/'.$url_noisette);
	if (!preg_match('`\[noisetier\(#REM\)([^]]*)\]`',$contenu_noisette,$matches)) {
		include_spip('inc/minipres');
		echo minipres(_T('noisetier:probleme_titre_noisette_sans_xml'),_T('noisetier:probleme_noisette_sans_xml'));
		exit;
		}
	$xml_noisette = $matches[1];
	$xml_noisette = noisetier_reecrire_crochets($xml_noisette);
	include_spip('inc/xml');
	$arbre = spip_xml_parse($xml_noisette);
	$titre = addslashes(corriger_caracteres(spip_xml_aplatit($arbre['titre'])));
	if ($titre=='') {
		include_spip('inc/minipres');
		echo minipres(_T('noisetier:probleme_titre_noisette_sans_titre'),_T('noisetier:probleme_noisette_sans_titre'));
		exit;
		}
	$descriptif = addslashes(corriger_caracteres(spip_xml_aplatit($arbre['description'])));
	$auteur = addslashes(corriger_caracteres(spip_xml_aplatit($arbre['auteur'])));
	$lien = addslashes(corriger_caracteres(spip_xml_aplatit($arbre['lien'])));
	$version = addslashes(corriger_caracteres(spip_xml_aplatit($arbre['version'])));
	$position = 1;
	$query = "SELECT MAX(position) AS positionmax FROM spip_noisettes WHERE zone='$zone'";
	$res = spip_query($query);
	if ($row = spip_fetch_array($res)) $position = $row['positionmax']+1;
	preg_match('/\/noisettes\/([[:graph:]]+).htm/',$url_noisette,$matches);
	$fond = $matches[1];
	
	// On fait les vérifications ici car la noisette n'est pas installée si problème.
	// Mais l'installation des mots et des paramètres se fait après l'installation des noisettes
	// car on a besoin de l'id_noisette.
	//Vérification des paramètres
	$params = $arbre['param'];
	$table_param = array();
	foreach ($params as $param) {
		$titre_param = addslashes(corriger_caracteres(spip_xml_aplatit($param['titre'])));
		$descriptif_param = addslashes(corriger_caracteres(spip_xml_aplatit($param['descriptif'])));
		$valeur = addslashes(corriger_caracteres(spip_xml_aplatit($param['valeur'])));
		if ($titre_param=='') {
			include_spip('inc/minipres');
			echo minipres(_T('noisetier:probleme_titre_param_sans_titre'),_T('noisetier:probleme_param_sans_titre'));
			exit;
		}
		if ($valeur=='') {
			include_spip('inc/minipres');
			echo minipres(_T('noisetier:probleme_titre_param_sans_valeur'),_T('noisetier:probleme_param_sans_valeur'));
			exit;
		}
		$table_param[] = "'$titre_param','$descriptif_param','$valeur'";
	}
	
	//Vérification des mots-clés
	$mots = $arbre['mot'];
	$table_mot = array();
	$types_mots = spip_abstract_showtable("spip_groupes_mots", '', true);
	$champs_interdits = array ('id_groupe','titre','descriptif','texte','unseul','obligatoire','minirezo','comite','maj');
	foreach ($mots as $mot) {
		$titre_mot = addslashes(corriger_caracteres(spip_xml_aplatit($mot['titre'])));
		$descriptif_mot = addslashes(corriger_caracteres(spip_xml_aplatit($mot['descriptif'])));
		$objet = addslashes(corriger_caracteres(spip_xml_aplatit($mot['objet'])));
		if ($titre_mot=='') {
			include_spip('inc/minipres');
			echo minipres(_T('noisetier:probleme_titre_mot_sans_titre'),_T('noisetier:probleme_mot_sans_titre'));
			exit;
		}
		if ($objet=='') {
			include_spip('inc/minipres');
			echo minipres(_T('noisetier:probleme_titre_mot_sans_objet'),_T('noisetier:probleme_mot_sans_objet'));
			exit;
		}
		$objets = explode(',',$objet);
		foreach ($objets as $objet) {
			//Vérification qu'il s'agit d'un type d'objet valable
			if ($types_mots['field'][$objet]=='' OR in_array($objet,$champs_interdits)){
				include_spip('inc/minipres');
				echo minipres(_T('noisetier:probleme_titre_mot_objet_incorrect'),_T('noisetier:probleme_mot_objet_incorrect'));
				exit;
			}
			// Si un mot porte sur plusieurs types d'objets, alors on le duplique
			$table_mot[] = array('titre'=>$titre_mot,'descriptif'=>$descriptif_mot,'objet'=>$objet);
		}
	}
	
	// Insertion de la noisette
	$id_noisette = spip_abstract_insert("spip_noisettes","(page,exclue,zone,position,titre,descriptif,auteur,lien,version,type,fond)","('$page','$exclue','$zone','$position','$titre','$descriptif','$auteur','$lien','$version','noisette','$fond')");
	
	// Insertion des variables d'environnement
	$envs = $arbre['env'];
	foreach ($envs as $env) {
		$env = corriger_caracteres($env);
		spip_abstract_insert("spip_params_noisettes","(id_noisette,type,titre)","('$id_noisette','env','$env')");
	}
	
	// Insertion des paramètres
	foreach ($table_param as $champs)
		spip_abstract_insert("spip_params_noisettes","(id_noisette,type,titre,descriptif,valeur)","('$id_noisette','param',$champs)");
	
	// Insertion des entrées de la noisette dans le head
	$heads = $arbre['head'];
	foreach ($heads as $head) {
		$head = addslashes(corriger_caracteres(spip_xml_aplatit($head)));
		spip_abstract_insert("spip_params_noisettes","(id_noisette,type,descriptif)","('$id_noisette','head','$head')");
	}
	
	// Insertion des mots-clés
	foreach ($table_mot as $mot)
		noisetier_ajout_mot($mot, $id_noisette);


	return $id_noisette;
}

function noisetier_reecrire_crochets ($texte) {
	// Transformation des <lien> en [ et des </lien> en ]
	$texte = preg_replace('`<url>`','[',$texte);
	$texte = preg_replace('`</url>`',']',$texte);
	// Transformation des <lg-**> en [**]
	$texte = preg_replace('`<lg-([[:alpha:]]{2})>`','[$1]',$texte);
	
	return $texte;
}

function noisetier_ajout_mot ($mot, $id_noisette) {
	// On récupère l'id_groupe et le type de mot. Si le groupe n'existe pas, on le créé
	$objet = $mot['objet'];
	$type = 'noisetier-'.$objet;
	// On recherche le groupe de mots-clés
	$res = spip_query("SELECT id_groupe FROM spip_groupes_mots WHERE titre='$type'");
	if ($row=spip_fetch_array($res))
		$id_groupe = $row['id_groupe'];
	else {
		//Cela signifie que le groupe de mots n'existe pas et qu'il faut le créer
		$descriptif = addslashes(corriger_caracteres(_T('noisetier:descriptif_groupe_mot')));
		$id_groupe = spip_abstract_insert("spip_groupes_mots","(titre,descriptif,$objet,minirezo)","('$type','$descriptif','oui','oui')");
	}
	// On vérifie si le mot-clé existe déjà dans la base, sinon on l'installe
	$descriptif = $mot['descriptif'];
	$titre = $mot['titre'];
	$res = spip_query("SELECT id_mot FROM spip_mots WHERE type='$type' AND titre='$titre'");
	if ($row=spip_fetch_array($res))
		$id_mot = $row['id_mot'];
	else
		//Cela signifie que le mot n'existe pas et qu'il faut le créer
		$id_mot = spip_abstract_insert("spip_mots","(titre,descriptif,id_groupe,type)","('$titre','$descriptif','$id_groupe','$type')");
	// On ajoute l'information dans la table spip_params_noisettes
	spip_abstract_insert("spip_params_noisettes","(titre,id_noisette,id_mot,type)","('$titre','$id_noisette','$id_mot','mot')");

}

?>