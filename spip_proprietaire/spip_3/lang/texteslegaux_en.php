<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/texteslegaux?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'business_cards' => '{{{Business Cards}}}',

	// C
	'carte_visite_info' => 'You can view or download our {{cards}} by clicking the following links:
- {{[site card->@classique@]}}
- {{[full web card->@complete@]}}
- {{[responsible card->@responsable@]}}
- {{[administrator card->@administrateur@]}}
- {{[webmaster card->@webmaster@]}}',
	'conditions_utilisation_abus' => '{{{Abuse}}}

Pursuant to [French law n° 2004-575 of 21 june 2004 on confidence in the digital economy|See the text (legifrance.gouv.fr) [fr]->http://www.legifrance.gouv.fr/affichTexte.do?cidTexte=JORFTEXT000000801164], to report a content issue or if you are a victim of fraudulent use of the site {{[@nom_site@->@url_site@]}}, please contact the administrator site by e-mail: [@proprietaire_mail_administratif@->mailto:@proprietaire_mail_administratif@].
',
	'conditions_utilisation_cookies' => '{{{Cookies}}}

Some personal information and some witnesses markers may be recorded by {{[@nom_site@->@url_site@]}} on the personal computer of the user without his permission ({"cookies" or "Java applets"}). Unless explicitly stated otherwise [*], these technical procedures are not essential for the proper functioning of {{[@nom_site@->@url_site@]}} and their use can of course be disabled in the browser used by the Internet user without its navigation may be affected. {{None of this information is retained by the site after disconnecting from the Internet.}}
 
<small>[*] It is worth to precise here that access to different private areas of the site may require acceptance by the user of the deposit of a cookie on his personal computer to a security issue.</small>
',
	'conditions_utilisation_infos_personnelles' => '{{{Collection and processing of personal data}}}

{{Any information collected by {{[@nom_site@->@url_site@]}} are never disclosed to third parties.}}
<br />Except in special cases, this information comes from voluntary registration of an email provided by the user, allowing him to receive a brochure or newsletter, to request its inclusion in the editorial board of the site or to inquire about a point whatever.

Pursuant to [French law n° 78-17 of 6 january 1978 relating to data, files and freedoms|See the text (legifrance.gouv.fr) [fr]->http://www.legifrance.gouv.fr/affichTexte.do?cidTexte=JORFTEXT000000886460], {{the user has a right to access, correct or delete personal information stored on the {{[@nom_site@->@url_site@]}}, exercisable at any time with the administrator of {{[@nom_site@->@url_site@]}}.}} For such requests, please send a letter to @proprietaire_forme@ @proprietaire_nom@ or contact email address: [@proprietaire_mail_administratif@->mailto:@proprietaire_mail_administratif@].
@cnil_texte@
',
	'conditions_utilisation_intro' => '{{{De facto acceptance}}}

{{By using {{[@nom_site@->@url_site@]}}, the user acknowledges and accepts the conditions listed here.}}
',
	'conditions_utilisation_liens' => '{{{Links to @nom_site@}}}

The establishment of {{[@nom_site@->@url_site@]}} links does not require authorization if they are not used for commercial or advertising purposes. You can freely use the links below into any type of support.

||@nom_site@ hypertext links|You can freely use the links below.||
|{{Link overview}}|{{HTML Code}}|
|<a href="@url_site@" title="@nom_site@ - @descriptif_site@">@url_site@</a>|<code>
<a href="@url_site@" title="@nom_site@ - @descriptif_site@">@url_site@</a>
</code>|
|<a href="@url_site@" title="@nom_site@ - @descriptif_site@"><img src="@logo_site@" border="0" style="max-width:120px;max-height:120px;" /></a>|<code>
<a href="@url_site@" title="@nom_site@ - @descriptif_site@"><img src="@logo_site@" border="0" style="max-width:120px;max-height:120px;" /></a>
</code>|',
	'conditions_utilisation_responsabilite' => '{{{Disclaimer}}}

{{[@nom_site@->@url_site@]}} includes information provided by external companies or hypertext links to other sites or other external sources that have not been developed by {{[@nom_site@->@url_site@]}}. Behaviors of target sites, possibly malicious, cannot be attached to the responsibility of {{[@nom_site@->@url_site@]}}. More generally, the content available on this site is for informational purposes. It is up to the user to use this information wisely. {{[@nom_site@->@url_site@]}} is not responsible for informations, opinions and recommendations of third parties.

Because {{[@nom_site@->@url_site@]}} can not control these sites or external sources, {{[@nom_site@->@url_site@]}} cannot be held responsible for the availability of these sites or external sources and cannot accept any responsibility for the content, advertising, products, services or other materials available from such sites or resources. In addition, {{[@nom_site@->@url_site@]}} cannot be held responsible for any damage or loss or alleged consequential in connection with the use or the fact of having trusted the content, goods or services available from such sites or sources external.
',
	'copyright_info' => '© Copyright @date@ @nom_site@ | All rights reserved.',
	'createur_mentions_legales' => '[createur<-]{{{Designer / Creator}}}

Whole site {{[@nom_site@->@url_site@]}}, design, graphics and applications, has been created and developed@createur_administrateur_texte@ by @createur_forme@ {{@createur_nom@}}. @createur_web@ @createur_mail_texte@
',
	'createur_mentions_legales_idem' => '[createur<-]{{{Designer / Creator}}}

Website {{[@nom_site@->@url_site@]}} has been created by its owner.
',

	// E
	'egalement_administrateur' => ' and is administrated',
	'enregistre_pres' => 'registered to',

	// H
	'hebergeur_mentions_legales' => '[hebergeur<-]{{{Hosting}}}

{{[@nom_site@->@url_site@]}} is hosted by @hebergeur_forme@ {{@hebergeur_nom@}}@type_serveur_texte@@os_serveur_texte@. @hebergeur_web@ @hebergeur_mail_texte@

{{@proprietaire_nom@}} assumes no liability for any interruptions for {{[@nom_site@->@url_site@]}} and its services.
',
	'hebergeur_mentions_legales_idem' => '[hebergeur<-]{{{Hosting}}}

Website {{[@nom_site@->@url_site@]}} is hosted by its owner.
',

	// M
	'mention_cnil' => 'As such, {{[@nom_site@->@url_site@]}} was the subject of a statement from the {{<abbr title="National French Commission for informatical freedom - Commission Nationale de l’informatique et des Libertés - www.cnil.fr">CNIL</abbr>}} on @date_cnil@, registered under reference {{@numero_cnil@.}}',
	'mentions_legales_copyright' => '{{{Contents and copyright}}}

Pursuant to [articles L. 111-1 et L. 123-1 of the French Intellectual Property Code|See the text (legifrance.gouv.fr) [fr]->http://www.legifrance.gouv.fr/affichCode.do?cidTexte=LEGITEXT000006069414], the entire contents of this site ({texts, images, videos and any media in general}), unless explicitly stated otherwise, is {{protected by copyright}}. Reproduction, even partial, of the contents of pages on this site without prior agreement of @proprietaire_forme@ [{{@proprietaire_nom@}}->#proprietaire] is strictly prohibited.
',
	'mentions_legales_fonctionnement' => '{{{Operation site}}}

The pages and content on this site are generated by {{SPIP}} free software, distributed under the GNU / GPL ({General Public License}). You can use it freely to make your own website. For more information, visit the website: [spip.net->http://www.spip.net].

<small>
{{SPIP, Système de Publication pour l’Internet
<br />Copyright © 2001-2008, Arnaud Martin, Antoine Pitrou, Philippe Rivière et Emmanuel Saint-James.}}
</small>
',

	// O
	'os_du_serveur' => ' running on a system {{@os_serveur@}}',

	// P
	'pas_config' => 'There is not enough information to generate this page... Please fill up those information on [the plugin configuration area "Legal Statements"->@url_config@].',
	'pour_les_contacter' => 'To contact them, use the email address : [@mail@->mailto:@mail@].',
	'proprietaire_mentions_legales' => '[editeur<-]{{{Owner / Publisher}}}

{{[@nom_site@->@url_site@]}} is a website edited by @proprietaire_forme@ {{@proprietaire_nom@}} and published by {{@proprietaire_nom_responsable@}}{@proprietaire_fonction_responsable_texte@}. Whole site {{[@nom_site@->@url_site@]}}, pages and contents, is the property of @proprietaire_forme@ {{@proprietaire_nom@}}. @proprietaire_web@
<br />{{[@nom_site@->@url_site@]}} administrative contact site : [@proprietaire_mail_administratif@->mailto:@proprietaire_mail_administratif@].
',

	// R
	'reportez_vous_au_site' => 'For more information, visit the website : [->@site@].',

	// S
	'sous_le_numero' => 'under reference',
	'sur_un_serveur' => ', on a {{@serveur@}} server',

	// T
	'test_fichier_langue' => 'TEST',

	// V
	'vcard_info' => 'If you use a mail manager, mobile phone, an address book or organizer, you can download our contact details at {{’vCard’ format}} ({Virtual Card}) below.

You can, depending on your system:
- directly use the following URL to download this information automatically: {{@vcard_url@}}
- view the card in your browser: {{[->@vcard_url@]}}
- save it to your computer ({put the file extension {{’.VCF’}} if it’s not the case}): {{[->@vcard_url_download@]}}'
);

?>
