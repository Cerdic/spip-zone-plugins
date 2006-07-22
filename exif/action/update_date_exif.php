<?php

//    Fichier cr�� pour SPIP avec un bout de code emprunt� � celui ci.
//    Distribu� sans garantie sous licence GPL./
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

function action_update_date_exif() {
  global $hash, $id_auteur, $date_conb;
  
  $id_mot = intval($id_auteur);

  include_spip("inc/actions");
  if (!verifier_action_auteur("update_date_exif $date_conb", $hash, $id_auteur)) {
	include_ecrire('inc_minipres');
	minipres(_T('info_acces_interdit'));
  }

  include_ecrire("inc_abstract_sql"); 


  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];

  
  $selectType = array('id_type');
  $fromType = array('spip_types_documents');
  $whereType = array('extension IN (\'jpg\',\'tiff\')');

  $rez = spip_abstract_select($selectType,$fromType,$whereType);
  $types = '';
  while($row = spip_abstract_fetch($rez)) {
    $types .= ','.$row['id_type'];
  }
  $types = substr($types,1);

  spip_abstract_free($rez);

  $selectDoc = array('id_document','fichier','date');
  $fromDoc = array('spip_documents');
  $whereDoc = array("id_type IN ($types)");

  $rez = spip_abstract_select($selectDoc,$fromDoc,$whereDoc);

  $total_doc = 0;

   include_spip('inc/exif');

  while($row = spip_abstract_fetch($rez)) {
	$fichier= $row['fichier'];
	$id_document = $row['id_document'];
        $old_date = $row['date'];
	$date_exif = date_exif($fichier);
        if($date_exif && $date_exif != $old_date){      
            spip_query("UPDATE ".$table_pref."_documents SET date = '$date_exif' WHERE id_document=$id_document");
            $total_doc++;
       }
  }
  
  if(!$_REQUEST['ajax']) 	redirige_par_entete(generer_url_ecrire('update_date_exif',"done=$total_doc"));
}
?>