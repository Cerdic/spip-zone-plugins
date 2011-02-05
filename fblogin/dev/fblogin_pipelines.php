<?php
/**
 * Plugin fblogin
 * Licence GPL (c) 2007-2009 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */







/**
 * ajouter l'uid soumis lors de la soumission du formulaire CVT editer_auteur
 * et lors de l'update d'un auteur a l'inscription en 2.1
 * 
 * @param array $flux
 * @return array
 */
function fblogin_pre_edition($flux){
	if ($flux['args']['table']=='spip_auteurs') {
		if (!is_null($fb_uid = _request('fb_uid'))) {
			include_spip('inc/fblogin');
			$flux['data']['fb_uid'] = $fb_uid;
		}
	}
	return $flux;
}



?>
