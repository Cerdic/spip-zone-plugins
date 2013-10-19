<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function balise_INTRODUCTION_DISTINGUANTE_dist($p) {

	$type = $p->type_requete;

	$_texte = champ_sql('texte', $p);
	$_descriptif = ($type == 'articles' OR $type == 'rubriques') ? champ_sql('descriptif', $p) : "''";

	if ($type == 'articles') {
		$_chapo = champ_sql('chapo', $p);
		$_texte = "(strlen($_descriptif))
		? ''
		: $_chapo . \"\\n\\n\" . $_texte";
	}

	// longueur en parametre, ou valeur par defaut
	if (($v = interprete_argument_balise(1,$p))!==NULL) {
		$longueur = 'intval('.$v.')';
	} else {
		switch ($type) {
			case 'articles':
				$longueur = '500';
				break;
			case 'breves':
				$longueur = '300';
				break;
			case 'rubriques':
			default:
				$longueur = '600';
				break;
		}
	}

	$f = chercher_filtre('introduction');
	$p->code = "$f($_descriptif, $_texte, $longueur, \$connect)";

	#$p->interdire_scripts = true;
	$p->etoile = '*'; // propre est deja fait dans le calcul de l'intro
	return $p;
}

?>