<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_TEST_STATIQUE($p)
{
	$id_auteur = interprete_argument_balise(1, $p);
	$id_rubrique = interprete_argument_balise(2,$p);
	$p->code = "'ID_AUTEUR: '.$id_auteur.'  - ID_SECTEUR:  '.$id_secteur";
	return $p;
}

?>