<?php

// anti-spam un peu brutal : 
//	1. une liste de mots interdits est consultee
//	2. si le mot existe dans un des textes d'un formulaire, on avertit !

// cette fonction est appelee automatiquement a chaque affichage de la page privee du Couteau Suisse
function spam_installe() {
	// tableau des mots interdits
	/*
	 ATTENTION :
	  	ce sont des portions de texte, sans delimitateur particulier : 
		si vous mettez 'asses' alors 'tasses' sera un mot interdit aussi !
		les parentheses servent de delimitateurs de mots : '(asses)'
	*/
	$spam_mots = array_merge(array(
		// des liens en dur ou simili...
		'<a href=', '</a>',
		'[url=', '[/url]',
		'[link=', '[/link]',
		// certains mots...
		// 'ejakulation', 'fucking', '(asses)',

	), defined('_spam_MOTS')?spam_liste_mots(_spam_MOTS):array());
	array_walk($spam_mots, 'spam_walk');
	ecrire_meta('cs_spam_mots', '/(' . join('|', $spam_mots) . ')/i');
	ecrire_metas();
}

// protege les expressions en vue d'une regexpr
// repere les mots entiers entre parentheses et les regexpr entre slashes
function spam_walk(&$item) {
	if(preg_match(',^\((.+)\)$,', $item, $reg))
		$item = '\b'.preg_quote($reg[1], '/').'\b';
	elseif(preg_match(',^\/(.+)\/$,', $item, $reg))
		$item = '('.$reg[1].')';
	else $item = preg_quote($item, '/');
}

// retourne un tableau de mots ou d'expressions a partir d'un texte
function spam_liste_mots($texte) {
	$texte = filtrer_entites(trim($texte));
	$split = explode('"', $texte);
	$c = count($split);
	$split2 = array();
	for($i=0; $i<$c; $i++) if (($s = trim($split[$i])) != ""){
		if (($i & 1) && ($i != $c-1)) {
			// on touche pas au texte entre deux ""
			$split2[] = $s;
		} else {
			// on rassemble tous les separateurs : \s\t\n
			$temp = preg_replace("/[\s\t\n\r]+/", "\t", $s);
			$temp = str_replace("+"," ", $temp);
			$split2 = array_merge($split2, explode("\t", $temp));
		}
	}
	return array_unique($split2);
}

// traitement anti-spam uniquement si $_POST est rempli et si l'espace n'est pas prive
if ( count($_POST) 
	// espace prive en clair dans l'url
	&& (strpos($_SERVER['PHP_SELF'],'/ecrire') === false) 
	// cas des actions
	&& !isset($_POST['action'])
	) {

	if (!isset($GLOBALS['meta']['cs_spam_mots'])) spam_installe();

	// champs du formulaire a visiter
	//    un message en forum : texte, titre, auteur
	//    un message a un auteur : texte_message_auteur_XX, sujet_message_auteur_XX, email_message_auteur_XX
	$spam_POST_reg = ',^(texte|titre|sujet|auteur|email|session),i';

	// on compile $spam_POST en fonction des variables $_POST trouvees
	$spam_POST_compile = array();
	foreach (array_keys($_POST) as $key)
		if (preg_match($spam_POST_reg, $key) && strpos($key, 'password')===false)
			$spam_POST_compile[] = $key;

	// boucle de censure
	foreach ($spam_POST_compile as $var) 
		if (preg_match($GLOBALS['meta']['cs_spam_mots'], $_POST[$var]))
			$_GET['action'] = "cs_spam";

	// nettoyage
	unset($spam_POST_reg, $spam_POST_compile);

	function action_cs_spam(){
		include_spip('inc/minipres');
		echo minipres(
			_T('couteau:lutte_spam'),
			_T('couteau:explique_spam')
		);
		return true;
	}
}
?>