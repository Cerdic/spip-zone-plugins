<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/autorite?lang_cible=it
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_mots_cles' => 'Attivare la gestione per parole chiave',
	'admin_complets' => 'Gli amministratori completi',
	'admin_restreints' => 'Amministratori limitati?',
	'admin_tous' => 'Tutti gli amministratori (compresi quelli con limitazioni)',
	'administrateur' => 'amministratore',
	'admins' => 'Gli amministratori',
	'admins_redacs' => 'Amministratori e Redattori',
	'admins_rubriques' => 'gli amministratori associati a delle rubriche hanno :',
	'attention_crayons' => '<small><strong>Attenzione.</strong> Le opzioni di seguito non possono funzionare se non utilizzi un plug-in con interfaccia di edizione (ad esempio <a href="http://contrib.spip.net/Les-Crayons">i Pennarelli</a>).</small>',
	'attention_version' => 'Attenzione le opzioni seguenti potrebbero non funzionare con la tua versione di SPIP :',
	'auteur_message_advitam' => 'L’autore del messaggio, ad vitam',
	'auteur_message_heure' => 'L’autore del messaggio, per un’ora',
	'auteur_modifie_article' => '<strong>Autore modifica articolo</strong> : ogni redattore può modificare gli articoli pubblicati dei quali è l’autore.
	<br />
	<i>N.B. : questa opzione si applica anche ai visitatori registrati, se sono autori e se è prevista un’interfaccia specifica.</i>',
	'auteur_modifie_email' => '<strong>Redattore modifica email</strong> : ogni redattore può modificare il suo indirizzo email sulla sua scheda informativa personale.',
	'auteur_modifie_forum' => '<strong>Autore modera forum</strong> : ogni redattore può moderare il forum per gli articoli dei quali è l’autore.',
	'auteur_modifie_petition' => '<strong>Autore modera contestazione</strong> : ogni redattore può moderare la contestazione per gli articoli dei quali è l’autore.', # RELIRE

	// C
	'config_auteurs' => 'Configurazione degli autori',
	'config_auteurs_rubriques' => 'Quali tipi di autori si possono <b>associare a delle rubriche</b> ?',
	'config_auteurs_statut' => 'Alla creazione di un autore, qual è lo <b>status di defaut</b> ?',
	'config_plugin_qui' => 'Chi può <strong>modificare la configurazione</strong> dei plug-in (attivazione...) ?',
	'config_site' => 'Configurazione del sito',
	'config_site_qui' => 'Chi può <strong>modificare la configurazione</strong> del sito ?',
	'crayons' => 'Pennarelli',

	// D
	'deja_defini' => 'Le autorizzazioni seguenti sono già definite altrove :', # RELIRE
	'deja_defini_suite' => 'Il plug-in « Autorità » non può modificare certe regolazioni e di conseguenza alcune delle seguenti rischiano di non funzionare correttamente.
	<br />Per risolvere questo problema, devi verificare che il tuo file <tt>mes_options.php</tt> (o un altro plug-in attivo) abbia definito queste funzioni.', # RELIRE
	'descriptif_1' => 'Questa pagina di configurazione è riservata al webmaster del sito :',
	'descriptif_2' => '<p>Se desideri modificare questa lista, devi cambiare il file <tt>config/mes_options.php</tt> (o crearlo se necessario) e indicarvi la lista degli identificativi degli altri webmasters, come segue :</p>
