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


function action_types_documents_inclus() {
  $redirect = _request('redirect');
  $hash = _request('hash');
  $id_auteur = intval(_request('id_auteur'));
  $date_comp = _request('date_comp');

  $id_type = intval(_request('id_type')); 
  $inclus = addslashes(_request("inclus_$id_type"));

  include_spip("inc/actions");
  if (!verifier_action_auteur("types_documents $date_comp", $hash, $id_auteur)) {
	include_spip('inc/minipres');
	minipres(_T('info_acces_interdit'));
  }
  
  /************************************************************************/
  /* delete */
  /************************************************************************/  
  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];

  spip_query("UPDATE `".$table_pref."_types_documents` SET inclus='$inclus' WHERE id_type=$id_type");

  if(!$_REQUEST['ajax']) 	redirige_par_entete(urldecode($redirect));
}
?>
