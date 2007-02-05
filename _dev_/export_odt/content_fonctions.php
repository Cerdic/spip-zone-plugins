<?php
function avant_propre($texte){
	
//<text:span text:style-name="T1">gras</text:span>
	return $texte;
}

function apres_propre($texte){
	
	$subst = array(
	",<strong [^>]*>,"=>'<text:span text:style-name="spip_gras">',
	",</strong>,"=>'</text:span>',
	",<i [^>]*>,"=>'<text:span text:style-name="spip_italic">',
	",</i>,"=>'</text:span>',
	);
	//$texte = preg_replace(array_keys($subst),array_values($subst),$texte);
	
	// convertir les paragraphes
	//$texte = preg_replace(",^\s*<p [^>]*>(.*)</p>\s*$,ms","\\1",$texte);
	
	$texte = unicode2charset($texte);

	return $texte;
}
?>