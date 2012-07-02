<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/fbmodeles?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cf_navigation' => 'Pozri [stĺpec s navigáciou->@url@]',
	'cfg_comment_appid' => 'Identifikátor na hlasovaciu stránku aplikácie; aplikácia sa musí vytvoriť.',
	'cfg_comment_border_color' => 'Zadajte kód farby v šestnástkovej sústave AJ S úvodnou mriežkou.',
	'cfg_comment_colorscheme' => 'Tu vyberte predvolený profil podľa modulov, ktoré sa použijú na zobrazenie.',
	'cfg_comment_font' => 'Tu si môžete vybrať písmo, ktoré sa použije na zobrazenie modulov.',
	'cfg_comment_identifiants' => '{{Utilisez les champs ci-dessous pour préciser les différents identifiants que vous souhaitez utiliser.}} Ils ne sont pas obligatoires, mais peuvent permettre notamment de suivre des statistiques précises proposées par Facebook.', # NEW
	'cfg_comment_pageid' => 'Identifikátor stránky; stránku treba vytvoriť.',
	'cfg_comment_reglages' => '{{Vous pouvez ici choisir certains réglages concernant les outils javascript de Facebook.}} Par défaut, les modèles utilisent le langage XFBML ({SDK javascript Facebook}) mais vous pouvez désactiver cette fonctionnalité, les outils seront alors chargés en frames.', # NEW
	'cfg_comment_url_page' => 'Celá internetová adresa vašej stránky alebo vášho profilu na Facebooku; budú ju využívať šablóny v predvolených nastaveniach (URL typu "<code>http://www.facebook.com/...</code>").',
	'cfg_comment_userid' => 'Prihlacovací (-ie) údaj(e) administrátorov zásuvných modulov. Môžete ich zadať viac, a oddeliť ich čiarkami.',
	'cfg_comment_xfbml' => 'Využitie javaskriptovej knižnice SDK Facebook a priradeného jazyka. Ak si zvolíte možnosť "nie", moduly budú zobrazené v režime "in-iframe" (v ráme).',
	'cfg_descr' => 'Vous devez ici définir les différents identifiants fournis par le système Facebook.<br /><br />Plus d\'infos : [->http://www.facebook.com/insights/].

Pour inclure les balises "Open Graph" en en-tête de vos pages publiques, vous devez insérer le modèle "insert_head_og" en lui passant l\'environnement : {{#MODELE{insert_head_og}{env}}}.
<br /><br />Plus d\'infos : [->http://developers.facebook.com/docs/opengraph/].', # NEW
	'cfg_descr_titre' => 'Šablóny Facebooku',
	'cfg_identifiants' => 'Prihlásenie na Facebook',
	'cfg_label_appid' => 'Prihlásenie pre aplikáciu "App ID"',
	'cfg_label_border_color' => 'Predvolená farba pozadia',
	'cfg_label_colorscheme' => 'Farebná schéma',
	'cfg_label_font' => 'Predvolené písmo',
	'cfg_label_pageid' => 'Prihlásenie pre stránku "Page ID"',
	'cfg_label_titre' => 'Nastavenia šablón Facebooku',
	'cfg_label_url_page' => 'URL stránky alebo profilu',
	'cfg_label_userid' => 'Prihlásenie používateľa "User ID"',
	'cfg_label_xfbml' => 'Použitie XFBML',
	'cfg_reglages' => 'Predvolené nastavenia',

	// D
	'defaut' => 'Predvolené',
	'doc_chapo' => 'Zásuvný modul Šablóny pre Facebook pre SPIP 2.0 ({a vyšší}) ponúka rad šablón alebo orieškov umožňujúcich jednoducho a rýchlo využívať zásuvné moduly sociálnej siete, ktoré poskytuje Facebook.',
	'doc_en_ligne' => 'Dokumentácia',
	'doc_titre_court' => 'Dokumentácia Šablón Facebooku',
	'doc_titre_page' => 'Stránka s dokumentáciou zásuvného modulu Šablóny Facebooku',
	'documentation' => '{{{Utilisation du plugin}}}

