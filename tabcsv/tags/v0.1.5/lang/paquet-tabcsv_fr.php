<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/tabcsv/trunk/lang/

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'tabcsv_description' => 'tabcsv est un modèle pour Spip qui permet de transformer du contenu ou des fichiers au format CSV (Comma-separated values) en tableau Spip.
	
-* Par défaut, il n\'y a pas de délimiteur de texte et le séparateur de champs est le ; (point-virgule).
-* Via les paramètres (voir documentation), on peut redéfinir le délimiteur de texte à " (guillemet double) et le séparateur de champs à ce qu\’on veut.
-* Il faut fixer le délimiteur de texte à " (guillemet double) :
-** Si les données du contenu CSV contiennent le caractère du séparateur de champ
-** Si ({{Attention, particularité du modèle !}}) les données du contenu CSV contiennent un ou des \’ (guillemets simples) ET que le séparateur de champ est le ; (point-virgule)

Le modèle a été testé sous Spip 3.1, mais il n\'y a pas à priori de raison pour qu\'il ne fonctionne pas également sous Spip 3.0, ou 2.1 et 2.0. Simplement, il faut tester. Des retours d\'utilisateurs sont les bienvenus à ce propos.',
	'tabcsv_nom' => 'Importation CSV',
	'tabcsv_slogan' => 'Importation de tableaux au format CSV dans des tableaux spip.'

);
?>