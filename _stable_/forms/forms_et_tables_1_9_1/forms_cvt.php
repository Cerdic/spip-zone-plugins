<?php

// creer le contexte de traitement des formulaires dynamiques en charger/valider/modifier
// et les hidden de l'url d'action
// http://doc.spip.org/@balise_ACTION_FORMULAIRE
function balise_ACTION_FORMULAIRE($p){
	$_url = interprete_argument_balise(1,$p);
	if (!$_form = interprete_argument_balise(2,$p)){
		$_form = "'".addslashes(basename($p->descr['sourcefile'],'.html'))."'";
	}
	$p->code = "";

	if (strlen($_url))
		$p->code .= " . (form_hidden($_url))";
	if (strlen($_form))
		$p->code .=
		// envoyer le nom du formulaire que l'on traite
		". '<input type=\'hidden\' name=\'formulaire_action\' value=\'' . $_form . '\' />'"
		// transmettre les eventuels args de la balise formulaire
		. ". '<input type=\'hidden\' name=\'formulaire_action_args\' value=\'' . @\$Pile[0]['formulaire_args']. '\' />'"
		. ". (@\$Pile[0]['_hidden']?@\$Pile[0]['_hidden']:'')";

	if (strlen($p->code))
		$p->code = "'<div>'" . $p->code . " . '</div>'";
	$p->interdire_scripts = false;
	return $p;
}

// http://doc.spip.org/@traiter_formulaires_dynamiques
function traiter_formulaires_dynamiques(){
	static $done = false;
	if (!$done) {
		if ($action = _request('action')) {
			define('_ESPACE_PRIVE', true);
			include_spip('base/abstract_sql'); // chargement systematique pour les actions
			include_spip('inc/autoriser'); // chargement systematique pour les actions
			include_spip('inc/headers');
			if (($v=_request('var_ajax'))
			 AND ($v!=='form')
			 AND ($args = _request('var_ajax_env'))
			 AND ($url = _request('redirect'))){
				$url = parametre_url($url,'var_ajax',$v,'&');
				$url = parametre_url($url,'var_ajax_env',$args,'&');
				set_request('redirect',$url);
			}
			$var_f = charger_fonction($action, 'action');
			$var_f();
			if ($GLOBALS['redirect']
			OR $GLOBALS['redirect'] = _request('redirect')){
				$url = urldecode($GLOBALS['redirect']);
				if (($v=_request('var_ajax'))
				 AND ($v!=='form')
				 AND ($args = _request('var_ajax_env'))) {
					$url = parametre_url($url,'var_ajax',$v,'&');
					$url = parametre_url($url,'var_ajax_env',$args,'&');
				}
				redirige_par_entete($url);
			}
			if (!headers_sent()
			AND !ob_get_length())
				http_status(204); // No Content
			exit;
		}

		// traiter les appels de bloc ajax (ex: pagination)
		if ($v = _request('var_ajax')
		AND $v !== 'form'
		AND $args = _request('var_ajax_env')) {
			include_spip('inc/filtres');
			if ($args = decoder_contexte_ajax($args)
			AND $fond = $args['fond']) {
				include_spip('public/parametrer');
				$contexte = calculer_contexte();
				$contexte = array_merge($args, $contexte);
				$page = evaluer_fond($fond,$contexte);
				include_spip('inc/actions');
				ajax_retour($page['texte']);
			}
			else {
				include_spip('inc/actions');
				ajax_retour('signature ajax incorrecte 1');
			}
			exit();
		}

		// traiter les formulaires dynamniques charger/verifier/traiter
		if ($form = _request('formulaire_action')
		AND $args = _request('formulaire_action_args')) {
			include_spip('inc/filtres');
			if ($args = decoder_contexte_ajax($args)
			AND $args['form'] == $form) {
				$verifier = charger_fonction("verifier","formulaires/$form/",true);
				$_POST["erreurs_$form"] = pipeline(
				  'formulaire_verifier',
					array(
						'args'=>array('form'=>$form,'args'=>$args),
						'data'=>$verifier?call_user_func_array($verifier,$args):array())
					);
				if ((count($_POST["erreurs_$form"])==0)){
					$rev = "";
					if ($traiter = charger_fonction("traiter","formulaires/$form/",true))
						$rev = call_user_func_array($traiter,$args);
					$rev = pipeline(
				  'formulaire_traiter',
					array(
						'args'=>array('form'=>$form,'args'=>$args),
						'data'=>$rev)
					);
					// traiter peut retourner soit un message, soit un array(editable,message)
					if (is_array($rev)) {
						$_POST["editable_$form"] = $rev[0];
						$_POST["message_ok_$form"] = $rev[1];
					} else
						$_POST["message_ok_$form"] = $rev;
				}
				// si le formulaire a ete soumis en ajax, on le renvoie direct !
				if (_request('var_ajax')){
					if (find_in_path('formulaire_.php','balise/',true)) {
						include_spip('inc/actions');
						array_unshift($args,$form);
						ajax_retour(inclure_balise_dynamique(call_user_func_array('balise_formulaire__dyn',$args),false),false);
						exit;
					}
				}
			} else {
				include_spip('inc/actions');
				ajax_retour('signature ajax incorrecte 2');
				exit;
			}
		}
		$done = true;
	}
}

