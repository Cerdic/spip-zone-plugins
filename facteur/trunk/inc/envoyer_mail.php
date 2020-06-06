<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Inc\Envoyer_mails
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_LOG_FACTEUR')) {
	define('_LOG_FACTEUR',_LOG_INFO);
}
if (!defined('_EMAIL_AUTO_CONVERT_TEXT_TO_HTML')) {
	define('_EMAIL_AUTO_CONVERT_TEXT_TO_HTML', true);
}

include_spip('classes/facteur');
// inclure le fichier natif de SPIP, pour les fonctions annexes
include_once _DIR_RESTREINT."inc/envoyer_mail.php";

/**
 * Extraire automatiquement le sujet d'un message si besoin
 * @param $message_html
 * @param string $message_texte
 * @return string
 */
function facteur_extraire_sujet($message_html, $message_texte = '') {
	if (strlen($message_html = trim($message_html))) {
		// dans ce cas on ruse un peu : extraire le sujet du title
		if (preg_match(",<title>(.*)</title>,Uims",$message_html,$m))
			return ($sujet = $m[1]);
		else {
			// fallback, on prend le body si on le trouve
			if (preg_match(",<body[^>]*>(.*)</body>,Uims", $message_html, $m)){
				$message_html = $m[1];
			}
			// et on le nettoie/decoupe comme du texte
			$message_texte = textebrut($message_html);
		}
	}
	else {
		$message_texte = supprimer_tags($message_texte);
	}

	// et on extrait la premiere ligne de vrai texte...
	// nettoyer le html et les retours chariots
	$message_texte = str_replace("\r\n", "\r", $message_texte);
	$message_texte = str_replace("\r", "\n", $message_texte);
	$message_texte = trim($message_texte);
	// decouper
	$message_texte = explode("\n", $message_texte);

	// extraire la premiere ligne de texte brut
	return ($sujet = array_shift($message_texte));
}


/**
 * @param array|string $destinataire
 *   si array : un tableau de mails
     si string : un mail ou une liste de mails séparés par des virgules
 * @param string $sujet
 * @param string|array $message
 *   au format string, c'est un corps d'email au format texte, comme supporte nativement par le core
 *   au format array, c'est un corps etendu qui peut contenir
 *     string texte : le corps d'email au format texte
 *     string html : le corps d'email au format html
 *     string from : email de l'envoyeur (prioritaire sur argument $from de premier niveau, deprecie)
 *     string nom_envoyeur : un nom d'envoyeur pour completer l'email from
 *     string cc : destinataires en copie conforme
 *     string bcc : destinataires en copie conforme cachee
 *     string|array repondre_a : une ou plusieurs adresses à qui répondre.
 *       On peut aussi donner une liste de tableaux du type :
 *         array('email' => 'test@exemple.com', 'nom' => 'Adresse de test')
 *       pour spécifier un nom d'envoyeur pour chaque adresse.
 *     string nom_repondre_a : le nom d'envoyeur pour compléter l'email repondre_a
 *     string adresse_erreur : addresse de retour en cas d'erreur d'envoi
 *     array pieces_jointes : listes de pieces a embarquer dans l'email, chacune au format array :
 *       string chemin : chemin file system pour trouver le fichier a embarquer
 *       string nom : nom du document tel qu'apparaissant dans l'email
 *       string encodage : encodage a utiliser, parmi 'base64', '7bit', '8bit', 'binary', 'quoted-printable'
 *       string mime : mime type du document
 *     array headers : tableau d'en-tetes personalises, une entree par ligne d'en-tete
 *     bool exceptions : lancer une exception en cas d'erreur (false par defaut)
 *     bool important : un flag pour signaler les messages important qui necessitent un feedback en cas d'erreur
 * @param string $from (deprecie, utiliser l'entree from de $message)
 * @param string $headers (deprecie, utiliser l'entree headers de $message)
 * @return bool
 * @throws Exception
 */
