<?php

// Fichier qui sert à injecter les entrées minimales dans la base de données pour un bon fonctionnement si elles ne sont pas présentes

// On définit ici les groupes et mots-clés utiles
$nom_groupe_racine='Compétences';

// Nom de la table de groupes de mots-clés
$Table_groupes='spip_groupes_mots';

// Nom de la table de groupes de mots-clés
$Table_mots='spip_mots';

// Définition des sous-groupes (domaines du CRCN)
$sous_groupes=array(
	"1.&nbsp;Information et données",
	"2.&nbsp;Communication et collaboration",
	"3.&nbsp;Création de contenus",
	"4.&nbsp;Protection et sécurité",
	"5.&nbsp;Environnement numérique"
);
// Définition des noms des fichiers images attachés à chaque domaine (logos des domaines)
$icones=array(
	"1.&nbsp;Information et données" => 'domaine1.png',
	"2.&nbsp;Communication et collaboration" => 'domaine2.png',
	"3.&nbsp;Création de contenus" => 'domaine3.png',
	"4.&nbsp;Protection et sécurité" => 'domaine4.png',
	"5.&nbsp;Environnement numérique" => 'domaine5.png'
);
// Définitions des mots-clés (compétences du CRCN)
$mots_cles = array(
	"1.&nbsp;Information et données" => array (
		"1. Mener une recherche et une veille d’informations",
		"2. Gérer des données",
		"3. Traiter des données"
	),
	"2.&nbsp;Communication et collaboration" => array(
		"1. Interagir",
		"2. Partager et publier",
		"3. Collaborer",
		"4. S'insérer dans le monde numérique"
	),
	"3.&nbsp;Création de contenus" => array(
		"1. Développer des documents textuels",
		"2. Développer des documents multimédia",
		"3. Adapter les documents à leur finalité",
		"4. Programmer"
	),
	"4.&nbsp;Protection et sécurité" => array(
		"1. Sécuriser l’environnement numérique",
		"2. Protéger les données personnelles et la vie privée",
		"3. Protéger la santé, le bien-être et l’environnement"
	),
	"5.&nbsp;Environnement numérique" => array(
		"1. Résoudre des problèmes techniques",
		"2. Évoluer dans un environnement numérique"
	),
);

// Tester la présence du groupe racine, si non présent, le créer
$test_groupe_racine_sql=sql_select('id_groupe',$Table_groupes,"titre='$nom_groupe_racine'");
$tab_test_groupe_racine=sql_fetch($test_groupe_racine_sql);
$test_groupe_racine=$tab_test_groupe_racine['id_groupe'];
if (!$test_groupe_racine) {
	sql_insertq($Table_groupes,array(
		'titre'=>$nom_groupe_racine,
		'descriptif'=>'',
		'texte'=>'',
		'unseul'=>'non',
		'obligatoire'=>'non',
		'tables_liees'=>'articles',
		'minirezo'=>'oui',
		'comite'=>'oui',
		'forum'=>'non',
		'id_parent'=>'0'
	));
	$test_groupe_racine_sql2=sql_select('id_groupe',$Table_groupes,"titre='$nom_groupe_racine'");
	$tab_test_groupe_racine2=sql_fetch($test_groupe_racine_sql2);
	$id_groupe_racine=$tab_test_groupe_racine2['id_groupe'];
	sql_updateq($Table_groupes, array('id_groupe_racine' => $id_groupe_racine), 'id_groupe=' . $id_groupe_racine);
}
else {$id_groupe_racine=intval($test_groupe_racine);}

// Tester la présence des sous-groupes, si non présents : les créer ainsi que leurs mots-clés

foreach ($sous_groupes as $titre_domaine){
	// On teste la présence du sous-domaine, si non présent, on le crée ainsi que ses mots-clés
	$test_sous_domaine_sql=sql_select('id_groupe',$Table_groupes,"titre='$titre_domaine' AND id_parent=$id_groupe_racine");
	$test_sous_domaine_tab=sql_fetch($test_sous_domaine_sql);
	$test_sous_domaine=$test_sous_domaine_tab['id_groupe'];
	if (!$test_sous_domaine) {
		// Créer le sous-groupe
		sql_insertq($Table_groupes,array(
			'titre'=>$titre_domaine,
			'descriptif'=>'',
			'texte'=>'',
			'unseul'=>'non',
			'obligatoire'=>'non',
			'tables_liees'=>'articles',
			'minirezo'=>'oui',
			'comite'=>'oui',
			'forum'=>'non',
			'id_parent'=>$id_groupe_racine,
			'id_parent'=>$id_groupe_racine
		));
		// Créer les mots-clés du sous-groupe
		$test_sous_domaine_sql2=sql_select('id_groupe',$Table_groupes,"titre='$titre_domaine' AND id_parent=$id_groupe_racine");
		$test_sous_domaine_tab2=sql_fetch($test_sous_domaine_sql2);
		$num_ss_groupe=intval($test_sous_domaine_tab2['id_groupe']);
		foreach ($mots_cles[$titre_domaine] as $mots) {
			sql_insertq($Table_mots,array(
				'titre'=>$mots,
				'descriptif'=>'',
				'texte'=>'',
				'id_groupe'=>$num_ss_groupe,
				'type'=>$titre_domaine,
				'id_groupe_racine'=>$id_groupe_racine
			));
		}
		// Attacher l'icône au sous-groupe
		$nom_fichier=$icones[$titre_domaine];
		copy(_DIR_PLUGIN_CRCN.'img/'.$nom_fichier,_DIR_IMG.'groupeon'.$num_ss_groupe.'.png');
	}

}

?>