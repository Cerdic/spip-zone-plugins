<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Outils de pr�sentation
 *
 */

include_spip('inc/presentation');

// D�finition d'un sous-bloc d�pliable
// Sous-bloc d'un formulaire, ins�r� avec un fieldset...
function gmap_sous_bloc_depliable($nom, $titre, $contenu, $mapId, $extraClass = '')
{
	$out = '';
	
	// D�but du wrapper global, qui prend l'�tat
	$out .= '<div id="'.$nom.'" class="sbd_main sbd_replie'.(($extraClass && strlen($extraClass)) ? ' '.$extraClass : '' ).'">' . "\n";
	
	// Titre et bouton d�pliant
	$out .= '<div class="sbd_closed">' . "\n";
	$out .= '	<div class="sbd-btn-bloc sbd-titre"><a id="sbd_btn_'.$nom.'_open" class="sbd-btn sbd-btn-down" title="'._T('gmap:sbd_btn_open').'" href="#"></a><span class="titre-sous-bloc">'.$titre.'</span></div>' . "\n";
	$out .= '</div>' . "\n";
	
	// Contenu
	$out .= '<div class="sbd_opened">' . "\n";
	$out .= '	<div id="formulaire_'.$nom.'" method="post" action="#">'."\n";
	$out .= '		<fieldset>' . "\n";
	$out .= '			<legend class="sbd-titre"><a id="sbd_btn_'.$nom.'_close" class="sbd-btn sbd-btn-up" title="'._T('gmap:sbd_btn_close').'" href="#"></a></legend>' . "\n";
	$out .= $contenu;
	$out .= '		</fieldset>' . "\n";
	$out .= '	</div>'."\n";
	$out .= '</div>' . "\n";
	
	// Fin du wrapper global
	$out .= '</div>' . "\n";
	
	// Scripts pour l'ouverture/fermeture
	$out .= '<script type="text/javascript">'."\n".'//<![CDATA['."\n";
	$out .= '
jQuery(document).ready(function() {
	SousBlocDepliant.bloc("'.$mapId.'", "'.$nom.'").initialize();
});
' . "\n";
	$out .= '//]]>'."\n".'</script>'."\n";
	
	return $out;
}

// Surcharge du bloc depliable pour envoyer des �v�nements
// $texte : texte du bouton
// $deplie : true (deplie) ou false (plie) ou -1 (inactif) ou 'incertain' pour que le bouton s'auto init au chargement de la page 
// $ids : id des div lies au bouton (facultatif, par defaut c'est le div.bloc_depliable qui suit)
// https://code.spip.net/@bouton_block_depliable
function gmap_bouton_block_depliable($texte,$deplie,$ids="", $eventTarget = "")
{
	// Le code est recopier de SPIP 2.0.9, seule les fonctions javascript depliant et depliant_clicancre sont modifi�es
	// Il me semble que de recopier le code, en faisant donc un nouveau m�canisme, est plus solide que de r�cup�rer
	// le code g�n�r� et de changer seulement l'appel aux fonctions...
	
	if (!_SPIP_AJAX)
		$deplie=true; // forcer un bouton deplie si pas de js
	$bouton_id = 'b'.substr(md5($texte.microtime()),0,8);

	$class = ($deplie===true)?" deplie":(($deplie==-1)?" impliable":" replie");
	if (strlen($ids))
	{
		$cible = explode(',',$ids);
		$cible = '#'.implode(",#",$cible);
	}
	else
		$cible = "#$bouton_id + div.bloc_depliable";

	return "<div "
	  .($bouton_id?"id='$bouton_id' ":"")
	  ."class='titrem$class'"
	  . (($deplie===-1)
	  	?""
	  	:" onmouseover=\"jQuery(this).gmap_depliant('$eventTarget', '$cible');\""
	  )
	  .">"
	  // une ancre pour rendre accessible au clavier le depliage du sous bloc
	  // on ne la mets pas en interface "accessible", dans laquelle il n'y  pas de sous bloc ... un comble !
	  . ($GLOBALS['spip_display']==4?"":"<a href='#' onclick=\"return jQuery(this).gmap_depliant_clicancre('$eventTarget', '$cible');\" class='titremancre'></a>")
	  . "$texte</div>"
	  . http_script( ($deplie==='incertain')
			? "jQuery(document).ready(function(){if (jQuery('$cible').is(':visible')) $('#$bouton_id').addClass('deplie').removeClass('replie');});"
			: '');

	return $retour;
}

