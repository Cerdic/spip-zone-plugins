<?php

// traitement anti-spam uniquement si $_POST est rempli et si l'espace n'est pas prive
if ( count($_POST) 
	// espace prive en clair dans l'url
	&& (strpos($_SERVER['PHP_SELF'],'/ecrire') === false) 
	// cas des actions
	&& !isset($_POST['action'])
	) {

	if (!isset($GLOBALS['meta']['cs_spam_mots'])) { include_spip('outils/spam'); spam_installe(); }

	// champs du formulaire a visiter
	//    un message en forum : texte, titre, auteur
	//    un message a un auteur : texte_message_auteur_XX, sujet_message_auteur_XX, email_message_auteur_XX
	$spam_POST_reg = ',^(?:texte|titre|sujet|auteur|email|session),i';
	// on compile $spam_POST en fonction des variables $_POST trouvees
	$spam_POST_compile = array();
	foreach (array_keys($_POST) as $key)
		if (preg_match($spam_POST_reg, $key) && strpos($key, 'password')===false)
			$spam_POST_compile[] = $key;
	// boucle de censure
	foreach ($spam_POST_compile as $var) 
		if (preg_match($GLOBALS['meta']['cs_spam_mots'], $_POST[$var])) {
			$_GET['action'] = "cs_spam";
			$_GET['var'] = $var;
		}
	// test IP compatible avec l'outil 'no_IP'
	if(preg_match($GLOBALS['meta']['cs_spam_ips'], $ip_)) $_GET['action'] = "cs_spam";
	// nettoyage
	unset($spam_POST_reg, $spam_POST_compile);

	function action_cs_spam(){
		include_spip('inc/minipres');
		echo minipres(
			_T('couteau:lutte_spam'),
			'<pre>'.$_POST[$_GET['var']].'</pre><div>'._T('couteau:explique_spam').'</div>'
		);
		exit;
	}
}
unset($ip_);
?>