<?php
// _SPIPLISTES_EXEC_COURRIER_PREVIEW
/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/affichage');


function exec_spiplistes_courrier_preview () {

	$id_courrier = intval(_request('id_courrier'));
	$format = (($f = _request('format')) && ($f=='texte')) ? $f : 'html';
	
	if($id_courrier > 0) {

		$sql_select = "texte,message_texte,statut";

		$sql_result = spip_query("SELECT $sql_select FROM spip_courriers WHERE id_courrier=$id_courrier LIMIT 1");

		if($row = spip_fetch_array($sql_result)) {
			foreach(explode(",", $sql_select) as $key) {
				$$key = $row[$key];
			}
			switch($format) {
				case 'html':
					$texte = $row['texte'];
					break;
				case 'texte':
					header("Content-Type: text/plain charset=".$GLOBALS['meta']['charset']);
					$texte = $row['message_texte'];
					if(empty($texte)) {
						$texte = version_texte($row['texte']);
					}
					break;
			}
		}
		else {
			$texte = _T('spiplistes:Erreur_appel_courrier');
		}
	}
	else {
		$texte = _T('spiplistes:Erreur_appel_courrier');
	}

	// ajax_retour() force 'html' dans header. Pas bon pour preview.
	if($format=='texte') echo($texte); 
	else ajax_retour($texte);
}
?>