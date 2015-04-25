<?php

function balise_LOGO_SPIP($p) {
	$p->code="filtrer('balise_img', find_in_path('spip.png'), _T('le_alt'), 'spip_logo')";
	return $p;
}

function traiter_logo_site_spip($logo_site_spip) {	
	return trim(recuperer_fond('logo/site_spip', array('logo_site_spip' => $logo_site_spip)));
}

function document_vu($id_objet,$objet,$id_document){
	include_spip('action/editer_liens');
        $objets_lies=array($objet=>$id_objet);
        $objets_source=array('document'=>$id_document);
        $qualif = array('vu'=>'oui');
        objet_associer($objets_source, $objets_lies, $qualif);
}

function logo_auto_declarer_tables_interfaces($interfaces) {
	$interfaces["table_des_traitements"]['LOGO_ARTICLE'][]= 'traiter_logo_article(%s, $Pile[$SP][\'id_article\'])';
	$interfaces["table_des_traitements"]['LOGO_ARTICLE_RUBRIQUE'][]= 'traiter_logo_article(%s, $Pile[$SP][\'id_article\'])';
	$interfaces["table_des_traitements"]['LOGO_RUBRIQUE'][]= 'traiter_logo_rubrique(%s, $Pile[$SP][\'id_rubrique\'])';
	return $interfaces;
}

function traiter_logo_rubrique($logo_rubrique, $id_rubrique) {
	return trim(recuperer_fond('logo/rubrique', array('logo_rubrique' => $logo_rubrique, 'id_rubrique' => $id_rubrique)));
}

function traiter_logo_article($logo_article, $id_article) {	
	return trim(recuperer_fond('logo/article', array('logo_article' => $logo_article, 'id_article' => $id_article)));
}

function traiter_logo_breve($logo_breve, $id_breve) {	
	return trim(recuperer_fond('logo/breve', array('logo_breve' => $logo_breve, 'id_breve' => $id_breve)));
}