<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip("inc/config");

/**
 * Envoi une newsletter a un destinataire
 *
 * @param array $destinataire
 *   description du destinataire
 *     string email
 *     string nom
 *     array listes
 *     string lang
 *     string status : on|pending|off
 *     string url_unsubscribe : url de desabonnement
 *
 *
 * @param array|string $corps
 *   string id de la newsletter
 *ou array contenu de la newsletter
 *     string sujet
 *     string html
 *     string texte
 *     string from : email de l'envoyeur (prioritaire sur argument $from de premier niveau, deprecie)
 *     string nom_envoyeur : un nom d'envoyeur pour completer l'email from
 *     string cc : destinataires en copie conforme
 *     string bcc : destinataires en copie conforme cachee
 *     string|array repondre_a : une ou plusieurs adresses a qui repondre
 *
 * @param array $options
 *   options d'envoi
 *     bool test : mode test (ajoute un [TEST] dans le sujet
 *     string tracking_id
 *
 * @return string|int
 *   vide si ok, ou message d'erreur sinon
 */
function newsletter_send_dist($destinataire,$corps,$options=array()){
	static $config = null;
	$erreur = "";
	$options = array_merge(array('test'=>false,'tracking_id'=>''),$options);
	if (is_null($config)){
		$config = lire_config("mailshot/");
		if (!isset($config['mailer'])) $config['mailer'] = 'defaut';
	}

	// refuser si pas de reglage specifique d'envoi mailshot et que facteur est configure pour utiliser mail()
	if ($config['mailer']=='defaut' AND lire_config("facteur_smtp")=='non'){
		$url_config = generer_url_ecrire("configurer_mailshot");
		spip_log("mailer non configure pour l'envoi de $corps a ".$destinataire['email'],'mailshot_send'._LOG_ERREUR);
		return _T('mailshot:erreur_aucun_service_configure',array('url'=>$url_config));
	}

	if (!is_array($corps)) {
		$content = charger_fonction("content","newsletter");
		$corps = $content($corps);
		if (!$corps OR !is_array($corps)){
			return _T('mailshot:erreur_generation_newsletter');
		}
	}
	$corps = array_merge(array('html'=>'','texte'=>'','sujet'=>''),$corps);

	if (!$corps['html'] AND !$corps['texte'])
		return "rien a envoyer !";
	if (!$corps['sujet'])
		return "il faut un sujet !";

	$corps_cont = array();

	// proceder au remplacement des variables contextuelles du destinataire
	$contextualize = charger_fonction("contextualize","newsletter");
	$corps_cont['sujet'] = $contextualize($corps['sujet'], $destinataire);
	$corps_cont['html'] = $contextualize($corps['html'], $destinataire);
	$corps_cont['texte'] = $contextualize($corps['texte'], $destinataire);

	// preparer les messages : generer un texte si manquant ou un html si manquant ?
	if (!$corps_cont['html']){
		$corps_cont['html'] = recuperer_fond("emails/texte",array('texte'=>$corps_cont['texte'],'sujet'=>$corps_cont['sujet']));
	}
	elseif (!$corps_cont['texte']){
		// tant pis... : pas de bras, pas de chocolat
	}

	// Mode test ?
	if ($options['test'])
		$corps_cont['sujet'] = "["._T('newsletter:info_test_sujet')."] " . $corps_cont['sujet'];

	// TODO : ajouter le tracking (1 image tracker + clic tracking sur les liens)



	// ---- Envoi proprement dit
	if (!function_exists('nettoyer_titre_email'))
		$envoyer_mail = charger_fonction('envoyer_mail','inc'); // pour nettoyer_titre_email()

	$sujet = nettoyer_titre_email($corps_cont['sujet']);
	$dest_email = $destinataire['email'];

	// mode TEST : forcer l'email
	if (defined('_TEST_EMAIL_DEST')) {
		if (!_TEST_EMAIL_DEST)
			return _T('mailshot:erreur_envoi_mail_bloque_debug');
		else {
			$dest_email = _TEST_EMAIL_DEST;
			// signaler cela comme une erreur, mais on continue quand meme
			$erreur = _T('mailshot:erreur_envoi_mail_force_debug',array('email'=>_TEST_EMAIL_DEST));
		}
	}

	// On cree l'objet Mailer (PHPMailer) pour le manipuler ensuite
	if (!$mailer_factory = charger_fonction($config['mailer'],'bulkmailer',true)
	  OR !$mailer = $mailer_factory(
			array(
				'email' => $dest_email,
				'sujet' => $sujet,
				'html' => &$corps_cont['html'],
				'texte' => &$corps_cont['texte'],
			))){

		$url_config = generer_url_ecrire("configurer_mailshot");
		spip_log("mailer non configure pour l'envoi de $corps a ".$destinataire['email'],'mailshot_send'._LOG_ERREUR);
		return _T('mailshot:erreur_aucun_service_configure',array('url'=>$url_config));
	}

	# Regler le From
	# TODO : reglage specifique mailshot
	// On ajoute le courriel de l'envoyeur s'il est fournit par la fonction
	if (empty($from) AND empty($mailer->From)) {
		$from = $GLOBALS['meta']["email_envoi"];
		if (empty($from) OR !email_valide($from)) {
			spip_log("Meta email_envoi invalide. Le mail sera probablement vu comme spam.","mailshot_send");
			$from = $dest_email;
		}
	}
	// "Marie Toto <Marie@toto.com>"
	if (preg_match(",^([^<>\"]*)<([^<>\"]+)>$,i",$from,$m)){
		$nom_envoyeur = trim($m[1]);
		$from = trim($m[2]);
	}
	if (!empty($from)){
		$mailer->From = $from;
		// la valeur par defaut de la config n'est probablement pas valable pour ce mail,
		// on l'ecrase pour cet envoi
		$mailer->FromName = $from;
	}
	// On ajoute le nom de l'envoyeur s'il fait partie des options
	if ($nom_envoyeur)
		$mailer->FromName = $nom_envoyeur;
	// Si plusieurs emails dans le from, pas de Name !
	if (strpos($mailer->From,",")!==false){
		$mailer->FromName = "";
	}

	# Regler le cc
	if (isset($corps['cc']) AND $cc=$corps['cc']){
		if (is_array($cc)) foreach ($cc as $courriel) $mailer->AddCC($courriel);
		else $mailer->AddCC($cc);
	}

	# Regler le bcc
	if (isset($corps['bcc']) AND $bcc=$corps['bcc']){
		if (is_array($bcc)) foreach ($bcc as $courriel) $mailer->AddBCC($courriel);
		else $mailer->AddBCC($bcc);
	}

	# Regler le Reply-to
	# TODO : reglage specifique mailshot
	if (isset($corps['repondre_a']) AND $repondre_a=$corps['repondre_a']){
		if (is_array($repondre_a)) foreach ($repondre_a as $courriel) $mailer->AddReplyTo($courriel);
		else $mailer->AddReplyTo($repondre_a);
	}

	// Si une adresse email a ete specifiee pour les retours en erreur, on l'ajoute
	if (!empty($corps['adresse_erreur']))
		$mailer->Sender = $corps['adresse_erreur'];


	// On passe dans un pipeline pour modifier tout le facteur avant l'envoi
	$mailer = pipeline('newsletter_pre_envoi', $mailer);

	// On genere les headers
	$head = $mailer->CreateHeader();

	// Et c'est parti on envoie enfin
	spip_log("mail via mailshot\n$head"."Destinataire:".print_r($destinataire['email'],true),'mail');
	spip_log("mail "."a :".print_r($destinataire['email'],true)."\n".trim($head),'mailshot_send'._LOG_DEBUG);

	// fixer les options d'envoi si possible (non dispo par Facteur mais par les surcharges)
	if (isset($mailer->send_options))
		$mailer->send_options = $options;
	$retour = $mailer->Send();

	if (!$retour) {
		spip_log("Erreur Envoi mail via Facteur : ".print_r($mailer->ErrorInfo,true),'mailshot_send'._LOG_ERREUR);
		return $mailer->ErrorInfo;
	}

	return $erreur;
}

?>