<?php 


//	  exec_date_exif.php
//    prend tous les documents jpg+tiff et leur donne leur date EXIF
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

//$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
//define('_DIR_PLUGIN_EXIF',(_DIR_PLUGINS.end($p)));

/***********************************************************************/
/* function*/
/***********************************************************************/


function verifier_admin() {
  global $connect_statut, $connect_toutes_rubriques;
  return (($connect_statut == '0minirezo') AND $connect_toutes_rubriques);
}

//------------------------la fonction qui fait tout-----------------------------------

function exec_update_date_exif() {
  global $connect_id_auteur;
  global $done;

  $done = intval($done);

  include_ecrire ("inc_presentation");

  debut_page('&laquo; '._T('exif:titre_page_update').' &raquo;', 'documents', 'mots');
  
  if(!verifier_admin()) {
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		fin_page();
		exit;
	}

if($done) echo "<h3>Synchronis&eacute; $done photos.";


echo '<form action="'.generer_url_action('update_date_exif').'" method="post">';
echo '<h3>Synchroniser les dates des documents</h3>';
echo '<input type="hidden" name="id_auteur" value="'.$connect_id_auteur.'"/>';
echo '<input type="hidden" name="date_conb" value="'.date('Ymd').'">';
echo '<input type="hidden" name="hash" value="'.calculer_action_auteur("update_date_exif ".date('Ymd')).'"/>';
echo '<input type="submit" value="'._T('valider').'"/>';
echo '</form>';

   fin_page();

}

?>