<pre>&lt;?php
  define(
    \'_ID_WEBMESTRES\',
    \'1:5:8\');
?&gt;</pre>
<p>A partire da SPIP 2.1, è anche possibile dare i diritti di webmaster ad un amministratore tramite la pagina d’editing dell’autore.</p>
<p>Nota bene : i webmasters così definiti non necessitano più di procedere all’autenticazione FTP per le operazioni più delicate (ad esempio l’aggiornamento del database).</p>

<a href=\'http://contrib.spip.net/Autorite\' class=\'spip_out\'>Cf. documentation</a>', # RELIRE
	'details_option_auteur' => '<small><br />Per il momento, l’opzione « autore » non funziona che per gli autori registrati (forum su abbonamento, per esempio). E, se è attivata, gli amministratori del sito possono anche modificare i forum.
	</small>', # RELIRE
	'droits_des_auteurs' => 'Diritti degli autori',
	'droits_des_redacteurs' => 'Diritti dei redattori',
	'droits_idem_admins' => 'gli stessi diritti di tutti gli amministratori',
	'droits_limites' => 'dei diritti limitati a queste rubriche',

	// E
	'effacer_base_option' => '<small><br />L’opzione raccomandata è « nessuno », l’opzione standard di SPIP è « gli amministratori » (ma sempre a seguito di verifica FTP).</small>', # RELIRE
	'effacer_base_qui' => 'Chi può <strong>cancellare</strong> il database del sito ?',
	'espace_publieur' => 'Spazio di pubblicazione aperto', # RELIRE
	'espace_publieur_detail' => 'Scegliete qui sotto un settore da trattare come spazio di pubblicazione aperto per redattori e /o visitatori registrati (a condizione di avere un’interfaccia, ad esempio i pennarelli, ed un formulario per inviare l’articolo) :', # RELIRE
	'espace_publieur_qui' => 'Vuoi aprire la pubblicazione — al di là degli amministratori :', # RELIRE
	'espace_wiki' => 'Spazio wiki',
	'espace_wiki_detail' => 'Scegliete qui sotto un settore da trattare come un wiki, ovvero modificabile da tutti a partire dallo spazio pubblico (a condizione di avere un’interfaccia, per esempio i pennarelli) :',
	'espace_wiki_mots_cles' => 'Spazio wiki per parole chiave', # RELIRE
	'espace_wiki_mots_cles_detail' => 'Scegliete qui sotto le parole chiave che attiveranno la modalità wiki, ovvero modificabile da tutti a partire dallo spazio pubblico (a condizione di avere un’interfaccia, per esempio i pennarelli)', # RELIRE
	'espace_wiki_mots_cles_qui' => 'Vuoi aprire questo wiki al di là degli amministratori :', # RELIRE
	'espace_wiki_qui' => 'Vuoi aprire questo wiki — al di là degli amministratori :', # RELIRE

	// F
	'forums_qui' => '<strong>Forum :</strong> chi può modificare il contenuto dei forum :',

	// I
	'icone_menu_config' => 'Autorità',
	'info_gere_rubriques' => 'Gestisce le rubriche seguenti :', # RELIRE
	'info_gere_rubriques_2' => 'Io gestisco le rubriche seguenti :',
	'infos_selection' => '(puoi selezionare più settori con il tasto shift)',
	'interdire_admin' => 'Spunta le caselle seguenti per interdire agli amministratori di creare',

	// M
	'mots_cles_qui' => '<strong>Parole chiave :</strong> chi può creare e cambiare le parole chiave :',

	// N
	'non_webmestres' => 'Questa regolazione non si applica ai webmasters.', # RELIRE
	'note_rubriques' => '(Da notare che solo gli amministratori possono creare delle rubriche, e, per gli amministratori limitati, ciò può venir fatto solo nelle loro rubriche.)',
	'nouvelles_rubriques' => 'nuove rubriche alla base del sito', # RELIRE
	'nouvelles_sous_rubriques' => 'nuove sottorubriche nell’arborescenza.',

	// O
	'ouvrir_redacs' => 'Aprire ai redattori del sito :',
	'ouvrir_visiteurs_enregistres' => 'Aprire ai visitatori registrati :',
	'ouvrir_visiteurs_tous' => 'Aprire a tutti i visitatori del sito :',

	// P
	'pas_acces_espace_prive' => '<strong>Accesso allo spazio privato interdetto :</strong> i redattori non hanno accesso allo spazio privato.',
	'personne' => 'Nessuno',
	'petitions_qui' => '<strong>Firme :</strong> chi può modificare le firme delle istanze :', # RELIRE
	'publication' => 'Pubblicazione',
	'publication_qui' => 'Chi può pubblicare sul sito :',

	// R
	'redac_tous' => 'Tutti i redattori',
	'redacs' => 'ai redattori del sito',
	'redacteur' => 'redattore',
	'redacteur_lire_stats' => '<strong>Redattore vede stats</strong> : i redattori possono visionare le statistiche.', # RELIRE
	'redacteur_modifie_article' => '<strong>Redattore modifica proposte</strong> : ogni redattore può modificare un articolo proposto per la pubblicazione, anche se non ne è l’autore.', # RELIRE
	'refus_1' => '<p>Solo i webmasters del sito', # RELIRE
	'refus_2' => 'sono autorizzati a modificare questi parametri.</p>
<p>Per saperne di più, voir plus, voir <a href="http://contrib.spip.net/Autorite">la documentation</a>.</p>', # RELIRE
	'reglage_autorisations' => 'Regolamentazione delle autorizzazioni', # RELIRE

	// S
	'sauvegarde_qui' => 'Chi può effettuare dei <strong>salvataggi</strong> ?', # RELIRE

	// T
	'tous' => 'Tutti',
	'tout_deselectionner' => ' deselezionare tutto', # RELIRE

	// V
	'valeur_defaut' => '(valore di default)',
	'visiteur' => 'visitatore',
	'visiteurs_anonymes' => 'i visitatori anonimi possono creare delle pagine nuove.', # RELIRE
	'visiteurs_enregistres' => 'ai visitatori registrati',
	'visiteurs_tous' => 'a tutti i visitatori del sito.',

	// W
	'webmestre' => 'El webmaster',
	'webmestres' => 'Los webmaster'
);

?>
