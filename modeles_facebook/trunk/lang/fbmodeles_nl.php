<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/fbmodeles?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cf_navigation' => 'Zie [navigatie kolom->@url@]',
	'cfg_comment_appid' => 'Identificatie van een applicatie.',
	'cfg_comment_border_color' => 'Geef de kleur voor de rand, voorafgegaan door een hekje.',
	'cfg_comment_colorscheme' => 'Kies hier het kleurenschema dat voor de modules moet worden gebruikt.',
	'cfg_comment_font' => 'Kies hier het lettertype dat moet worden gebruikt in de modules.',
	'cfg_comment_identifiants' => '{{Gebruik onderstaande velden om de identificaties op te geven die je wilt gebruiken.}} Ze zijn niet verplicht, maar laten je toe statistieken van Facebook te volgen.',
	'cfg_comment_pageid' => 'Identificatie van een Facebook bladzijde.',
	'cfg_comment_reglages' => '{{Hier kun je de instellingen van Javascript voor Facebook instellen.}} Standaard wordt gebruik gemaakt van XFBML ({SDK javascript Facebook}) maar je kunt dit uitschakelen, waardoor frames gebruikt worden.',
	'cfg_comment_url_page' => 'Volledige URL van de Facebook bladzijde of het van profiel; deze wordt standaard door de modellen gebruikt (URL van het type "<code>http://www.facebook.com/...</code>").',
	'cfg_comment_userid' => 'Een of meer gebruikersnamen, gescheiden door een komma.',
	'cfg_comment_xfbml' => 'Gebruik van javascript van de Facebook SDK.<br />Wanneer je "nee" kiest, wordt iframe gebruikt.',
	'cfg_descr' => 'Geef hier je Facebook indentificatie op.<br /><br />Meer info op: [facebook.com/insights->http://www.facebook.com/insights/].

Voor "Open Graph" moet je "insert_head_og" in de heading opnemen: {{#MODELE{insert_head_og}{env}}}.
<br /><br />Meer info op: [developers.facebook.com->http://developers.facebook.com/docs/opengraph/].',
	'cfg_descr_titre' => 'Facebook modellen',
	'cfg_identifiants' => 'Identificatie Facebook',
	'cfg_label_appid' => 'App ID',
	'cfg_label_border_color' => 'Standaard kleur rand',
	'cfg_label_colorscheme' => 'Kleurenschema',
	'cfg_label_font' => 'Standaard lettertype',
	'cfg_label_pageid' => 'Page ID',
	'cfg_label_titre' => 'Configuratie van Facebook modellen',
	'cfg_label_url_page' => 'URL van de bladzijde of het profiel',
	'cfg_label_userid' => 'User ID',
	'cfg_label_xfbml' => 'Gebruik van XFBML',
	'cfg_reglages' => 'Standaard instellingen',

	// D
	'defaut' => 'Standaard',
	'doc_chapo' => 'Plugin Facebook Modellen voor SPIP 2.0 ({en hoger}) biedt een set modellen om op eenvoudig wijze functionaliteit van Facebook te integreren.',
	'doc_en_ligne' => 'Documentatie op Spip-Contrib',
	'doc_titre_court' => 'Documentatie Facebook Modellen',
	'doc_titre_page' => 'Documentatiebladzijde van Facebook Modellen',
	'documentation' => '{{{Gebruik van de plugin}}}

Zoals hierboven aangegeven, bevatten de modellen direct de gewenste opties.

Elk model heeft meerdere opties, waarvan enkele verplicht zijn. Een volledige lijst vind je in de map "<code>modeles/</code>" van de plugin.

De plugin bevat ook een {{Open Graph}} module, de metadata die gebruikt wordt door Facebook. Om deze te gebruiken moet je handmatig in de modellen van je bladzijde een "{{insert_head_og}}" invoegen.

{{Let op:}} Dit model moet de huidige waardes van de bladzijde kennen en dus in elk model ({"article.html", "rubrique.html" ...}) worden opgenomen. Niet in een algemene ({"inc_head.html"}) die aangeeft: 
<cadre class=\'spip\'>
{{#MODELE{insert_head_og}{env}}}
</cadre>
',

	// E
	'exemple' => '{{{Voorbeeld}}}

De verschillende blokken hieronder geven een voorbeeld van ieder model. Gegevens zijn fictief. Kijk bij ieder model voor de opties.',

	// F
	'fb_modeles' => 'Facebook Modellen',

	// I
	'info_doc' => 'Heb je problemen met het weergeven van deze bladzijde, [klik dan hier->@link@].',
	'info_doc_titre' => 'Opmerking over het weergeven van deze bladzijde',
	'info_skel_contrib' => 'Volledige documentatie online op [spip-contrib->http://www.spip-contrib.fr/?article3567].',
	'info_skel_doc' => 'Deze documentatie-bladzijde is uitgevoerd als standaard SPIP-model ({bestanden in map "squelettes-dist/"}). Lukt het je niet deze bladzijde te tonen, of gebruikt jouw site afwijkende modellen, dan lukt het waarschijnlijk met een van onderstaande links:

-* [Modus "tekst"->@mode_brut@] ({eenvoudige HTML + INSERT_HEAD})
-* [Modus "model Zpip"->@mode_zpip@] ({model Z compatibel})
-* [Modus "model SPIP"->@mode_spip@] ({compatibel})',

	// J
	'javascript_inactif' => 'Javascript is niet geactiveerd in de browser. Bepaalde functionaliteit zal niet werken...',

	// L
	'licence' => 'Plugin voor SPIP 2.0+ : {{"Facebook Models" - copyright © 2009 [Piero Wbmstr->http://www.spip-contrib.net/PieroWbmstr] onder ([GPL->http://www.opensource.org/licenses/gpl-3.0.html]}) licentie}}.',

	// N
	'new_window' => 'Nieuw venster',
	'non' => 'nee',

	// O
	'oui' => 'ja',

	// P
	'page_test' => 'Testpagina (lokaal)',
	'page_test_in_new_window' => 'Testpagina in nieuw venster',
	'personnalisation' => '{{{Personalisatie}}}

Elk model toont zijn inhoud in een blok van het type <code>div</code> met een CSS-class van het type <code>fb_modeles fb_XXX</code> waarbij {{XXX}} de naam van het model is. Zo kan ieder model in een stylesheet gepersonaliseerd worden.


Bijvoorbeeld voor module Facebook "Send":
<cadre class="spip">
<div class="fb_modeles fb_send">
     ... inhoud ... 
</div>
</cadre>',

	// S
	'sep' => '----',

	// T
	'titre_original' => 'Facebook Modellen, plugin voor SPIP 2.0+'
);

?>
