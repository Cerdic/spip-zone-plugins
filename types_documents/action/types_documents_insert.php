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


function action_types_documents_insert() {
  $redirect = _request('redirect');
  $hash = _request('hash');
  $id_auteur = intval(_request('id_auteur'));
  $date_comp = _request('date_comp');

  $ext = addslashes(_request('ext'));
  $titre = addslashes(_request('titre'));
  $desc = addslashes(_request('desc'));
  $upload = _request('upload')?'oui':'non';
  $mime_type = addslashes(_request('mime'));
  $inclus = addslashes(_request('inclus'));

  include_spip("inc/actions");
  if (!verifier_action_auteur("types_documents $date_comp", $hash, $id_auteur)) {
	include_spip('inc/minipres');
	minipres(_T('info_acces_interdit'));
  }
  
  /************************************************************************/
  /* insertion */
  /************************************************************************/
  include_spip("base/abstract_sql");
  spip_abstract_insert('spip_types_documents','(titre,descriptif,extension,mime_type,inclus,upload,maj)',"('$titre','$desc','$ext','$mime_type','$inclus','$upload',NOW())");

  if(!$_REQUEST['ajax']) 	redirige_par_entete(urldecode($redirect));
}
?>
