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

	'merge_multiple_head_tags_label_case' => 'Fusionner les balises &lt;head&gt; multiples',
	'merge_multiple_head_tags_explication' => 'S\'il y a plusieurs balises &lt;head&gt; dans le document, leur contenu sera fusionné dans la première.',

	'merge_multiple_body_tags_label_case' => 'Fusionner les balises &lt;body&gt; multiples',
	'merge_multiple_body_tags_explication' => 'S\'il y a plusieurs balises &lt;body&gt; dans le document, leur contenu sera fusionné dans la première.',

	// CSS
	'clean_css_comments_label_case' => 'Supprimer les commentaires des balises &lt;style&gt;',
	'clean_css_comments_explication' => 'Supprime tous les commentaires situés dans les balises &lt;style&gt;',

	'remove_comments_with_cdata_tags_css_label_case' => 'Supprimer les commentaires CDATA',
	'remove_comments_with_cdata_tags_css_explication' => 'en XHTML, les contenus à l\'intérieur des balises de &lt;style&gt; sont parfois encapsulés dans des commentaires CDATA pour la compatibilité XML. Si cette option est activée, les commentaires CDATA sont supprimés.',

	'shift_link_tags_to_head_label_case' => 'Déplacer les balises &lt;link&gt; stylesheet dans le &lt;head&gt;',
	'shift_link_tags_to_head_explication' => 'Déplace toutes les balises &lt;link rel = "stylesheet"&gt; situées en dehors du &lt;head&gt; à l\'intérieur',

	'ignore_link_schema_tags_label_case' => 'Ignorer les balises &lt;link&gt; Schema.org (microdata)',
	'ignore_link_schema_tags_explication' => 'Les balises &lt;link&gt; peuvent aussi être utilisées dans les balises &lt;body&gt; pour spécifier des Microdatas (Schema.org). A juste titre, ces balises n\'appartiennent pas au &lt;head&gt;, elles sont donc ignorées par défaut.',

	'shift_meta_tags_to_head_label_case' => 'Déplacer les balises &lt;meta&gt; dans le &lt;head&gt;',
	'shift_meta_tags_to_head_explication' => 'Option de commodité pour les pages qui sont construites dynamiquement dans le back-end. Déplace n\'importe quelle balise situées en dehors du &lt;head&gt; à l\'intérieur.',

	'ignore_meta_schema_tags_label_case' => 'Ignorer les balises &lt;meta&gt; Schema.org (microdata)',
	'ignore_meta_schema_tags_explication' => '&lt;meta&gt; les balises peuvent aussi être utilisées dans les balises &lt;body&gt; pour spécifier les microdonnées Schema.org. A juste titre, ces balises n\'appartiennent pas à &lt;head&gt;, elles sont donc ignorées par défaut.',

	'shift_style_tags_to_head_label_case' => 'Déplacer les balises &lt;style&gt; dans le &lt;head&gt;',
	'shift_style_tags_to_head_explication' => 'Déplace toutes les balises &lt;style&gt; situées en dehors du &lt;head&gt; à l\'intérieur',

	'combine_style_tags_label_case' => 'Combiner les CSS inline dans une seule balise &lt;style&gt;',
	'combine_style_tags_explication' => 'Combine les CSS inline de la page dans une seule balise &lt;style&gt;. Ne combinera pas les balises ayant des attributs media différents.',

	// Javascript
	'clean_js_comments_label_case' => 'Supprimer les commentaires Javascript',
	'clean_js_comments_explication' => 'Supprime tous les commentaires situés dans les balises &lt;script&gt;.',

	'remove_comments_with_cdata_tags_js_label_case' => 'Supprimer les commentaires CDATA',
	'remove_comments_with_cdata_tags_js_explication' => 'en XHTML, les contenus à l\'intérieur des balises de &lt;script&gt; sont parfois encapsulés dans des commentaires CDATA pour la compatibilité XML. Si cette option est activée, les commentaires CDATA sont supprimés.',

	'compression_ignored_tags_label_case' => 'Balises ignorées par la compression',
	'compression_ignored_tags_explication' => 'Liste des balises (en minuscules) pour lesquelles ignorer la compression. Ceci afin d\'empêcher HTML Minifier d\'interférer avec le comportement des balises comme &lt;textarea&gt; et &lt;pre&gt;, où les espaces blancs supprimés peuvent affecter le contenu affiché.',

	'shift_script_tags_to_bottom_label_case' => 'Déplacer toutes les balises &lt;script&gt; à la fin du &lt;body&gt;',
	'shift_script_tags_to_bottom_explication' => 'Déplace toutes les balises &lt;script&gt; à la fin de de la page html',

	'combine_javascript_in_script_tags_label_case' => 'Combiner les Javascripts inline dans une seule balise &lt;script&gt;',
	'combine_javascript_in_script_tags_explication' => 'Combine les Javascripts inline de la page dans une seule balise &lt;script&gt;. Attention peut casser certaines pages.',

	'ignore_async_and_defer_tags_label_case' => 'Ne pas déplacer les balises &lt;script&gt; ayant un attribut async ou defer',
	'ignore_async_and_defer_tags_explication' => 'Les balises &lt;script&gt; qui ont les attributs async ou defer ne seront pas déplacées vers le bas de la page. Il est recommandé d\'activer ce paramètre, car ces balises ne sont pas bloquantes et n\'ont donc pas besoin d\'être déplacées.',

	// Autres
	'section_general' => 'Options générales',
	'section_css' => 'Options CSS',
	'section_javascript' => 'Options Javascript',

	'config_super_safe' => 'ultra-sûres',
	'config_safe' => 'sûres',
	'config_moderate' => 'modérées',
	'config_fully_optimised' => 'optimisées au maximum',
	'config_label' => 'Appliquer les options de compression et d’optimisation',
);
