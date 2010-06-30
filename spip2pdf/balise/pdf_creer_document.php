<?php

function balise_PDF_CREER_DOCUMENT($p){
	include_spip('inc/spip2pdf_pdf_fonctions');
	if ($p->param && !$p->param[0][0]) {
		for($i=1; $i<count($p->param[0]);$i++){
			$param[$i] = calculer_liste($p->param[0][$i],
			$p->descr,
			$p->boucles,
			$p->id_boucle);
			// autres filtres (???)
			//array_shift($p->param);
		}
		$p->code = 'spip2pdf_creer(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code='spip2pdf_creer()';
		$p->interdire_scripts = false;
	}
	return $p;
}

?>