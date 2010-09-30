<?php
// Attention l'ordre est important : migrer 1) les rubriques / categories 2) les articles 3)  les sites puis appliquer purifier_spip()


function dot_purifier_spip(){
	sql_updateq('spip_rubriques',array('descriptif'=>''));	
}





function dot_migrer_blog($blog_id){
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
	dot_purifier_spip();
	spip_log("#### fin migration $blog_id ####",'dot2');
		
}

?>