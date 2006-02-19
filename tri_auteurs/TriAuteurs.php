<?php

define('_DIR_PLUGIN_TRI_AUTEURS',(_DIR_PLUGINS . basename(dirname(__FILE__))));

function TriAuteurs_ajouter_boite_gauche($arguments) {

  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];

  $res = spip_query("SHOW COLUMNS FROM `".$table_pref."_auteurs_articles` LIKE 'rang'");
  if(!spip_fetch_array($res)) {
	spip_query("ALTER TABLE `".$table_pref."_auteurs_articles` ADD `rang` BIGINT NOT NULL DEFAULT 0;");
  }
  spip_free_result($res);


  if($arguments['args']['exec'] == 'articles') {
	$arguments['data'] .= TriAuteurs_boite_tri_auteurs($arguments['args']['id_article'],$arguments['args']['id_rubrique']);
	}
  return $arguments;
}

function TriAuteurs_boite_tri_auteurs($id_article,$id_rubrique) {
  global $spip_lang_left;
  include_ecrire('inc_abstract_sql');
  $to_ret .= '		<script type="text/javascript" src="'._DIR_PLUGIN_TRI_AUTEURS.'/javascript/prototype.js"></script>';
  $to_ret .= '		<script type="text/javascript" src="'._DIR_PLUGIN_TRI_AUTEURS.'/javascript/scriptaculous.js"></script>';
  $to_ret .= '	<script type="text/javascript">';
  $to_ret .= "function TriAuteurs_initialiseSort() {
	Sortable.create('liste_tri_auteurs',{tag:'a',onUpdate:updateTriAuteur});
  }";
  $to_ret .= "function updateTriAuteur() {
           var url = '".generer_url_ecrire('tri_auteurs')."';
           pars = Sortable.serialize('liste_tri_auteurs',{name:'o',tag:'a'})+'&id_article=$id_article';
    new Ajax.Request(url,{method:'post',parameters:pars});
  }";
  $to_ret .= "Event.observe(window, 'load', TriAuteurs_initialiseSort, false);";
  $to_ret .= ' </script>';



  $to_ret .= '<div>&nbsp;</div>';
  $to_ret .= '<div class="bandeau_rubriques" style="z-index: 1;">';
  $to_ret .= "<div style='position: relative;'>";
  $to_ret .= "<div style='position: absolute; top: -12px; $spip_lang_left: 3px;'>
	<img src='"._DIR_PLUGIN_TRI_MOTS."/img/updown.png'/></div>";
  $to_ret .= "<div style='background-color: white; color: black; padding: 3px; padding-$spip_lang_left: 30px; border-bottom: 1px solid #444444;' class='verdana2'><b>"._T('ordonner_auteurs')."</b></div>";
  $to_ret .= "</div>";

  $to_ret .= '<div class="plan-articles">';
  $from = array('spip_auteurs_articles as lien','spip_auteurs as auteurs');
  $select = array('lien.rang','lien.id_auteur','auteurs.nom');
  $where = array('lien.id_auteur=auteurs.id_auteur',"lien.id_article=$id_article");

  $rez = spip_abstract_select($select,$from,$where);
  $to_ret .= '<div class="plan-articles" id="liste_tri_auteurs">';
  while($row = spip_abstract_fetch($rez)) {
    $to_ret .= '<a href="#auteur_'.$row['id_auteur'].'" id="auteur_'.$row['id_auteur'].'">
<div class="arial1" style="float: right; color: black; padding-left: 4px;">
<b> '._T('trimots:rang').'&nbsp;'.$row['rang'].'</b>
</div>';
	$to_ret .= $row['nom'].'</a>';
  }
  $to_ret .= '</div>';
  $to_ret .= '</div></div>';
  return $to_ret;
}

?>
