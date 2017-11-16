<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_titre_parametrages' => 'Paramétrages',

	// H
	'htmlminifier_titre' => 'HTML Minifier',

	// T
	'titre_page_configurer_htmlminifier' => 'Configurer le plugin HTML Minifier',

	// Général
	'clean_html_comments_label_case' => 'Supprimer les commentaires HTML',
	'clean_html_comments_explication' => 'Supprime tous les commentaires HTML, sauf les commentaires conditionnels. Préserve également &lt;!-- extra --&gt; et  &lt;!-- keepme: --&gt;',

	'show_signature_label_case' => 'Afficher la signature dans la source',
	'show_signature_explication' => 'Ajoute un commentaire en fin de page indiquant que la source a été minifiée par HTML Minifier',

	'compression_mode_explication' => 'Mode de compression',
	'compression_mode_none' => 'aucune',
	'compression_mode_all_whitespace_not_newlines' => 'supprimer les espaces mais garder les retours à la ligne',
	'compression_mode_all_whitespace' => 'supprimer les espaces et les retours à la ligne',

	// CSS
	'clean_css_comments_label_case' => 'Supprimer les commentaires des balises &lt;style&gt;',
	'clean_css_comments_explication' => 'Supprime tous les commentaires situés dans les balises &lt;style&gt;',

	'shift_link_tags_to_head_label_case' => 'Déplacer les balises &lt;link&gt; stylesheet dans le &lt;head&gt;',
	'shift_link_tags_to_head_explication' => 'Déplace toutes les balises &lt;link rel = "stylesheet"&gt; situées en dehors du &lt;head&gt; dans le &lt;head&gt;',

	'shift_style_tags_to_head_label_case' => 'Déplacer les balises &lt;style&gt; dans le &lt;head&gt; ',
	'shift_style_tags_to_head_explication' => 'Déplace toutes les balises &lt;style&gt; situées en dehors du &lt;head&gt; dans le &lt;head&gt;',

	'combine_style_tags_label_case' => 'Combiner les CSS inline dans une seule balise &lt;style&gt;',
	'combine_style_tags_explication' => 'Combine les CSS inline de la page dans une seule balise &lt;style&gt;. Ne combinera pas les balises ayant des attributs media différents.',

	// Javascript
	'clean_js_comments_label_case' => 'Supprimer les commentaires Javascript',
	'clean_js_comments_explication' => 'Supprime tous les commentaires situés dans les balises &lt;script&gt;.',

	'remove_comments_with_cdata_tags_label_case' => 'Supprimer les commentaires CDATA',
	'remove_comments_with_cdata_tags_explication' => 'En XHTML les balises &lt;script&gt; sont parfois encapsulées dans des blocs CDATA commentés pour les rendre compatibles avec XML. Par défaut, ces balises CDATA commentées sont conservées pour l\'intégrité du document.',

	'compression_ignore_script_tags_label_case' => 'Ne pas compresser le contenu dans &lt;script&gt; tags',
	'compression_ignore_script_tags_explication' => 'Ne minimisera pas le contenu des balises &lt;script&gt; cela pourrait casser le code Javascript.',

	'shift_script_tags_to_bottom_label_case' => 'Déplacer toutes les balises &lt;script&gt; à la fin du &lt;body&gt;',
	'shift_script_tags_to_bottom_explication' => 'Déplace toutes les balises &lt;script&gt; à la fin de de la page html',

	'combine_javascript_in_script_tags_label_case' => 'Combiner les Javascripts inline dans une seule balise &lt;script&gt;',
	'combine_javascript_in_script_tags_explication' => 'Combine les Javascripts inline de la page dans une seule balise &lt;style&gt;. Attention peut casser certaines pages.',

	'section_general' => 'Options générales',
	'section_css' => 'Options CSS',
	'section_javascript' => 'Options Javascript',
);
