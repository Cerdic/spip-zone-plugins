<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/piwik?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_creer_site' => 'Vytvoriť stránku',
	'action_recuperer_liste' => 'Získať zoznam stránok',

	// C
	'cfg_description_piwik' => 'Tu môžete uviesť svoj identifikátor pre piwik, ako aj adresu svojho štatistického servera.',
	'cfg_erreur_recuperation_data' => 'Pri komunikácii so serverom sa vyskytol problém – prosím, skontrolujte adresu a token',
	'cfg_erreur_token' => 'Váš identifikačný reťazec je neplatný',
	'cfg_erreur_user_token' => 'Používateľské meno alebo reťazec nie sú správne.',

	// E
	'explication_adresse_serveur' => 'Zadajte adresu stránky bez "http://" alebo "https://" a bez lomky na konci',
	'explication_conformite_cnil' => 'Vloží <a href="http://www.cnil.fr/fileadmin/documents/approfondir/dossier/internet/Configuration_piwik.pdf"> JavaScriptovú funkciu,</a> ktorá umožňuje <a href="http://www.cnil.fr/vos-obligations/sites-web-cookies-et-autres-traceurs/outils-et-codes-sources/la-mesure-daudience/">nastaviť zhodu s kontrolnou cookie,</a> čo určuje CNIL.',
	'explication_creer_site' => 'Odkaz vám umožňuje vytvoriť stránku na serveri s Piwikom, ktorá bude potom dostupná na zozname. Pred kliknutím skontrolujte, či ste správne zadali adresu a názov svojej stránky v SPIPe, lebo tieto údaje sa budú neskôr používať.',
	'explication_exclure_ips' => 'Ak vymenujete niekoľko adries, ktoré majú byť vylúčené, oddeľte ich bodkočiarkami',
	'explication_identifiant_site' => 'Zoznam dostupných stránok na serveri s Piwikom bol automaticky získaný pomocou odoslaných údajov. Zo zoznamu si vyberte tú, ktorú chcete použiť.',
	'explication_mode_insertion' => 'Na vloženie kódu potrebného na správne fungovanie zásuvného modulu sú dva spôsoby. Cez "insert_head" (automatický spôsob, čo sa však nedá upraviť) alebo vložením tagu (manuálne, vložením tagu #PIWIK do päty vašich stránok), čo sa dá upraviť celkom podľa vašich želaní.',
	'explication_recuperer_liste' => 'Tento odkaz sa používa na získavanie zoznamu stránok, ktoré môže váš účet riadiť na vašom serveri s Piwikom.',
	'explication_restreindre_statut_prive' => 'Vyberte funkcie používateľov, ktorí nebudú zarátaní do štatistík pre súkromnú zónu',
	'explication_restreindre_statut_public' => 'Vyberte funkcie používateľov, ktorí sa nebudú brať do úvahy pri štatistikách pre verejne prístupnú stránku',
	'explication_token' => 'Identifikačný token je dostupný vo vašich osobných predvoľbách alebo v časti aplikácie na vašom serveri s Piwikom',

	// I
	'info_aucun_site_compte' => 'K vášmu účtu na Piwiku nie sú priradené žiadne stránky.',
	'info_aucun_site_compte_demander_admin' => 'O pridanie zodpovedajúcej stránky musíte požiadať administrátora svojho servera s Piwikom.',

	// L
	'label_adresse_serveur' => 'Internetová adresa servera (https:// alebo http://)',
	'label_comptabiliser_prive' => 'Pridať návštevy súkromnej zóny',
	'label_conformite_cnil' => 'Dodržiavanie CNIL',
	'label_creer_site' => 'Vytvoriť stránku na serveri s Piwikom',
	'label_exclure_ips' => 'Vylúčiť určité IP adresy',
	'label_identifiant_site' => 'Identifikátor vašej stránky na serveri s Piwikom',
	'label_mode_insertion' => 'Režim vkladania pre verejne prístupné stránky',
	'label_piwik_user' => 'Používateľský účet Piwiku',
	'label_recuperer_liste' => 'Získať zoznam stránok na serveri s Piwikom',
	'label_restreindre_auteurs_prive' => 'Obmedziť prístup určitým prihláseným používateľom (súkromná zóna)',
	'label_restreindre_auteurs_public' => 'Obmedziť prístup určitým prihláseným používateľom (verejné)',
	'label_restreindre_statut_prive' => 'Obmedziť prístup do súkromnej zóny pre určité funkcie',
	'label_restreindre_statut_public' => 'Obmedziť prístup do súkromnej zóny pre určité funkcie',
	'label_token' => 'Identifikačný reťazec na server',

	// M
	'mode_insertion_balise' => 'Vložiť pomocou tagu #PIWIK (musíte upraviť svoje šablóny)',
	'mode_insertion_pipeline' => 'Automatické vkladanie pomocou reťazenia údajov "insert_head"',

	// P
	'piwik' => 'Piwik',

	// T
	'texte_votre_identifiant' => 'Váš identifikátor',
	'textes_url_piwik' => 'Váš server pre piwik'
);

?>
