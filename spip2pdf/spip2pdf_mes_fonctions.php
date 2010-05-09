<?php

$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_SPIP2PDF',(_DIR_PLUGINS.end($p)));


function balise_PDF_CREER_DOCUMENT($p){
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



function balise_PDF_SORTIR_DOCUMENT($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_sortir_document(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="spip2pdf_sortir_document()";
		$p->interdire_scripts = false;
	}
	return $p;
}

function balise_PDF_HEADER_LOGO($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_header_logo(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}

function balise_PDF_HEADER($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_header(' . implode(',',$param ). ')';
		$p->interdire_scripts = true;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}

function balise_PDF_HEADER_TEXTE($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_header_text(' . implode(',',$param ). ')';
		$p->interdire_scripts = true;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}


function balise_PDF_AJOUTER_IMAGE($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_ajouter_image(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}


function balise_PDF_CELLULE($p){
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


function balise_PDF_MULTI_CELLULE($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_multi_cellule(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}


//done
function balise_PDF_ECRIRE_HTML($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_write_html(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}


function balise_PDF_PAGE($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_page(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="spip2pdf_page()";
		$p->interdire_scripts = false;
	}
	return $p;
}

function balise_PDF_POSITION($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_setXY(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}

function balise_PDF_DRAW_COLOR($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_draw_color(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}


function balise_PDF_FILL_COLOR($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_fill_color(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}


function balise_PDF_TEXT_COLOR($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_text_color(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}

function balise_PDF_FONT($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_font(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}

function balise_PDF_MARGES($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_marges(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}

function balise_PDF_LN($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_ln(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="spip2pdf_ln()";
		$p->interdire_scripts = false;
	}
	return $p;
}




function balise_PDF_MULTICOLONNE($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_multicol(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}


function balise_PDF_LANG_DIR($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_lang_dir(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code="";
		$p->interdire_scripts = false;
	}
	return $p;
}




function balise_PDF_AJOUTER_FONT($p){
	if ($p->param && !$p->param[0][0]) {
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


function balise_PDF_DESACTIVE_HEADER($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_desactive_header(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code= 'spip2pdf_desactive_header()';
		$p->interdire_scripts = false;
	}
	return $p;
}

 
function balise_PDF_REACTIVE_HEADER($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_reactive_header(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code= 'spip2pdf_reactive_header()';
		$p->interdire_scripts = false;
	}
	return $p;
}


function balise_PDF_LIENS($p){
	if ($p->param && !$p->param[0][0]) {
		$param = init_balise_param($p);
		$p->code = 'spip2pdf_liens(' . implode(',',$param ). ')';
		$p->interdire_scripts =false;
	}else{
		$p->code= 'spip2pdf_reactive_header()';
		$p->interdire_scripts = false;
	}
	return $p;
}

function init_balise_param($p){
	if ($p->param && !$p->param[0][0]) {
		for($i=1; $i<count($p->param[0]);$i++){
			$param[$i] = calculer_liste($p->param[0][$i],
			$p->descr,
			$p->boucles,
			$p->id_boucle);
		}
	}
	return $param;
}

/**
 * ********************************************************** **
 * EXTRA FILTERS FUNCTIONS FOR HELPING THE GENERATION OF DOC  **
 * ********************************************************** **
 */

/*
 * Strip the html code of images inside a text.
 * 
 * @param $texte the texte on which we want to strip the image
 * @return $texte the texte with images striped
 */
function spip2pdf_supprimer_images($texte){
	$pattern = array('/<img[^>]*>/i');
	$replacement = array("");
	return preg_replace($pattern,$replacement,$texte);	
}

?>