Comme montré ci-dessus, les modèles s\'incluent directement en leur passant les options souhaitées.

Chaque modèle peut recevoir une liste d\'options, dont certaines sont nécessaires à son affichage. Pour une liste complète, reportez-vous aux informations des en-tête de fichiers de modèles, dans le répertoire "<code>modeles/</code>" du plugin.

Le plugin propose également un modèle générant des informations {{Open Graph}}, les metas informations utilisées par Facebook, propres à chaque objet SPIP. Pour l\'utiliser, vous devez ajouter manuellement en en-tête de vos squelettes le modèle "{{insert_head_og}}".

{{Attention - }}Ce modèle nécessite de recevoir l\'environnement courant, vous devez donc l\'inclure dans chacun des squelettes de pages ({"article.html", "rubrique.html" ...}) et non dans l\'inclusion globale en en-tête ({"inc_head.html"}) en indiquant : 
<cadre class=\'spip\'>
{{#MODELE{insert_head_og}{env}}}
</cadre>
', # NEW

	// E
	'exemple' => '{{{Exemple}}}

Les différents blocs ci-dessous vous présentent un exemple de chaque modèle avec des valeurs fictives. Reportez-vous au modèle correspondant pour les options.', # NEW

	// F
	'fb_modeles' => 'Šablóny Facebooku',

	// I
	'info_doc' => 'Ak sa vám táto stránka nezobrazuje správne, [kliknite sem.->@link@]',
	'info_doc_titre' => 'Poznámka o zobrazení tejto stránky',
	'info_skel_contrib' => 'Stránka s kompletnou dokumentáciou spip-contribu online: [->http://www.spip-contrib.fr/?article3567].',
	'info_skel_doc' => 'Cette page de documentation est conçue sous forme de squelette SPIP fonctionnant avec la distribution standard ({fichiers du répertoire "squelettes-dist/"}). Si vous ne parvenez pas à visualiser la page, ou que votre site utilise ses propres squelettes, les liens ci-dessous vous permettent de gérer son affichage :

-* [Mode "texte simple"->@mode_brut@] ({html simple + balise INSERT_HEAD})
-* [Mode "squelette Zpip"->@mode_zpip@] ({squelette Z compatible})
-* [Mode "squelette SPIP"->@mode_spip@] ({compatible distribution})', # NEW

	// J
	'javascript_inactif' => 'Javascript je vo vašom prehliadači vypnutý. Niektoré funkcie nebudú fungovať.',

	// L
	'licence' => 'Zásuvný modul pre SPIP >2.0: {{"Šablóny pre Facebook" – copyright © 2009 [Piero Wbmstr->http://www.spip-contrib.net/PieroWbmstr] s licenciou [GPL->http://www.opensource.org/licenses/gpl-3.0.html].}}',

	// N
	'new_window' => 'Nové okno',
	'non' => 'Nie',

	// O
	'oui' => 'Áno',

	// P
	'page_test' => 'Testovacia stránka (lokálne)',
	'page_test_in_new_window' => 'Testovacia stránka v novom okne',
	'personnalisation' => '{{{Personnalisation}}}

Chaque modèle présente son contenu dans un bloc de type <code>div</code> portant des classes CSS du type <code>fb_modeles fb_XXX</code> où {{XXX}} est le nom du modèle. Cela permet une personnalisation des styles pour l\'ensemble des modèles et pour chacun d\'eux.


Par exemple pour le module Facebook "Send" :
<cadre class="spip">
<div class="fb_modeles fb_send">
     ... contenu ... 
</div>
</cadre>', # NEW

	// S
	'sep' => '----',

	// T
	'titre_original' => 'Šablóny pre Facebook, zásuvný modul pre SPIP >2.0'
);

?>
