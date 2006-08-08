<?php
//
// #HTTP_IMG_PACK{$img, $alt, $att, $title=''} -> http_img_pack($img, $alt, $att, $title)
//
function minipres_http_img_pack($img, $alt, $att, $title=''){
	include_spip("inc/minipres");
	return http_img_pack($img, $alt, $att, $title);
}
function balise_HTTP_IMG_PACK_dist($p) {

	if ($p->param && !$p->param[0][0]) {
		$p->code =  calculer_liste($p->param[0][1],
					$p->descr,
					$p->boucles,
					$p->id_boucle);

		$args =  calculer_liste($p->param[0][2],
					$p->descr,
					$p->boucles,
					$p->id_boucle);
		$p->code .= ','.$args;
			
		$args =  calculer_liste($p->param[0][3],
					$p->descr,
					$p->boucles,
					$p->id_boucle);
		$p->code .= ','.$args;
		
		$args =  calculer_liste($p->param[0][4],
					$p->descr,
					$p->boucles,
					$p->id_boucle);
		$p->code .= ','.$args;
	}

	$p->code = 'http_img_pack(' . $p->code .')';

	#$p->interdire_scripts = true;
	return $p;
}

function minpres_http_wrapper($img){
	include_spip("inc/minipres");
	return http_wrapper($img);
}
function balise_HTTP_WRAPPER_dist($p) {

	if ($p->param && !$p->param[0][0]) {
		$p->code =  calculer_liste($p->param[0][1],
					$p->descr,
					$p->boucles,
					$p->id_boucle);
	}

	$p->code = 'minpres_http_wrapper(' . $p->code .')';

	#$p->interdire_scripts = true;
	return $p;
}

$GLOBALS['dossier_squelettes'] .= ":".find_in_path('dist_back/');

?>