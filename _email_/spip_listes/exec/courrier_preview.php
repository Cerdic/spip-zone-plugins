<?php

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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_courrier_preview()
{

global $id_message;

 
$query_m = "SELECT * FROM spip_messages WHERE id_message=$id_message";
$result_m = spip_query($query_m);

	while($row = spip_fetch_array($result_m)) {
	    $texte = $row["texte"];
	    $texte = eregi_replace("__bLg__[0-9@\.A-Z_-]+__bLg__","",$texte);
	  	$texte = stripslashes($texte);
	  	$temp_style = ereg("<style[^>]*>[^<]*</style>", $texte, $style_reg);
	  	if (isset($style_reg[0])) $style_str = $style_reg[0]; 
	                         else $style_str = "";
	  	$texte = ereg_replace("<style[^>]*>[^<]*</style>", "__STYLE__", $texte);
	    $texte = propre($texte); // pb: enleve aussi <style>...
	    $texte = propre_bloog($texte);
	    $texte = ereg_replace("__STYLE__", $style_str, $texte);
	    echo "$texte";
	}

}
?>
