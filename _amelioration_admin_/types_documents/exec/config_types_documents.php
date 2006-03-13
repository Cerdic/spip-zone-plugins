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


define('_DIR_PLUGIN_TYPES_DOCUMENTS',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__).'/..'))))));

function exec_config_types_documents() {
  global $connect_statut, $connect_toutes_rubriques;
  
  include_spip("inc/presentation");
  include_spip ("base/abstract_sql");
  
  debut_page('&laquo; '._T('typesdocuments:titre_page').' &raquo;', 'configurations', 'types_documents');
  
  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	echo _T('avis_non_acces_page');
	exit;
  }
  
  if ($connect_statut == '0minirezo' AND $connect_toutes_rubriques ) {
	
	echo '<br><br><br>';
	
	gros_titre(_T('typesdocuments:gros_titre'));
	
	barre_onglets("configuration", "config_types_documents");
	
	/*Affichage*/
	debut_gauche();	
	
	debut_boite_info();
	echo propre(_T('typesdocuments:help'));
	fin_boite_info();
	
	debut_droite();
	
	include_spip('inc/config');
	avertissement_config();
	
	echo "<div class='liste'>";
	bandeau_titre_boite2(_T('typesdocuments:titre_page'), "article-24.gif");
	
	echo afficher_liste_debut_tableau();
	
	$select = array('*');
	$from = array('spip_types_documents');
	
	$s_count = array('count(*)');
	$f_count = array('spip_documents');
	
	$table = array();
	$table[] = array('','<strong>'._T('typesdocuments:type').'</strong>','<strong>'._T('typesdocuments:extension').'</strong>','<strong>'._T('typesdocuments:description').'</strong>','<strong>'._T('typesdocuments:permission').'</strong>','<strong>'._T('typesdocuments:inclus').'</strong>','<strong>'._T('typesdocuments:nombre_documents').'</strong>');
	$rez = spip_abstract_select($select,$from);
	while($row = spip_abstract_fetch($rez)) {
	  $vals = '';	
	  $id_type = intval($row['id_type']);
	  $titre = $row['titre'];
	  $ext = $row['extension'];
	  $mime = $row['mime_type'];
	  $inclus = $row['inclus'];
	  $upload = $row['upload'];
	  $desc = $row['description'];
	  include_spip('inc/documents');
	  $vignette = vignette_par_defaut($ext,false);
	  if($vignette) {
	  $vals[] = '<img src="'.$vignette.'" alt="'.$titre.'"/>';
	  } else
		$vals[] = '';
	  $vals[] = $titre;    
	  $vals[] = $ext;
	  $vals[] = $desc;
	  $vals[] = '<input type="checkbox" disabled="true" name="upload['.$id_type.']"'.(($upload=='oui')?' checked="true"':'').'/>';
	  $vals[] = $inclus;
	  
	  list($count) = spip_abstract_fetsel($s_count,$f_count,array("id_type=$id_type"));
	  $vals[] = $count;
	  
	  $table[] = $vals;
	}
	spip_abstract_free($rez);
	
	$largeurs = array(24,11,11,80,11,11,11);
	$styles = array('','arial1','arial11','arial11','arial11','arial11','arial11');	
	
	afficher_liste($largeurs, $table, $styles);

	echo afficher_liste_fin_tableau();

	$redirect = generer_url_ecrire('types_documents');

	echo '<form action="'.generer_url_action('types_documents_insert').'" method="post">';
	
	echo afficher_liste_debut_tableau();
	
	$v = array(
			   vignette_par_defaut('defaut',false);,
			   '<input type="text" name="titre"/>',
			   '<input type="text" name="ext"/>',
			   '<input type="text" name="desc"/>',
			   '<input type="checkbox" name="upload" checked="true"/>',
			   '<input type="text" name="mime"/>',
			   '<select name="inclus"><option value="image"/><option value="embed"/></select>',
			   ''
			   );

	afficher_liste($largeurs, array($v), $styles);

	echo afficher_liste_fin_tableau();
	
	echo '<input type="hidden" name="id_auteur" value="'.$connect_id_auteur.'"/>';
	echo '<input type="hidden" name="date_conb" value="'.date('Ymd').'">';
	echo '<input type="hidden" name="hash" value="'.calculer_action_auteur("update_date_exif ".date('Ymd')).'"/>';	
	echo '<input type="submit" value="'._T('valider').'"/>';
	echo '</form>';
	echo '</div>';
  } 
  
  fin_page();

}

?>
