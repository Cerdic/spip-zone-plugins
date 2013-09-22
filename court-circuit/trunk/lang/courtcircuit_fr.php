<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/court-circuit/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aide_en_ligne' => 'Aide en ligne',

	// C
	'configurer_courtcircuit' => 'Configurer les règles de court-circuitage des rubriques',
	'courtcircuit' => 'Court-circuit',

	// E
	'explication_liens_rubriques' => 'Modifier l’URL des rubriques redirigées directement dans les squelettes ?',
	'explication_regles' => 'Les différentes règles ci-dessous sont testées dans cet ordre. Si aucune règle ne définit une redirection, alors la rubrique sera affichée normalement.',
	'explication_restreindre_langue' => 'Si cette option est activée, seuls les articles de la langue active seront pris en compte pour le calcul de la rediretion. Cette option n’est utile que si vos rubriques contiennent des articles de différentes langues. Ne pas utiliser si votre site est organisé en secteurs de langue ou si vous utilisez des champs multi.',
	'explication_sousrubrique' => 'Parcourir la première sous-rubrique (tri par numéro du titre et date) ? Les règles de redirection seront testées à nouveau dans cette sous-rubrique.',
	'explication_variantes_squelettes' => 'Exemple : squelettes de la forme rubrique-2.html ou rubrique=3.html.',

	// I
	'item_appliquer_redirections' => 'Appliquer les règles de redirection',
	'item_jamais_rediriger' => 'Ne jamais rediriger',
	'item_ne_pas_rediriger' => 'Ne pas rediriger',
	'item_rediriger_sur_article' => 'Rediriger sur cet article',

	// L
	'label_article_accueil' => 'Article d’accueil de la rubrique',
	'label_composition_rubrique' => 'Rubrique avec composition',
	'label_exceptions' => 'Exceptions',
	'label_liens' => 'URL des rubriques',
	'label_liens_rubriques' => 'Agir sur la balise #URL_RUBRIQUE ?',
	'label_plus_recent' => 'Article le plus récent de la rubrique',
	'label_plus_recent_branche' => 'Article de la branche le plus récent',
	'label_rang_un' => 'Premier article (articles numérotés)',
	'label_regles' => 'Règles de redirection des rubriques',
	'label_restreindre_langue' => 'Ne prendre en compte que les articles de la langue ?',
	'label_sousrubrique' => 'Sous-rubriques',
	'label_un_article' => 'Seul article de la rubrique',
	'label_variantes_squelettes' => 'Rubrique avec variante de squelettes'
);

?>
