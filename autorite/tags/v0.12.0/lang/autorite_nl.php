<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/autorite?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_mots_cles' => 'Activeer het beheer per trefwoord',
	'admin_complets' => 'De volledige beheerders',
	'admin_restreints' => 'Beperkte beheerders?',
	'admin_tous' => 'Alle beheerders (inclusief beperkte)',
	'administrateur' => 'beheerder',
	'admins' => 'De beheerders',
	'admins_redacs' => 'Beheerders en Redacteurs',
	'admins_rubriques' => 'de aan rubrieken gekoppelde beheerders hebben:',
	'attention_crayons' => '<small><strong>Let op:</strong> Onderstaande instellingen werken slechts wanneer je een plugin gebruikt die aanpassing vanaf de publieke site mogelijk maakt (zoals bijvoorbeeld <a href="https://contrib.spip.net/Les-Crayons">Crayons</a>).</small>',
	'attention_version' => 'Let op. De volgende keuze werken mogelijk niet met jouw versie van SPIP:',
	'auteur_message_advitam' => 'De auteur van het bericht, ad vitam',
	'auteur_message_heure' => 'De auteur van het bericht, gedurende een uur',
	'auteur_modifie_article' => '<strong>Auteur past artikel aan</strong>: elke redacteur mag gepubliceerder artikelen aanpassen waarvan hij de auteur is.
	<br />
	<i>N.B.: deze optie geldt ook voor geregistreerde bezoekers, wanneer deze de auteur zijn en een specifieke interface beschikbaar is.</i>',
	'auteur_modifie_email' => '<strong>Redacteur past email aan</strong>: elke redacteur kan zijn eigen e-mailadres aanpassen.',
	'auteur_modifie_forum' => '<strong>Auteur modereert forum</strong>: elke redacteur kan het forum modereren van een artikel waarvan hij de auteur is.',
	'auteur_modifie_petition' => '<strong>Auteur modereert petitie</strong>: elke redacteur kan de petitie modereren van een artikel waarvan hij de auteur is.',

	// C
	'config_auteurs' => 'Configuratie van auteurs',
	'config_auteurs_rubriques' => 'Welk type auteurs kan worden <b>geassocieerd aan rubrieken</b>?',
	'config_auteurs_statut' => 'Wat is bij het aanmaken van een auteur de <b>standaard status</b>?',
	'config_plugin_qui' => 'Wie mag <strong>de configuratie aanpassen</strong> van plugins (activeren...) ?',
	'config_site' => 'Configuratie van de site',
	'config_site_qui' => 'Wie mag <strong>de configuratie aanpassen</strong> van de site?',
	'crayons' => 'Crayons',

	// D
	'deja_defini' => 'De volgende autorisaties werden al elders gedefinieerd:',
	'deja_defini_suite' => 'De plugin « Autorité » kan ze niet aanpassen. Sommige van onderstaande instellingen werken mogelijk niet.
	<br />Om di tprobleem op te lossen moet je controleren of het bestand <tt>mes_options.php</tt> (of een andere actieve plugin) deze functies heeft gedefinieerd.',
	'descriptif_1' => 'Deze configuratiepagina is voorbehouden aan webmasters van de site:',
	'descriptif_2' => '<p>Wanneer je deze lijst wilt aanpassen, moet je het bestand <tt>config/mes_options.php</tt> aanpassen (of aanmaken) en hier de lijst van andere webmasters op de volgende manier vermelden:</p>
