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


function action_tri_mots() {
  include_spip('inc/securiser_action');
  $redirect = _request('redirect');
  $hash = _request('hash');
  $order = _request('order');
  $id_mot = intval(_request('id_mot'));
  $id_table = addslashes(_request('id_table'));
  $table = addslashes(_request('table'));

  $id_auteur =  intval($GLOBALS['auteur_session']['id_auteur']);

  //include_spip("inc/sessions");
  include_spip('inc/actions');
  if (!verifier_action_auteur("tri_mots $table $id_table $id_mot", $hash, $id_auteur)) {
	include_spip('inc/minipres');
	minipres(_T('info_acces_interdit'));
  }
  
  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];

  /************************************************************************/
  /* insertion */
  /************************************************************************/
  //o[]=118&o=120&o[]=128
  if($order) {
	$order = split('&',$order);
	for($i=0;$i<count($order);$i++) {
	  spip_query("UPDATE ".$table_pref."_mots_$table SET rang = $i WHERE id_mot=$id_mot AND $id_table=".intval(substr($order[$i],4)));
	}
  }
  
  if(!$_REQUEST['ajax']) 	redirige_par_entete(urldecode($redirect));
}
?>
