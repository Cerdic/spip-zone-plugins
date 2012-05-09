<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/texteslegaux?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'business_cards' => '{{{Vizitky}}}',

	// C
	'carte_visite_info' => 'Našu {{vizitku}} si môžete pozrieť, keď kliknete na tieto odkazy:
- {{[vizitka stránky,->@classique@]}}
- {{[vizitka celého webu,->@complete@]}}
- {{[vizitka človeka zodpovedného za stránku,->@responsable@]}}
- {{[vizitka administrátora,->@administrateur@]}}
- {{[vizika webmastera.->@webmaster@]}}',
	'conditions_utilisation_abus' => '{{{Abuse}}}

Pursuant to [French law n° 2004-575 of 21 june 2004 on confidence in the digital economy|See the text (legifrance.gouv.fr) [fr]->http://www.legifrance.gouv.fr/affichTexte.do?cidTexte=JORFTEXT000000801164], to report a content issue or if you are a victim of fraudulent use of the site {{[@nom_site@->@url_site@]}}, please contact the administrator site by e-mail: [@proprietaire_mail_administratif@->mailto:@proprietaire_mail_administratif@].
', # NEW
	'conditions_utilisation_cookies' => '{{{Cookies}}}

Some personal information and some witnesses markers may be recorded by {{[@nom_site@->@url_site@]}} on the personal computer of the user without his permission ({"cookies" or "Java applets"}). Unless explicitly stated otherwise [*], these technical procedures are not essential for the proper functioning of {{[@nom_site@->@url_site@]}} and their use can of course be disabled in the browser used by the Internet user without its navigation may be affected. {{None of this information is retained by the site after disconnecting from the Internet.}}
 
<small>[*] It is worth to precise here that access to different private areas of the site may require acceptance by the user of the deposit of a cookie on his personal computer to a security issue.</small>
', # NEW
	'conditions_utilisation_infos_personnelles' => '{{{Collection and processing of personal data}}}

{{Any information collected by {{[@nom_site@->@url_site@]}} are never disclosed to third parties.}}
<br />Except in special cases, this information comes from voluntary registration of an email provided by the user, allowing him to receive a brochure or newsletter, to request its inclusion in the editorial board of the site or to inquire about a point whatever.

Pursuant to [French law n° 78-17 of 6 january 1978 relating to data, files and freedoms|See the text (legifrance.gouv.fr) [fr]->http://www.legifrance.gouv.fr/affichTexte.do?cidTexte=JORFTEXT000000886460], {{the user has a right to access, correct or delete personal information stored on the {{[@nom_site@->@url_site@]}}, exercisable at any time with the administrator of {{[@nom_site@->@url_site@]}}.}} For such requests, please send a letter to @proprietaire_forme@ @proprietaire_nom@ or contact email address: [@proprietaire_mail_administratif@->mailto:@proprietaire_mail_administratif@].
@cnil_texte@
', # NEW
	'conditions_utilisation_intro' => '{{{Akceptovanie de facto}}}

{{Využívaním stránky {{[@nom_site@->@url_site@]}} používateľ potvrdzuje a akceptuje podmienky, ktoré sú tu uvedené.}}
',
	'conditions_utilisation_liens' => '{{{Links to @nom_site@}}}

The establishment of {{[@nom_site@->@url_site@]}} links does not require authorization if they are not used for commercial or advertising purposes. You can freely use the links below into any type of support.

||@nom_site@ hypertext links|You can freely use the links below.||
|{{Link overview}}|{{HTML Code}}|
|<a href="@url_site@" title="@nom_site@ - @descriptif_site@">@url_site@</a>|<code>
<a href="@url_site@" title="@nom_site@ - @descriptif_site@">@url_site@</a>
</code>|
|<a href="@url_site@" title="@nom_site@ - @descriptif_site@"><img src="@logo_site@" border="0" /></a>|<code>
<a href="@url_site@" title="@nom_site@ - @descriptif_site@"><img src="@logo_site@" border="0" /></a>
</code>|
', # NEW
	'conditions_utilisation_responsabilite' => '{{{Disclaimer}}}

{{[@nom_site@->@url_site@]}} includes information provided by external companies or hypertext links to other sites or other external sources that have not been developed by {{[@nom_site@->@url_site@]}}. Behaviors of target sites, possibly malicious, cannot be attached to the responsibility of {{[@nom_site@->@url_site@]}}. More generally, the content available on this site is for informational purposes. It is up to the user to use this information wisely. {{[@nom_site@->@url_site@]}} is not responsible for informations, opinions and recommendations of third parties.

Because {{[@nom_site@->@url_site@]}} can not control these sites or external sources, {{[@nom_site@->@url_site@]}} cannot be held responsible for the availability of these sites or external sources and cannot accept any responsibility for the content, advertising, products, services or other materials available from such sites or resources. In addition, {{[@nom_site@->@url_site@]}} cannot be held responsible for any damage or loss or alleged consequential in connection with the use or the fact of having trusted the content, goods or services available from such sites or sources external.
', # NEW
	'copyright_info' => '© Copyright @date@ @nom_site@ | Všetky práva vyhradené.',
	'createur_mentions_legales' => '[createur<-]{{{Dizajnér/autor}}}

Celú stránku {{[@nom_site@->@url_site@]}}, dizajn, grafiku a aplikácie vytvoril(a) a naprogramoval(a) @createur_administrateur_texte@ z(o) @createur_forme@ {{@createur_nom@.}} @createur_web@ @createur_mail_texte@
',
	'createur_mentions_legales_idem' => '[createur<-]{{{Dizajnér/autor}}}

Internetovú stránku {{[@nom_site@->@url_site@]}} vytvoril jej šéfredaktor.
',

	// E
	'egalement_administrateur' => ' a riadi ju',
	'enregistre_pres' => 'zaregistrovaná u',

	// H
	'hebergeur_mentions_legales' => '[Poskytovateľ webhostingu <-]{{{Poskytovateľ webhostingu}}}

Stránku {{[@nom_site@->@url_site@]}} prevádzkuje @hebergeur_forme@ {{@hebergeur_nom@}}@type_serveur_texte@@os_serveur_texte@. @hebergeur_web@ @hebergeur_mail_texte@

{{@proprietaire_nom@}} neprijíma žiadnu zodpovednosť pre prerušenie prevádzky stránky {{[@nom_site@->@url_site@]}} a jej služieb.
',
	'hebergeur_mentions_legales_idem' => '[poskytovateľ webhostingu<-]

Internetovú stránku {{[@nom_site@->@url_site@]}} prevádzkuje jej šéfredaktor.
',

	// M
	'mention_cnil' => 'Tento nadpisom {{[@nom_site@->@url_site@]}} mal predmet  deklarácie u {{<abbr title="Francúzska štátna komisia pre počítačové slobody - Commission Nationale de l\'informatique et des Libertés - www.cnil.fr">CNIL</abbr>}} dňa @date_cnil@, zaregistrovaná pod číslom {{@numero_cnil@.}}',
	'mentions_legales_copyright' => '{{{Contents and copyright}}}

Pursuant to [articles L. 111-1 et L. 123-1 of the French Intellectual Property Code|See the text (legifrance.gouv.fr) [fr]->http://www.legifrance.gouv.fr/affichCode.do?cidTexte=LEGITEXT000006069414], the entire contents of this site ({texts, images, videos and any media in general}), unless explicitly stated otherwise, is {{protected by copyright}}. Reproduction, even partial, of the contents of pages on this site without prior agreement of @proprietaire_forme@ [{{@proprietaire_nom@}}->#proprietaire] is strictly prohibited.
', # MODIF
	'mentions_legales_fonctionnement' => '{{{Operation site}}}

The pages and content on this site are generated by {{SPIP}} free software, distributed under the GNU / GPL ({General Public License}). You can use it freely to make your own website. For more information, visit the website: [spip.net->http://www.spip.net].

<small>
{{SPIP, Système de Publication pour l\'Internet
<br />Copyright © 2001-2008, Arnaud Martin, Antoine Pitrou, Philippe Rivière et Emmanuel Saint-James.}}
</small>
', # NEW

	// O
	'os_du_serveur' => ' funguje na systéme {{@os_serveur@}}',

	// P
	'pas_config' => 'Na vytvorenie tejto stránky nie je dosť údajov. Vyplňte, prosím, tieto údaje v [časti s nastaveniami zásuvného modulu s názvom "Informácie právneho charakteru".->@url_config@]',
	'pour_les_contacter' => 'Ak ich chcete kontaktovať, použite e-mailovú adresu: [@mail@.->mailto:@mail@]',
	'proprietaire_mentions_legales' => '[editeur<-]{{{Majiteľ/šéfredaktor}}}

{{[@nom_site@->@url_site@]}} je internetová stránka, ktorú upravuje @proprietaire_forme@ {{@proprietaire_nom@}} a publikuje{{@proprietaire_nom_responsable@}}{@proprietaire_fonction_responsable_texte@.} Celá stránka {{[@nom_site@->@url_site@,]}} (pod)stránky a ich obsah, sú majetkom @proprietaire_forme@ {{@proprietaire_nom@.}} @proprietaire_web@
<br />{{[@nom_site@->@url_site@]}} kontakt na správu stránky: [@proprietaire_mail_administratif@->mailto:@proprietaire_mail_administratif@].
',

	// R
	'reportez_vous_au_site' => 'Pre viac informácií navštívte stránku: [->@site@]',

	// S
	'sous_le_numero' => 'pod číslom',
	'sur_un_serveur' => ', na serveri {{@serveur@}}',

	// T
	'test_fichier_langue' => 'TEST',

	// V
	'vcard_info' => 'If you use a mail manager, mobile phone, an address book or organizer, you can download our contact details at {{\'vCard\' format}} ({Virtual Card}) below.

You can, depending on your system:
- directly use the following URL to download this information automatically: {{@vcard_url@}}
- view the card in your browser: {{[->@vcard_url@]}}
- save it to your computer ({put the file extension {{\'.VCF\'}} if it\'s not the case}): {{[->@vcard_url_download@]}}' # NEW
);

?>
