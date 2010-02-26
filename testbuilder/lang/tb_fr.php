<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/_stable_/acces_restreint/lang/
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'bouton_tester' => 'Tester !',
	'bouton_combinatoire' => 'Jeu de tests combinatoires',
	'bouton_supprimer_tous' => 'Supprimer tous les tests',

	'ok_test_ajoute' => "Test ajout&eacute; : ",
	'ok_tests_supprimes' => "Tests supprim&eacute;s.",
	'ok_test_supprime' => "Test supprim&eacute;",
	'ok_n_tests_combi_crees' => "@nb@ tests combinatoires cr&eacute;es.",

	'erreur_argument_vide' => "Cet argument ne peut &ecirc;tre vide car il est suivi par d'autres",
	'erreur_test_combinatoire_types_requis' => 'Pas de test a creer ! Indiquez le type de chaque argument',

	'un_essai' => '1 jeu de test',
	'nb_essais' => '@nb@ jeux de tests',

	'texte_explication' =>'Pour creer un test saisissez une valeur pour chaque argument. Attention, la saisie sera interpretée directement par PHP.
Une chaine doit donc être entre guillemets, <tt>true</tt> est un booleen, <tt>array(...)</tt> un tableau etc ...

Si vous laissez des arguments vides, ceux-ci seront ignorés dans l\'appel php, permettant de tester les valeurs par defaut.

Pour creer jeu de tests combinatoire, entrez simplement un pseudo-type correspondant a un jeu de test pour chaque argument et cliquez sur le bouton <tt>Jeu de tests combinatoires</tt>.
Les pseudo-types disponibles sont :
-* <tt>bool</tt>
-* <tt>int</tt>
-* <tt>string</tt>
-* <tt>date</tt> pour une date textuelle
-* <tt>time</tt> pour une date au format unix
-* <tt>array</tt>
'
);

?>
