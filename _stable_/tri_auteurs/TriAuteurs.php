<?php

//    Fichier créé pour SPIP avec un bout de code emprunté à celui ci.
//    Distribué sans garantie sous licence GPL./
//    Copyright (C) 2006  Pierre ANDREWS
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_TRI_AUTEURS',(_DIR_PLUGINS.end($p)));

function TriAuteurs_affiche_droite($arguments) {  
  global $connect_statut, $connect_toutes_rubriques, $connect_id_rubrique;
  if(_request('exec') == 'articles') {
	include('tri_auteurs_utils.php');
	$table_pref = 'spip';
	if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];

	include_spip('base/abstract_sql');
	
	$from = array('spip_articles');
	$select = array('id_rubrique');
	$where = array("id_article="._request('id_article'));
	
	$rez =  spip_abstract_fetch(spip_abstract_select($select,$from,$where));

	$id_rubrique = $rez['id_rubrique'];
	
	if(($connect_statut == '0minirezo' AND $connect_toutes_rubriques)
	   OR TriAuteurs_verifier_auteur($arguments['args']['id_article'])
	   OR ($connect_statut == "0minirezo" AND isset($connect_id_rubrique[$id_rubrique]))) {
	  
	  //Installation
	  if(!lire_meta('TriAuteurs:installe')) {
		$res = spip_query("SHOW COLUMNS FROM `".$table_pref."_auteurs_articles` LIKE 'rang'");
		if(!spip_fetch_array($res)) {
		  spip_query("ALTER TABLE `".$table_pref."_auteurs_articles` ADD `rang` BIGINT NOT NULL DEFAULT 0;");
		}
		spip_free_result($res);
		ecrire_meta('TriAuteurs:installe',true);
		ecrire_metas();
	  }

	  $arguments['data'] .= TriAuteurs_boite_tri_auteurs(_request('id_article'));
	}
  }
  return $arguments;
}

function TriAuteurs_boite_tri_auteurs($id_article) {
  global $spip_lang_left,$connect_id_auteur;
  
  include_spip('base/abstract_sql');

  $from = array('spip_auteurs_articles as lien','spip_auteurs as auteurs');
  $select = array('lien.rang','lien.id_auteur','auteurs.nom');
  $where = array('lien.id_auteur=auteurs.id_auteur',"lien.id_article=$id_article");
  $order = array('lien.rang');

  $rez = spip_abstract_select($select,$from,$where,'',$order);
  $to_ret = '';

  if(spip_abstract_count($rez) > 1) {
	
	
	$to_ret .= '<div>&nbsp;</div>';
	$to_ret .= '<div class="bandeau_rubriques" style="z-index: 1;">';
	$to_ret .= "<div style='position: relative;'>";
	$to_ret .= "<div style='position: absolute; top: -12px; $spip_lang_left: 3px;'>
	<img src=\""._DIR_PLUGIN_TRI_AUTEURS."/img/updown.png\"/></div>";
	$to_ret .= "<div style='background-color: white; color: black; padding: 3px; padding-$spip_lang_left: 30px; border-bottom: 1px solid #444444;' class='verdana2'><b>"._T('triauteurs:ordonner_auteurs')."</b></div>";
	$to_ret .= "</div>";
	
	$to_ret .= '<div class="plan-articles" id="liste_tri_auteurs_cont">';
	$to_ret .= '<div id="liste_tri_auteurs">';
	while($row = spip_abstract_fetch($rez)) {
	  $to_ret .= '<div id="auteur_'.$row['id_auteur'].'">';
	  $to_ret .= $row['nom'].'</div>';
	}
	$to_ret .= '</div>';
	$to_ret .= '</div></div>';
		$to_ret .= "\n".'		<script type="text/javascript" src="'._DIR_PLUGIN_TRI_AUTEURS.'/javascript/prototype.js"></script>';
	$to_ret .= "\n".'		<script type="text/javascript" src="'._DIR_PLUGIN_TRI_AUTEURS.'/javascript/scriptaculous.js"></script>';
	$to_ret .= "\n".'	<script type="text/javascript">//<![CDATA[';
	$to_ret .= "\nfunction TriAuteurs_initialiseSort() {
	Sortable.create('liste_tri_auteurs',{onUpdate:updateTriAuteur,ghosting:true,tag:'div'});
  }";
	$hash = calculer_action_auteur("tri_auteurs $id_article");
	$to_ret .= "\nfunction updateTriAuteur() {
           var url = '".generer_url_action('tri_auteurs')."';
           var pars = Sortable.serialize('liste_tri_auteurs',{name:'o',tag:'div'})+'&id_article=$id_article&id_auteur=$connect_id_auteur&hash=$hash';
           new Ajax.Request(url,{method:'post',parameters:pars});
  }";
	//	$to_ret .= "Event.observe(window, 'load', TriAuteurs_initialiseSort, false);";
	$to_ret .= "\n".'TriAuteurs_initialiseSort();'.
	  "\n".'//]]></script>';
	
  }
  return $to_ret;
}


function TriAuteurs_header_prive($texte) {
  $texte.= '<link rel="stylesheet" type="text/css" href="' ._DIR_PLUGIN_TRI_AUTEURS. '/tri_auteurs.css" />' . "\n";
  return $texte;
}

?>
