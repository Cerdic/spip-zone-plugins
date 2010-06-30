<?php

function balise_PDF_CELLULE($p){
	include_spip('inc/spip2pdf_pdf_fonctions');
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_cellule(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="spip2pdf_cellule()";
		$p->interdire_scripts = false;
	}
	return $p;
}

?>