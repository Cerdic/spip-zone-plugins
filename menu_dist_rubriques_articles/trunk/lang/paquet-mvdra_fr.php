<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'mvdra_description' => 'Le plugin surcharge la noisette inclure/navsub.html pour inclure les articles aux rubriques, et ne peut pas faire autrement que surcharger le squelette des articles squelettes-dist/article.html pour remplacer
	<code><INCLURE{fond=inclure/navsub, id_rubrique} /></code>
	par
	<code><INCLURE{fond=inclure/navsub, id_rubrique, id_article} /></code>.
	et enfin retirer toute la boucle
	<code><B_articles_rubrique> ... <BOUCLE_articles_rubrique> ... </BOUCLE_articles_rubrique> ... </B_articles_rubrique></code>',
	'mvdra_slogan' => 'Ajouter les articles dans l\'arborescence verticale des rubriques de squelettes-dist.',

);