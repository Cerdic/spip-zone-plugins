<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/autorite?lang_cible=it
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'admins_redacs' => 'Amministratori e Redattori',
	'admins_rubriques' => 'gli amministratori associati a delle rubriche hanno :',
	'attention_crayons' => '<small><strong>Attenzione.</strong> Le modifiche qui sotto non possono funzionare se non utilizzi un plug-in con interfaccia di edizione (ad esempio <a href="http://contrib.spip.net/Les-Crayons">les Crayons</a>).</small>', # RELIRE
	'auteur_message_advitam' => 'L’autore del messaggio, ad vitam', # RELIRE
	'auteur_message_heure' => 'L’autore del messaggio, per un’ora', # RELIRE
	'auteur_modifie_article' => '<strong>Autore modifica articolo</strong> : ogni redattore può modificare gli articoli pubblicati dei quali è l’autore (e, di conseguenza, moderare il forum e la le richieste associate).
	<br />
	<i>N.B. : questa opzione si applica anche ai visitatori registrati, se sono autori e se è prevista un’interfaccia specifica.</i>', # RELIRE
	'auteur_modifie_email' => '<strong>Redattore modifica email</strong> : ogni redattore può modificare il suo indirizzo email sulla sua scheda ifnormativa personale.', # RELIRE
	'auteur_modifie_forum' => '<strong>Autore modera forum</strong> : ogni redattore può moderare il forum per gli articoli dei quali è l’autore.', # RELIRE
	'auteur_modifie_petition' => '<strong>Autore modera la contestazione</strong> : ogni redattore può moderare la contestazione per gli articoli dei quali è l’autore.', # RELIRE

	// C
	'config_auteurs_rubriques' => 'Quali tipi di autori si può <b>associare a delle rubriche</b> ?', # RELIRE
	'config_auteurs_statut' => 'Creando un autore, qual è lo <b>status di defaut</b> ?', # RELIRE

	// D
	'deja_defini' => 'Le autorizzazioni seguenti sono già state definite altrove :', # RELIRE
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
	'details_option_auteur' => '<small><br />Per il momento, l’opzione « autore » non funziona che per gli autori registrati (forum su abbonamento, per esempio). E, se è attivata, gli amministratori del sito hanno anche la possibilità di modificare i forum.
	</small>', # RELIRE
	'droits_des_redacteurs' => 'Diritti dei redattori',
	'droits_idem_admins' => 'gli stessi diritti di tutti gli amministratori',
	'droits_limites' => 'dei diritti limitati a queste rubriche',

	// E
	'effacer_base_option' => '<small><br />L’opzione raccomandata è « nessuno », l’opzione standard di SPIP è « gli amministratori » (ma sempre a seguito di verifica FTP).</small>', # RELIRE
	'effacer_base_qui' => 'Chi può <strong>cancellare</strong> il database del sito ?', # RELIRE
	'espace_publieur_detail' => 'Scegliete qui sotto un settore da trattare come spazio di pubblicazione aperto per redattori e /o visitatori registrati (a condizione di avere un’interfaccia, ad esempio i pennarelli, ed un formulario per inviare l’articolo) :', # RELIRE
	'espace_publieur_qui' => 'Vuoi aprire la pubblicazione — al di là degli amministratori :', # RELIRE
	'espace_wiki_detail' => 'Scegliete qui sotto un settore da trattare come un wiki, ovvero modificabile da tutti a partire dallo spazio pubblico (a condizione di avere un’interfaccia, per esempio i pennarelli) :', # RELIRE
	'espace_wiki_mots_cles_detail' => 'Scegliete qui sotto le parole chiave che attiveranno la modalità wiki, ovvero modificabile da tutti a partire dallo spazio pubblico (a condizione di avere un’interfaccia, per esempio i pennarelli)', # RELIRE
	'espace_wiki_mots_cles_qui' => 'Vuoi aprire questo wiki al di là degli amministratori :', # RELIRE
	'espace_wiki_qui' => 'Vuoi aprire questo wiki — al di là degli amministratori :', # RELIRE

	// I
	'icone_menu_config' => 'Autorità',
	'infos_selection' => '(puoi selezionare più settori con il tasto shift)', # RELIRE
	'interdire_admin' => 'Spunta le caselline seguenti per interdire agli amministratori di creare', # RELIRE

	// M
	'mots_cles_qui' => '<strong>Parole chiave :</strong> chi può creare e modificare le parole chiave :', # RELIRE

	// N
	'non_webmestres' => 'Questa regolazione non si applica ai webmasters.', # RELIRE
	'note_rubriques' => '(Da notare che solo gli amministratori possono creare delle rubriche, e, per gli amministratori limitati, ciò può venir fatto solo nelle loro rubriche.)', # RELIRE
	'nouvelles_rubriques' => 'nuove rubriche alla base del sito', # RELIRE
	'nouvelles_sous_rubriques' => 'nuove sottorubriche nell’arborescenza.', # RELIRE

	// O
	'ouvrir_redacs' => 'Aprire ai redattori del sito :',
	'ouvrir_visiteurs_enregistres' => 'Aprire ai visitatori registrati :',
	'ouvrir_visiteurs_tous' => 'Aprire a tutti i visitatori del sito :',

	// P
	'pas_acces_espace_prive' => '<strong>Accesso allo spazio privato interdetto :</strong> i redattori non hanno accesso allo spazio privato.', # RELIRE
	'petitions_qui' => '<strong>Signatures :</strong> qui peut modifier les signatures des pétitions :', # MODIF

	// R
	'redac_tous' => 'Tous les rédacteurs', # MODIF
	'redacs' => 'aux rédacteurs du site', # MODIF
	'redacteur' => 'rédacteur', # MODIF
	'redacteur_lire_stats' => '<strong>Rédacteur voit stats</strong> : les rédacteurs peuvent visualiser les statistiques.', # MODIF
	'redacteur_modifie_article' => '<strong>Rédacteur modifie proposés</strong> : chaque rédacteur peut modifier un article proposé à la publication, même s’il n’en est pas auteur.', # MODIF
	'refus_2' => 'sont autorisés à modifier ces paramètres.</p>
<p>Pour en savoir plus, voir <a href="http://contrib.spip.net/Autorite">la documentation</a>.</p>', # MODIF
	'reglage_autorisations' => 'Réglage des autorisations', # MODIF

	// T
	'tout_deselectionner' => ' tout déselectionner', # MODIF

	// V
	'valeur_defaut' => '(valeur par défaut)', # MODIF
	'visiteurs_anonymes' => 'les visiteurs anonymes peuvent créer de nouvelles pages.', # MODIF
	'visiteurs_enregistres' => 'aux visiteurs enregistrés', # MODIF
	'visiteurs_tous' => 'à tous les visiteurs du site.', # MODIF

	// W
	'webmestre' => 'El webmaster',
	'webmestres' => 'Los webmaster'
);

?>
