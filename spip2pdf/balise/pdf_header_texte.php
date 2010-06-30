<?php

function balise_PDF_HEADER_TEXTE($p){
	if ($p->param && !$p->param[0][0]) {
		include_spip('inc/spip2pdf_pdf_fonctions');
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_header_text(' . implode(',',$param ). ')';
		$p->interdire_scripts = true;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}

?>