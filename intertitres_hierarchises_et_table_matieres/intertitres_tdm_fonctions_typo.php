<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// fonction qui convertit les intertitres d'enluminures type {ß{titre}ß}
// ou ß est un nombre en intertitres avec des étoiles type {{{* (avec ß étoiles)
// {1{ sera converti en {{{* qui équivaut à {{{
// {2{ sera converti en {{{**, etc.
function IntertitresTdm_pre_propre($texte) {
  $texte=preg_replace_callback ("/(\{(\d)\{)(.*?)(\}\\2\})/",
				create_function (
					'$matches',
					'return "{{{".str_repeat("*",$matches[2]).$matches[3]."}}}";'
					),
				$texte);
 return $texte;
}

function IntertitresTdm_post_propre($texte) {
	//le second paramètre est vide, c'est à dire qu'on n'affiche pas la table seule.
	$new_texte = IntertitresTdm_table_des_matieres($texte);
	return $new_texte;
}
?>