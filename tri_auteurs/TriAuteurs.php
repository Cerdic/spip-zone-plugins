<?php

define('_DIR_PLUGIN_TRI_AUTEURS',(_DIR_PLUGINS . basename(dirname(__FILE__))));


function TriAuteurs_ajouter_boite_gauche($arguments) {  
  if($arguments['args']['exec'] == 'articles') {
	include('tri_auteurs_utils.php');
	$table_pref = 'spip';
	if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
	if(TriAuteurs_verifier_admin() OR TriAuteurs_verifier_admin_restreint($arguments['args']['id_rubrique']) 
	   OR TriAuteurs_verifier_auteur($arguments['args']['id_article'])) {
	  
	  $res = spip_query("SHOW COLUMNS FROM `".$table_pref."_auteurs_articles` LIKE 'rang'");
	  if(!spip_fetch_array($res)) {
		spip_query("ALTER TABLE `".$table_pref."_auteurs_articles` ADD `rang` BIGINT NOT NULL DEFAULT 0;");
	  }
	  spip_free_result($res);
	  
	  $arguments['data'] .= TriAuteurs_boite_tri_auteurs($arguments['args']['id_article'],$arguments['args']['id_rubrique']);
	}
  }
  return $arguments;
}

function TriAuteurs_boite_tri_auteurs($id_article,$id_rubrique) {
  global $spip_lang_left;
  
  $from = array('spip_auteurs_articles as lien','spip_auteurs as auteurs');
  $select = array('lien.rang','lien.id_auteur','auteurs.nom');
  $where = array('lien.id_auteur=auteurs.id_auteur',"lien.id_article=$id_article");
  $order = 'lien.rang';

  $rez = spip_abstract_select($select,$from,$where,$order);
  $to_ret = '';
 
  if(spip_abstract_count($rez) > 1) {
	$to_ret .= '		<script type="text/javascript" src="'._DIR_PLUGIN_TRI_AUTEURS.'/javascript/prototype.js"></script>';
	$to_ret .= '		<script type="text/javascript" src="'._DIR_PLUGIN_TRI_AUTEURS.'/javascript/scriptaculous.js"></script>';
	$to_ret .= '	<script type="text/javascript">';
	$to_ret .= "function TriAuteurs_initialiseSort() {
	Sortable.create('liste_tri_auteurs',{onUpdate:updateTriAuteur});
  }";
	$to_ret .= "function updateTriAuteur() {
           var url = '".generer_url_ecrire('tri_auteurs')."';
           var pars = Sortable.serialize('liste_tri_auteurs',{name:'o'})+'&id_article=$id_article';
           new Ajax.Request(url,{method:'post',parameters:pars});
  }";
	$to_ret .= "Event.observe(window, 'load', TriAuteurs_initialiseSort, false);";
	$to_ret .= ' </script>';
	
	
	
	$to_ret .= '<div>&nbsp;</div>';
	$to_ret .= '<div class="bandeau_rubriques" style="z-index: 1;">';
	$to_ret .= "<div style='position: relative;'>";
	$to_ret .= "<div style='position: absolute; top: -12px; $spip_lang_left: 3px;'>
	<img src='"._DIR_PLUGIN_TRI_MOTS."/img/updown.png'/></div>";
	$to_ret .= "<div style='background-color: white; color: black; padding: 3px; padding-$spip_lang_left: 30px; border-bottom: 1px solid #444444;' class='verdana2'><b>"._T('triauteurs:ordonner_auteurs')."</b></div>";
	$to_ret .= "</div>";
	
	$to_ret .= '<div class="plan-articles">';
	$to_ret .= '<ol id="liste_tri_auteurs">';
	while($row = spip_abstract_fetch($rez)) {
	  $to_ret .= '<li id="auteur_'.$row['id_auteur'].'">';
	  $to_ret .= $row['nom'].'</li>';
	}
	$to_ret .= '</ol>';
	$to_ret .= '</div></div>';
  }
  return $to_ret;
}


function TriAuteurs_ajouter_styles($texte) {
  $texte.= '<link rel="stylesheet" type="text/css" href="' ._DIR_PLUGIN_TRI_AUTEURS. '/tri_auteurs.css" />' . "\n";
  return $texte;
}

?>
