<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/spip_proprietaire/spip_3/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'business_cards' => '{{{Business Cards}}}',

	// C
	'carte_visite_info' => 'Vous pouvez visualiser ou télécharger nos {{cartes de visite}} en cliquant sur les liens suivants :
- {{[carte de visite du site->@classique@]}}
- {{[carte de visite web complète->@complete@]}}
- {{[carte de visite du responsable->@responsable@]}}
- {{[carte de visite de l’administrateur->@administrateur@]}}
- {{[carte de visite du webmestre du site->@webmaster@]}}',
	'conditions_utilisation_abus' => '{{{Abus}}}

En application de la [loi n° 2004-575 du 21 juin 2004 pour la confiance dans l’économie numérique|Voir le texte (legifrance.gouv.fr)->http://www.legifrance.gouv.fr/affichTexte.do?cidTexte=JORFTEXT000000801164], pour signaler un contenu litigieux ou si vous êtes victime d’une utilisation frauduleuse du site {{[@nom_site@->@url_site@]}}, merci de contacter l’administrateur du site à l’adresse courriel : [@proprietaire_mail_administratif@->mailto:@proprietaire_mail_administratif@].
',
	'conditions_utilisation_cookies' => '{{{Cookies}}}

Certaines informations nominatives et certains marqueurs témoins peuvent être enregistrées par {{[@nom_site@->@url_site@]}} sur l’ordinateur personnel de l’internaute sans expression de la volonté de ce dernier ({"cookies" ou "applets JAVA"}). Sauf indication contraire explicite [*], ces procédés techniques ne sont pas indispensables au bon fonctionnement de {{[@nom_site@->@url_site@]}} et leur utilisation peut bien entendu être désactivée dans le navigateur utilisé par l’internaute sans que sa navigation en soit affectée. {{Aucune de ces informations n’est conservée par le site après déconnexion de l’internaute.}}

<small>[*] Il est utile de préciser ici que les accès aux différents espaces privés du site peuvent nécessiter l’acceptation par l’internaute du dépôt d’un cookie sur son ordinateur personnel pour une question de sécurité.</small>
',
	'conditions_utilisation_infos_personnelles' => '{{{Collecte et traitement de données personnelles}}}

{{Les informations éventuellement recueillies par {{[@nom_site@->@url_site@]}} ne sont jamais communiquées à des tiers.}}
<br />Sauf cas particulier, ces informations proviennent de l’enregistrement volontaire d’une adresse courriel fournie par l’internaute, lui permettant de recevoir une documentation ou une newsletter, de demander son inscription dans le comité rédactionnel du site ou de se renseigner sur un point quelconque. 

En application de la [loi n° 78-17 du 6 janvier 1978 relative à l’informatique, aux fichiers et aux libertés|Voir le texte (legifrance.gouv.fr)->http://www.legifrance.gouv.fr/affichTexte.do?cidTexte=JORFTEXT000000886460], {{l’internaute dispose d’un droit d’accès, de rectification et de suppression des informations personnelles le concernant stockées par {{[@nom_site@->@url_site@]}}, qu’il peut exercer à tout moment auprès de l’administrateur de {{[@nom_site@->@url_site@]}}.}} Pour une demande de ce type, veuillez adresser un courrier à @proprietaire_forme@ @proprietaire_nom@ ou contacter l’adresse courriel : [@proprietaire_mail_administratif@->mailto:@proprietaire_mail_administratif@].
@cnil_texte@
',
	'conditions_utilisation_intro' => '{{{Acceptation de fait}}}

{{En utilisant {{[@nom_site@->@url_site@]}}, l’internaute prend note et accepte les conditions ici énumérées.}}
',
	'conditions_utilisation_liens' => '{{{Liens vers @nom_site@}}}

L’établissement de lien vers {{[@nom_site@->@url_site@]}} ne requiert pas d’autorisation dès lors qu’ils ne sont pas utilisés à des fins commerciales ou publicitaires. Vous pouvez utiliser librement les liens ci-dessous sur tout type de support.

||Liens hypertextes vers @nom_site@|Vous pouvez utiliser librement les liens ci-dessous.||
|{{Aperçu du lien}}|{{Code HTML}}|
|<a href="@url_site@" title="@nom_site@ - @descriptif_site@">@url_site@</a>|<code>
<a href="@url_site@" title="@nom_site@ - @descriptif_site@">@url_site@</a>
</code>|
|<a href="@url_site@" title="@nom_site@ - @descriptif_site@"><img src="@logo_site@" border="0" style="max-width:120px ;max-height:120px ;" /></a>|<code>
<a href="@url_site@" title="@nom_site@ - @descriptif_site@"><img src="@logo_site@" border="0" style="max-width:120px ;max-height:120px ;" /></a>
</code>|
',
	'conditions_utilisation_responsabilite' => '{{{Limitation de responsabilité}}}

{{[@nom_site@->@url_site@]}} comporte des informations mises à disposition par des sociétés externes ou des liens hypertextes vers d’autres sites ou d’autres sources externes qui n’ont pas été développés par {{[@nom_site@->@url_site@]}}. Les comportements des sites cibles, éventuellement malveillants, ne sauraient être rattachés à la responsabilité de {{[@nom_site@->@url_site@]}}. Plus généralement, le contenu mis à disposition sur ce site est fourni à titre informatif. Il appartient à l’internaute d’utiliser ces informations avec discernement. La responsabilité de {{[@nom_site@->@url_site@]}} ne saurait être engagée de fait quant aux informations, opinions et recommandations formulées par des tiers.

Dans la mesure où {{[@nom_site@->@url_site@]}} ne peut contrôler ces sites ou sources externes, {{[@nom_site@->@url_site@]}} ne peut être tenu pour responsable de la mise à disposition de ces sites ou sources externes et ne peut supporter aucune responsabilité quant aux contenus, publicités, produits, services ou tout autre matériel disponible sur ou à partir de ces sites ou sources externes. De plus, {{[@nom_site@->@url_site@]}} ne pourra être tenu responsable de tous dommages ou pertes avérés ou allégués consécutifs ou en relation avec l’utilisation ou le fait d’avoir fait confiance au contenu, à des biens ou des services disponibles sur ces sites ou sources externes. 
',
	'copyright_info' => '© Copyright @date@ @nom_site@ | Tous droits réservés.',
	'createur_mentions_legales' => '[createur<-]{{{Concepteur / Créateur}}}

L’ensemble du site {{[@nom_site@->@url_site@]}}, conception, charte graphique et applications, a été créé et développé@createur_administrateur_texte@ par @createur_forme@ {{@createur_nom@}}. @createur_web@ @createur_mail_texte@
',
	'createur_mentions_legales_idem' => '[createur<-]{{{Concepteur / Créateur}}}

Le site {{[@nom_site@->@url_site@]}} a été créé par l’éditeur.
',

	// E
	'egalement_administrateur' => ' et est administré',
	'enregistre_pres' => 'enregistré auprès de',

	// H
	'hebergeur_mentions_legales' => '[hebergeur<-]{{{Hébergeur}}}

{{[@nom_site@->@url_site@]}} est hébergé par @hebergeur_forme@ {{@hebergeur_nom@}}@type_serveur_texte@@os_serveur_texte@. @hebergeur_web@ @hebergeur_mail_texte@

{{@proprietaire_nom@}} décline toute responsabilité quant aux éventuelles interruptions du site {{[@nom_site@->@url_site@]}} et de ses services.
',
	'hebergeur_mentions_legales_idem' => '[hebergeur<-]{{{Hébergeur}}}

Le site {{[@nom_site@->@url_site@]}} est hébergé par l’éditeur.
',

	// M
	'mention_cnil' => 'À ce titre, {{[@nom_site@->@url_site@]}} a fait l’objet d’une déclaration auprès de la {{<abbr title="Commission Nationale de l\'Informatique et des Libertés - www.cnil.fr">CNIL</abbr>}} le @date_cnil@, enregistrée sous la référence {{@numero_cnil@.}}',
	'mentions_legales_copyright' => '{{{Contenus et droits de reproduction}}}

En application des [articles L. 111-1 et L. 123-1 du Code de la Propriété Intellectuelle|Voir le texte (legifrance.gouv.fr)->http://www.legifrance.gouv.fr/affichCode.do?cidTexte=LEGITEXT000006069414], l’ensemble des contenus de ce site ({textes, images, vidéos et tout média en général}), sauf mention contraire explicite, est {{protégé par le droit d’auteur}}. La reproduction, même partielle, des contenus des pages de ce site sans accord préalable de @proprietaire_forme@ [{{@proprietaire_nom@}}->#proprietaire] est strictement interdite.
',
	'mentions_legales_fonctionnement' => '{{{Fonctionnement du site}}}

Les pages et le contenu de ce site sont générés par le logiciel libre {{SPIP}}, distribué sous licence GNU / GPL ({Licence Publique Générale}). Vous pouvez l’utiliser librement pour réaliser votre propre site web. Pour plus d’informations, reportez-vous au site : [spip.net->http://www.spip.net].

<small>
{{SPIP, Système de Publication pour l’Internet
<br />Copyright © 2001-2008, Arnaud Martin, Antoine Pitrou, Philippe Rivière et Emmanuel Saint-James.}}
</small>
',

	// O
	'os_du_serveur' => ' fonctionnant sur un système {{@os_serveur@}}',

	// P
	'pas_config' => 'Il n’y a pas assez d’information pour générer cette page ... Veuillez renseigner ces informations sur [l’espace de configuration du plugin "Mentions Légales"->@url_config@].',
	'pour_les_contacter' => 'Pour les contacter, utilisez l’adresse courriel : [@mail@->mailto:@mail@].',
	'proprietaire_mentions_legales' => '[editeur<-]{{{Propriétaire / Éditeur}}}

{{[@nom_site@->@url_site@]}} est un site web édité par @proprietaire_forme@ {{@proprietaire_nom@}} et publié sous la direction de {{@proprietaire_nom_responsable@}}{@proprietaire_fonction_responsable_texte@}. L’ensemble du site {{[@nom_site@->@url_site@]}}, pages et contenus, est la propriété de @proprietaire_forme@ {{@proprietaire_nom@}}. @proprietaire_web@
<br />Contact administratif du site {{[@nom_site@->@url_site@]}} : [@proprietaire_mail_administratif@->mailto:@proprietaire_mail_administratif@].
',

	// R
	'reportez_vous_au_site' => 'Pour plus d’informations, reportez-vous au site : [->@site@].',

	// S
	'sous_le_numero' => 'sous le numéro',
	'sur_un_serveur' => ', sur un serveur {{@serveur@}}',

	// T
	'test_fichier_langue' => 'TEST',

	// V
	'vcard_info' => 'Si vous utilisez un gestionnaire de mails, un téléphone portable, un carnet d’adresse ou un organiseur électronique, vous pouvez télécharger nos coordonnées complètes au {{format ’vCard’}} ({Virtual Card}) ci-dessous.

Vous pouvez, selon votre système :
- utiliser directement l’adresse URL ci-dessous pour charger automatiquement ces informations : {{@vcard_url@}}
- visualiser la carte dans votre navigateur : {{[->@vcard_url@]}}
- l’enregistrer sur votre ordinateur ({indiquer obligatoirement une extension de fichier {{’.VCF’}} si ce n’est pas le cas}) : {{[->@vcard_url_download@]}}'
);

?>
