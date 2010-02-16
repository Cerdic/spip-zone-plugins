<?php 
if (!defined("_ECRIRE_INC_VERSION"))
    return;
    
function balise_ENVOYERAMI($p) {
	return calculer_balise_dynamique($p, 'ENVOYERAMI', array('url'));
}

function balise_ENVOYERAMI_stat($args, $filtres) {
  	return array(isset($args[1]) ? $args[1] : $args[0], (isset($args[2]) ? $args[2] : ''));
}

function balise_ENVOYERAMI_dyn($url) {
	if (!$url 		# pas d'url passee en filtre ou dans le contexte
	AND !$url = _request('url') # ni d'url passee par l'utilisateur
	)
		$url = parametre_url(self(), '', '', '&');
	if (_request('valider') == 'ok') {
        // on verifie que tous les champs sont bien renseignes
		$page = recuperer_fond('page/mail_'. _request('lang'), array(
																	'url' => $url, 
																	'emetteur' => _request('emetteur_mail'),
																	'nom' => _request('emetteur_nom'),
																	'destinataire_nom' => _request('destinataire_nom'),
																	'message' => nl2br(_request('message')),
																)); 
		include_spip('inc/mail');
		include_spip('inc/charsets');
		$charset = $GLOBALS['meta']['charset'];
		envoyer_mail(_request('destinataire_mail'), 'Recommander à un ami', $page, _request('emetteur_mail'), "Content-Type: text/html; charset=$charset\nMIME-Version: 1.0\n");
        return array('formulaires/envoyer_ami', 0, array('url'=>$url, 'affiche'=>"non"));
    } else {
        return array('formulaires/envoyer_ami', 0, array('url'=>$url));
    }
}
?>