// Cadre depliable
function gmap_cadre_depliable($logo, $titre, $deplie, $corps, $seed, $eventTarget = "")
{
	$id = $seed.substr(md5($texte.microtime()),0,8);
	$bouton = gmap_bouton_block_depliable($titre, $deplie, $id, $eventTarget);
	$wrapper = debut_cadre('trait-couleur', $logo, '', $bouton, '', '', false);
	$wrapper .= debut_block_depliable($deplie, $id);
	$wrapper .= '<div class="cadre_padding">' . "\n";
	$wrapper .= $corps;
	$wrapper .= '<div class="nettoyeur"></div>' . "\n";
	$wrapper .= "</div>\n"; // padding
	$wrapper .= "</div>\n"; // block depliable
	$wrapper .= fin_cadre();
	return $wrapper;
}

// Utilitaire pour ajouter le bouton "submit" � la mode de spip-core
// $action = nom de l'action ex�cut�e
// $corps = contenu du formulaire
// $logo = logo du formulaire
// $titre = titre
// $clic = texte du bouton (par d�faut : "Valider")
// $atts_i = attributs du champ input (par d�faut : class="fondo" style="float:right")
// $atts_span = attributs du champ span qui contient le bouton (par d�faut vide)
function gmap_formulaire_submit($action, $corps, $logo, $titre, $clic='', $atts_i='', $atts_span = "")
{
	global $spip_lang_right;

	// D�but du formulaire
	$wrapper = debut_cadre_trait_couleur($logo, true, '', $titre);
	$spipAction = generer_url_ecrire('configurer_gmap');
	$wrapper .= '
	<form action="'.$spipAction.'" method="post">
	<div class="configurer_gmap">';
	$wrapper .= '
		' . form_hidden($spipAction);
	$wrapper .= '
		<input type="hidden" name="config" value="'.$action.'" />';
	
	// Ajouter le corps du formulaire
	$wrapper .= $corps;

	// Ajouter le bouton
	if (!$atts_i) 
		$atts_i = ' style="float: '.$spip_lang_right.'"';
	if (!$clic)  $clic =  _T('bouton_valider');
		$submit = '<input type="submit" value="'.$clic.'" '.$atts_i.' />';
	$wrapper .= "<div><span" . $atts_span . ">" . $submit . "</span></div>";
	  
	// Fin du formulaire
	$wrapper .= '</div></form>';
	$wrapper .= fin_cadre_trait_couleur(true);
	  
	return $wrapper;
}

// Mise en forme d'un formulaire ajax pour les configurations
// $mainAction = action principale, i.e. nom des fonction dans action/ et exec/ pour traiter le formulaire
// $action = nom du formulaire (par ex. "import")
// $retour = formulaire appel� sur Ajax n'est pas activ�
// $corps = contenu du formulaire
// $logo = logo du formulaire
// $titre = titre
// $button = texte du bouton ("Valider" par d�faut)
function gmap_formulaire_ajax($mainAction, $action, $retour, $corps, $logo, $titre, $button = null, $deplie=-1, $eventTarget="")
{
	// Ajouter la m�canique ajax 
	// ajax_action_post(
	// $action = action effectu�e
	// $arg = 
	// $retour = 
	// $gra = 
	// $corps = contenu
	// $clic='', $atts_i='', $atts_span = "", $args_ajax='')
	$corps = ajax_action_post($mainAction, $action, $retour, '', $corps, $button);
	
	// Ajouter le cadre
	$corps = gmap_cadre_depliable($logo, $titre, $deplie, $corps, $action, $eventTarget);

	// Afficher le tout
	//ajax_action_greffe($fonction, $id, $corps)
	// Place un element HTML dans une div nommee, sauf si c'est un appel Ajax car alors la div y est deja 
	// $fonction = denomination semantique du bloc, que l'on retouve en attribut class
	// $id = id de l'objet concerne si il y a lieu ou "", sert a construire un identifiant unique au bloc ("fonction-id")
	// $corps = contenu
	return ajax_action_greffe($mainAction.'-'.$action, "", $corps);
}

?>