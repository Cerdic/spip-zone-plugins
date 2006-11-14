<?php 

if (!function_exists("introduction")) {

	function introduction ($type, $texte, $chapo='', $descriptif='') {
		switch ($type) {
			case 'articles':
				if ($descriptif)
					return propre($descriptif);
				else if (substr($chapo, 0, 1) == '=')	// article virtuel
					return '';
				else
					return PtoBR(propre(supprimer_tags(couper_intro(pas_de_qcm_introduction($chapo."\n\n\n".$texte), 500))));
				break;
			case 'breves':
				return PtoBR(propre(supprimer_tags(couper_intro(pas_de_qcm_introduction($texte), 300))));
				break;
			case 'forums':
				return PtoBR(propre(supprimer_tags(couper_intro(pas_de_qcm_introduction($texte), 600))));
				break;
			case 'rubriques':
				if ($descriptif)
					return propre(pas_de_qcm_introduction($descriptif));
				else
					return PtoBR(propre(supprimer_tags(couper_intro(pas_de_qcm_introduction($texte), 600))));
				break;
		}
	}
} 

function pas_de_qcm_introduction($texte) {
	$texte = preg_replace("/<qcm>[\s\n\t]*\nT\s+([^\n]*)/", "[\\1]\n<qcm>", $texte);
	$texte = preg_replace(',<(qcm)>(.*)<\/\1>,UimsS', '', $texte);
	return $texte;
} 
?>