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
define('_DIR_PLUGIN_TRI_MOTS',(_DIR_PLUGINS.end($p)));

function TriMots_affiche_droite($arguments) {
  global $connect_statut, $connect_toutes_rubriques;
  if (($connect_statut == '0minirezo') AND $connect_toutes_rubriques) {
	if($arguments['args']['exec'] == 'articles') {
	  $arguments['data'] .= TriMots_boite_tri_mots($arguments['args']['id_article'],'articles','id_article','articles');
	}
	else if($arguments['args']['exec'] == 'naviguer') {
	  $arguments['data'] .= TriMots_boite_tri_mots($arguments['args']['id_rubrique'],'rubriques','id_rubrique','naviguer');
	}
	else if($arguments['args']['exec'] == 'auteurs_edit') {
	  $arguments['data'] .= TriMots_boite_tri_mots($arguments['args']['id_auteur'],'auteurs','id_auteur','auteurs_edit');
	}
	else if(($arguments['args']['exec'] == 'mots_edit') && ($arguments['args']['new'] != 'oui')) {
	  $arguments['data'] .= icone(_T('trimots:titre_page',array('objets' => _T('public:articles'))),generer_url_ecrire('tri_mots','objet=articles&ident_objet=id_article&id_mot='.$arguments['args']['id_mot'].'&retour='.urlencode(generer_url_ecrire('mots_edit',"id_mot=".$arguments['args']['id_mot']))), '../'._DIR_PLUGIN_TRI_MOTS.'/img/updown.png', "rien.gif");
	$arguments['data'] .= icone(_T('trimots:titre_page',array('objets' => _T('public:rubriques'))),generer_url_ecrire('tri_mots','objet=rubriques&ident_objet=id_rubrique&id_mot='.$arguments['args']['id_mot'].'&retour\
='.urlencode(generer_url_ecrire('mots_edit',"id_mot=".$arguments['args']['id_mot']))), '../'._DIR_PLUGIN_TRI_MOTS.'/img/updown.png', "rien.gif");
	}
  }
  return $arguments;
}

function TriMots_boite_tri_mots($id,$objet,$id_objet,$retour) {
  global $spip_lang_left;
  include_spip('base/abstract_sql');
  $to_ret = '<div>&nbsp;</div>';
  $to_ret .= '<div class="bandeau_rubriques" style="z-index: 1;">';
  $to_ret .= "<div style='position: relative;'>";
  $to_ret .= "<div style='position: absolute; top: -12px; $spip_lang_left: 3px;'>
	<img src='"._DIR_PLUGIN_TRI_MOTS."/img/updown.png'/></div>";
  $to_ret .= "<div style='background-color: white; color: black; padding: 3px; padding-$spip_lang_left: 30px; border-bottom: 1px solid #444444;' class='verdana2'><b>"._T('trimots:ordonner',array('objets' => _T("public:$objet")))."</b></div>";
  $to_ret .= "</div>";

  $to_ret .= '<div class="plan-articles">';
  $from = array("spip_mots_$objet as lien",'spip_mots as mots');
  $installe = unserialize(lire_meta('TriMots:installe'));
  if(isset($installe[$objet])) {
	$select = array('lien.rang','lien.id_mot','mots.titre');
  } else {
	$select = array('lien.id_mot','mots.titre');
  }
  $where = array('lien.id_mot=mots.id_mot',"lien.$id_objet=$id");
  $un_mot = false;

  $rez = spip_abstract_select($select,$from,$where);
  $to_ret .= '<div class="plan-articles">';
  while($row = spip_abstract_fetch($rez)) {
    $to_ret .= '<a href="'.generer_url_ecrire('tri_mots','objet='.$objet.'&ident_objet='.$id_objet.'&id_mot='.$row['id_mot'].'&retour='.urlencode(generer_url_ecrire($retour,"$id_objet=$id"))).'">
<div class="arial1" style="float: right; color: black; padding-left: 4px;">
<b> '._T('trimots:rang').'&nbsp;'.($row['rang']?$row['rang']:0).'</b>
</div>';
	$to_ret .= $row['titre'].'</a>';
	$un_mot =true;
  }
  $to_ret .= '</div>';
  $to_ret .= '</div></div>';
  if($un_mot)
   return $to_ret;
  else
   return '';
}


function TriMots_header_prive($texte) {
  $texte.= '<link rel="stylesheet" type="text/css" href="' ._DIR_PLUGIN_TRI_MOTS. '/tri_mots.css" />' . "\n";
  return $texte;
}

?>
