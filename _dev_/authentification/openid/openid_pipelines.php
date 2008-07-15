<?php
/*
function openid_formulaire_charger($flux){
	if ($flux['args']['form'] == 'editer_auteur'){
	}
	return $flux;
}

function openid_formulaire_verifier($flux){
	if ($flux['args']['form'] == 'editer_auteur'){
#		if (!is_openid(_request('openid')) {
#			$flux['data']['openid'] = _T('openid:erreur_openid');
#		}
	}
	return $flux;
}
*/

function openid_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		include_spip('public/assembler');
		$openid = recuperer_fond('formulaires/inc-openid', $flux['args']);
		$flux['data'] = preg_replace('%(<li class="editer_email(.*?)</li>)%is', '$1'."\n".$openid, $flux['data']);
	}
	return $flux;
}
?>
