<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	'zless_description' => 'Selectionne les feuilles de style au format .less de préférence, et les convertit en css à la volée. Si une feuille n’est disponible qu’au format .css, elle est utilisée telle quelle.

Le plugin peut être utilisé en production pour travailler systématiquement avec LESS,
ou comme outil de construction rapide d’un thème CSS, pour générer les feuilles statiques à partir de variables LESS
	
Les feuilles de styles less sont les versions paramétrées des feuilles de styles CSS de Zpip-dist, elles-même tirées directement du framework BaseCSS
	',
	'zless_slogan' => 'Utiliser Less avec Zpip-dist',
);
