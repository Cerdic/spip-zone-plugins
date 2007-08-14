<?php

// anti-spam un peu brutal : 
//	1. une liste de mots interdits est consultee
//	2. si le mot existe dans un des textes de formulaire', 'l'ensemble est reduit a : ''

// si aucun post ou espace prive, on n'insiste pas !
if (!count($_POST) || (strpos($_SERVER["PHP_SELF"],'/ecrire') !== false)) return;

// tableau des mots interdits
/*
 ATTENTION :
  1. ce sont des portions de texte, sans delimitateur particulier : 
     si vous mettez 'asses' alors 'tasses' sera un mot interdit aussi !
  2. ces mots sont integralement injectes dans une expression reguliere.
     si vous ne savez pas ce que c'est, utilisez uniquement des lettres ou des chiffres !
*/
$spam_mots = array(
	// des liens en dur...
	'<a\s+href="', '</a>',
	// certains mots...
	'gorgeous', 'nurses', 'sensored', 'sucking', 'erotic', 'swallowing', 'horny', 'naked',
	'schoolgirl', 'blowjobs', 'lesbian', 'orgasms', 'superbabes', 'shaving', 'nasty', 'humping', 
	'beauties', 'tortured', 'gagged', 'pumping', 'hardcore', 'upskirt', 'miniskirt', 'biracial',
	'climaxing', 'bondage', 'ejakulation', 'fucking',
);
$spam_mots_reg = ',(' . join('|', $spam_mots) . '),i';

// champs de formulaires a visiter
//    un message en forum : texte, titre, auteur
//    un message  a un auteur : texte_message_auteur_XX, sujet_message_auteur_XX, email_message_auteur_XX
$spam_POST_reg = ',^(texte|titre|sujet|auteur|email),i';

// on compile $spam_POST en fonction des variables $_POST trouvees
$spam_POST_compile = array();
foreach (array_keys($_POST) as $key)
 if (preg_match($spam_POST_reg, $key)) $spam_POST_compile[] = $key;


// boucle de censure
foreach ($spam_POST_compile as $var) 
	if (preg_match($spam_mots_reg, $_POST[$var]))
		$_GET['action'] = "cs_spam";

// nettoyage
unset($spam_mots, $spam_mots_reg, $spam_POST_reg, $spam_POST_compile);

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

?>