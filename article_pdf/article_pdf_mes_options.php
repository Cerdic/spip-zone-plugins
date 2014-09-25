<?php

// options
if (!defined('_DIR_LIB')) define('_DIR_LIB', _DIR_RACINE . 'lib/');
if (!defined('_DIR_FPDF_LIB')) define('_DIR_FPDF_LIB', _DIR_LIB . 'h5c1accba-fpdf17/');


function balise_ARTICLE_PDF_dist($p) {
		if (!is_array($p->param))
			$p->param=array();

		// Produire le premier argument {article_pdf}
		$texte = new Texte;
		$texte->type='texte';
		$texte->texte='article_pdf';
		$param = array(0=>NULL, 1=>array(0=>$texte));
		array_unshift($p->param, $param);

		// Transformer les filtres en arguments
		for ($i=1; $i<count($p->param); $i++) {
			if ($p->param[$i][0]) {
				if (!strstr($p->param[$i][0], '='))
					break;# on a rencontre un vrai filtre, c'est fini
				$texte = new Texte;
				$texte->type='texte';
				$texte->texte=$p->param[$i][0];
				$param = array(0=>$texte);
				$p->param[$i][1] = $param;
				$p->param[$i][0] = NULL;
			}
		}

		// Appeler la balise #MODELE{article_pdf}{arguments}
		if (!function_exists($f = 'balise_modele'))
			$f = 'balise_modele_dist';
		return $f($p);
	}
function balise_RUBRIQUE_PDF_dist($p) {
		if (!is_array($p->param))
			$p->param=array();

		// Produire le premier argument {article_pdf}
		$texte = new Texte;
		$texte->type='texte';
		$texte->texte='rubrique_pdf';
		$param = array(0=>NULL, 1=>array(0=>$texte));
		array_unshift($p->param, $param);

		// Transformer les filtres en arguments
		for ($i=1; $i<count($p->param); $i++) {
			if ($p->param[$i][0]) {
				if (!strstr($p->param[$i][0], '='))
					break;# on a rencontre un vrai filtre, c'est fini
				$texte = new Texte;
				$texte->type='texte';
				$texte->texte=$p->param[$i][0];
				$param = array(0=>$texte);
				$p->param[$i][1] = $param;
				$p->param[$i][0] = NULL;
			}
		}

		// Appeler la balise #MODELE{article_pdf}{arguments}
		if (!function_exists($f = 'balise_modele'))
			$f = 'balise_modele_dist';
		return $f($p);
	}
?>