<pre>&lt;?php
  define(
    \'_ID_WEBMESTRES\',
    \'1:5:8\');
?&gt;</pre>
<p>Vanaf SPIP 2.1 kunnen de rechten van webmaster aan een beheerder worden gegeven via de auteurpagina.</p>
<p>Let op: de op deze manier gedefinieerde webmasters hoeven geen authenticatie via FTP te doen voor gevoelige handelingen (zoals het aanpassen van de database).</p>

<a href=\'https://contrib.spip.net/Autorite\' class=\'spip_out\'>Zie documentatie</a>
',
	'details_option_auteur' => '<small><br />Momenteel werkt de optie «auteur» slechts voor geregistreerde auteurs (zoals bij forum op abonnement). En, indien actief, mogen de beheerders ook forums aanpassen.
	</small>',
	'droits_des_auteurs' => 'Rechten van auteurs',
	'droits_des_redacteurs' => 'Rechten van redacteurs',
	'droits_idem_admins' => 'dezelfde rechten als alle beheerders',
	'droits_limites' => 'rechten beperkt tot deze rubrieken',

	// E
	'effacer_base_option' => '<small><br />De aanbevolen optie is «niemand», de standaard optie van SPIP is «de beheerders» (maar altijd met FTP verificatie).</small>',
	'effacer_base_qui' => 'Wie mag de database van de site <strong>wissen</strong>?',
	'espace_publieur' => 'Open publicatieruimte',
	'espace_publieur_detail' => 'Kies hieronder een hoofdrubriek die als open publicatieruimte moet worden behandeld voor redacteurs en/of geregistreerde bezoekers (op voorwaarde dat hiervoor een interface zoals Crayons beschikbaar is, alsmede een formulier voor het aanleveren van een artikel):',
	'espace_publieur_qui' => 'Wil je publicatie openstellen — buiten beheerders:',
	'espace_wiki' => 'Wiki ruimte',
	'espace_wiki_detail' => 'Kies hieronder een hoofdrubriek die als wiki fungeert, dus aanpasbaar vanaf de publieke site (mits daarvoor een interface asls Crayons beschikbaar is):',
	'espace_wiki_mots_cles' => 'Wiki ruimte per trefwoord',
	'espace_wiki_mots_cles_detail' => 'Kies hieronder de trefwoorden die de wiki modus activeren,  dus aanpasbaar vanaf de publieke site (mits daarvoor een interface asls Crayons beschikbaar is)',
	'espace_wiki_mots_cles_qui' => 'Wil je deze wiki openstellen voor anderen dan beheerders:',
	'espace_wiki_qui' => 'Wil je deze wiki openstellen voor anderen dan beheerders:',

	// F
	'forums_qui' => '<strong>Forums:</strong> wie mag de inhoud van forums aanpassen:',

	// I
	'icone_menu_config' => 'Autorité',
	'info_gere_rubriques' => 'Beheert de volgende rubrieken:',
	'info_gere_rubriques_2' => 'Ik beheer de volgende rubrieken:',
	'infos_selection' => '(je kunt met de shift meerdere rubrieken kiezen)',
	'interdire_admin' => 'Kruis hieronder de vakjes aan die beheerders niet mogen aanmaken',

	// M
	'mots_cles_qui' => '<strong>Trefwoorden:</strong> wie mag trefwoorden aanmaken en aanpassen:',

	// N
	'non_webmestres' => 'Deze instelling is niet van toepassing op webmasters.',
	'note_rubriques' => '(Alleen beheerders kunnen rubrieken aanmaken en voor beperkte beheerders geldt dat uitsluitend binnen hun rubrieken.)',
	'nouvelles_rubriques' => 'nieuwe hoofdrubrieken (aan de root van de site)',
	'nouvelles_sous_rubriques' => 'nieuwe subrubrieken.',

	// O
	'ouvrir_redacs' => 'Open voor redacteurs van de site:',
	'ouvrir_visiteurs_enregistres' => 'Open voor geregistreerde bezoekers:',
	'ouvrir_visiteurs_tous' => 'Open voor alle bezoekers van de site:',

	// P
	'pas_acces_espace_prive' => '<strong>Geen toegang tot het privé gedeelte:</strong> redacteurs hebben geen toegang tot het privé gedeelte.',
	'personne' => 'Niemand',
	'petitions_qui' => '<strong>Ondertekeningen:</strong> wie mag ondertekeningen van petities aanpassen:',
	'publication' => 'Publicatie',
	'publication_qui' => 'Wie mag op de site publiceren:',

	// R
	'redac_tous' => 'Alle redacteurs',
	'redacs' => 'aan d eredacteurs van de site',
	'redacteur' => 'redacteur',
	'redacteur_lire_stats' => '<strong>Redacteur ziet stats</strong>: de redacteurs kunnen de statistieken bekijken.',
	'redacteur_modifie_article' => '<strong>Redacteur past voorgesteld aan</strong>: elke redacteur kan een ter publicaite voorgesteld artikel aanpassen, zelfs wanneer hij niet de auteur is.',
	'redacteurs_voir_auteurs' => '<strong>Redacteur ziet auteurs</strong>: redacteurs kunnen <strong>de lijst van auteurs met hun e-mailadres</strong> zien en de pagina van andere auteurs in het privé gedeelte?',
	'refus_1' => '<p>Alleen de webmasters van de site',
	'refus_2' => 'mogen deze parameters aanpassen.</p>
<p>Lees voor meer informatie <a href="https://contrib.spip.net/Autorite">de documentatie</a>.</p>',
	'reglage_autorisations' => 'Instelling van autorisaties',

	// S
	'sauvegarde_qui' => 'Wie mag een <strong>backup</strong> uitvoeren?',

	// T
	'tous' => 'Alle',
	'tout_deselectionner' => ' selectie verwijderen',

	// V
	'valeur_defaut' => '(standaardwaarde)',
	'visiteur' => 'bezoeker',
	'visiteurs_anonymes' => 'anonieme bezoekers mogen een nieuwe pagina aanmaken.',
	'visiteurs_enregistres' => 'voor geregistreerde bezoekers',
	'visiteurs_tous' => 'voor alle bezoekers van de site.',

	// W
	'webmestre' => 'De webmaster',
	'webmestres' => 'De webmasters'
);
