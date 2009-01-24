<?php
// fonction qui convertit les intertitres d'enluminures type {�{titre}�}
// ou � est un nombre en intertitres avec des �toiles type {{{* (avec � �toiles)
// {1{ sera converti en {{{* qui �quivaut � {{{
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
  //le second param�tre est vide, c'est � dire qu'on n'affiche pas la table seule.
 $new_texte = IntertitresTdm_table_des_matieres($texte);
 return $new_texte;
}
?>