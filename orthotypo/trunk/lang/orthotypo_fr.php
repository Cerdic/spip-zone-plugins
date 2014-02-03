<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;
 
$GLOBALS[$GLOBALS['idx_lang']] = array(

	// O
	'orthotypo_titre' => 'Ortho-Typographie',

	// C
	'cfg_exemple' => 'Exemple',
	'cfg_exemple_explication' => 'Explication de cet exemple',
	'cfg_titre_parametrages' => 'Paramétrages',

	'label_guillemets_1' => 'Correction automatique des guillemets, selon la langue',
	'explication_guillemets' => 'Remplacer automatiquement les guillemets droits (") par les guillemets typographiques (&#171;&#187;&#8220;&#8221;&#8222;) de la langue de composition et guillemette correctement la balise <tt>&lt;q></tt>. Les liens automatiques <code>[->1]</code> vers des articles dont le titre contient des &#171;guillemets fran&#231;ais&#187; passent en guillemets &#8220;de second niveau&#8221;. Le remplacement, transparent pour l\'utilisateur, ne modifie pas le texte mais seulement l\'affichage final.',

	'label_exposants_1' => 'Am&eacute;lioration typographique des abr&eacute;viations avec exposants',
	'explication_exposants' => 'Am&eacute;liorer le rendu typographique d\'abr&eacute;viations fr&eacute;quentes par la mise en exposant de leurs &eacute;l&eacute;ments et/ou leur correction. Les abr&eacute;viations obtenues sont conformes &agrave; celles de l\'Imprimerie nationale telles qu\'indiqu&eacute;es dans le {Lexique des r&egrave;gles typographiques en usage &agrave; l\'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l\'Imprimerie nationale, Paris, 2002).',

	'label_mois_1' => 'Eviter de couper les dates en fin de ligne',
	'explication_mois' => 'Corriger les dates au format "10 Mars" en remplaçant l\'espace entre le jour et le mois par un espace ins&#233;cable.',

	'label_caps_1' => 'Styler les mots en lettres capitales',
	'explication_caps' => 'Encadrer les mots écrits en lettres capitales dans une balise <code>&lt;span class="caps"></code>. Par exemple, <code>ONU</code> devient <code>&lt;span class="caps">ONU&lt;/span></code>. De ce fait, les mots en lettres capitales peuvent être stylés en css',

	'label_fines_1' => 'Espaces fines',
	'explication_fines' => 'Traiter les espaces fines au voisinage des ponctuations doubles et des guillemets.',

	'legend_corrections' => 'Corrections automatiques',
	'label_corrections_1' => 'Activer les corrections automatiques',
	'explication_corrections' => 'Corriger automatiquement le texte selon les règles ci-dessous.',
	'label_corrections_regles' => 'Règles de correction',
	'explication_corrections_regles' => 'Indiquez une règle par ligne sous la forme <br/><code>mot = remplacement</code><br/> pour un simple remplacement, ou <br/><code>/m[ao]t/ = m$1t</code><br/> pour un remplacement par expression régulière',

	// T
	'titre_page_configurer_orthotypo' => 'Typographie et remplacements',
);

?>