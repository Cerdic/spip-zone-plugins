<?php
/*
+--------------------------------------------+
| ICOP 1.0 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| action ajout de couleur dans l'interface
+--------------------------------------------+
*/

function action_habillages_ecrirecouleur() {
	global $_POST;
	global $redirect;
	global $action, $arg, $hash, $id_auteur;
	# arg => rien !
	
	// controle action
	include_spip('inc/securiser_action');
	if (!verifier_action_auteur("$action $arg", $hash, $id_auteur)) {
		include_spip('inc/minipres');
		minipres(_T('info_acces_interdit'));
	}
	$ac=_request('ajout_coul');
	
	if(count($ac)>0) {
		$ac=implode(',',$ac);
	} else { $ac=''; }
	ecrire_meta('habillages_couleurs',$ac);
	ecrire_metas();

	
	redirige_par_entete(rawurldecode($redirect));	
}
?>
