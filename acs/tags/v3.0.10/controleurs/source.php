<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2009
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

function controleurs_source_dist($regs) {
  global $spip_lang;
  
	include_spip('inc/filtres');
	
  list(,$crayon,$type,$champ,$id) = $regs;
  $page = str_replace('_slash_', '/', str_replace('_tiret_', '-', $champ));
  $file = find_in_path($page.'.html');
  $source = file_get_contents($file);
  
  // il faut passer champ=>source pour les comparaisons dans action/crayons_store
  $crayon = new SecureCrayon("source-$champ-" . $id, array($champ => $source));
  $html = $crayon->code().
'<textarea name="content_'.$crayon->key.'_'.$champ.'" style="width:'.$crayon->w.'px; height:'.$crayon->h.'px; font-size: '._request('font-size').'">'.
entites_html($source).
'</textarea>';
	return array($html, null);
}

?>