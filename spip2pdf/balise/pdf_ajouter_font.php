<?php

function balise_PDF_AJOUTER_FONT($p){
	if ($p->param && !$p->param[0][0]) {
		include_spip('inc/spip2pdf_pdf_fonctions');
		for($i=1; $i<count($p->param[0]);$i++){
			$param[$i] = calculer_liste($p->param[0][$i],
			$p->descr,
			$p->boucles,
			$p->id_boucle);
		}
		$p->code = 'spip2pdf_addFont(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}

?>