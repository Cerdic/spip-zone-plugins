<?php

// This is a SPIP module file  --  Ceci est un fichier module de SPIP

$GLOBALS['i18n_spiplistes_it'] = array(


//_
'_aide' => 'SPIP-Listes permette di inviare una lettera a chi &egrave; abbonato. &Egrave; possibile utilizzare un modello preimpostato, scrivere un testo non formattato o
comporre il messaggio in HTML. 
<br /><br /> Gli iscritti definiscono on line il proprio status di abbonamento, le liste alle quali vogliono far parte e il formato 
col quale desiderano ricevere le comunicazioni (HTML/testo). <br /><br />Ogni messaggio verr&agrave; tradotto automaticamente in formato testo per quegli iscritti che ne hanno fatto richiesta.<br /><br /><b>Nota:</b><br />L\'invio delle e-mail pu&ograve; durare alcuni minuti: i flussi partono a poco a poco mentre gli utenti visitano il sito pubblico. &Egrave; anche possibile provocare manualmente l\'invio dei flussi cliccando sul link "controlla gli invii" durante un invio.',

// A
'abo_1_lettre' => 'Iscrizione alla newsletter di ',
'abonnement_0' => 'Iscrizione',
'abonnement'=>'Si desidera modificare la propria iscrizione alla newsletter',
'abonnement_bouton'=>'Modifica l&#39;iscrizione',
'abonnement_cdt' => '<a href=\'http://bloog.net\'>SPIP-Listes</a>' ,
'abonnement_change_format'=>'&Egrave; possibile cambiare il formato del messaggio oppure cancellarsi dalla lista: ',
'abonnement_mail' => 'Per modificare il proprio abbonamento &egrave; necessario andare all\'indirizzo seguente',
'abonnement_mail_passcookie' => '(questo &egrave; un messaggio automatico)
Per modificare la propria iscrizione alla newsletter di questo sito:
@nom_site_spip@ (@adresse_site@)

Andate all\'indirizzo seguente:

    @adresse_site@/abonnement.php3?d=@cookie@

In seguito potete confermare la variazione del vostro abbonamento.',
'abonnement_modifie'=>'Le modifiche sono state registrate',
'abonnement_nouveau_format'=>'Da ora riceverete i messaggi con il seguente formato: ',
'abonnement_titre_mail'=>'Modifica l\'abbonamento',
'abonnement_texte_mail'=>'Indicare nello spazio sottostante l\'indirizzo email con il quale vi siete registrati precedentemente. 
Riceverete una email che vi permetter&agrave; di accedere alla pagina di modifica del vostro abbonamento.',
'abonner' => 'iscriversi',
'actualiser' => 'Aggiorna',
'adresse' => 'Indicare qui l\'indirizzo da utilizzare come Reply-To (se vuoto verr&agrave; utilizzato l\'indirizzo del webmaster):',
'adresses_importees' => 'Indirizzi importati',
'aff_redac' => 'Messaggi in corso di redazione',
'aff_encours' => 'Messaggi in corso di spedizione',
'aff_envoye' => 'Messaggi inviati',
'aff_lettre_auto' => 'Newsletter inviate',
'aff_envoye' => 'Messaggi inviati',
'alerte_edit' => 'Attenzione: questo messaggio pu&ograve; essere modificato da tutti gli amministratori del sito e viene spedito a tutti gli iscritti. Non utilizzare la newsletter se non per informare via email di avvenimenti importanti della vita del sito.',
'alerte_modif' => '<b>Dopo aver visualizzato il messaggio &egrave; possibile modificarne il contenuto</b>',
'annuler_envoi' => 'Annulla l\'invio',

// B 
 
//C
'Cette_liste_est' => 'Questa lista &egrave;',
'charger_patron' => 'Scegli un modello',
'charger_le_patron' => 'Genera il messaggio',
'Configuration' => 'Configurazione',
'courriers' => 'Messaggi',

//D
'definir_squel' => 'Scegli il modello di messaggio per l\'anteprima',
'definir_squel_choix' => 'Durante la stesura di un nuovo messaggio, SPIP-Listes permette di caricare un modello preimpostato. Cliccando su un pulsante, nel corpo del messaggio verr&agrave; caricato il contenuto di uno dei modelli di layout della cartella <b>/patrons</b> (situata alla radice del proprio sito SPIP). <p><b>&Egrave; possibile creare e modificare questi modelli a piacimento.</b></p> <ul><li>Questi modelli possono contenere codice HTML classico</li>
<li>Questo modello pu&ograve; contenere cicli di SPIP</li>
<li>Dopo aver caricato il patron, &egrave; possibile riscrivere il messaggio prima dell\'invio (per aggiungere del testo)</li>
</ul><p>La funzione "carica un modello preimpostato" permette di utilizzare delle maschere in HTML personalizzate per i propri messaggi oppure di creare delle newsletter tematiche il cui contenuto viene definito usando i cicli di SPIP.</p><p>Attenzione: questo modello non deve contenere tag BODY, HEAD o HTML ma solo codice HTML o cicli SPIP.</p>',
'definir_squel_texte' => 'Se si dispone dei codici di accesso FTP &egrave; possibile aggiungere modelli SPIP nella cartella /patrons (alla radice del proprio sito SPIP).',
'devenir_redac'=>'diventare redattore di questo sito',
'devenir_abonne'=>'Iscriversi su questo sito',
'desabonnement_valid'=>'L\'indirizzo seguente non &egrave; pi&ugrave; iscritto alla newsletter' ,
'pass_recevoir_mail'=>'Riceverete una email con le indicazioni per modificare il proprio abbonamento. ',
'desabonnement_confirm'=>'State per cancellare l\'abbonamento alla newsletter',
'date_depuis'=>'depuis @delai@', //FIX ME

//E
'email' => 'E-mail',
'envoi' => 'Invio:',
'envoi_nouv' => 'Invio delle novit&agrave;',
'envoi_program' => 'Invio programmato',
'envoi_texte' => 'Se questo messaggio &egrave; pronto potete inviarlo',
'exporter' => 'Esporta l\'elenco degli abbonati',

//F
'faq' => 'FAQ',
'forum' => 'Forum',
'ferme' => 'Questa discussione &egrave; chiusa',
'form_forum_identifiants' => 'Conferma',
'form_forum_identifiant_confirm'=>'L\'iscrizione &egrave; stata registrata. Riceverete una email di conferma.',
'format' => 'Formato',

//H
'Historique_des_envois' => 'Storico degli invii',

//I
'info_auto' => 'SPIP-Listes per SPIP pu&ograve; inviare periodicamente agli iscritti, l\'annuncio delle ultime novit&agrave; del sito (articoli e brevi pubblicati di recente).',
'info_heberg' => 'Alcuni hoster disattivano l\'invio automatico di email dai propri server. In tal caso, le seguenti funzionalit&agrave; di SPIP-Listes per SPIP non potranno funzionare',
'info_nouv' => 'L\'invio delle novit&agrave; &egrave; stato attivato',
'info_nouv_texte' => 'Prossimo invio delle novit&agrave; tra @proch@ giorni',
'inscription_mail_forum' => 'Questi sono i dati di identificazione per connettersi al sito @nom_site_spip@ (@adresse_site@)',
'inscription_mail_redac' => 'Questi sono i dati di identificazione per connettersi al sito @nom_site_spip@ (@adresse_site@) e all\'interfaccia di redazione (@adresse_site@/ecrire)',
'inscription_visiteurs' => 'L\'abbonamento permette di accedere alle zone riservate del sito,
di intervenire nei forum riservati ai visitatori registrati e di ricevere le newsletter.' ,

'inscription_redacteurs' =>'L\'area di redazione di questo sito &egrave; aperta ai visitatori previa iscrizione.
Dopo essersi registrati &egrave; possibile consultare gli articoli in corso di redazione, proporre articoli 
e partecipare a tutti i forum.  L\'iscrizione permette di accedere anche alle zone riservate del sito 
e ricevere le newsletter.',
'import_export' => 'Importa / Esporta',

//J
'jours' => 'giorni',

//L
'Listes_de_diffusion' => 'Newsletter',
'login' => 'Connessione',
'logout' => 'Sconnessione',
'lot_suivant' => 'Inizia l\'invio del flusso seguente',
'lieu' => 'Localizzazione',
'lettre_d_information' => 'Newsletter',
'liste_numero' => 'LISTE NUM&Eacute;RO***FIX ME***',


//M
'mail_format' => 'Siete iscritti alla newsletter del sito @nom_site_spip@ con il formato',
'mail_non' => 'Non siete iscritti alla newsletter del sito @nom_site_spip@',
'message_arch' => 'Messaggio archiviato',
'messages_auto' => 'Messaggi automatici',
'messages_auto_texte' => '<p>Di default il modello delle novit&agrave; permette di inviare in maniera automatica l\'elenco degli articoli e delle brevi pubblicati sul sito a partire dalla data dell\'invio automatico precedente. </p><p>&Egrave; possibile personalizzare il messaggio definendo l\'indirizzo di un logo e di un\'immagine di sfondo per i titoli di parti, modificando il file <b>"nouveautes.html"</b> (situato alla radice del proprio sito SPIP).</p>',
'message_redac' => 'In corso di redazione e pronto all\'invio',
'message_en_cours' => 'Messaggio in partenza',
'message_type' => 'Email',
'membres_liste' => 'Elenco dei Membri',
'membres_groupes' => 'Gruppi di utenti',
'membres_profil' => 'Profilo',
'membres_messages_deconnecte' => 'Sconnettersi per verificare i propri messaggi privati',
'membres_sans_messages_connecte' => 'Non ci sono nuovi messaggi',
'membres_avec_messages_connecte' => 'Hai @nombres@ nuovo/i messaggio/i',
'message' => 'Messaggio: ',
'message_date' => 'Inviato il ',
'message_sujet' => 'Oggetto ',
'messages' => 'Messaggi',
'Messages_automatiques' => 'Messaggi automatici',
'messages_derniers' => 'Ultimi messaggi',
'messages_forum_clos' => 'Forum disattivato',
'messages_nouveaux' => 'Nuovi messaggi',
'messages_pas_nouveaux' => 'Nessun nuovo messaggio',
'messages_non_lus_grand' => 'Nessun nuovo messaggio',
'messages_repondre' => 'Nuova risposta',
'messages_voir_dernier' => 'Vedi l\'ultimo messaggio',
'moderateurs' => 'Moderatori',
'modifier' => 'Modifica',
'mis_a_jour' => 'Updated', // FIXME

//n
'nom' => 'Nome dell\'utente',
'Nouveau_courrier' => 'Nuova email',
'nouveaux_messages' => 'Nuovi messaggi',
'Nouvelle_liste_de_diffusion' => 'Nuova lista di diffusione',

//P
'par_date' => 'Per data di iscrizione',
'patron_disponibles' => 'Modelli disponibili',
'Patrons' => 'Modelli',
'poster' => 'Invia un messaggio',
'plein_ecran' => '(Plein &eacute;cran) ****FIX ME***',


//R
'recherche' => 'Cerca',
'revenir_haut' => 'Vai a inizio pagina',
'reponse' => 'In risposta al messaggio',
'retour' => 'Indirizzo email del maintainer della lista (Reply-To)',

//S
'suivi' => 'Gestione degli abbonamenti',
'Suivi_des_abonnements' => 'Gestione degli abbonamenti',
'sujet_nouveau' => 'Nuovo argomento',
'sujet_auteur' => 'Autore',
'sujet_visites' => 'Visite',
'sujets' => 'Argomenti',
'sujets_aucun' => 'Nessun argomento in questo forum al momento',
'site' => 'Sito web',
'sujet_clos_titre' => 'Argomento chiuso',
'sujet_clos_texte' => 'Questo argomento &egrave; chiuso, non &egrave; possibile scrivere.',
 
 //T
'texte_boite_en_cours' => 'SPIP-Listes sta inviando un messaggio automatico. <p> &Egrave; possibile provocare l\'invio accelerato dei flussi usando il link in basso.</p> <p>Questo box si chiuder&agrave; al termine dell\'invio.</p>',
'texte_lettre_information' => 'Questa &egrave; la newsletter di ',
'Tous_les' => 'Tutti i',

//V
'voir' => 'vedere',
'vous_pouvez_egalement' => 'Puoi anche',
'vous_inscrire_auteur' => 'iscriverti come autore',

// ====================== spip_listes.php3 ======================
'abon' => 'ABBONATI',
'abon_ajouter' => 'AGGIUNGI UN ABBONATO &nbsp; ',
'abonees' => 'Tutti gli abbonati',
'abonne_listes' => 'Questo contatto &egrave; abbonato alle liste seguenti',
'abonne_aucune_liste' => 'Iscritti senza lista',
'abonnement_simple' => '<b>Abbonamento semplice: </b><br><i>Gli abbonati ricevono un messaggio di conferma a seguito della loro iscrizione</i>',
'abonnement_code_acces' => '<b>Abbonamento con codici di accesso: </b><br><i>Gli abbonati ricevono anche login e password per essere identificati sul sito. </i>',
'abonnement_newsletter' => '<b>Abbonamento alla newsletter</b>',
'acces_a_la_page' => 'Non avete i diritti di accesso a questa pagina.',
'adresse_deja_inclus' => 'L\'indirizzo email risulta gi&agrave; iscritto',
'autorisation_inscription' => 'SPIP-Listes ha appena attivato l\'autorizzazione di iscriversi ai visitatori del sito',

'choisir' => 'Scegli',
'choisir_cette' => 'Scegli questa lista',
'confirme_envoi' => 'Si prega di confermare l\'invio',

'date_act' => 'Dati aggiornati',
'date_ref' => 'Data di riferimento',
'desabo' => 'cancella iscrizione',
'desabonnement' => 'Cancellazione dell\'iscrizione&nbsp;',
'desabonnes' => 'Non pi&ugrave; iscritti',
'desole' => 'Spiacente',
'destinataire' => 'destinatario',
'destinataires' => 'Destinatari',
'efface' => '&egrave; stato cancellato dalle liste e dal database',
'efface_base' => '&egrave; stato cancellato dalle liste e dal database',
'email_adresse' => 'Indirizzo email di prova',
'email_collec' => 'Email di gruppo',
'email_test' => 'Invia una email di prova',
'email_test_liste' => 'Invia ad una lista di diffusione',
'email_tester' => 'Testa per email',
'env_esquel' => 'Invio programmato del modello',
'env_maint' => 'Invia adesso',
'envoyer' => 'invia l&#39;email',
'envoyer_a' => 'Invia a',
'erreur' => 'Errore',
'erreur_import' => 'Il file di importazione causa un errore alla linea ',

'format_date' => 'd/m/Y',

'html' => 'HTML',

'importer' => 'Importa un elenco di abbonati',
'importer_fichier' => 'Importa un file',
'importer_fichier_txt' => '<p><b>L\'elenco di abbonati deve essere un file di solo testo in cui vi &egrave; un solo indirizzo email per riga</b></p>',
'importer_preciser' => '<p>Specificare le liste e il formato corrispondente all\'importazione degli abbonati</p>',
'inconnu' => 'non &egrave; pi&ugrave; abbonato alla lista',

'liste_diff_publiques' => 'Liste di diffusione pubbliche<br><i>La pagina del sito pubblico propone l\'iscrizione a queste liste.</i>',
'liste_sans_titre' => 'Lista senza titolo',
'listes_internes' => 'Liste di diffusione interne<br /><i>Al momento dell\'invio di un messaggio queste liste sono proposte ai destinatari</i>',
'listes_poubelle' => 'Liste di diffusione nel cestino',
'lock' => 'Lock attivo: ',

'mail_a_envoyer' => 'Numero di email da inviare: ',
'mail_tache_courante' => 'Email inviati per la task corrente: ',
'messages_auto_envoye' => 'Messaggi automatici inviati',
'message_en_cours' => 'Si sta inviando un messaggio',
'message_presque_envoye' =>'Si sta inviando questo messaggio.',
'mode_inscription' => 'Definire il modo di iscrizione dei visitatori',
'modif_envoi' => '&Egrave; possibile modificarlo o chiederne l\'invio.',
'modifier_liste' => 'Modifica questa lista',

'nb_abonnes' => 'Nelle liste: ',
'nb_inscrits' => 'Nel sito:  ',
'nb_listes' => 'Iscrizioni in tutte le liste: ',
'non_program' => 'Non c\'&egrave; alcun messaggio automatico programmato per questa lista.',
'nouvelle_abonne' => 'L\'abbonato seguente &egrave; stato aggiunto alla lista',

'pas_acces' => 'Non avete i diritti di accesso per questa pagina.',
'plus_abonne' => ' non &egrave; abbonato alla lista ',
'prochain_envoi_aujd' => 'Prossimmo invio previsto oggi',
'prochain_envoi_prevu' => 'Prossimo invio previsto ',
'prochain_envoi_prevu_dans' => 'Prossimo invio previsto tra',
'prog_env' => 'Programmare un invio automatico',
'prog_env_non' => 'non programmare l\'invio',
'program' => 'Programmazione dei messaggi automatici',

'reinitialiser' => 'Aggiorna',
'remplir_tout' => '&Egrave; obbligatorio riempire tutti i campi',
'repartition' => 'Distribuzione',
'retour_link' => 'Indietro',

'sans_envoi' => 'Attenzione, l\'indirizzo email di prova che avete fornito non corrisponde ad alcun abbonato, <br />non &egrave; possibile effettuare l\'invio, &egrave; necessario ripetere la procedura<br /><br />',
'squel' => 'Modello: &nbsp;',
'statut_interne' => 'Interna',
'statut_publique' => 'Pubblica',
'suivi_envois' => 'Gestione degli invii',
'supprime_contact' => 'Eliminare definitivamente questo contatto',
'supprime_contact_base' => 'Elimina definitivamente dal database',

'tableau_bord' => 'Pannello',
'texte' => 'testo',
'toutes' => 'Tutti gli iscritti',
'txt_abonnement' => '(Scrivi qui il testo per l\'abbonamento a questa lista, visualizzato sul sito pubblico se la lista &egrave; attiva)',
'txt_inscription' => 'Testo di iscrizione: ',

'une_inscription' => 'Un abbonato trovato',

'val_texte' => 'Testo',
'version' => 'versione',
'voir_historique' => 'Vedi lo storico degli invii',



// ====================== inscription-listes.php3 / abonnement.php3 ======================

'abo_listes' => '<b>Abbonamento alle liste di diffusione di </b>',
'acces_refuse' => 'Non &egrave; pi&ugrave; possibile accedere a questo sito',

'confirmation_format' => ' in formato ',
'confirmation_liste_unique_1' => 'Siete iscritto alla newsletter del sito',
'confirmation_liste_unique_2' =>'Avete scelto di ricevere i messaggi indirizzati alla lista seguente:',
'confirmation_listes_multiples_1' => 'Siete iscritti alle newsletter del sito ',
'confirmation_listes_multiples_2' => 'Avete scelto di ricevere i messaggi indirizzati alle liste seguenti:',

'erreur_adresse' => 'Errore: l\'indirizzo email fornito non &egrave; valido',

'infos_liste' => 'Informazioni su questa lista',


// ====================== spip-meleuse.php3 ======================

'contacts' => 'Numero di contatti',
'contacts_lot' => 'Contatti di questo flusso',
'editeur' => 'Editore: ',
'envoi_en_cours' => 'Invio in corso',
'envoi_tous' => 'Invio a destinazione di tutti gli iscritti',
'envoi_listes' => 'Invio a destinazione degli abbonati alla lista: ',
'envoi_erreur' => 'Errore: SPIP-Listes non trova il destinatario per questo messaggio',
'email_reponse' => 'Email di risposta: ',
'envoi_annule' => 'Invio annullato',
'envoi_fini' => 'Invii terminati',
'erreur_destinataire' => 'Errore destinatario: invio non effettuato',
'erreur_sans_destinataire' => 'Errore: nessun destinatario trovato per questo messaggio',
'erreur_mail' => 'Errore : invio della posta impossibile (verificare che la funzione mail() del php sia disponibile)',

'forcer_lot' => 'Inizia l\'invio del flusso seguente',

'non_courrier' => 'Non ci sono pi&ugrave; email da inviare',
'non_html' => 'Sembra che il programma di messaggistica non possa visualizzare correttamente la versione grafica (HTML) di questa email',
'sans_adresse' => 'Email non inviata -> Prego specificare un indirizzo di risposta',



// ====================== inc_import_patron.php3 ======================

'confirmer' => 'Conferma',

'lettre_info' => 'La newsletter del sito',

'patron_erreur' => 'Il modello specificato non d&agrave; alcun risultato con i parametri selezionati',



// ====================== listes.html ======================

'abonees_titre' => 'Abbonati',


// ====================== inc-presentation.php3 ======================

'listes_emails' => 'Newsletter',


// ====================== mes-options.php3 ======================


'options' => 'radio|brut|Formato:|HTML,Testo,Cancellazione|html,texte,non',

// ====================== mes-options.php3 ======================

'bonjour' => 'Salve,',

'inscription_response' => 'Siete abbonato alla newsletter del sito ',
'inscription_responses' => 'Siete abbonati alle newsletter del sito ',
'inscription_liste' => 'Avete scelto di ricevere i messaggi indirizzati alla lista seguente: ',
'inscription_listes' => 'Avete scelto di ricevere i messaggi indirizzati alle liste seguenti: ',
'inscription_format' => ' in formato '

);

?>