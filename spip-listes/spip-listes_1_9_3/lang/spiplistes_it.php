<?php
/**
 * Pack langue italien
 * 
 * @package spiplistes
 */
 // $LastChangedRevision: 47066 $
 // $LastChangedBy: paladin@quesaco.org $
 // $LastChangedDate: 2011-04-25 19:54:15 +0200 (Lun 25 avr 2011) $

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS['i18n_spiplistes_it'] = array(

// CP-20081126: classement par scripts
// action/spiplistes_agenda.php
// action/spiplistes_changer_statut_abonne.php
// action/spiplistes_envoi_lot.php
// action/spiplistes_journal.php
// action/spiplistes_lire_console.php
// action/spiplistes_liste_des_abonnes.php
// action/spiplistes_listes_abonner_auteur.php
// action/spiplistes_moderateurs_gerer.php
'voir_historique' => 'Vedi il log degli invii'
, 'pas_de_liste_prog' => "Nessun lista programmata."

// action/spiplistes_supprimer_abonne.php
// balise/formulaire_abonnement.php
// formulaires/spip_listes_inscriptions.php
// patrons/messages_abo/standard.html
, 'email_incorrect' => 'Indirizzo email non corretto.'
, 'inscription_liste_f' => 'Hai scelto di ricevere i messaggi indirizzati alla Newsletter seguente in formato @f@: '
, 'inscription_listes_f' => 'Hai scelto di ricevere i messaggi indirizzati alle Newsletter seguenti in formato @f@: '
, 'inscription_reponse_s' => 'Sei abbonato alla newsletter del sito @s@'
, 'inscription_reponses_s' => 'Sei abbonato alle newsletter del sito @s@\n'
, 'vous_abonne_aucune_liste' => "Non sei abbonato ad alcuna newsletter"
, 'liste_dispo_site_' => 'Newsletter disponibili su questo sito: '
, 'listes_dispos_site_' => 'Newsletter disponibili su questo sito: '
, 'desole_pas_de_liste' => 'Spiacente, non sono disponibili newsletter al momento.'
, 'definir_votre_pass' => 'Per definire la tua password, '
, 'pour_vous_abonner' => 'Per abbonarti alle newsletter'
// obsolete
, 'abonnement_mail_passcookie' => '
  <br/>
  Per modificare la propria iscrizione alla newsletter del sito <strong>@nom_site_spip@</strong> (@adresse_site@),
  vai all\'indirizzo seguente:<br /><br />
  <a href="@adresse_site@/spip.php?page=abonnement&d=@cookie@">@adresse_site@/spip.php?page=abonnement&d=@cookie@</a><br /><br />
  In seguito potrai confermare la variazione del tuo abbonamento.
  <br />' // et balise suivante
, 'bienvenue_sur_la_liste_' => 'Benvenuto sulla newsletter del sito '
, 'vos_abos_sur_le_site_' => 'I tuoi abbonamenti sul sito '
, 'votre_format_de_reception_' => 'Il tuo formato di ricezione '
, '_cliquez_lien_formulaire' => 'clicca su questo link per accedere al modulo presente sul sito'
, 'pour_modifier_votre_abo_' => 'Per modificare il tuo abbonamento '
, 'abonnement_explication' => 'Inserisci il tuo indirizzo email nel campo qui sotto e seleziona la lista o le liste alle quali desideri abbonarti.'
, 'abonnement_presentation' => '
  Inserisci il tuo indirizzo email nel campo qui sotto.
  Riceverai a questo indirizzo una email di conferma d\'iscrizione ed un link.
  Questo link ti permetterà di selezionare le newsletter.
	'
, 'confirmation_inscription' => 'Conferma dell\'iscrizione'
, 'suspendre_abonnement_' => 'Sospendi la mia iscrizione '
, 'vous_etes_redact' => 'Ti sei iscritto come redattore.'
, 'vous_etes_membre' => 'Sei abbonato alle newsletter di questo sito.
  Talvolta è necessario autentificarsi per poter accedere alle newsletter.'
, 'saisie_erreurs' => 'I tuoi dato contengono degli errori!'
, 'demande_ok' => 'La tua richiesta è stata processata. Riceverai a breve una conferma.'
, 'demande_ko' => 'Spiacente, ma un errore è stato riscontrato durante l\'invio della conferma di iscrizione.
    Per favore, prova a iscriverti più tardi.'
, 'champ_obligatoire' => 'Questo campo è obbligatorio'
// balise/formulaire_modif_abonnement.php
, 'abonnement_modifie' => 'Le modifiche sono state registrate'
, 'abonnement_nouveau_format' => 'Da ora riceverai i messaggi con il seguente formato: '

// base/spiplistes_init.php
, 'autorisation_inscription' => 'SPIP-Listes ha appena attivato l\'autorizzazione di iscriversi ai visitatori del sito'

// base/spiplistes_tables.php
// base/spiplistes_upgrade.php
// docs/spiplistes_aide_fr.html
// exec/spiplistes_abonne_edit.php
, 'adresse_mail_obligatoire' => "Indirizzo email mancante. Impossibile abbonarsi."
, 'abonne_sans_format' => "Questo account &egrave; attualmente non abbonato. Non &egrave; stato selezionato
  nessun formato per l'\invio della newsletter. Seleziona un formato di invio per questo account per
  completare l\'abbonamento."
, 'desabonner_temporaire' => "Disabilita temporaneamente questo account."
, 'desabonner_definitif' => "Cancella questo account da tutte le newsletter."
, 'export_etendu_' => "Esportazione avanzata "
, 'exporter_statut' => "Esporta lo stato (invitato, redattore, ecc.)"
, 'editer_fiche_abonne' => "Modifica il file dell\'abbonato"
, 'edition_dun_abonne' => "Modifica di un abbonato"
, 'format_de_reception' => "Formato di invio" // + formulaire
, 'format_reception' => "Formato di invio:"
, 'format_de_reception_desc' => "Puoi scegliere un formato globale di invio delle email per  
   questo abbonato.<br /><br />
   Puoi anche temparaneamente disabilitare questo contatto. 
   Rester&agrave; iscritto nelle liste nelle quali &egrave; iscritto, ma le email 
   non gli saranno inviate fin quando non avrai definito un formato per l'invio delle email."
, 'mettre_a_jour' => '<h3>SPIP-listes si sta aggiornando</h3>'
, 'regulariser' => 'regolarizza i non abbonati sulla newsletter...<br />'
, 'supprimer_ce_contact' => "Cancella questo contatto"
, 'abonne_listes' => 'Questo contatto &egrave; abbonato alle seguenti liste'
, 'n_duplicata_mail' => '@n@ duplicato(i)'
, 'n_incorrect_mail' => '@n@ errata(e)'

// exec/spiplistes_abonnes_tous.php
, 'repartition_abonnes' => "Ripartizione degli abbonati"
, 'abonnes_titre' => 'Abbonati'
, 'chercher_un_auteur' => "Cerca un autore"
, 'une_inscription' => 'Un abbonato trovato'
, 'suivi' => 'Gestione degli abbonamenti' // + presentation
, 'abonne_aucune_liste' => 'Iscritti senza Newsletter'
, 'format_aucun' => "Nessuno"
, 'repartition_formats' => "Ripartizione dei formati"

// exec/spiplistes_aide.php
// exec/spiplistes_autocron.php
// exec/spiplistes_config.php
, 'format_courrier_defaut' => 'Formato di invio delle email'
, 'format_courrier_defaut_desc' => 'Questo formato è applicato alle nuove iscrizioni.'
, 'personnaliser_le_courrier' => 'Personalizza l\'email'
, 'personnaliser_le_courrier_desc' =>
	"Puoi personalizzare l'email per ogni abbonato inserendo 
   nel tuo modello i tag necessari. Ad esempio, puoi inserire
   il nome del tuo abbonato nella sua email al momento dell'invio,
   mettendo nel tuo modello _AUTEUR_NOM_ (nota il trattino basso all'inizio ed alla fine del tag)."
, 'formulaire_abonnement' => 'Modulo di <em>Iscrizione</em>'
, 'formulaire_abonnement_effet' => 'Applica l\'effetto apri/chiudi ai testi descrittivi delle newsletter.'
, 'utiliser_smtp' => "Utilizza SMTP"
, 'opt_smtp_use_ssl' => 'Utilizza SSL (Secure Sockets Layer)'
, 'requiert_identification' => "Richiede una identificazione"
, 'adresse_smtp' => "Indirizzo email del <em>mittente</em> SMTP"
, '_aide_install' => "<p>Benvenuto nel mondo di SPIP-Listes.</p>
	<p class='verdana2'>In maniera predefinita al momento dell'installazione, SPIP-Listes &egrave; in modalit&agrave; <em>simulazione 
	di invio</em> al fine di permetterti di scoprire le sue funzionalit&agrave; 
	e di effettuare i tuoi primi test.</p>
	<p class='verdana2'>Per impostare le varie opzioni di SPIP-Listes, vai <a href='@url_config@'>sulla 
	pagina di configurazione</a>.</p>"
, 'adresse_envoi_defaut' => "Indirizzo di invio predefinito"
, 'adresse_email_reply_to' => 'Indirizzp di ritorno (reply-to)'
, 'adresse_on_error_defaut' => 'Indirizzo di ritorno predefinito per gli errori (return-path)'
, 'pas_sur' => '<p>Se non sei sicuro, scegli la funzione mail di PHP.</p>'
, 'complement_des_courriers' => "Aggiunte alle email"
, 'complement_lien_en_tete' => "Link sull'email"
, 'complement_ajouter_lien_en_tete' => "Aggiungi un link in testa alla email"
, 'complement_lien_en_tete_desc' => "Questa opzione ti permette di aggiungere all'inzio dell'email HTML inviata il link 
   alla mail originale presente sul tuo sito."
, 'completer_titre_courrier_nom_site' => 'Completa il titolo della email'
, 'completer_titre_courrier_nom_site_desc' => 'Il titolo delle newsletter è
  automaticamente completato con il nome del sito.'
, 'lien_gestion_inscription' => 'Link di gestione dell\'iscrizione'
, 'lien_gestion_inscription_desc' => 'All\'invio della email,
  un piccolo link verrà inserito alla fine dell\'email,
  che proporrà all\'abbonato la gestione delle proprie iscrizioni
  attraverso in modulo sul sito (iscrizioni e formato di ricezione).'
, 'lien_gestion_inscription_label' => 'Sostituisci quresto link con
  un link verso un semplice modulo di iscrizione
  (non propone le altre newsletter disponibili, nè il formato di ricezione)'
, 'complement_tampon_editeur' => "Aggiungere la sezione Editore"
, 'complement_tampon_editeur_desc' => '
  Questa sezione è un piccolo blocco di testo
  aggiunto automaticamente alla fine dell\'email al momento dell\'invio.<br />
  Questo blocco è un piccolo modello modificabile, configurabile, composto
  dal nome dell\'editore, dai suoi dati e dal suo logo.
  '
, 'patron_du_lien' => 'Modello del link'
, 'complement_tampon_editeur_label' => "Angigungi la sezione Editore alla fine dell'email"
, 'envoi_des_courriers' => "Invio delle email"
, 'log_console' => "Console"
, 'log_console_debug' => 'Modalità debug'
, 'log_console_debug_activer' => 'Attiva la modalità debug (produce molto testo. Da disattivare se inutile)'
, 'log_details_console' => "Dettagli della console"
, 'log_voir_destinataire' => "Elenca gli indirizzi email dei destinatari nella console al momento dell'invio."
, 'log_console_syslog_desc' => "Sei in una rete locale (@IP_LAN@). Se necessario, puoi attivare la console su syslog invece che sul log di SPIP (consigliato con unix)."
, 'log_console_syslog_texte' => "Attiva il log si sistema (invia su syslog)"
, 'log_console_syslog' => "Console syslog"
, 'log_voir_le_journal' => "Mostra il log di SPIP-Listes"
, 'log_configurer' => 'Configura la console'
, 'recharger_journal' => "Aggiorna il log"
, 'fermer_journal' => "Ferma il log"
, 'methode_envoi' => 'Metodo d\'invio'
, 'mode_suspendre_trieuse' => "Sospendi la procedura di invio delle newsletter"
, 'suspendre_le_tri_des_listes' => "Questa opzione ti consente - in caso di sovraccarico - di sospendere la procedura di invio delle 
	newsletter programmate e di ridefinire i parametri 
	di invio. Disattiva in seguito questa opzione per riprendere la procedura di invio delle 
	newsletter programmate."
, 'mode_suspendre_meleuse' => "Sospendi l'invio delle email"
, 'suspendre_lenvoi_des_courriers' => "Questa opzione ti consente - in caso di sovraccarico 
	- di annullare l'invio delle email. Disattiva questa opzione per 
	riprendere gli invii in corso. "
, 'nombre_lot' => 'Numero di invii per lotto'
, 'php_mail' => 'Utilizza la funzione mail() di PHP'
, 'patron_du_tampon_' => "Modello della sezione : "
, 'patron_de_pied_' => "Modello del piede "
, 'personnaliser_le_courrier_label' => "Attiva la personalizzazione delle email"
, 'parametrer_la_meleuse' => "Parametri dell'invio"
, 'smtp_hote' => 'Host'
, 'smtp_port' => 'Porta'
, 'simulation_desactive' => "Modalit&agrave; simulazione disattivata."
, 'simuler_les_envois' => "Simula l'invio delle email"
, 'abonnement_simple' => '<strong>Abbonamento semplice : </strong><br /><em>Gli abbonati riceveranno un messaggio 
	di conferma dell\'avvenuto abbonamento</em>'
, 'abonnement_code_acces' => '<strong>Abbonamento con codici di accesso: </strong><br /><i>Gli abbonati 
	riceveranno anche un login ed una password che permetter&agrave; loro di identificarsi sul sito. </i>'
, 'mode_inscription' => 'parametri per l\'iscrizione dei visitatori'
, 'jeu_de_caracteres' => 'Set di caratteri'
, 'opt_optin_titre' => 'Filtro Doppio Opt-In'
, 'opt_optin_desc' => 'Al momento dell\'iscrizione, l\'abbonato riceve una
  richiesta di conferma via email.
  Questa conferma - chiamata <em>Doppio Opt-In</em> - consente di validare
  l\'effettiva volontà dell\'abbonato di ricevere la newsletter. Questo filtro non
  riguarda l\'invio di email dirette.'
, 'opt_optin_confirmer' => 'Filtra gli invii su Opt-In per le email delle newsletter.'

// exec/spiplistes_courrier_edit.php
, 'generer_le_contenu' => "Genera il contenuto"
, 'langue_du_courrier_' => "Lingua dell'email:"
, 'generer_apercu' => "Genera e mostra anteprima"
, 'a_partir_de_patron' => "A partire da un modello"
, 'avec_introduction' => "Con un testo di introduzione"
, 'calcul_patron_attention' => "Certi modelli inseriscono nel loro cotenuto il testo qui di seguito (Testo della email). 
	Se aggiorni la tua email, ricorda di cancellare questo testo prima di generare il contenuto."
, 'charger_patron' => 'Scegli un modello per le email'
, 'courrier_numero_' => "Email numero:" // + _gerer
, 'creer_un_courrier_' => "Nuova email :"
, 'choisir_un_patron_' => "Scegli un modello "
, 'courrier_edit_desc' => 'Puoi scegliere di generare automaticamente il contenuti dell\'email
	o di scrivere semplicemente la tua email nella casella <strong>testo dell\'email</strong>.'
, 'contenu_a_partir_de_date_' => "Contenuto a partire da questa data "
, 'cliquez_generer_desc' => "Clicca su <strong>@titre_bouton@</strong> per aggiungere il risultato 
	nella casella @titre_champ_texte@."
, 'lister_articles_de_rubrique' => "Ed elenca gli articoli della rubrica "
, 'lister_articles_mot_cle' => "ed elenca gli articoli con parola chiave "
, 'edition_du_courrier' => "Modifica dell'email" // + gerer
, 'generer_un_sommaire' => "Genera un sommario"
, 'generer_patron_' => "Genera il modello "
, 'generer_patron_avant' => "prima del sommario"
, 'generer_patron_apres' => "dopo il somamrio."
, 'introduction_du_courrier_' => "Introduzione della tua email, prima del contenuto prelevato dal sito "
, 'modifier_un_courrier__' => "Modifica una email:"
, 'modifier_ce_courrier' => "Modifica questa email"
, 'sujet_courrier' => '<strong>Oggetto dell\'email</strong> [obbligatorio]'
, 'texte_courrier' => '<strong>Testo dell\'email</strong> (HTML consentito)'
, 'avec_patron_pied__' => "Con il modello di pi&egrave; pagina: "
, 'icone_creation_courrier' => 'Crea una nuova email'

// exec/spiplistes_courrier_gerer.php
, 'erreur_adresse_email_invalide' => 'Errore: l\'indirizzo email che hai fornito non &egrave; valido'
, 'langue_' => '<strong>Lingua :</strong>&nbsp;'
, 'calcul_patron' => 'Calcolo con il modello versione testo'
, 'calcul_html' => 'Calcolo della versione HTML della email'
, 'dupliquer_ce_courrier' => "Duplica questa email"
, 'destinataire_sans_format_alert' => "Destinatario senza formato di ricezione.
	Seleziona un formato di ricezione (testo o html) per questo account o seleziona un altro destinatario."
, 'envoi_date' => 'Data dell\'invio: '
, 'envoi_debut' => 'Inizio dell\'invio: '
, 'envoi_fin' => 'Fine dell\'invio: '
, 'erreur_envoi' => 'Numero di invii in errore: '
, 'erreur_liste_vide' => "Errore: questa newsletter non ha abbonati."
, 'erreur_courrier_introuvable' => "Errore: questa email non esiste." // + previsu
, 'envoyer_ce_courrier' => "Invia questa email"
, 'format_html__n' => "Formato html: @n@"
, 'format_texte__n' => "Formato testo: @n@"
, 'message_arch' => 'Email archiviata'
, 'message_en_cours' => 'Email in corso di invio'
, 'message_type' => 'Email'
, 'sur_liste' => 'Sulla lista' // + casier
, 'supprimer_ce_courrier' => "Elimina questa email"
, 'email_adresse' => 'Indirizzo email di test' // + liste
, 'email_test' => 'Invia una email di test'
, 'erreur_courrier_titre_vide' => "Errore: la tua email non ha il titolo."
, 'message_en_cours' => 'Questa email &egrave; in corso di redazione'
, 'modif_envoi' => 'Puoi modificarla o richiederne l\'invio'
, 'message_presque_envoye' =>'Questa email sta per essere inviata'
, 'erreur_adresse_email_inconnue' => 'Attenzione, l\indirizzo email di test che hai fornito non corrisponde a 
	nessun abbonato, <br />l\'invio non pu&ograve; essere effettuato, ripeti la procedura<br /><br />'
, 'arreter_envoi' => 'Ferma l\'invio di questa email'
, 'modifier_cette_liste' => 'Modifica questa newsletter'
, 'supprimer_cette_liste' => 'Elimina questa newsletter'

// exec/spiplistes_courrier_previsu.php
, 'lettre_info' => 'Newsletter del sito'

// exec/spiplistes_courriers_casier.php
// exec/spiplistes_import_export.php
, 'exporter_une_liste_d_abonnes' => "Esporta una lista di abbonati"
, 'exporter_une_liste_de_non_abonnes' => "Esporta un a lista di non abbonati"
, '_aide_import' => "Puoi importare qui una lista di abbonati direttamente dal tuo computer.<br />
	Questa lista di abbonati deve essere in formato testo, una riga
  per abbonato. Ogni riga deve essere cos&igrave; composta:<br />
	<tt style='display:block;margin:0.75em 0;background-color:#ccc;border:1px solid #999;padding:1ex;'>indirizzo@email<span style='color:#f66'>[separatore]</span>login<span style='color:#f66'>[separatore]</span>nome</tt>
	<tt style='color:#f66'>[separatore]</tt> &egrave; un carattere di tabulazione o un punto e virgola.<br /><br />
	L\'indirizzo email deve essere unico, come il login. Se l\'indirizzo email 
   o se il login esiste nel database del sito, la riga sar&agrave; ignorata.<br />
	Il primo campo indirizzo@email &egrave; obbligatorio. Gli altri due campi possono 
   essere ignorati (puoi importare le liste provenienti da vecchie versioni di
   SPIP-Listes)."
, 'annuler_envoi' => "Annulla l'invio" // + _gerer
, 'envoi_patron' => 'Invio del modello'
, 'import_export' => 'Importa / Esporta'
, 'incorrect_ou_dupli' => " (non corretto o duplicato)"
, 'membres_liste' => 'Lista degli abbonati'
, 'messages_automatiques' => 'Email automatiche programmate'
, 'pas_de_liste_pour_import' => "Devi creare almeno una newsletter
  , o essere moderatore di una newsletter,
  per poter importare
	i tuoi abbonati."
, 'resultat_import' => "Risultato dell'importazione"
, 'selectionnez_une_liste_pour_import' => "Devi selezionare almeno una newsletter
  per poter importare gli abbonati."
, 'selectionnez_une_liste_de_destination' => "Seleziona una o pi&ugrave; newsletter per i tuoi abbonati."
, 'tous_les_s' => "Ogni @s@"
, 'toutes_les_semaines' => "Ogni settimana"
, 'tous_les_mois' => "Ogni mese, "
, 'tous_les_ans' => "Ogni anno"
, 'version_html' => '<strong>Versione HTML</strong>'
, 'version_texte' => '<strong>Versione testo</strong>'
, 'erreur_import' => 'Il file di importazione presenta un errore alla linea '
, 'envoi_manuel' => "Invio manuale"
, 'format_date' => 'Y/m/d'
, 'importer' => 'Importa una lista di abbonati'
, 'importer_fichier' => 'Importa un file'
, 'importer_fichier_txt' => '<p><strong>La tua lista di abbonati deve essere un semplice file (testo) 
	che presenta un indirizzo email per riga</strong></p>'
, 'importer_preciser' => '<p>Seleziona le newsletter ed il formato per la tua importazione di abbonati</p>'
, 'prochain_envoi_prevu' => 'Prossimo invio previsto' // + gerer
, 'option_import_' => 'Optioni d\'importazione '
, 'forcer_abos_' => 'Forza gli abbonamenti (se l\'indirizzo email esiste in base dati, forza l\'abbonamento
  per la selezione, per questo abbonato).'
, 'erreur_import_base' => 'Errore importazione. Dato non corretti o errore base SQL.'
, 'erreur_n_fois' => '(errore verificatosi @n@ volte)'
, 'liste_de_destination_s' => 'Newsletter di destinazione: @s@'
, 'listes_de_destination_s' => 'Newsletter di destinazione: @s@'
, 'pas_dimport' => 'Nessuna importazione. O il file è vuoto, o tutti gli indirizzi sono già iscritti.'
, 'nb_comptes_importees_en_ms_dont_' => '@nb@ file importati in @ms@ ms. di cui: '
, 'nb_fiches_crees' => '@nb@ account creati'
, 'nb_comptes_modifies' => '@nb@ account modificati'
, 'nb_auteurs_exists' => '@nb@ account già in base dati'
, 'nb_abos_enregistres_pour_nb_listes' => '@nb@ abbonamenti registrati per @nl@ newsletter.'
, 'nb_comptes_ignores' => '@nb@ account ignorati (già in base dati)'
, 'format_de_reception_' => 'Formato di ricezione: '

// exec/spiplistes_liste_edit.php
, 'texte_dinsctription_' => "testo di iscrizione: "
, 'creer_une_liste_' => "Nuova newsletter"
, 'en_debut_de_semaine' => "all'inizio della settimana"
, 'en_debut_de_mois' => "all'inizio del mese"
, 'envoi_non_programme' => "Invio non programmato"
, 'edition_dune_liste' => "Modifica la newsletter"
, 'texte_contenu_pied' => '<br />(Mesasggio aggiunto alla fine di ogni email al momento dell\'invio)<br />'
, 'texte_pied' => '<p><strong>testo del pi&egrave; pagina</strong>'
, 'modifier_liste' => 'Modifica questa newsletter '
, 'txt_abonnement' => 'Indica qui il testo per l\'abbonamento a questa newsletter, mostrato
	sul sito pubblico se la lista &egrave; attiva'

// exec/spiplistes_liste_gerer.php
, 'forcer_les_abonnement_liste' => "Forza gli abbonamenti a questa newsletter"
, 'periodicite_tous_les_n_s' => "Periodicit&agrave;: ogni @n@ @s@"
, 'liste_sans_titre' => 'Newsletter senza titolo'
, 'statut_interne' => "Privata"
, 'statut_publique' => "Pubblica"
, 'adresse' => 'Indica qui l\'indirizzo da utilizzare per le risposte alle email
	(in maniera predefinta, sarà utilizzato l\'indirizzo del webmaster come indirizzo di risposta).
	<br /><br />
  L\'indirizzo può essere immesso come:
	<ul style="margin-top:0">
	<li>webmaster@example.org</li>
	<li>Nome &#60;webmaster@example.org&#62;</li>
	</ul>'
, 'ce_courrier_ne_sera_envoye_qu_une_fois' => "Questa email sar&agrave; inviata solo una volta."
, 'adresse_de_reponse' => "Indirizzo di risposta"
, 'adresse_mail_retour' => 'Indirizzo email del gestore nella newsletter (reply-to)'
, 'attention_action_retire_invites' => "Attenzione: questa azione elimina gli invitati dalla lista degli abbonati."
, 'a_partir_de' => "A partire da"
, 'apercu_plein_ecran' => "Anteprima a pieno schermo in una nuova finestra"
, 'attention_suppression_liste' => "Attenzione! Hai richiesto la cancellazione di una newsletter. 
	Gli abbonati saranno eliminati automaticamente dalla newsletter. "
, 'abonner_tous_les_invites_public' => "Abbona tutti i membri invitati a questa newsletter pubblica."
, 'abonner_tous_les_inscrits_prives' => "Abbona tutti i membri a questa lista privata, tranne gli invitati."
, 'boite_confirmez_envoi_liste' => "Hai richiesto l'invio immediato di questa
  newsletter <br />
	Per favore, conferma la richiesta."
, 'cette_liste_est_' => "Questa newsletter &egrave; : @s@"
, 'confirmer_la_suppression_de_la_liste' => "Conferma la cancellazione della newsletter "
, 'confirmez_requete' => "Conferma la richiesta."
, 'date_expedition_' => "data di invio "
, 'dernier_envoi_le_' => "Ultimo invio il:"
, 'forcer_abonnement_desc' => "Puoi forzare qui gli abbonamenti a questa newsletter, sia per tutti 
   i membri iscritti (visitatori, autori e amministratori),
   sia per tutti i visitatori."
, 'forcer_abonnement_aide' => "<strong>Attenzione</strong>: un membro abbonato non riceve necessariamente 
   l'email di questa newsletter. Bisogna attendere che confermi il formato di ricezione: html o solo testo.<br />
	Puoi forzare il formato per abbonato
  <a href='@lien_retour@'>sulla pagina di gestione degli abbonati</a>"
, 'forcer_abonnements_nouveaux' => "Selezionando l'opzione <strong>Forza gl iabbonamenti nel formato...</strong>, 
	confermi il formato di ricezione dei nuovi abbonati.
	I vecchi abbonati conservano la loro preferenza di ricezione."
, 'forcer_desabonner_tous_les_inscrits' => "Cancella tutti i membri iscritti per questa newsletter."
, 'gestion_dune_liste' => "gestione della newsletter"
, 'message_sujet' => 'Oggetto '
, 'mods_cette_liste' => "I moderatori della newsletter"
, 'nbre_abonnes' => "Numero di abbonati: "
, 'nbre_mods' => "Numero di moderatori: "
, 'patron_manquant_message' => "Devi selezionare un modello prima di parametrizzare l'invio di questa 
	newsletter."
, 'liste_sans_patron' => "newsletter senza modello." // courriers_listes
, 'patron_grand_' => "Modello "
, 'sommaire_date_debut' => "A partire dalla data definita di seguito"
, 'abos_cette_liste' => "Gli abbonati a questa newsletter"
, 'confirme_envoi' => 'Conferma l\'invio'
, 'env_esquel' => 'INvio programmato del modello'
, 'env_maint' => 'Invia ora'
, 'date_act' => 'Dati aggiornati'
, 'forcer_les_abonnements_au_format_' => "Forza gli abboanamenti al formato: "
, 'pas_denvoi_auto_programme' => "Non esiste alcun invio automatico pianificato per questa newsletter."
, 'pas_de_periodicite' => "Nessuna periodicit&agrave;."
, 'prog_env' => 'Programma un invio automatico'
, 'prog_env_non' => 'Non programmare l\'invio'
, 'conseil_regenerer_pied' => "<br />Questo modello proviene da una vecchia versione diSPIP-Listes.<br />
	Consiglio: seleziona nuovamente il modello di pi&egrave; pagina per tener conto del multilinguismo
	e/o la versione testo del modello"
, 'boite_alerte_manque_vrais_abos' => "Non esistono abbonati per questa newsletter,
	o gli abbonati non hanno un formato di ricezione.
	<br />
	Correggi il formato di ricezione per almeno un abbonato prima di richiedere l'invio."	
, 'diffusion' => 'Diffusione'
, 'tous_les_' => 'ogni '

// exec/spiplistes_listes_toutes.php
// exec/spiplistes_maintenance.php
, 'annulation_chrono_' => "Annullamento del cron per "
, 'conseil_sauvegarder_avant' => "<strong>Consiglio</strong>: effettuare un backup del database prima di confermare la cancellazione di 
   @objet@. Non sar&agrave; possibile annullare."
, 'des_formats' => "dei formati"
, 'des_listes' => "delle newsletter"
, 'des_abonnements' => 'degli abbinamenti'
, 'confirmer_supprimer_formats' => "Cancella i formati di ricezione degli abbonati."
, 'nettoyer_table_formats' => 'Cancella la tabella dei formati.'
, 'maintenance_objet' => "Manutenzione @objet@"
, 'nb_abos' => "qt."
, 'pas_de_liste' => "Nessuna newsletter del tipo 'invio non programmato'."
, 'pas_de_format' => "Nessun formato di ricezione definito per gli abbonati."
, 'pas_de_liste_en_auto' => "Nessuna newsletter del tipo 'invio programmato' (chrono)."
, 'forcer_formats_' => 'Forza il formato di ricezione '
, 'forcer_formats_desc' => 'Forza il formato di ricezione per tutti gli abbonati...'
, 'modification_objet' => 'Modifica @objet@'
, 'suppression_de__s' => "Cancellazione di : @s@"
, 'suppression_' => "Cancella @objet@"
, 'suppression_chronos_' => "Cancella gli invii programmati (chrono) "
, 'suppression_chronos_desc' => "Se elimini il chrono, la newsletter non sar&agrave; eliminata. La sua periodicit&agrave; 
	sar&agrve; conservata ma l'invio sar&agrave; sospeso. Per riattivare il chrono, bisogna ridefinire la data
  del primo invio. "
, 'supprimer_les_listes' => "Cancella le newsletter"
, 'supprimer_la_liste' => "Cancella la newsletter..."
, 'suspendre_abonnements' => "Sospende gli abbonamenti per questo account"
, 'separateur_de_champ_' => "Separatore di campo "
, 'separateur_tabulation' => "tabulazione (<code>\\t</code>)"
, 'separateur_semicolon' => "punto e virgola (<code>;</code>)"
, 'nettoyage_' => 'Pulizia '
, 'confirmer_nettoyer_abos' => 'Conferma la pulizia della tabella degli abbonati.'
, 'pas_de_pb_abonnements' => 'Nessun errore riscontrato sulla tabella degli abbonamenti.'
, '_n_abos_' => ' @n@ abbonamenti '
, '_1_abo_' => ' 1 abbonamentot '
, 'aucun_abonmt' => 'nessun abbonamento.'
, '_n_auteurs_' => ' @n@ autori '
, '_1_auteur_' => ' 1 autore '
, 'abonnes' => 'abbonati'
, '_n_abonnes_' => ' @n@ abbonati '
, '1_abonne' => '1 abbonato'
, 'aucun_abo' => 'nessun abbonato.'
, 'supprimer_les_chronos' => 'Elimina i chrono'
, 'casier_vide' => 'Vuoto'
, 'suppression_de_titre_result' => 'Eliminazione di : @titre@ @result@'
, 'total_1_abonne' => 'Totale: 1 abbonato'
, 'total_n_abonnes' => 'Totale: @n@ abbonati'
, 'total_1_abo_obsolet' => 'Totale: 1 abbonamento*formato obsoleto'
, 'total_n_abo_obsolet' => 'Totale: @n@ abbonamenti/formati obsoleti'

// exec/spiplistes_menu_navigation.php
// exec/spiplistes_voir_journal.php
// genie/spiplistes_cron.php
// inc/spiplistes_agenda.php
, 'boite_agenda_titre_' => "Planning degli invii "
, 'boite_agenda_legende' => "Su @nb_jours@ giorni"
, 'boite_agenda_voir_jours' => "Vedi sui @nb_jours@ giorni mancanti"

// inc/spiplistes_api.php
// inc/spiplistes_api_abstract_sql.php
// inc/spiplistes_api_courrier.php
// inc/spiplistes_api_globales.php
// inc/spiplistes_api_journal.php
, 'titre_page_voir_journal' => "Log di SPIP-Listes"
, 'mode_debug_actif' => "Modalit&agrave; debug attiva"

// inc/spiplistes_api_presentation.php
, '_aide' => '<p>SPIP-Listes consente di inviare una o pi&ugrave; email automatiche a degli abbonati</p>
	<p>Puoi scrivere un testo semplice, comporre il tuo messaggio in HMTL o applicare un "modello" alla tua email</p>
	<p>Grazie ad un form di iscrizione pubblico, gli abbonati definiscono il loro stato di abbonamento, 
	le newsletter alle quali si abbonano ed il formato
	nel quale desiderano ricevere le email (HTML/testo). </p>
	<p>Ogni email sar&agrave; tradotta automaticamente in formato testo per gli abbonati che ne hanno fatto richiesta.</p>
	<p><strong>Nota :</strong><br />L\'invio delle email pu&ograve; richiedere qulche minuto: i lotti partono un po\' 
	alla volta quando i visitatori navigano sul sito pubblico. Puoi anche forzare manualmente l\invio dei lotti 
	cliccando sul link "gestione degli invii" dopo
  averne effettuato uno.</p>'
, 'envoi_en_cours' => 'Invio in corso'
, 'nb_destinataire_sing' => " destinatario"
, 'nb_destinataire_plur' => " destinatari"
, 'aucun_destinataire' => "nessun destinatario"
, '1_liste' => '@n@ newsletter'
, 'n_listes' => '@n@ newsletter'
, 'utilisez_formulaire_ci_contre' => "Utilizza questo form per attivare/disattivare questa opzione."
, 'texte_boite_en_cours' => 'SPIP-Listes sta inviando una email.<p>Questo box scomparir&agrave; una volta che l\'invio sar&agrave; completo.</p>'
, 'meleuse_suspendue_info' => "L'invio delle email in attesa di spedizione &egrave; sospeso."
, 'casier_a_courriers' => "Gestione email" // + courriers_casier
, 'pas_de_donnees' => "Spiacente, ma il dato richiesto non esiste nella base dati."
, '_dont_n_sans_format_reception' => ", di cui @n@ senza formato di ricezione"
, 'mode_simulation' => "Modalit&agrave: simulazione"
, 'mode_simulation_info' => "La modalit&agrave; simulazione &egrave; attiva. La procedura di invio simula l'invio delle email. 
	In realt&agrave; nessuna email sar&agrave; spedita."
, 'meleuse_suspendue' => "Invio sopseso"
, 'meleuse_reactivee' => "Invio riattivato"
, 'nb_abonnes_sing' => " abbonato;"
, 'nb_abonnes_plur' => " abbonati"
, 'nb_moderateur_sing' => " moderatore"
, 'nb_moderateur_plur' => " moderatori"
, 'aide_en_ligne' => "Aiuto in linea"

// inc/spiplistes_dater_envoi.php
, 'attente_validation' => "In attesa di convalida"
, 'courrier_en_cours_' => "Email in elaborazione "
, 'date_non_precisee' => "Data non precisata"

// inc/spiplistes_destiner_envoi.php
, 'email_tester' => 'Testa l\'email'
, 'choix_non_defini' => 'Nessuna scelta definita.'
, 'destination' => "Destinatario"
, 'aucune_liste_dispo' => "Nessuna newsletter disponibile."

// inc/spiplistes_import.php
// inc/spiplistes_lister_courriers_listes.php
, 'prochain_envoi_' => "Prossimo invio "

// inc/spiplistes_listes_forcer_abonnement.php
// inc/spiplistes_listes_selectionner_auteur.php
, 'lien_trier_nombre' => "Ordina per numero di abbonati"
, 'abonner_format_html' => "Abbonati nel formato HTML"
, 'abonner_format_texte' => "Abbonati nel formato testo"
, 'ajouter_un_moderateur' => "Aggiungi un moderatore "
, 'desabonner' => "Cancella"
, 'pas_adresse_email' => "Nessun indirizzo email"
, 'sup_mod' => "Cancella questo moderatore"
, 'supprimer_un_abo' => "Cancella un abbonato a questa newsletter"
, 'supprimer_cet_abo' => "Cancella questo abbonato a questa newsletter" // + pipeline
, 'abon_ajouter' => "Aggiungi un abbonato "
, '_au_format_s' => ' nel formato @s@'

// inc/spiplistes_mail.inc.php
// inc/spiplistes_meleuse.php
, 'erreur_sans_destinataire' => 'Errore : nessun destinatario pu&ograve; essere trovato per questa email'
, 'envoi_annule' => 'Invio annullato'
, 'sans_adresse' => ' Email non inviata -> Definisci un indirizzo per le risposte'
, 'erreur_mail' => 'Errore: invio dell\'email impossibile (verifica se mail() del php &egrave; disponibile)'
, 'msg_abonne_sans_format' => 'formato di ricezione mancante'
, 'modif_abonnement_html' => "<br />Clicca qui per modificare il tuo abbonamento"
, 'modif_abonnement_text' => 'Per modificare il tuo abbonamento, vai all\'indirizzo seguente: '
, 'stop_abonnement_html' => 'Per annullare l\'abbonamento, clicca qui.'
, 'stop_abonnement_text' => 'Per annullare l\'abbonamento, clicca su questo link: '
, 'msg_abonne_sans_format' => "formato di ricezione mancante"

// inc/spiplistes_naviguer_paniers.php
// inc/spiplistes_pipeline_I2_cfg_form.php
// inc/spiplistes_pipeline_affiche_milieu.php
, 'adresse_email_obligatoire' => "L'indirizzo email &egrave; obbligatorio per potersi abbonare alle newsletter. 
	Se desideri utilizzare questo servizio, per favore inserisci l'indirizzo email. "
, 'alert_abonnement_sans_format' => "Il tuo abbonamento &egrave; sospeso. Non riceverai pi&ugrave; le email delle newsletter 
	di seguito elencate. Per ricevere nuovamente le email delle tue newsletter preferite,
  scegli un formato di ricezione 
	ed invia il form. "
, 'abonnements_aux_courriers' => "Abbonamenti alle email"
, 'forcer_abonnement_erreur' => "Errore tecnico segnalato durante la modifica degli abbonati di una newsletter. 
	Verifica questa newsletter prima di proseguire."
, 'format_obligatoire_pour_diffusion' => "Per confermare l'abbonamento di questo account, devi selezionare un formato di 
	ricezione."
, 'valider_abonnement' => "Convalida questo abbonamento"
, 'vous_etes_abonne_a_la_liste_selectionnee' => 'Sei abbonato alla newsletter selezionata.'
, 'vous_etes_abonne_aux_listes_selectionnees' => 'Sei abbonato alle newsletter selezionate.'
, 'vous_etes_abonne_aux_listes_selectionnees_' => "Sei abbonato alle newsletter selezionate "
, 'vous_n_etes_plus_abonne_aux_listes' => 'Non sei più abbonato alle newsletter.'

// inc/spiplistes_pipeline_ajouter_boutons.php
// inc/spiplistes_pipeline_ajouter_onglets.php
// inc/spiplistes_pipeline_header_prive.php
// inc/spiplistes_pipeline_insert_head.php

// formulaires, patrons, etc.
, 'abo_1_lettre' => 'Newsletter'
, 'abonnement_seule_liste_dispo' => "Abbonamento alla sola newsletter disponibile "
, 'abo_listes' => 'Abbonamento'
, 'abonnement_0' => 'Abbonamento'
, 'inscrivez_vous_' => 'Iscriviti : '
, 'votre_adresse_mail' => 'Il tuo indirizzo email'
, 'inscription_newsletter' => 'Iscrizione Newsletter'
, 'email_deja_enregistre' => 'Questo indirizzo email è già registrato.'
, 'inscrivez_vous_newsletter' => 'Iscriviti per ricevere le newsletter'
, 'abonnement_titre_mail'=>'Modifica il tuo abbonamento'
, 'votre_abo_listes' => 'Il tuo abbonamento alle newsletter'
, 'lire' => 'Leggi'
, 'listes_de_diffusion_' => "Newsletter "
, 'jour' => 'giorno'
, 'jours' => 'giorni'
, 'abonnement_bouton'=>'Modifica il tuo abbonamento'
, 'abonnement_cdt' => "<a href='http://bloog.net/spip-listes/'>SPIP-Listes</a>"
, 'abonnement_change_format' => "Puoi cambiare il formato di ricezione o cancellare l'abbonamento : "
, 'abonnement_texte_mail' => 'Indica qui sotto l\'indirizzo email con il quale ti sei  
	precedentemente abbonato. 
	Riceverai una email che ti permetter&agrave; di accedere alla pagina per modificare il tuo abbonamento.'
, 'article_entier' => 'Leggi l\'intero articolo'
, 'form_forum_identifiants' => 'Conferma'
, 'form_forum_identifiant_confirm'=>'Il tuo abbonamento &egrave; stato registrato, riceverai una email di conferma.'
, 'demande_enregistree_retour_mail' => '
  La tua richiesta è registrata. Riceverai una email di conferma.
	'
, 'effectuez_modif_validez' => 'Effettua le modifiche desiderate per il tuo abbonamento, poi salva.'
, 'desabonnement_confirmez_' => 'Conferma la cancellazione
  dalla newsletter:'
, 'je_confirme_desinscrition' => 'Elimina questa iscrizione'
, 'vous_etes_desabonne' => '
  Sei ora cancellato dalle newsletter,
  ma la tua iscrizione è sempre valida. Per tornare a questo modulo di modifica
  di abbonamento, utilizza il link che ti è stato inviato o inserisci nuovamente il tuo
  indirizzo email nel modulo di iscrizione.
	'
, 'inscription_mail_forum' => 'Ecco i tuoi dati per accedere al sito @nom_site_spip@ (@adresse_site@)'
, 'inscription_mail_redac' => 'Ecco i tuoi dati per accedere al sito @nom_site_spip@ (@adresse_site@) 
	ed all\'interfaccia redazionale (@adresse_site@/ecrire)'
, 'inscription_visiteurs' => 'L\'abbonamento ti consente di accedere alle sezioni private del sito,
	di partecipare ai forum riservati agli abbonati e di ricevere 
	le newsletter.'
, 'inscription_redacteurs' =>'Lo spazio redazionale di questo sito &egrave; aperto agli utenti previa iscrizione.
	Una volta registrati, potrai consultare gli articoli in corso di redazione, proporre degli articoli
	e partecipare a tutti i forum. L\'iscrizione ti consente anche di accedere alle sezioni 
	private del sito e di ricevere le newsletter.'
, 'mail_non' => 'Non sei abbonato alla newsletter del sito @nom_site_spip@'
, 'messages_auto' => 'Email automatica'
, 'nouveaute_intro' => 'Salve, <br />Ecco le novit&agrave; pubblicate sul sito'
, 'nom' => 'Nome utente'
, 'texte_lettre_information' => 'Ecco la newsletter di '
, 'vous_pouvez_egalement' => 'Puoi anche'
, 'vous_inscrire_auteur' => 'iscriverti come autore'
, 'voir_discussion' => 'Vedere la discussione'
, 'inconnu' => 'non &egrave; pi&ugrave; abbonato alla newsletter'
, 'infos_liste' => 'Informazioni su questa newsletter'
, 'editeur' => 'Editore : '
, 'html_description' => " Formattazione avanzata (caratteri in grassetto o corsivo, eventualmente con immagini)"
, 'texte_brut' => "testo semplice"
, 'vous_etes_abonne_aux_listes_' => "Sei abbonato alle newsletter:"
, 'vous_etes_abonne_a_la_liste_' => "Sei abbonato alla newsletter:"
, 'votre_email' => 'La tua email'
, 'votre_email_' => 'La tua email: '
, 'liste_inconnue' => 'newsletter sconosciuta'
, 'cet_email_pas_valide' => 'Questa email non è valida'
, 'cet_email_deja_enregistre' => 'Questa email è già registrata.'
, 'erreur_traitement_action' => 'Un errore tecnico non consente questa azione.'
, 'pas_abonne_liste' => 'Tu non sei, o non sei più, abbonato a questa newsletter.'
, 'action_interdite' => 'Questa azione non è autorizzata.'
, 'vous_etes_desinscrit' => 'Sei ora cancellato da questa newsletter.'
, 'difficile_lire_msg' => 'Se incontri delle difficoltà a leggere questa email, '
, 'vous_consultez_ori' => 'puoi consultare l\'originale sul sito'
, 'vous_recevez_ce_courrier' => 'Ricevi questa mail in seguito all\'iscrizione online'
, 'ce_mail_envoye_a_' => 'Questo messaggio è indirizzato a '
, 'si_mail_par_erreur' => 'Se questa mail ti è stata consegnata per errore,
    avvisaci e cancella questo messaggio.'

// spiplistes_mes_fonctions.php
, 'date_depuis' => 'da @delai@'


// tableau items *_options
, 'liste_de_destination' => "Lista di destinazione"
, 'listes_1_du_mois' => "Pubbliche, 1<sup><small>°</small></sup> del mese."
, 'liste_diffusee_le_premier_de_chaque_mois' => "Newsletter inviata il primo di ogni mese. "
, 'listes_autre' => "Altra periodicit&agrave;"
, 'listes_autre_periode' => "Newsletter pubbliche altra periodicit&agrave;"
, 'listes_privees_autre_periode' => 'Newsletter private altra periodicità'
, 'listes_diffusion_prive' => "Newsletter private"
, 'liste_hebdo' => "Newsletter settimanale"
, 'listes_privees_hebdo' => 'Newsletter private settimanali'
, 'publiques_hebdos' => "Pubbliche, settimanali"
, 'listes_diffusion_hebdo' => "Newsletter pubbliche settimanali"
, 'liste_mensuelle' => "Newsletter mensile"
, 'publiques_mensuelles' => "Pubbliche, mensili"
, 'listes_diffusion_mensuelle' => "Newsletter pubbliche mensili"
, 'listes_privees_mensuelle' => 'Newsletter private mensili'
, 'listes_diffusion_publiques_desc' => "L'abbonamento a questa newsletter &egrave; proposto sul sito pubblico."
, 'liste_annuelle' => "Newsletter annuale"
, 'publiques_annuelles' => "Pubbliche annuali"
, 'privees_annuelles' => 'Private annuali'
, 'listes_diffusion_annuelle' => "Newsletter pubbliche annuali"
, 'listes_privees_annuelle' => 'Newsletter private annuali'
, 'listes_diffusion_publique' => 'Newsletter pubbliche'
, 'listes_diffusion_privees' => 'Newsletter private'
, 'listes_diffusion_privees_desc' => "L'abbonamento a queste liste &egrave; riservato agli amministratori ed agli autori del sito."
, 'listes_diffusion_suspendue' => 'Newlstter spospese'
, 'listes_diffusion_suspendue_desc' => " "
, 'courriers_en_cours_de_redaction' => 'Email in corso di redazione'
, 'courriers_en_cours_denvoi' => 'Email in corso di invio'
, 'courriers_prets_a_etre_envoye' => "Email pronte per essere inviate"
, 'courriers_publies' => "Email pubbliche"
, 'courriers_auto_publies' => "Email autoametiche pubblicate"
, 'courriers_stope' => "Email fermate in corso di invio"
, 'courriers_vides' => "Email annullate (cancellate)"
, 'courriers_sans_destinataire' => "Email senza destinatario (elenco vuoto)"
, 'courriers_sans_liste' => "Email senza abbonati (elenco mancante)"
, 'devenir_redac'=>'diventa redattore per questo sito'
, 'devenir_membre' => 'diventa membro del sito'
, 'devenir_abonne' => "Iscriviti a questo sito"
, 'desabonnement_valid'=>'Il seguente indirizzo non &egrave; pi&ugrave; abbonato alla newsletter' 
, 'pass_recevoir_mail'=>'Riceverai una email che ti indicher&agrave; come modificare il tuo abbonamento. '
, 'discussion_intro' => 'Salve, <br />Ecco le discussioni iniziate sul sito'
, 'en_redaction' => "In redazione"
, 'en_cours' => "In corso"
, 'editeur_nom' => "Nemo dell'editore "
, 'editeur_adresse' => "Indirizzo "
, 'editeur_rcs' => "N&deg; RCS "
, 'editeur_siret' => "N&deg; SIRET "
, 'editeur_url' => "URL dels sito dell'editore "
, 'editeur_logo' => "URL del logo dell'editore "
, 'envoi_abandonne' => "Invio abbandonato"
, 'liste_prive' => "Newsletter privata"
, 'liste_publique' => "Newsletter pubblica"
, 'message_redac' => 'In corso di redazione e pronta all\'invio'
, 'prets_a_envoi' => "Pronta all'invio"
, 'publies' => "Pubblicate"
, 'publies_auto' => "Pubblicate (auto)"
, 'stoppes' => "Fermate"
, 'sans_destinataire' => "Senza destinatario"
, 'sans_abonnement' => "Senza abbonamento"
, 'sans_abonne' => "senza abbonati"
, 'sans_moderateur' => "senza moderatore"
, 'inconnu' => 'Sconosciuto'

// raccourcis des paniers
, 'aller_au_panier_' => "Vai alla gruppo "
, 'aller_aux_listes_' => "Vai alle newsletter "
, 'nouveau_courrier' => 'Crea una nuova email'
, 'nouvelle_liste_de_diffusion' => 'Crea una nuova newsletter'
, 'trieuse_suspendue' => "Invio automatico sospeso"
, 'trieuse_suspendue_info' => "La procedura per le newsletter programmate &egrave;sospeso."
, 'trieuse_reactivee' => "Invio automatico riattivato"

// mots 
, 'ajout' => 'Aggiungi'
, 'aucun' => "nessuno"
, 'configuration' => 'Configurazione'
, 'courriers' => 'Email'
, 'creation' => 'Creazione'
, '_de_' => " di "
, '_dont_' => ' di cui '
, '_avec_' => ' con '
, 'email' => 'E-mail'
, 'format' => 'Formato'
, 'modifier' => 'Modifica'
, 'max_' => "Max "
, 'patron_' => "Modello : "
, 'spiplistes' => "SPIP-Listes"
, 'recherche' => 'Cerca'
, 'retablir' => "Ripristina"
, 'site' => 'Sito web'
, 'sujets' => 'Oggetti'
, 'sup_' => "Canc."
, 'total' => "Totale "
, 'voir' => 'vedi'
, 'vides' => "Vuoto"
, 'choisir' => 'Scegli'
, 'desabo' => 'non abbonato'
, 'desabonnement' => 'Cancella abbonamento&nbsp;'
, 'desabonnes' => 'Non abbonati'
, 'destinataire' => 'destinatario'
, 'destinataires' => 'Destinatari'
, 'erreur' => 'Errore'
, 'html' => 'HTML'
, 'retour_link' => 'Torna'
, 'texte' => 'Testo'
, 'version' => 'versione'
, 'fichier_' => 'File '

, 'jquery_inactif' => 'jQuery non rilevato. Si prega di attivarlo.'
, 'javascript_inactif' => 'Javascript non attivo. Questa pagina non può funzionare
  senza Javascript. Si prega di attivarlo.'


// description de la page abonnement
, 'description_page_abonnement' => 'Pagina sulla quali gli abbonati vengono rediretti per iscriversi o modificare il loro abbonamento.'
, 'nom_page_abonnement' => 'Pagina di abbonamento alle newsletter'

// noisettes
, 'noisette_formulaire_spip_listes_inscription' => 'Modulo di iscrizione alle newsletter'
, 'noisette_formulaire_gestion_abonnement' => 'Modulo di gestione dell\'abbonamento alle newsletter'
, 'modifier_abonnement' => 'Modifica il tuo abbonamento alle newsletter'
, 'noisette_formulaire_inscription_gestion_abonnement' => 'Modulo di iscrizione e gestione dell\'abbonamento alle newsletter'
, 'description_noisette_formulaire_inscription_gestion_abonnement' => 'Mostra il modulo di gestione dell\'abbonamento se un cookie è presente o se un auitore è identificato, altrimenti mostra il modulo di iscrizione.'
, 'lettres_information' => 'Newsletter'



///////
// a priori, pas|plus utilise'
, 'supprime_contact_base' => 'Supprimer d&eacute;finitivement de la base'
, 'forcer_lot' => 'Provoquer l\'envoi du lot suivant'
, 'erreur_destinataire' => 'Erreur destinataire : pas d\'envoi'
, 'contacts_lot' => 'Contacts de ce lot'
, 'envoi_fini' => 'Envois termin&eacute;s'
, 'non_courrier' => 'Pas / plus de courrier &agrave; envoyer'
, 'non_html' => 'Votre logiciel de messagerie ne peut apparemment pas afficher correctement la version graphique (HTML) de cet e-mail'
, 'envoi_erreur' => 'Erreur : SPIP-Listes ne trouve pas de destinataire pour ce courrier'
, 'email_reponse' => 'Email de r&eacute;ponse : '
, 'envoi_listes' => 'Envoi &agrave; destination des abonn&eacute;s &agrave; la liste : '
, 'confirmer' => 'Confirmer'
, 'listes_emails' => 'Lettres d\'information'
, 'info_liste_1' => 'liste'
, 'bonjour' => 'Bonjour,' // deja dans SPIP
, 'envoi_tous' => 'Envoi &agrave; destination de tous les inscrits'
, 'patron_detecte' => '<p><strong>Patron d&eacute;tect&eacute; pour la version texte</strong><p>'
, 'val_texte' => 'Texte'
, 'membres_sans_messages_connecte' => 'Vous n\'avez pas de nouveaux messages'
, 'messages_derniers' => 'Derniers Messages'
, 'pas_abonne_en_ce_moment' => "n'est pas abonn&eacute;"
, 'reinitialiser' => 'reinitialiser'
, 'mail_a_envoyer' => 'Nombre de mails &agrave; envoyer : '
, 'lettre_d_information' => 'Lettre d\'information'
, 'desole' => 'D&eacute;sol&eacute;'
, 'historique_des_envois' => 'Historique des envois'
, 'abonnement'=>'Vous souhaitez modifier votre abonnement &agrave; la lettre d\'information'
, 'patron_disponibles' => 'Patrons disponibles'
, 'liste_diff_publiques' => 'Listes de diffusion publiques<br /><i>La page du site public propose l\'inscription &agrave; ces listes.</i>'
, 'messages_non_lus_grand' => 'Pas de nouveaux messages'
, 'messages_repondre' => 'Nouvelle R&eacute;ponse'
, 'liste_abandonnee' => "Liste abandonn&eacute;e"
, 'par_date' => 'Par date d\'inscription'
, 'info_auto' => 'SPIP-Listes pour spip peut envoyer r&eacute;guli&egrave;rement aux inscrits, l\'annonce des derni&egrave;res nouveaut&eacute;s du site (articles et br&egrave;ves r&eacute;cemment publi&eacute;s).'
, 'format2' => 'Format :'
, 'liste_des_abonnes' => "Liste des abonn&eacute;s"
, 'lieu' => 'Localisation'
, 'efface_base' => 'a &eacute;t&eacute; effac&eacute; des listes et de la base'
, 'lot_suivant' => 'Provoquer l\'envoi du lot suivant'
, 'listes_internes' => 'Listes de diffusion internes<br /><i>Au moment de l\'envoi d\'un courrier, ces listes sont propos&eacute;es parmi les destinataires</i>'
, 'adresses_importees' => "Adresses import&eacute;es"
, 'aff_envoye' => 'Courriers envoy&eacute;s'
, 'abonner' => 's\'abonner'
, 'abonnes_liste_int' => 'Abonn&eacute;s aux listes internes : '
, 'abonnes_liste_pub' => 'Abonn&eacute;s aux listes publiques : '
, 'actualiser' => 'Actualiser'
, 'a_destination_de_' => '&agrave; destination de '
, 'aff_lettre_auto' => 'Lettres des nouveaut&eacute;s envoy&eacute;es'
, 'alerte_edit' => 'Le formulaire ci-dessous permet de modifier le texte d\'un courrier. 
	Vous pouvez choisir de commencer par importer un patron pour g&eacute;n&eacute;rer le contenu de votre message.'
, 'alerte_modif' => '<strong>Apr&egrave;s l\'affichage de votre courrier, vous pourrez en modifier le contenu</strong>'
, 'lock' => 'Lock actif : '
, 'apercu' => "Aper&ccedil;u"
, 'bouton_listes' => 'Lettres d\'information'
, 'bouton_modifier' => 'Modifier ce courrier'
, 'dans_jours' => 'dans'
, 'charger_le_patron' => 'G&eacute;n&eacute;rer le courrier'
, 'choix_defini' => 'Pas de choix d&eacute;fini.\n'
, 'definir_squel_choix' => 'A la r&eacute;daction d\'un nouveau courrier, SPIP-Listes vous permet de charger un patron. 
	En appuyant sur un bouton, vous chargez dans le corps du courrier le contenu d\'un des squelettes du 
	repertoire <strong>/patrons</strong> (situ&eacute; &agrave; la racine de votre site Spip). 
	<p><strong>Vous pouvez &eacute;diter et modifier ces squelettes selon vos go&ucirc;ts.</strong></p> 
	<ul><li>Ces squelettes peuvent contenir du code HTML classique</li>
	<li>Ce squelette peut contenir des boucles Spip</li>
	<li>Apr&egrave;s le chargement du patron, vous pourrez re-&eacute;diter le courrier avant envoi (pour ajouter du texte)</li>
	</ul><p>La fonction "charger un patron" permet donc d\'utiliser des gabarits HTML personnalis&eacute;s pour vos courriers 
	ou de cr&eacute;er des lettres d\'information th&eacute;matiques dont le contenu est d&eacute;fini gr&acirc;ce aux boucles Spip.</p>
	<p>Attention : ce squelette ne doit pas contenir de balises body, head ou html mais juste du code HTML ou des boucles Spip.</p>'
, 'definir_squel' => 'Choisir le mod&egrave;le de courrier &agrave; pr&eacute;visualiser'
, 'courrier_realise_avec_spiplistes' => "Courrier r&eacute;alis&eacute; avec SPIP-Listes"
, 'definir_squel_texte' => 'Si vous disposez des codes d\'acc&egrave;s au FTP, vous pouvez ajouter des squelettes SPIP dans le r&eacute;pertoire /patrons (&agrave; la racine de votre site Spip).'
, 'dernier_envoi'=>'Dernier envoi il y a'
, 'desabonnement_confirm'=>'Vous &ecirc;tes sur le point de r&eacute;silier votre abonnement &agrave; la lettre d\'information'
, 'date_depuis'=>'depuis @delai@'
, 'envoi_charset' => 'Charset de l\'envoi'
, 'envoi_nouv' => 'Envoi des nouveaut&eacute;s'
, 'envoi_program' => 'Envoi programm&eacute;'
, 'envoi_smtp' => 'Lors d\'un envoi via la m&eacute;thode SMTP ce champ d&eacute;finit l\'adresse de l\'envoyeur.'
, 'envoi_texte' => 'Si ce courrier vous convient, vous pouvez l\'envoyer'
, 'email_envoi' => 'Envoi des emails'
, 'envoi' => 'Envoi :'
, 'erreur_install' => '<h3>erreur: spip-listes est mal install&eacute;!</h3>'
, 'erreur_install2' => '<p>V&eacute;rifier les &eacute;tapes d\'installation, notamment si vous avez bien renomm&eacute;<i>mes_options.txt</i> en <i>mes_options.php</i>.</p>'
, 'exporter' => 'Exporter la liste d\'abonn&eacute;s'
, 'erreur_appel_courrier' => "Erreur lors de l'appel du courrier"
, 'faq' => 'FAQ'
, 'forum' => 'Forum'
, 'ferme' => 'Cette discussion est cl&ocirc;tur&eacute;e'
, 'gestion_du_courrier' => "Gestion du courrier"
, 'info_heberg' => 'Certains h&eacute;bergeurs d&eacute;sactivent l\'envoi automatique de mails depuis leurs serveurs. 
	Dans ce cas, les fonctionnalit&eacute;s suivantes de SPIP-Listes pour SPIP ne fonctionneront pas'
, 'info_nouv' => 'Vous avez activ&eacute; l\'envoi des nouveaut&eacute;s'
, 'info_nouv_texte' => 'Prochain envoi des nouveaut&eacute;s dans @proch@ jours'
, 'log' => 'Logs'
, 'login' => 'Connexion'
, 'logout' => 'D&eacute;connexion'
, 'mail_format' => 'Vous &ecirc;tes abonn&eacute; &agrave; la lettre d\'information du site @nom_site_spip@ en format'
, 'messages_auto_texte' => '<p>Par d&eacute;faut, le squelette des nouveaut&eacute;s permet d\'envoyer automatiquement 
	la liste des articles et br&egrave;ves publi&eacute;s sur le site depuis le dernier envoi automatique. </p>
	<p>vous pouvez personnaliser le message en d&eacute;finissant l\'adresse d\'un logo et d\'une image de fond 
	pour les titres de parties en &eacute;ditant le fichier nomm&eacute; <strong>"nouveautes.html"</strong> 
	(situ&eacute; &agrave; dans le rep&eacute;rtoire /dist).</p>'
, 'membres_groupes' => 'Groupes d\'utilisateurs'
, 'membres_profil' => 'Profil'
, 'membres_messages_deconnecte' => 'Se connecter pour v&eacute;rifier ses messages priv&eacute;s'
, 'membres_avec_messages_connecte' => 'Vous avez @nombres@ nouveau(x) message(s)'
, 'message' => 'Message : '
, 'message_date' => 'Post&eacute; le '
, 'messages' => 'Courriers'
, 'messages_forum_clos' => 'Forum d&eacute;sactiv&eacute;'
, 'messages_nouveaux' => 'Nouveaux messages'
, 'messages_pas_nouveaux' => 'Pas de nouveaux messages'
, 'messages_voir_dernier' => 'Voir le dernier message'
, 'moderateurs' => "Mod&eacute;rateur(s)"
, 'mis_a_jour' => 'Mis &agrave; jour'
, 'nouveaux_messages' => 'Nouveaux messages'
, 'numero' => 'N&nbsp;'
, 'photos' => 'Photos'
, 'poster' => 'Poster un Message'
, 'publie' => 'Publi&eacute; le'
, 'aucune_liste_publique' => "Aucune liste de diffusion publique disponible."
, 'revenir_haut' => 'Revenir en haut de la page'
, 'reponse' => 'En r&eacute;ponse au message'
, 'reponse_plur' => 'r&eacute;ponses'
, 'reponse_sing' => 'r&eacute;ponse'
, 'retour' => 'Adresse email du gestionnaire de la liste (reply-to)'
, 'suivi_des_abonnements' => 'Suivi des abonnements'
, 'sujet_nouveau' => 'Nouveau sujet'
, 'sujet_auteur' => 'Auteur'
, 'sujet_visites' => 'Visites'
, 'sujet_courrier_auto' => 'Sujet du courrier automatique : '
, 'sujets_aucun' => 'Pas de sujet dans ce forum pour l\'instant'
, 'sujet_clos_titre' => 'Sujet Clos'
, 'sujet_clos_texte' => 'Ce sujet est clos, vous ne pouvez pas y poster.'
, 'masquer_le_journal_SPIPLISTES' => "Masquer le journal de SPIP-Listes"
, 'abon' => 'LES ABONNES'
, 'abonees' => 'tous les abonn&eacute;s'
, 'abonnement_newsletter' => '<strong>Abonnement &agrave; la lettre d\'information</strong>'
, 'acces_a_la_page' => 'Vous n\'avez pas acc&egrave;s &agrave; cette page.'
, 'adresse_deja_inclus' => 'Adresse d&eacute;j&agrave; connue'
, 'choisir_cette_liste' => 'Choisir cette liste'
, 'Charger_un_patron' => "Charger un patron"
, 'date_ref' => 'Date de r&eacute;f&eacute;rence'
, 'efface' => 'a &eacute;t&eacute; effac&eacute; des listes et de la base'
, 'email_collec' => 'R&eacute;diger un courrier'
, 'email_test_liste' => 'Envoyer vers une liste de diffusion'
, 'envoyer' => 'envoyer le mail'
, 'envoyer_a' => 'Envoi vers '
, 'listes_poubelle' => 'Vos listes de diffusion &agrave; la poubelle'
, 'liste_numero_:' => 'Liste num&eacute;ro :'
, 'mail_tache_courante' => 'Mails envoy&eacute;s pour la t&acirc;che courante : '
, 'messages_auto_envoye' => 'Courriers automatiques envoy&eacute;s'
, 'nb_abonnes' => 'Dans les listes : '
, 'nb_inscrits' => 'Dans le site :  '
, 'nb_listes' => 'Incriptions dans toutes les listes : '
, 'nouvelle_abonne' => 'L\'abonn&eacute; suivant a &eacute;t&eacute; ajout&eacute; la liste'
, 'pas_acces' => 'Vous n\'avez pas acc&egrave;s &agrave; cette page.'
, 'plus_abonne' => ' n\'est plus abonn&eacute; &agrave; la liste '
, 'prochain_envoi_aujd' => 'Prochain envoi pr&eacute;vu aujourd\'hui'
, 'prochain_envoi_prevu_dans' => 'Prochain envoi pr&eacute;vu dans '
, 'program' => 'Programmation des courriers automatiques'
, 'plein_ecran' => "(Plein &eacute;cran)"
, 'remplir_tout' => 'Tous les champs doivent &ecirc;tre remplis'
, 'repartition' => 'R&eacute;partition'
, 'squel' => 'Patron : &nbsp;'
, 'suivi_envois' => 'Suivi des envois'
, 'supprime_contact' => 'Supprimer ce contact d&eacute;finitivement'
, 'tableau_bord' => 'Tableau de bord'
, 'toutes' => 'Tous les inscrits'
, 'acces_refuse' => 'Vous n\'avez plus acc&egrave;s &agrave; ce site'
, 'confirmation_format' => ' en format '
, 'confirmation_liste_unique_1' => 'Vous &ecirc;tes abonn&eacute; &agrave la liste d\'information du site'
, 'confirmation_liste_unique_2' =>'Vous avez choisi de recevoir les courriers adress&eacute;s &agrave la liste suivante :'
, 'confirmation_listes_multiples_1' => 'Vous &ecirc;tes abonn&eacute; aux listes d\'informations du site '
, 'confirmation_listes_multiples_2' => 'Vous avez choisi de recevoir les courriers adress&eacute;s aux listes suivantes :'
, 'contacts' => 'Nombre de contacts'
, 'patron_erreur' => 'Le patron sp&eacute;cifi&eacute; ne donne pas de r&eacute;sulat avec les param&egrave;tres choisis'
, 'abonees_titre' => 'Abonn&eacute;s'
, 'options' => 'radio|brut|Format :|Html,Texte,D&eacute;sabonnement|html,texte,non'

);

?>