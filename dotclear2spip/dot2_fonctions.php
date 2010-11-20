<?php
// Attention l'ordre est important : migrer 1) les rubriques / categories 2) les articles 3)  les sites puis appliquer purifier_spip()


function dot_purifier_spip(){
	sql_updateq('spip_rubriques',array('descriptif'=>''));	
	sql_updateq('spip_articles',array('PS'=>''));
}

function generer_htacess($id,$url,$type){
	defined('_DIR_SITE') ? $chemin = _DIR_SITE._NOM_TEMPORAIRES_ACCESSIBLES."htacesss.txt" : $chemin = _DIR_RACINE._NOM_TEMPORAIRES_ACCESSIBLES."htaccesss.txt";
	var_dump($chemin);
	$contenu = '';
	lire_fichier($chemin,$contenu);
	ecrire_fichier($chemin,$contenu."RedirectPermanent \"/$url\" http://exemple.tld/spip.php?$type$id\n");
}




function dot_migrer_blog($blog_id){
	include_spip('sale_fonctions');
	include_spip('migrer/dot_category');
	include_spip('migrer/dot_comments');
	include_spip('migrer/dot_contents');
	include_spip('migrer/dot_medias');
	include_spip('migrer/dot_sites');
	include_spip('migrer/dot_users');
	$crud = charger_fonction('crud','action');
	spip_log("#### Début migration ####",'dot2');
	$rubrique_defaut = $crud('create','rubriques','',array('titre'=>_T('Rubrique d\'import DC')));
	$groupe_defaut=sql_getfetsel('id_groupe','spip_groupes_mots','`titre`='.sql_quote(_L('tags de dotclear')));
	$rubrique_defaut = $rubrique_default['result']['id'];
	dot2_migrer_rubriques($blog_id);
	dot2_migrer_articles($blog_id,$rubrique_defaut,$groupe_defaut);
	dot2_migrer_sites($blog_id,$rubrique_defaut);
	remplacer_liens_internes_articles();
	dot_purifier_spip();
	spip_log("#### fin migration $blog_id ####",'dot2');
		
}

?>