// Encoder un contexte pour l'ajax, le signer avec une cle, le crypter
// avec le secret du site, le gziper si possible...
// l'entree peut etre serialisee (le #ENV** des fonds ajax et ajax_stat)
// http://doc.spip.org/@encoder_contexte_ajax
function encoder_contexte_ajax($c) {
	if (is_string($c)
	AND !is_null(@unserialize($c)))
		$c = unserialize($c);

	// supprimer les parametres debut_x
	// pour que la pagination ajax ne soit pas plantee
	// si on charge la page &debut_x=1 : car alors en cliquant sur l'item 0,
	// le debut_x=0 n'existe pas, et on resterait sur 1
	foreach ($c as $k => $v)
		if (strpos($k,'debut_') === 0)
			unset($c[$k]);

	include_spip("inc/securiser_action");
	$cle = calculer_cle_action($c);
	$c = serialize(array($c,$cle));
	if (function_exists('gzdeflate'))
		$c = gzdeflate($c);
	$c = _xor($c);
	$c = base64_encode($c);
	return $c;
}

// la procedure inverse de encoder_contexte_ajax()
// http://doc.spip.org/@decoder_contexte_ajax
function decoder_contexte_ajax($c) {
	include_spip("inc/securiser_action");

	$c = @base64_decode($c);
	$c = _xor($c);
	if (function_exists('gzinflate'))
		$c = @gzinflate($c);
	list($env, $cle) = @unserialize($c);

	if ($cle == calculer_cle_action($env))
		return $env;
}

// encrypter/decrypter un message
// http://www.php.net/manual/fr/language.operators.bitwise.php#81358
// http://doc.spip.org/@_xor
function _xor($message, $key=null){
	if (is_null($key)) {
		include_spip("inc/securiser_action");
		$key = pack("H*", calculer_cle_action('_xor'));
	}

	$keylen = strlen($key);
	$messagelen = strlen($message);
	for($i=0; $i<$messagelen; $i++)
		$message[$i] = ~($message[$i]^$key[$i%$keylen]);

	return $message;
}


// Le secret du site doit rester aussi secret que possible, et est eternel
// On ne doit pas l'exporter
// http://doc.spip.org/@secret_du_site
function secret_du_site() {
	if (!isset($GLOBALS['meta']['secret_du_site'])
	OR !strlen($GLOBALS['meta']['secret_du_site'])
	) {
		include_spip('inc/acces');
		ecrire_meta('secret_du_site', creer_uniqid(), 'non');
	}
	return $GLOBALS['meta']['secret_du_site'];
}

// http://doc.spip.org/@calculer_cle_action
function calculer_cle_action($action) {
	return md5($action . secret_du_site());
}

// http://doc.spip.org/@verifier_cle_action
function verifier_cle_action($action, $cle) {
	return ($cle == calculer_cle_action($action));
}


// http://doc.spip.org/@redirige_formulaire
function redirige_formulaire($url, $equiv = '') {
	if (!_request('var_ajax')
	  && !_request('var_ajaxcharset')
		&& !headers_sent()
		&& !$_GET['var_mode']){
		include_spip('inc/headers');
		redirige_par_entete(str_replace('&amp;','&',$url), $equiv);
	}
	else {
		$url = strtr($url, "\n\r", "  ");
		# en theorie on devrait faire ca tout le temps, mais quand la chaine
		# commence par ? c'est imperatif, sinon l'url finale n'est pas la bonne
		if ($url[0]=='?')
			$url = url_de_base().$url;
		$url = str_replace('&amp;','&',$url);
		spip_log("redirige formulaire ajax: $url");
		include_spip('inc/filtres');
		return
		"<script type='javascript'>window.location='$url';</script>"
		. http_img_pack('searching.gif','');
	}
}
?>