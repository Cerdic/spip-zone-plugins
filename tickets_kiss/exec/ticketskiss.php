<?php
// Traitement de la page recapitulative des ticketskiss
function exec_ticketskiss () {

	include_spip('inc/presentation');
	include_spip('inc/mots');

	$titre_page = _T('ticketskiss:titre_liste');

	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = "forum";
	$sous_rubrique = "ticketskiss";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(
		_T('ticketskiss:titre_liste').' - '._T('ticketskiss:titre'),
		$rubrique,
		$sous_rubrique
	);
	
	// Valeur par défaut du contexte
	$contexte = array('classement' => 'asuivre');
	// On écrase par l'environnement
	$contexte = array_merge($contexte, $_GET, $_POST);
	// On appelle la noisette de presentation
	echo recuperer_fond('prive/contenu/ticketskiss', $contexte);
	
	echo fin_page();
}

?>
