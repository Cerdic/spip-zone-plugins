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


//celle la n'est appelee qu'avec de l'ajax...
function action_types_documents_update() {
  $hash = _request('hash');
  $id_auteur = intval(_request('id_auteur'));
  $date_comp = _request('date_comp');

  include_spip("inc/actions");
  if (!verifier_action_auteur("types_documents $date_comp", $hash, $id_auteur)) {
	include_spip('inc/minipres');
	minipres(_T('info_acces_interdit'));
  }
  
  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];

  $id_type = intval(_request('id_type'));

  $fields = array('titre',
				  'extension',
				  'mime_type',
				  'inclus',
				  'description');

  $setter = '';
  $f = '';
  $new_val = 'error...';

  //on cherche le champ � mettre � jour (field) et sa valeur (value)
  foreach($fields as $fi) {
	$f = addslashes(_request('field'));
	if($f == $fi) {
	  $val = addslashes(_request('value'));
	  $setter = "$fi='$val'";
	  $new_val = $val;
	  break;
	}
  }

  /************************************************************************/
  /* update */
  /************************************************************************/
  if($setter) {
	$rez = spip_query("UPDATE ".$table_pref."_types_documents SET $setter WHERE id_type=$id_type");
	if($row = spip_fetch_array()) {
	  $new_val = $row[$f];
	}
	spip_free_result($rez);
  }

  //on retourne la nouvelle valeure
  echo $new_val;
}
?>
