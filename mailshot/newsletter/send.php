<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
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
 *   id de la newsletter (string)
 *   contenu de la newsletter (array)
 *     string sujet
 *     string html
 *     string texte
 *     string from : email de l'envoyeur (prioritaire sur argument $from de premier niveau, deprecie)
 *     string nom_envoyeur : un nom d'envoyeur pour completer l'email from
 *     string cc : destinataires en copie conforme
 *     string bcc : destinataires en copie conforme cachee
 *     string|array repondre_a : une ou plusieurs adresses à qui répondre
 *
 * @param array $options
 *   options d'envoi
 *     bool test : mode test (ajoute un [TEST] dans le sujet
 *     string tracking_id
 *
 * @return string
 *   vide si ok, ou message d'erreur sinon
 */
function newsletter_send_dist($destinataire,$corps,$options=array()){
	$options = array_merge(array('test'=>false,'tracking_id'=>''),$options);

	if (!is_array($corps)) {
		$content = charger_fonction("content","newsletter");
		$corps = $content($corps);
	}
	$corps = array_merge(array('html'=>'','texte'=>'','sujet'=>''),$corps);

	if (!$corps['html'] AND !$corps['texte'])
		return "rien a envoyer !";
	if (!$corps['sujet'])
		return "il faut un sujet !";

	// proceder au remplacement des variables contextuelles du destinataire
	$contextualize = charger_fonction("contextualize","newsletter");
	$corps['html'] = $contextualize($corps['html'], $destinataire);
	$corps['texte'] = $contextualize($corps['texte'], $destinataire);

	// preparer les messages : generer un texte si manquant ou un html si manquant ?
	if (!$corps['html']){
		$corps['html'] = recuperer_fond("emails/texte",array('texte'=>$corps['texte'],'sujet'=>$corps['sujet']));
	}
	elseif (!$corps['texte']){
		// tant pis... : pas de bras, pas de chocolat
	}

	// Mode test ?
	if ($options['test'])
		$corps['sujet'] = "["._T('newsletter:info_test_sujet')."] " . $corps['sujet'];

	// TODO : ajouter le tracking (1 image tracker + clic tracking sur les liens)



	// ---- Envoi proprement dit, via facteur (~ envoyer_mail)
	include_spip('classes/facteur');
	$envoyer_mail = charger_fonction('envoyer_mail','inc'); // pour nettoyer_titre_email()

	$sujet = nettoyer_titre_email($corps['sujet']);
	$dest_email = $destinataire['email'];

	// mode TEST : forcer l'email
	if (defined('_TEST_EMAIL_DEST')) {
		if (!_TEST_EMAIL_DEST)
			return false;
		else
			$dest_email = _TEST_EMAIL_DEST;
	}

	// On crée l'objet Facteur (PHPMailer) pour le manipuler ensuite
	$options = array(
		'filtre_images' => false,
	);
	$facteur = new Facteur($dest_email, $sujet, $corps['html'], $corps['texte'], $options);

	# Regler le From
	# TODO : reglage specifique mailshot
	// On ajoute le courriel de l'envoyeur s'il est fournit par la fonction
	if (empty($from) AND empty($facteur->From)) {
		$from = $GLOBALS['meta']["email_envoi"];
		if (empty($from) OR !email_valide($from)) {
			spip_log("Meta email_envoi invalide. Le mail sera probablement vu comme spam.","mailshot");
			$from = $dest_email;
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
		$facteur->FromName = $from;
	}
	// On ajoute le nom de l'envoyeur s'il fait partie des options
	if ($nom_envoyeur)
		$facteur->FromName = $nom_envoyeur;
	// Si plusieurs emails dans le from, pas de Name !
	if (strpos($facteur->From,",")!==false){
		$facteur->FromName = "";
	}

	# Regler le cc
	if (isset($corps['cc']) AND $cc=$corps['cc']){
		if (is_array($cc)) foreach ($cc as $courriel) $facteur->AddCC($courriel);
		else $facteur->AddCC($cc);
	}

	# Regler le bcc
	if (isset($corps['bcc']) AND $bcc=$corps['bcc']){
		if (is_array($bcc)) foreach ($bcc as $courriel) $facteur->AddBCC($courriel);
		else $facteur->AddBCC($bcc);
	}

	# Regler le Reply-to
	# TODO : reglage specifique mailshot
	if (isset($corps['repondre_a']) AND $repondre_a=$corps['repondre_a']){
		if (is_array($repondre_a)) foreach ($repondre_a as $courriel) $facteur->AddReplyTo($courriel);
		else $facteur->AddReplyTo($repondre_a);
	}

	// Si une adresse email a été spécifiée pour les retours en erreur, on l'ajoute
	if (!empty($corps['adresse_erreur']))
		$facteur->Sender = $corps['adresse_erreur'];

	// On passe dans un pipeline pour modifier tout le facteur avant l'envoi
	$facteur = pipeline('newsletter_pre_envoi', $facteur);

	// On génère les headers
	$head = $facteur->CreateHeader();

	// Et c'est parti on envoie enfin
	spip_log("mail via mailshot\n$head"."Destinataire:".print_r($destinataire,true),'mail');
	spip_log("mail\n$head"."Destinataire:".print_r($destinataire,true),'mailshot'._LOG_DEBUG);
	$retour = $facteur->Send();

	if (!$retour) {
		spip_log("Erreur Envoi mail via Facteur : ".print_r($facteur->ErrorInfo,true),'mailshot'._LOG_ERREUR);
		return $facteur->ErrorInfo;
	}

	return '';
}

?>