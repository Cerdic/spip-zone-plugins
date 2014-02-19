<?php

function nettoyer_exergues($texte){
	/* [exergue<-] et <exergue> sur #TEXTE** */

	$texte = preg_replace(',<p><a name="exergue"></a></p>,Uims','',$texte);
	$texte = preg_replace(',</?exergue ?/?>,Uims','',$texte);

	/* div et span */

	preg_match_all(',<span class="spip_exergue">(.*?)</span>,ims',$texte, $regs);
	$i = 0 ;
	foreach ($regs[0] as $reg) {							
		$texte = str_replace($reg,$regs[1][$i],$texte);
	$i ++ ;
	}

	preg_match_all(',<div class="spip_exergue">(.*?)</div>,ims',$texte, $regs);
	$i = 0 ;
	foreach ($regs[0] as $reg) {							
		$texte = str_replace($reg,$regs[1][$i],$texte);
	$i ++ ;
	}

	return $texte ;
}
