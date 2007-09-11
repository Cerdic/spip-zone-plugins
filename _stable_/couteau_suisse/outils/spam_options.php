<?php

// anti-spam un peu brutal : 
//	1. une liste de mots interdits est consultee
//	2. si le mot existe dans un des textes d'un formulaire, on avertit !

// traitement anti-spam uniquement si $_POST est rempli et si l'espace n'est pas prive
if ( count($_POST) 
	// espace prive en clair dans l'url
	&& (strpos($_SERVER['PHP_SELF'],'/ecrire') === false) 
	// cas des actions
	&& !isset($_POST['action'])
	) {

	// tableau des mots interdits
	/*
	 ATTENTION :
	  	ce sont des portions de texte, sans delimitateur particulier : 
		si vous mettez 'asses' alors 'tasses' sera un mot interdit aussi !
	*/
	$spam_mots = array(
		// des liens en dur...
		'<a href=', '</a>',
		// certains mots...
		'gorgeous', 'nurses', 'sensored', 'sucking', 'erotic', 'swallowing', 'horny', 'naked',
		'schoolgirl', 'blowjobs', 'lesbian', 'orgasms', 'superbabes', 'shaving', 'nasty', 'humping', 
		'beauties', 'tortured', 'gagged', 'pumping', 'hardcore', 'upskirt', 'miniskirt', 'biracial',
		'climaxing', 'bondage', 'ejakulation', 'fucking',
	);
	foreach($spam_mots as $i=>$mot) $spam_mots[i] = preg_quote($mot,',');
	$spam_mots = ',(' . join('|', $spam_mots) . '),i';
	
	// champs de formulaires a visiter
	//    un message en forum : texte, titre, auteur
	//    un message a un auteur : texte_message_auteur_XX, sujet_message_auteur_XX, email_message_auteur_XX
	$spam_POST_reg = ',^(texte|titre|sujet|auteur|email),i';
	
	// on compile $spam_POST en fonction des variables $_POST trouvees
	$spam_POST_compile = array();
	foreach (array_keys($_POST) as $key)
	 if (preg_match($spam_POST_reg, $key)) $spam_POST_compile[] = $key;
	
	// boucle de censure
	foreach ($spam_POST_compile as $var) 
		if (preg_match($spam_mots, $_POST[$var]))
			$_GET['action'] = "cs_spam";
	
	// nettoyage
	unset($spam_mots, $spam_POST_reg, $spam_POST_compile);
	
	function action_cs_spam(){
		include_spip('inc/minipres');
		$page = minipres(
			_T('cout:lutte_spam'),
			_T('cout:explique_spam')
		);
		// a partir de spip 1.9.2 ces fonctions ne font plus l'echo directement
		if ($GLOBALS['spip_version']>=1.92) echo $page;
		return true;
	}
}
?>