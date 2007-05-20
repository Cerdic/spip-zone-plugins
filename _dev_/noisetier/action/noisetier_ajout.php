<?php

function action_noisetier_ajout_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	if ($arg=='ajout_texte') $id_noisette = noisetier_ajout_texte();
	if ($arg=='ajout_noisette') $id_noisette = noisetier_ajout_noisette();
}

function noisetier_ajout_texte() {
	include_spip('base/abstract_sql');
	include_spip('inc/filtres');
	$page = _request('page');
	$zone = _request('zone');
	$titre = addslashes(corriger_caracteres(_T('noisetier:titre_nouveau_texte')));
	$descriptif = addslashes(corriger_caracteres(_T('noisetier:descriptif_nouveau_texte')));
	$position = 1;
	$query = "SELECT MAX(position) AS positionmax FROM spip_noisettes WHERE zone='$zone'";
	$res = spip_query($query);
	if ($row = spip_fetch_array($res)) $position = $row['positionmax']+1;
	$id_noisette = spip_abstract_insert("spip_noisettes","(page,zone,position,titre,descriptif,type)","('$page','$zone','$position','$titre','$descriptif','texte')");
	return $id_noisette;
}

function noisetier_ajout_noisette() {
	include_spip('base/abstract_sql');
	include_spip('inc/filtres');
	global $infos_ajout_noisette;
	$infos_ajout_noisette = "";
	$page = _request('page');
	$zone = _request('zone');
	$url_noisette = _request('url_noisette');
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
	$position = 1;
	$query = "SELECT MAX(position) AS positionmax FROM spip_noisettes WHERE zone='$zone'";
	$res = spip_query($query);
	if ($row = spip_fetch_array($res)) $position = $row['positionmax']+1;
	preg_match('/\/noisettes\/([[:graph:]]+).htm/',$url_noisette,$matches);
	$fond = $matches[1];
	
	//Vérification des paramètres
	$params = $arbre['param'];
	$table_param = array();
	foreach ($params as $param) {
		$titre = addslashes(corriger_caracteres(spip_xml_aplatit($param['titre'])));
		$descriptif = addslashes(corriger_caracteres(spip_xml_aplatit($param['descriptif'])));
		$valeur = addslashes(corriger_caracteres(spip_xml_aplatit($param['valeur'])));
		if ($titre=='') {
			include_spip('inc/minipres');
			echo minipres(_T('noisetier:probleme_titre_param_sans_titre'),_T('noisetier:probleme_param_sans_titre'));
			exit;
		}
		if ($valeur=='') {
			include_spip('inc/minipres');
			echo minipres(_T('noisetier:probleme_titre_param_sans_valeur'),_T('noisetier:probleme_param_sans_valeur'));
			exit;
		}
		$table_param[] = "'$titre','$descriptif','$valeur'";
	}
	
	//Vérification des mots-clés
	$mots = $arbre['mot'];
	$table_mot = array();
	foreach ($mots as $mot) {
		$titre = addslashes(corriger_caracteres(spip_xml_aplatit($param['titre'])));
		$descriptif = addslashes(corriger_caracteres(spip_xml_aplatit($param['descriptif'])));
		$objet = addslashes(corriger_caracteres(spip_xml_aplatit($param['objet'])));
		if ($titre=='') {
			include_spip('inc/minipres');
			echo minipres(_T('noisetier:probleme_titre_mot_sans_titre'),_T('noisetier:probleme_mot_sans_titre'));
			exit;
		}
		if ($objet=='') {
			include_spip('inc/minipres');
			echo minipres(_T('noisetier:probleme_titre_mot_sans_objet'),_T('noisetier:probleme_mot_sans_objet'));
			exit;
		}
		$table_mot[] = "'$titre','$descriptif','$objet'";
	}
	
	// Insertion de la noisette
	$id_noisette = spip_abstract_insert("spip_noisettes","(page,zone,position,titre,descriptif,type,fond)","('$page','$zone','$position','$titre','$descriptif','noisette','$fond')");
	
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


	return $id_noisette;
}

function noisetier_reecrire_crochets ($texte) {
	// Transformation des <lien> en [ et des </lien> en ]
	$texte = preg_replace('`<lien>`','[',$texte);
	$texte = preg_replace('`</lien>`',']',$texte);
	// Transformation des <lg-**> en [**]
	$texte = preg_replace('`<lg-([[:alpha:]]{2})>`','[$1]',$texte);
	
	return $texte;
}

?>