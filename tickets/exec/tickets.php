<?php
// Traitement de la page recapitulative des tickets
function exec_tickets () {

	include_spip('inc/presentation');
	include_spip('inc/mots');

	$titre_page = _T('tickets:titre_liste');

	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = "forum";
	$sous_rubrique = "tickets";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(
		_T('tickets:titre_liste').' - '._T('tickets:titre'),
		$rubrique,
		$sous_rubrique
	);
	
	// Valeur par défaut du contexte
	$contexte = array('classement' => 'asuivre');
	// On écrase par l'environnement
	$contexte = array_merge($contexte, $_GET, $_POST);
	// On appelle la noisette de presentation
	echo recuperer_fond('prive/contenu/tickets', $contexte);
	
	echo fin_page();
}

?>
