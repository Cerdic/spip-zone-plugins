<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2009
# Copyleft: licence GPL - Cf. LICENCES.txt


function controleurs_traduction_dist($regs) {
	global $spip_lang;
	include_spip('inc/filtres');
	
	list(,$crayon,$type,$champ,$id) = $regs;
	$crayon = new SecureCrayon("traduction-".$champ."-".$id);
	$val = _T('acs:'.$champ);
	$html = $crayon->code();
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