function inc_envoyer_mail($destinataire, $sujet, $message, $from = "", $headers = "") {
	$message_html	= '';
	$message_texte	= '';
	$nom_envoyeur = $cc = $bcc = $repondre_a = '';
	$pieces_jointes = array();
	$important = false;

	// si $message est un tableau -> fonctionnalites etendues
	// avec entrees possible : html, texte, pieces_jointes, nom_envoyeur, ...
	if (is_array($message)) {
		// si on fournit un $message['html'] deliberemment vide, c'est qu'on n'en veut pas, et donc on restera au format texte
		$message_html   = isset($message['html']) ? ($message['html'] ? $message['html'] : ' ') : "";
		$message_texte  = isset($message['texte']) ? nettoyer_caracteres_mail($message['texte']) : "";
		$pieces_jointes = isset($message['pieces_jointes']) ? $message['pieces_jointes'] : array();
		$nom_envoyeur   = isset($message['nom_envoyeur']) ? $message['nom_envoyeur'] : "";
		$from = isset($message['from']) ? $message['from']: $from;
		$cc   = isset($message['cc']) ? $message['cc'] : "";
		$bcc  = isset($message['bcc']) ? $message['bcc'] : "";
		$repondre_a = isset($message['repondre_a']) ? $message['repondre_a'] : "";
		$nom_repondre_a = isset($message['nom_repondre_a']) ? $message['nom_repondre_a'] : '';
		$adresse_erreur = isset($message['adresse_erreur']) ? $message['adresse_erreur'] : "";
		$headers = isset($message['headers']) ? $message['headers'] : $headers;
		if (is_string($headers)){
			$headers = array_map('trim',explode("\n",$headers));
			$headers = array_filter($headers);
		}
		$important = (isset($message['important']) ? !!$message['important'] : $important);
	}
	// si $message est une chaine -> compat avec la fonction native SPIP
	// gerer le cas ou le corps est du html avec un Content-Type: text/html dans les headers
	else {
		if (preg_match(',Content-Type:\s*text/html,ims',$headers)){
			$message_html	= $message;
		}
		else {
			// Autodetection : tester si le mail est en HTML
			if (strpos($headers,"Content-Type:")===false
				AND strpos($message,"<")!==false // eviter les tests suivants si possible
				AND $ttrim = trim($message)
				AND substr($ttrim,0,1)=="<"
				AND substr($ttrim,-1,1)==">"
				AND stripos($ttrim,"</html>")!==false){

				$message_html	= $message;
			}
			// c'est vraiment un message texte
			else
				$message_texte	= nettoyer_caracteres_mail($message);
		}
		$headers = array_map('trim',explode("\n",$headers));
		$headers = array_filter($headers);
	}

	if(!strlen($sujet)){
		$sujet = facteur_extraire_sujet($message_html, $message_texte);
	}

	$sujet = nettoyer_titre_email($sujet);

	// si le mail est en texte brut, on l'encapsule dans un modele surchargeable
	// pour garder le texte brut, il suffit de faire un modele qui renvoie uniquement #ENV*{texte}
	if ($message_texte AND ! $message_html AND _EMAIL_AUTO_CONVERT_TEXT_TO_HTML){
		$message_html = recuperer_fond("emails/texte",array('texte'=>$message_texte,'sujet'=>$sujet));
	}
	$message_html = trim($message_html);

	// si le mail est en HTML sans alternative, la generer
	if ($message_html AND !$message_texte){
		$message_texte = facteur_mail_html2text($message_html);
	}

	$exceptions = false;
	if (is_array($message) AND isset($message['exceptions'])){
		$exceptions = $message['exceptions'];
	}

	// mode TEST : forcer l'email
	if (defined('_TEST_EMAIL_DEST')) {
		if (!_TEST_EMAIL_DEST){
			spip_log($e=_T('facteur:erreur_envoi_bloque_constante'), 'mail.' . _LOG_ERREUR);
			if ($exceptions) {
				throw new Exception($e);
			}
			return false;
		}
		else
			$destinataire = _TEST_EMAIL_DEST;
	}

	// plusieurs destinataires peuvent etre fournis separes par des virgules
	// c'est un format standard dans l'envoi de mail
	// les passer au format array pour phpMailer
	// mais ne pas casser si on a deja un array en entree
	// si pas destinataire du courriel on renvoie false (eviter les warning PHP : ligne 464 de phpmailer-php5/class.phpmailer.php
	// suppression des adresses de courriels invalides, si aucune valide, renvoyer false (eviter un warning PHP : ligne 464 de phpmailer-php5/class.phpmailer.php)
	if (is_array($destinataire))
		$destinataire = implode(", ",$destinataire);

	if(strlen($destinataire) > 0){
		$destinataire = array_map('trim',explode(",",$destinataire));
		foreach ($destinataire as $key => $value) {
			if(!email_valide($value))
				unset($destinataire[$key]);
		}
		if(count($destinataire) == 0) {
			spip_log($e="Aucune adresse email de destination valable pour l'envoi du courriel.", 'mail.' . _LOG_ERREUR);
			if ($exceptions) {
				throw new Exception($e);
			}
			return false;
		}
	}
	else {
		if ($bcc) {
			// On peut envoyer de mail que en bcc
			$destinataire = '';
		} else {
			spip_log($e="Aucune adresse email de destination valable pour l'envoi du courriel.", 'mail.' . _LOG_ERREUR);
			if ($exceptions) {
				throw new Exception($e);
			}
			return false;
		}
	}

	// On crée l'objet Facteur (PHPMailer) pour le manipuler ensuite
	$options = array();
	if ($exceptions){
		$options['exceptions'] = $exceptions;
	}
	include_spip('inc/facteur');
	$facteur = facteur_factory($options);

	$facteur->setDest($destinataire);
	$facteur->setObjet($sujet);
	$facteur->setMessage($message_html, $message_texte);

	// On ajoute le courriel de l'envoyeur s'il est fournit par la fonction
	if (empty($from) AND empty($facteur->From)) {
		$from = $GLOBALS['meta']["email_envoi"];
		if (empty($from) OR !email_valide($from)) {
			spip_log("Meta email_envoi invalide. Le mail sera probablement vu comme spam.", 'mail.' . _LOG_ERREUR);
			if(is_array($destinataire) && count($destinataire) > 0)
				$from = $destinataire[0];
			else
				$from = $destinataire;
		}
	}

	// "Marie Toto <Marie@toto.com>"
	if (preg_match(",^([^<>\"]*)<([^<>\"]+)>$,i",$from,$m)){
		$nom_envoyeur = trim($m[1]);
		$from = trim($m[2]);
	}
	if (!empty($from)){
		$facteur->From = $from;
		// la valeur par défaut de la config n'est probablement pas valable pour ce mail,
		// on l'écrase pour cet envoi
		$facteur->FromName = '';
	}

	// On ajoute le nom de l'envoyeur s'il fait partie des options
	if ($nom_envoyeur){
		$facteur->FromName = $nom_envoyeur;
	}

	// Si plusieurs emails dans le from, pas de Name !
	if (strpos($facteur->From,",")!==false){
		$facteur->FromName = "";
	}

	// S'il y a des copies à envoyer
	if ($cc){
		if (is_array($cc))
			foreach ($cc as $courriel)
				$facteur->AddCC($courriel);
		else
			$facteur->AddCC($cc);
	}

	// S'il y a des copies cachées à envoyer
	if ($bcc){
		if (is_array($bcc))
			foreach ($bcc as $courriel)
				$facteur->AddBCC($courriel);
		else
			$facteur->AddBCC($bcc);
	}

	// S'il y a une adresse de reply-to
	if ($repondre_a) {
		if (is_array($repondre_a)) {
			foreach ($repondre_a as $courriel) {
				if (is_array($courriel)) {
					$facteur->AddReplyTo($courriel['email'], $courriel['nom']);
				} else {
					$facteur->AddReplyTo($courriel);
				}
			}
		} elseif ($nom_repondre_a) {
			$facteur->AddReplyTo($repondre_a, $nom_repondre_a);
		} else {
			$facteur->AddReplyTo($repondre_a);
		}
	}

	// S'il y a des pièces jointes on les ajoute proprement
	if (count($pieces_jointes)) {
		foreach ($pieces_jointes as $piece) {
			if (!empty($piece['chemin']) and file_exists($piece['chemin'])) {
				$facteur->AddAttachment(
					$piece['chemin'],
					isset($piece['nom']) ? $piece['nom']:'',
					(isset($piece['encodage']) AND in_array($piece['encodage'],array('base64', '7bit', '8bit', 'binary', 'quoted-printable'))) ? $piece['encodage']:'base64',
					isset($piece['mime']) ? $piece['mime']:Facteur::_mime_types(pathinfo($piece['chemin'], PATHINFO_EXTENSION))
				);
			}
			else {
				spip_log("Piece jointe manquante ignoree : ".json_encode($piece),'facteur' . _LOG_ERREUR);
			}
		}
	}

	// Si une adresse email a été spécifiée pour les retours en erreur, on l'ajoute
	if (!empty($adresse_erreur))
		$facteur->Sender = $adresse_erreur;

	if ($important) {
		$facteur->setImportant();
	}

	// si entetes personalises : les ajouter
	// attention aux collisions : si on utilise l'option cc de $message
	// et qu'on envoie en meme temps un header Cc: xxx, yyy
	// on aura 2 lignes Cc: dans les headers
	if (!empty($headers)) {
		foreach($headers as $h){
			// verifions le format correct : il faut au moins un ":" dans le header
			// et on filtre le Content-Type: qui sera de toute facon fourni par facteur
			if (strpos($h,":")!==false
			  AND strncmp($h,"Content-Type:",13)!==0)
				$facteur->AddCustomHeader($h);
		}
	}

	// On passe dans un pipeline pour modifier tout le facteur avant l'envoi
	$facteur = pipeline('facteur_pre_envoi', $facteur);

	// Et c'est parti on envoie enfin
	$backtrace = facteur_backtrace();
	$trace = $facteur->getMessageLog();
	spip_log("mail via facteur\n$trace",'mail'._LOG_FACTEUR);
	spip_log("mail\n$backtrace\n$trace",'facteur'._LOG_FACTEUR);

	// si c'est un mail important, preparer le forward a envoyer en cas d'echec
	// mais on delegue la gestion de cet envoi au facteur qui est le seul a savoir quoi faire
	// en fonction de la reponse et du modus operandi pour connaitre le status du message
	if ($important and $dest_alertes = $facteur->Sender) {
		$dest = (is_array($destinataire) ? implode(', ', $destinataire) : $destinataire);
		$sujet_alerte = _T('facteur:sujet_alerte_mail_fail', array('dest' => $dest, 'sujet' => $sujet));
		$args = func_get_args();
		$args[0] = $dest_alertes;
		$args[1] = $sujet_alerte;
		$args[2]['important'] = false; // ne pas faire une alerte sur l'envoi de l'alerte etc.
		if (!empty($args[2]['pieces_jointes'])) {
			foreach ($args[2]['pieces_jointes'] as $k=>$pj) {
				// passer les chemins en absolus car on sait pas si l'alerte sera lancee depuis le meme cote racine/ecrire
				$args[2]['pieces_jointes'][$k]['chemin'] = realpath($pj['chemin']);
			}
		}
		$facteur->setSendFailFunction('envoyer_mail', $args, 'inc/');
	}

	$retour = $facteur->Send();

	if (!$retour){
		spip_log("Erreur Envoi mail via Facteur : ".print_r($facteur->ErrorInfo,true),'mail'._LOG_ERREUR);
		// si le mail est important, c'est le facteur qui aura gere l'envoi de l'alerte fail
	}

	return $retour ;
}

/**
 * Retourne la pile de fonctions utilisée pour envoyer un mail
 *
 * @note
 *     Ignore les fonctions `include_once`, `include_spip`, `find_in_path`
 * @return array|string
 *     pile d'appel
 **/
function facteur_backtrace($limit=10) {
	$trace = debug_backtrace();
	$caller = array_shift($trace);
	while (count($trace) and (empty($trace[0]['file']) or $trace[0]['file'] === $caller['file'] or $trace[0]['file'] === __FILE__)) {
		array_shift($trace);
	}

	$message = count($trace) ? $trace[0]['file'] . " L" . $trace[0]['line'] : "";
	$f = array();
	while (count($trace) and $t = array_shift($trace) and count($f)<$limit) {
		if (in_array($t['function'], array('include_once', 'include_spip', 'find_in_path'))) {
			break;
		}
		$f[] = $t['function'];
	}
	if (count($f)) {
		$message .= " [" . implode("(),", $f) . "()]";
	}

	return $message;
}
