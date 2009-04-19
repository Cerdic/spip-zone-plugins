<?php
function select_champ($bidon, $champ='', $en_cours){
	$options = NULL;
	if ($champ ==  '')
		return $options;
		
	switch(strtolower($champ))
	{
		case 'jalon':
			if (defined('_TICKETS_LISTE_JALONS'))
				$define = _TICKETS_LISTE_JALONS;
			break;
		case 'version':
			if (defined('_TICKETS_LISTE_VERSIONS'))
				$define = _TICKETS_LISTE_VERSIONS;
			break;
		case 'projet':
			if (defined('_TICKETS_LISTE_PROJETS'))
				$define = _TICKETS_LISTE_PROJETS;
			break;
		case 'composant':
			if (defined('_TICKETS_LISTE_COMPOSANTS'))
				$define = _TICKETS_LISTE_COMPOSANTS;
			break;
		default:
			$define = '';
			break;
	}
	if ($define ==  '')
		return $options;

	$liste = explode(':', $define);
	foreach ($liste as $_item) {
		if ($_item != '') {
			$selected = ($_item == $en_cours) ? ' selected="selected"' : ''; 
			$options .= '<option value="' . $_item . '"' . $selected . '>' . $_item . '</option>';
		}
	}

	return $options;
}

function classer_par_jalon($bidon) {
	$page = NULL;
	if (defined('_TICKETS_LISTE_JALONS')) {
		$liste = explode(":", _TICKETS_LISTE_JALONS);
		foreach($liste as $_jalon) {
			$page .= recuperer_fond('prive/contenu/tickets_liste_long', 
				array_merge($_GET, array('ajax'=>true, 'visible'=>'false', 'titre' => _T('tickets:champ_jalon').$_jalon, 'statut' => 'ouvert', 'jalon' => $_jalon)));
		}
	}
	return $page;
}

?>
