<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2009
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Controleur pour les traductions de composants ACS
 */
function controleurs_traduction_dist($regs) {
	global $spip_lang;
	include_spip('inc/filtres');
	include_spip('inc/composant/composant_traduction');
	
	list(,$crayon,$type,$champ,$id,$class) = $regs;
	
	// On lit la langue du crayon et le cadre dans sa classe, qui comprend la classe lang_xx 
	if (preg_match('/\blang_([^_|^\W]+)(?:_(\w+)){0,1}\b/', $class, $matches)) {
		$lang = $matches[1];
		$cadre = $matches[2];
	}
	else
		return array(_U('crayons:donnees_mal_formatees').' (lang)', 'err');

	// On lit le composant et le nom de variable dans le champ, qui est de forme <composant>_<variable>
	if (preg_match('/\b([^_|^\W]+)_(\w+)\b/', $champ, $matches)) {
		$c = $matches[1];
		$var = $matches[2];
	}
	else
		return array(_U('crayons:donnees_mal_formatees').' (champ)', 'err');

	$tr = lecture_composant_traduction($c, $lang, $cadre);
	$val = $tr[$var];
	$crayon = new SecureCrayon("traduction-".$champ."-".$id);
	$html = $crayon->code();
	$html .= '<input type="hidden" name="cadre_'.$crayon->key.'" value="'.$cadre.'" />';
	$html .= '<input type="hidden" name="lang_'.$crayon->key.'" value="'.$lang.'" />';
	$html .= '<input type="hidden" name="oldval_'.$crayon->key.'" value="'.entites_html($val).'" />';
	$html .= '<input class="crayon-active" type="text"'
					. ' style="width:'.$crayon->w.'px; height:'.$crayon->h.'px; font-size: '._request('font-size').';"'
					. ' name="'.$champ.'_'.$crayon->key.'"'
					. ' value="'.entites_html($val).'" />'."\n";
	$html = '<form class="formulaire_crayon" method="post" action="'
		. url_absolue(parametre_url(self(),'action', 'crayons_traduction_store'))
		. '" enctype="multipart/form-data">'
		. $html
		. crayons_boutons()
		. '</form>';
		
	return array($html, NULL);
}

?>
