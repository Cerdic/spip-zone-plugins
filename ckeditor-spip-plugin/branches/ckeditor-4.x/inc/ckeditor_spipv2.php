<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function ckeditor_barre_onglets($rubrique, $ongletCourant, $class="barre_onglet"){
	include_spip('inc/presentation');
	include_spip('inc/ckeditor_constantes') ;

	$res = '';

	$cpt = 0 ;

	foreach(definir_barre_onglets($rubrique) as $exec => $onglet) {
		if ($cpt==_CKE_MAX_ONGLETS) {
			$cpt = 0 ;
			$res .= '</tr><tr>' ;
		} else {
			$cpt++ ;
		}

		$url= $onglet->url ? $onglet->url : generer_url_ecrire($exec);
		$res .= onglet(_T($onglet->libelle), $url, $exec, $ongletCourant, $onglet->icone);

	}

	return  !$res ? '' : (debut_onglet($class) . $res . fin_onglet());
}


?>
