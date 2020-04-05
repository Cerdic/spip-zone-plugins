<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

	$test = 'table_matieres:Bug_1';
// recherche test.inc qui nous ouvre au monde spip
$deep = 2;
$include = '../../tests/test.inc';
while (!defined('_SPIP_TEST_INC') && $deep++ < 4) {
	$include = '../' . $include;
	@include $include;
}
if (!defined('_SPIP_TEST_INC')) {
	die("Pas de $include");
}

	include_spip('public/assembler'); //recuperer_fond()
	include_spip('inc/texte'); //propre() etc...
	include_spip('tablematieres_fonctions'); //ce qu'on on test

	$c = dutexte();
	$d = dutexte2();


	//deux textes differents ne doivent pas renvoye un texte identique
	if(table_matieres($c) ==
	table_matieres($d))
		die('bug reproduit');

	//un texte qui passe une deuxieme fois pour afficher la balise #TABLE_MATIERE
	if(table_matieres($c) !=
	table_matieres($c))
		die('bug introduit');

	die('OK');


	//
	// DONNEES
	//

	function dutexte() {
		return '{{{test}}}

hop

{{{in english with <sup>exposant</sup>}}}

<multi>thanks[fr]merci</multi>

{{{*-*-*}}}

des intertitres sans texte

{{{<multi>then...[fr]alors...</multi>}}}

last chapter[[avec une note de bas de page pour voir si elle est doubl�e ou pas avec table_matieres]]

{{{*-*-*}}}

Des intertitres non humains et du <code><code></code> dans les paragraphes.

<code>on remplace les triples accolades : {{{comme ceci}}}</code>

dans un cadre aussi :

<cadre>truc
{{{comme cela}}}
muche</cadre>

{{{Bug Majeur}}}

<math>
$$
\begin{array}{ccl}
|x_n-x_*|&=&|g(x_{n-1})-g(x_*)|=|g\'(\xi_{n-1})||x_{n-1}-x_*|\leq\tau|x_{n-1}-x_*|\\
|x_n-x_*|&\leq&\tau|x_{n-1}-x_*|\leq\tau^2|x_{n-2}-x_*|\leq\ldots\leq\tau^n|x_{0}-x_*|
\end{array}
$$
essai
{{{test}}}
autre
$$
\begin{array}{ccl}
|x_n-x_*|&=&|g(x_{n-1})-g(x_*)|=|g\'(\xi_{n-1})||x_{n-1}-x_*|\leq\tau|x_{n-1}-x_*|\\
|x_n-x_*|&\leq&\tau|x_{n-1}-x_*|\leq\tau^2|x_{n-2}-x_*|\leq\ldots\leq\tau^n|x_{0}-x_*|
\end{array}
$$
</math>

{{{apr�s les maths}}}

du texte et tout �a...

{{{apr�s les maths, on fait un intertitre de grande longueur pour v�rifier une petite fonction sympa}}}

du texte et tout �a...';
	}

	function dutexte2() {
		return 'un test de table_matieres :

{{{voici une inttroduction}}}

un texte court

{{{second chapitre}}}

contient du code :

<code>on remplace les triples accolades : {{{comme ceci}}}</code>

dans un cadre aussi :

<cadre>truc
{{{comme cela}}}
muche</cadre>

{{{troisi�me phase}}}

fin du texte';
	}

?>
