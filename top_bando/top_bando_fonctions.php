<?php
/**
 * Ce plugin permet de g&#233;rer le bandeau de sommet des pages: hauteur et images.
 *
 * @author     cy_altern
 * @license    GNU/GPL
 * @package    plugins
 * @subpackage top_bando
 * @category   Interface Privee
 * @version    $Id$
 */


/**
 * supprimer le # dans une chaine (cf couleurs pour image_typo)
 * @param string $txt
 * @return string
 */
function top_bando_suprime_diese($txt) {
	return str_replace('#', '', $txt);
}

/**
 * Modifier le fichier d'upload
 * @return string
 */
function affiche_upload(){
	$ret = '';
	$iconifier = charger_fonction('iconifier', 'inc');
	$ret .= $iconifier('id_syndic', 0, 'cfg&amp;cfg=top_bando');
	$Trech = array('#<div([^>]*?cadre.*?)>.*?<img[^>]*?cadre-icone[^>]*?>#Uim', '#<\/div>(\W*?<\/div>\W*?<script.*?)$#Uim', '#<a[^>]*?aide[^>]*?>[\W]*<img[^>]*?>[\W]*<\/a>#Uim');
	$Tremp = array('<fieldset $1><legend>'._T('topbando:choix_images').'</legend><p>'._T('topbando:explication_choix_images').'</p>', '</fieldset> $1','');

	$ret = preg_replace($Trech, $Tremp, $ret);
	return $ret;
}

?>