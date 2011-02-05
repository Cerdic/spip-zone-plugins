<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2010 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */

$GLOBALS['liste_des_authentifications']['fblogin'] = 'fblogin';


/**
 * Afficher l'openid sur le formulaire de login
 *	->Utilise uniquement pour spip 2.0.x
 * et sur le form inscription
 *
 * @param <type> $flux
 * @return <type>
 */
function fblogin_recuperer_fond($flux) {
	if ($flux['args']['fond']=='formulaires/login' AND version_compare($GLOBALS['spip_version_branche'],"2.1.0 dev","<")){
		include_spip('inc/facebook');
		$flux['data']['texte'] = openid_login_form($flux['data']['texte'],$flux['data']['contexte']);
	}
	if ($flux['args']['fond']=='formulaires/inscription'){

		$insc = recuperer_fond('formulaires/inc-inscription-fblogin',$flux['data']['contexte']);
		$flux['data']['texte'] = str_replace('<ul',$insc . '<ul',$flux['data']['texte']);

	}
	return $flux;
}

?>