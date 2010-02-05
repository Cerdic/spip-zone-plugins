<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

function skeleditor_affichage_final($texte){
	if (isset($_COOKIE['spip_admin'])){
		if ($GLOBALS['var_inclure']){
			$url = generer_url_ecrire('skeleditor','retour='.parametre_url(self(),'var_mode','inclure').'&f=');
			$texte .= "<script>jQuery(function(){jQuery('.inclure_blocs h6:first-child').each(function(){
				jQuery(this).html(\"<a href='$url\"+jQuery(this).html()+\"'>\"+jQuery(this).html()+'</a>');
			})});</script>";
		} else {
			$lien = "<a href='".parametre_url(self(),'var_mode','inclure')."' class='spip-admin-boutons' "
			."id='inclure'>"._T('skeleditor:squelette')."</a>";
			$texte .= "<script>jQuery(function(){jQuery('#spip-admin').append(\"$lien\");});</script>";
		}
	}
	return $texte;
}


?>