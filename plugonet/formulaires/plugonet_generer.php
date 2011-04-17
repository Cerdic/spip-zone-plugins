<?php

function formulaires_plugonet_generer_charger() {
	if (!_request('pluginxml') OR $_SERVER['REQUEST_MODE'] == 'POST')
		return 	array();
	else return formulaires_plugonet_generer_traiter();
}

function formulaires_plugonet_generer_verifier() {
	$erreurs = array();
	$obligatoires = array('pluginxml');
	foreach ($obligatoires as $_obligatoire){
		if(!_request($_obligatoire)){
			$erreurs[$_obligatoire] = _T('langonet:message_nok_champ_obligatoire');
		}
	}
	return $erreurs;
}

function formulaires_plugonet_generer_traiter() {
	// Recuperation des champs du formulaire
	$pluginxml = _request('pluginxml');
	$forcer = (_request('forcer')) ? true : false;
	$simuler = (_request('simuler')) ? true : false;

	// Generation du fichier
 	$generer = charger_fonction('plugonet_generer','inc');
 	list($erreurs, $commandes) = $generer($pluginxml, $forcer, $simuler);

	// Formatage et affichage des resultats
	// -- Message global sur la generation des fichiers : toujours ok aujourd'hui
	// -- Texte des resultats par fichier traite
	$retour = array();
 	list($message, $analyse) = formater_resultats($erreurs);
 	$retour['message_ok']['resume'] = $message;
 	$retour['message_ok']['resultats'] = $analyse;
	$retour['editable'] = true;
	
	return $retour;
}

function formater_resultats($erreurs) {
	// Nombre de fichiers traites
	$nb_fichiers = count($erreurs);	

	// Determiniation des compteurs d'erreurs et de notices
	$nb_errlec = $nb_errinf = $nb_errval = $nb_notval = 0;
	$texte = '';
	foreach ($erreurs as $_pluginxml => $_erreurs) {
		// Erreur : chaque type d'erreur est exclusif
		$nb_errlec = ($_erreurs['erreur_lecture_pluginxml']) ? $nb_errlec + 1 : $nb_errlec;
		$nb_errinf = ($_erreurs['erreur_information_pluginxml']) ? $nb_errinf + 1 : $nb_errinf;
		$nb_errval = (count($_erreurs['erreur_validation_paquetxml']) > 0) ? $nb_errval + 1 : $nb_errval;
		// Notice : pseudo-validation du plugin.xml
		$nb_notval = (count($_erreurs['notice_validation_pluginxml']) > 0) ? $nb_notval + 1 : $nb_notval;
		// texte de retour
		if ($_erreurs['erreur_lecture_pluginxml'])
			$texte .= 
				'<div class="error">'  . "\n" . 
				_T('plugonet:message_nok_lecture_pluginxml', array('nb' => $_pluginxml . ' :')) .
				'</div>';
		else if ($_erreurs['erreur_information_pluginxml'])
			$texte .= 
				'<div class="error">'  . "\n" . 
				_T('plugonet:message_nok_information_pluginxml', array('nb' => $_pluginxml . ' :')) .
				'</div>';
		else if ($_erreurs['erreur_validation_paquetxml'])
			$texte .= 
				'<div class="error">'  . "\n" . 
				_T('plugonet:message_nok_validation_paquetxml', array('nb' => $_pluginxml . ' :')) .
				'</div>';
		else
			$texte .= 
				'<div class="success">'  . "\n" . 
				_T('plugonet:message_ok_generation_paquetxml', array('nb' => $_pluginxml . ' :')) .
				'</div>';
	}
	
	$nb_nok = $nb_errlec + $nb_errinf + $nb_errval;
	$nb_ok = $nb_fichiers - $nb_nok;
	if ($nb_nok> 0) {
		$details = 
			'<br />' . 
			($nb_errlec > 0 
				? '-> ' . un_ou_plusieurs($nb_errlec, 'plugonet:message_nok_lecture_pluginxml')
				: '') .
			($nb_errinf > 0 
				? '-> ' . un_ou_plusieurs($nb_errinf, 'plugonet:message_nok_information_pluginxml')
				: '') .
			($nb_errval > 0 
				? '-> ' . un_ou_plusieurs($nb_errval, 'plugonet:message_nok_validation_paquetxml')
				: '') .
			($nb_ok > 0 
				? '<br />-> ' . un_ou_plusieurs($nb_ok, 'plugonet:message_ok_generation_paquetxml')
				: '');
	}
	else {
		$details = 
			'<br />-> ' . 
			un_ou_plusieurs($nb_fichiers, 'plugonet:message_ok_generation_paquetxml') .
			($nb_notval > 0 
				? '<br />-> ' . un_ou_plusieurs($nb_notval, 'plugonet:message_nok_validation_pluginxml')
				: '');
	}
	$message = un_ou_plusieurs(
					$nb_fichiers,
					'plugonet:message_generation_paquetxml',
					array('details' => $details));

	return array($message, $texte);
}


/**
 * Gestion du singulier et du pluriel pour une chaine de langue
 * Prend aussi en compte les langues supportant le duel
 *
 * @param int				$nb
 * @param string			$chaine_un
 * @param array / string	$options
 * @return string
 */

// $nb					=> le compteur !
// $chaine_un			=> item de langue pour le singulier (format php)
// $extras				=> parametres additionnels valables pour le singulier et le pluriel
//						   la cle 'nb' n'est pas consideree comme un parametre mais comme un 
//						   renommage de parametre valeur par defaut fixe a @nb@
//						   Si on veut juste renommer 'nb' on peut passer la chaine directe
function un_ou_plusieurs($nb, $chaine_un, $options=array()) {
	static $spip_lang_duel = array('ar');
	global $spip_lang;

	if (!$nb=intval($nb)) 
		return '';

	// Par defaut, le parametre eventuel designant la valeur dans la chaine est reperer par @nb@
	// On teste si il est precise une option differente : dans ce cas on supprime aussi cette cle
	// des parametres optionnels
	$params = array('nb' => $nb);
	if (is_array($options)) {
		if (array_key_exists('nb', $options)) {
			$params = array($options['nb'] => $nb);
			unset($options['nb']);
		}
		$params = array_merge($params, $options);
	}
	else if ($options)
		$params = array($options => $nb);

	// Traitement des langues qui supportent le duel : on verifie que la chaine existe
	$texte = '';
	if (in_array($spip_lang, $spip_lang_duel) AND ($nb == 2)) {
		$item = $chaine_un . '_duel';
		$texte = _T($item, $params);
		// On verifie que l'item duel existe bien et que le texte retourne est donc le bon
		// Sinon on essayera d'utiliser l'item pluriel comme pour les autres langues
		// -- Le test n'est pas terrible mais c'est la seule solution aujourd'hui !!!
		$item_non_trouve = str_replace('_', ' ', (($n = strpos($item,':')) === false ? $item : substr($item, $n+1)));
		if (!$texte OR ($texte == $item_non_trouve))
			$texte = '';
	}
	
	// Traitement des autres cas incluant aussi l'absence de chaine duel pour une langue le supportant
	if (!$texte)
		if ($nb == 1)
			$texte = _T($chaine_un, $params);
		else
			$texte = _T($chaine_un . '_pluriel', $params);
	
	return $texte;
}

?>