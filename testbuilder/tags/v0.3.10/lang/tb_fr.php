<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/_stable_/acces_restreint/lang/
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'testbuilder' => 'TestBuilder',

	'label_resultat_essai' => 'R&eacute;sultat attendu',
	'bouton_tester' => 'Tester !',
	'bouton_combinatoire' => 'Jeu de tests combinatoires',
	'bouton_supprimer_tous' => 'Supprimer tous les tests',
	'bouton_recalculer_tous' => 'Recalculer tous les tests',
	'bouton_creer_test' => 'Cr&eacute;er un test !',

	'ok_test_ajoute' => "Test ajout&eacute; : ",
	'ok_tests_supprimes' => "Tests supprim&eacute;s.",
	'ok_test_recalcules' => "Les tests ont été recalculés",
	'ok_test_supprime' => "Test supprim&eacute;",
	'ok_n_tests_combi_crees' => "@nb@ tests combinatoires cr&eacute;es.",

	'erreur_argument_vide' => "Cet argument ne peut &ecirc;tre vide car il est suivi par d'autres",
	'erreur_test_combinatoire_types_requis' => 'Pas de test a creer ! Indiquez le type de chaque argument',
	'erreur_test_combinatoire_resultat_ignore' => 'Le r&eacute;sultat est calcul&eacute; automatiquement pour chaque essai combinatoire',
	'erreur_pseudo_type_inconnu' => 'Aucun jeu de donn&eacute;es n\'est d&eacute;fini pour ce type',

	'un_essai' => '1 jeu de test',
	'nb_essais' => '@nb@ jeux de tests',

	'modifier' => 'Modifier le test',

	'texte_presentation' => 'Selectionner le script PHP pour lequel vous souhaitez construire des jeux de tests.

Les fichiers tests seront cr&eacute;&eacute;s dans le repertoire <tt>tests/</tt> si le script fait partie de SPIP.
Si le script appartient a un plugin ou une extension, le test sera cr&eacute;&eacute; dans un sous r&eacute;pertoire <tt>tests/</tt> du plugin.

Les droits doivent &ecirc;tre suffisants pour permettre l\'&eacute;criture de fichier et la cr&eacute;ation de r&eacute;pertoires.

{{Ne laissez pas ce plugin install&eacute; sur un site en ligne}}
',
	'texte_choisir_fonction' => 'Les fonctions ci-contre ont &eacute;t&eacute; d&eacute;tect&eacute;es dans le script PHP.
		Si un test homonyme a la fonction existe, il est indiqu&eacute; et il est propos&eacute; de le modifier.

		Seuls les tests cr&eacute;&eacute;s par TestBuilder sont effectivement modifiables.
		Pour les tests cr&eacute;&eacute;s manuellement, le jeu de test ne pourra pas être retrouv&eacute;.',
	'texte_explication' =>'Pour creer un test saisissez une valeur pour chaque argument. Attention, la saisie sera interpret&eacute;e directement par PHP.
Une chaine doit donc &ecirc;tre entre guillemets, <tt>true</tt> est un booleen, <tt>array(...)</tt> un tableau etc ...

Si vous laissez des arguments vides, ceux-ci seront ignor&eacute;s dans l\'appel php, permettant de tester les valeurs par defaut.

Pour creer jeu de tests combinatoire, entrez simplement un pseudo-type correspondant a un jeu de test pour chaque argument et cliquez sur le bouton <tt>Jeu de tests combinatoires</tt>.
Les pseudo-types disponibles sont :
-* <tt>bool</tt>
-* <tt>int</tt>
-* <tt>int8</tt> de 0 a 255
-* <tt>float01</tt> flottant de 0 a 1
-* <tt>string</tt>
-* <tt>iso-string</tt> pour du texte avec des accents ISO-8859
-* <tt>utf8-string</tt> pour du texte avec des accents UTF-8
-* <tt>date</tt> pour une date textuelle
-* <tt>time</tt> pour une date au format unix
-* <tt>email</tt> pour des chaine au format adresse email
-* <tt>array</tt>
-* <tt>image</tt> chemin vers un fichier image
-* <tt>mimetype</tt> string de nommage des mime-type
-* <tt>version</tt> Numeros de version a 1,2 ou 3 digits, avec variantes dev, alpha, beta, RC, pl
-* <tt>operateur</tt> pour des operateurs de comparaison
'
);

?>
