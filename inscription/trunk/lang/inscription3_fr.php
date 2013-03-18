<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/inscription/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'a_confirmer' => 'À confirmer',
	'activation_compte' => 'Activez votre compte',
	'admin' => 'Admin',
	'afficher_tous' => 'Afficher tous les utilisateurs',
	'ajouter_adherent' => 'Créer un nouvel utilisateur',
	'aucun' => 'Aucun',
	'aucun_resultat_recherche' => 'Il n\'y a aucun résultat pour votre recherche.',
	'autre' => 'Autre',

	// B
	'bouton_suppression_compte' => 'Supprimer votre compte',

	// C
	'cfg_description' => 'Paramétrer les champs supplémentaires pour les utilisateurs et d\'autres fonctionnalités.',
	'cfg_titre' => 'Inscription 3',
	'choix_affordance_email' => 'Email',
	'choix_affordance_libre' => 'Choix libre (ci-dessous)',
	'choix_affordance_login' => 'Login (par défaut dans SPIP)',
	'choix_affordance_login_email' => 'Login et email',
	'choix_feminin' => 'Madame',
	'choix_inscription_texte_aucun' => 'Aucun',
	'choix_inscription_texte_libre' => 'Choix libre (ci-dessous)',
	'choix_inscription_texte_origine' => 'Celui d\'origine (défaut de SPIP)',
	'choix_masculin' => 'Monsieur',
	'compte_active' => 'Votre compte sur @nom_site@',
	'configuration' => 'Configuration des champs des utilisateurs',
	'contacts_personnels' => 'Contacts personnels',

	// D
	'delete_user_select' => 'Supprimer le(s) utilisateur(s) sélectionné(s)',
	'descriptif_page_inscription' => 'Inscription au site @site@',
	'descriptif_plugin' => 'Vous trouverez ici tous les utilisateurs inscrits sur le site. Leur statut est indiqué par la couleur de leur icone.<br /><br />Vous pouvez configurer des champs supplémentaires, proposés en option aux visiteurs au moment de l\'inscription.',
	'divers' => 'Divers',

	// E
	'email_bonjour' => 'Bonjour @nom@,',
	'erreur_chaine_valide' => 'Veuillez insérer une chaine de caractères',
	'erreur_chainelettre' => '(composée uniquement de lettres)',
	'erreur_chainenombre' => '(composée de lettres et/ou de chiffres)',
	'erreur_champ_obligatoire' => 'Ce champ est obligatoire',
	'erreur_compte_attente' => 'Votre compte est en attente de validation',
	'erreur_compte_attente_mail' => 'Cette adresse est associée à un compte non validé',
	'erreur_effacement_auto_impossible' => 'Le compte ne peut être effacé automatiquement, contactez-nous.',
	'erreur_info_statut' => 'L\'utilisateur @nom@ a pour statut "@statut@".',
	'erreur_inscription_desactivee' => 'L\'inscription est désactivée sur ce site',
	'erreur_login_deja_utilise' => 'Le login est déja utilisé, veuillez en choisir un autre.',
	'erreur_naissance_futur' => 'Êtes vous vraiment né dans le futur?',
	'erreur_naissance_moins_cinq' => 'Avez vous vraiment moins de 5 ans?',
	'erreur_naissance_plus_110' => 'Avez vous vraiment plus de 110 ans?',
	'erreur_numero_valide' => 'Veuillez insérer un numéro valide',
	'erreur_numero_valide_international' => 'Ce numéro doit être sous la forme internationale (ex: +32 475 123 456)',
	'erreur_reglement_obligatoire' => 'Vous devez accepter le règlement',
	'erreur_signature_deja_utilise' => 'Cette valeur est déjà utilisé par un autre utilisateur.',
	'erreur_suppression_compte_connecte' => 'Vous devez être connecté au site pour supprimer votre compte.',
	'erreur_suppression_compte_non_auteur' => 'Vous n\'avez pas les droits suffisant pour supprimer ce compte.',
	'erreur_suppression_compte_webmestre' => 'Le compte à supprimer est celui d\'un webmestre, vous ne pouvez le supprimer.',
	'erreur_suppression_comptes_impossible' => 'La suppression de compte a échoué',
	'exp_statut_rel' => 'Champ différent du statut de SPIP, celui-ci sert pour le controle interne d\'une institution',
	'explication_affordance_form' => 'Champ affiché sur les formulaires d\'identification (#LOGIN_PUBLIC)',
	'explication_auto_login' => 'Si le mot de passe est rempli dans le formulaire, l\'utilisateur sera automatiquement connecté à la validation du formulaire de création de compte.',
	'explication_creation' => 'Enregistre dans la base de donnée la date de création du compte.',
	'explication_info_internes' => 'Options qui seront stockées dans la base de données mais ne seront pas affichées dans le formulaire des nouveaux utilisateurs',
	'explication_inscription_texte' => 'Texte d\'introduction visible au début du formulaire d\'inscription',
	'explication_modifier_logo_auteur' => 'Pour modifier le logo (à gauche), double clickez dessus.',
	'explication_password_complexite' => 'Ajoute une vérification javascript du mot de passe lorsque les utilisateurs sont invités à le choisir ou le modifier.',
	'explication_reglement_article' => 'L\'article "<a href="@url@" class="spip_in">@titre@</a>" est utilisé comme article de règlement.',
	'explication_statut' => 'Choisissez le statut que vous voulez attribuer aux nouveaux utilisateurs',
	'explication_suppression_compte' => 'Valider la suppression de votre compte (@nom@ - @email@)',
	'explication_valider_compte' => 'Les comptes doivent être validés par un administrateur avant de pouvoir être utilisés.',

	// F
	'fiche_adherent' => 'Fiche utilisateur',
	'fiche_expl' => 'Le champ sera visible sur la fiche de l\'utilisateur (page auteur de l\'espace public)',
	'fiche_mod_expl' => 'Le champ sera modifiable depuis l\'interface publique par l\'utilisateur si on utilise un formulaire d\'édition de profil (#FORMULAIRE_EDITER_AUTEUR) ou par le plugin "crayons"',
	'form_expl' => 'Le champ sera affiché sur le formulaire #FORMULAIRE_INSCRIPTION',
	'form_oblig_expl' => 'Rendre la saisie obligatoire dans les formulaires d\'inscription et de modification',
	'form_retour_aconfirmer' => 'Votre compte a correctement été créé. Il est en attente de validation d\'un administrateur.',
	'form_retour_inscription_pass' => 'Votre compte a correctement été créé. Vous pouvez l\'utiliser immédiatement pour vous connecter au site en utilisant votre email d\'inscription comme identifiant.',
	'form_retour_inscription_pass_logue' => 'Votre compte a correctement été créé. Vous êtes actuellement correctement identifié.',
	'formulaire_inscription' => 'Formulaire d\'inscription',
	'formulaire_inscription_ok' => 'Votre inscription a bien été prise en compte. Vous allez recevoir par courrier électronique vos identifiants de connexion.',
	'formulaire_remplir_obligatoires' => 'Veuillez remplir les champs obligatoires',
	'formulaire_remplir_validation' => 'Veuillez vérifier les champs qui ne sont pas validés.',

	// I
	'icone_afficher_utilisateurs' => 'Afficher les utilisateurs',
	'info_aconfirmer' => 'à confirmer',
	'info_cextras_desc' => 'Champs extras déjà présents en base.',
	'info_connection' => 'Informations de connexion',
	'info_defaut_desc' => 'Possibilités de paramétrage',
	'info_pass_faible' => 'Faible',
	'info_pass_fort' => 'Fort',
	'info_pass_moyen' => 'Moyen',
	'info_pass_tres_faible' => 'Très Faible',
	'info_pass_tres_fort' => 'Très fort',
	'info_perso_desc' => 'Informations personnelles qui seront demandées aux nouveaux utilisateurs du site',
	'infos_personnelles' => 'Informations personnelles',

	// L
	'label_adresse' => 'Adresse',
	'label_affordance_form' => 'Paramétrage des formulaires d\'identification',
	'label_affordance_form_libre' => 'Texte en cas de choix libre',
	'label_auto_login' => 'Identification automatique',
	'label_bio' => 'Biographie',
	'label_civilite' => 'Civilité',
	'label_code_postal' => 'Code postal',
	'label_commentaire' => 'Commentaire',
	'label_creation' => 'Date de création de la fiche',
	'label_email' => 'E-Mail',
	'label_fax' => 'Fax',
	'label_fonction' => 'Fonction',
	'label_inscription_depuis' => 'Membre depuis le @date@.',
	'label_inscription_texte' => 'Introduction du formulaire',
	'label_inscription_texte_libre' => 'Texte en cas de choix libre',
	'label_login' => 'Nom d\'utilisateur (login)',
	'label_logo_auteur' => 'Logo',
	'label_mobile' => 'Mobile',
	'label_naissance' => 'Date de naissance',
	'label_nom' => 'Signature',
	'label_nom_famille' => 'Nom de famille',
	'label_nom_site' => 'Nom du site',
	'label_pass' => 'Mot de passe',
	'label_password_complexite' => 'Vérifier la complexité du mot de passe',
	'label_password_retaper' => 'Confirmez le mot de passe',
	'label_pays' => 'Pays',
	'label_pays_defaut' => 'Pays par défaut',
	'label_pgp' => 'Clé PGP',
	'label_prenom' => 'Prénom',
	'label_profession' => 'Profession',
	'label_public_reglement' => 'J\'ai lu et j\'accepte le règlement',
	'label_public_reglement_url' => 'J\'ai lu et j\'accepte le <a href="@url@" class="spip_in reglement">règlement</a>',
	'label_public_reglement_url_mediabox' => 'J\'ai lu et j\'accepte le <a href="@url@" @js@ class="spip_in reglement">règlement</a>',
	'label_reglement' => 'Règlement à valider',
	'label_reglement_article' => 'Article original du site correspondant au règlement',
	'label_reglement_explication' => 'Afficher une case règlement et forcer sa validation',
	'label_secteur' => 'Secteur',
	'label_sexe' => 'Civilité',
	'label_societe' => 'Société / Association',
	'label_statut' => 'Statut',
	'label_surnom' => 'Surnom',
	'label_telephone' => 'Téléphone',
	'label_travail' => 'professionnel',
	'label_url_site' => 'Url du site',
	'label_url_societe' => 'Site société',
	'label_validation_numero_international' => 'Forcer les numéros de téléphone à être sous la forme internationale',
	'label_valider_comptes' => 'Valider les comptes',
	'label_ville' => 'Ville',
	'label_website' => 'Site Internet',
	'legend_oubli_pass' => 'Pas de mot de passe / mot de passe oublié',
	'legende' => 'Légende',
	'legende_affordance_form' => 'Formulaire d\'identification',
	'legende_cextras' => 'Champs extras',
	'legende_formulaire_inscription' => 'Formulaire d\'inscription',
	'legende_info_defaut' => 'Informations obligatoires',
	'legende_info_internes' => 'Informations internes',
	'legende_info_perso' => 'Informations personnelles',
	'legende_password' => 'Mot de passe',
	'legende_reglement' => 'Règlement du site',
	'legende_validation' => 'Validations',
	'lisez_mail' => 'Un email vient d\'être envoyé à l\'adresse fournie. Pour activer votre compte veuillez suivre les instructions.',
	'liste_adherents' => 'Voir la liste des utilisateurs',
	'liste_comptes_titre' => 'Liste des utilisateurs',

	// M
	'menu_info_inscription3' => 'Lien vers une page d\'inscription du site',
	'menu_nom_inscription3' => 'Lien vers l\'inscription',
	'menu_titre_lien_inscription' => 'Inscription',
	'message_auteur_inscription_confirmer_contenu_admin' => '@nom@ a demandé à avoir un compte sur le site. Vous pouvez valider ou invalider cette requète.',
	'message_auteur_inscription_confirmer_contenu_user' => 'votre compte est actuellement en attente de validation d\'un administrateur du site.',
	'message_auteur_inscription_confirmer_titre_admin' => '[@nom_site_spip@] Demande de validation du compte de @nom@',
	'message_auteur_inscription_confirmer_titre_user' => '[@nom_site_spip@] Votre compte est en attente de validation',
	'message_auteur_inscription_pass' => 'votre compte a correctement été créé. Vous avez choisi vous-même votre mot de passe.',
	'message_auteur_inscription_pass_rappel_login' => 'Rappel : votre login est "@login@".',
	'message_auteur_inscription_pass_titre_user' => '[@nom_site_spip@] Votre compte a été créé',
	'message_auteur_inscription_verifier_contenu_plusieurs' => 'Plusieurs comptes sont en attente :',
	'message_auteur_inscription_verifier_contenu_un' => 'Un compte est en attente :',
	'message_auteur_inscription_verifier_titre_plusieurs' => '[@nom_site_spip@] Plusieurs comptes utilisateurs à valider',
	'message_auteur_inscription_verifier_titre_un' => '[@nom_site_spip@] Un compte utilisateur à valider',
	'message_auteur_invalide_contenu_admin' => '@admin@ a refusé le compte de @nom@.',
	'message_auteur_invalide_contenu_user' => 'un administrateur a refusé la validation de votre compte.',
	'message_auteur_invalide_titre_admin' => '[@nom_site_spip@] Compte de @nom@ refusé',
	'message_auteur_invalide_titre_user' => '[@nom_site_spip@] Votre compte a été refusé',
	'message_auteur_valide_contenu_admin' => '@admin@ a validé le compte de @nom@.',
	'message_auteur_valide_titre_admin' => '[@nom_site_spip@] Compte de @nom@ validé',
	'message_auto' => '(ceci est un message automatique)',
	'message_compte_efface' => 'Votre compte a été effacé.',
	'message_modif_email_ok' => 'Votre adresse email a correctement été modifiée.',
	'message_users_supprimes_nb' => '@nb@ utilisateur(s) supprimé(s).',
	'message_users_supprimes_un' => 'Un utilisateur a été supprimé.',
	'modif_pass_titre' => 'Modifier votre mot de passe',
	'mot_passe_reste_identique' => 'Votre mot de passe n\'a pas été modifié.',

	// N
	'no_user_selected' => 'Vous n\'avez sélectionné aucun utilisateur.',
	'nom_explication' => 'votre nom ou votre pseudo',
	'non_renseigne' => 'non renseigné.',
	'non_renseignee' => 'non renseignée.',

	// O
	'option_choisissez' => 'Choisissez',

	// P
	'par_defaut' => 'Ce champ est obligatoire',
	'pass_indiquez_cidessous' => 'Indiquez ci-dessous l\'adresse email sous laquelle vous
			vous êtes précédemment enregistré. Vous
			recevrez un email vous indiquant la marche à suivre pour
			modifier votre accès.',
	'pass_oubli_mot' => 'Changement de votre mot de passe',
	'pass_rappel_email' => 'Rappel : votre adresse email est "@email@".',
	'pass_rappel_login_email' => 'Rappel : votre login est "@login@" et votre adresse email est "@email@".',
	'pass_recevoir_mail' => 'Vous allez recevoir un email vous indiquant comment modifier votre accès au site.',
	'password_obligatoire' => 'Le mot de passe est obligatoire.',
	'probleme_email' => 'Problème de mail : l\'email d\'activation ne peut pas être envoyé.',
	'profil_droits_insuffisants' => 'Desolé vous n\'avez pas le droit de modifier cet auteur<br/>',
	'profil_modifie_ok' => 'Les modifications de votre profil ont bien été prises en compte.',

	// R
	'raccourcis' => 'Raccourcis',
	'recherche_case' => 'Dans le champ :',
	'recherche_utilisateurs' => 'Rechercher un utilisateur',
	'recherche_valeur' => 'Rechercher :',

	// S
	'statut_rel' => 'Statut interne',
	'statuts_actifs' => 'Les couleurs des icones correspondent aux statuts suivants : ',
	'supprimer_adherent' => 'Supprimer utilisateurs',

	// T
	'table_expl' => 'Le champ sera affiché sur la liste des utilisateurs (espace privé)',
	'texte_email_confirmation' => 'Votre compte est actif, vous pouvez dès maintenant vous connecter au site en utilisant vos identifiants personnels.

Votre login est : @login@
et vous venez choisir votre mot de passe.

Merci de votre confiance

L\'équipe de @nom_site@
@url_site@',
	'texte_email_inscription' => 'vous êtes sur le point de confirmer votre inscription au site @nom_site@.

Cliquer le lien ci-dessous pour activer votre compte et choisir votre mot de passe.

@link_activation@


Merci de votre confiance.

L\'équipe de @nom_site@.
@url_site@

Si vous n\'avez pas demandé cette inscription ou si vous ne voulez plus faire partie de notre site, cliquez le lien ci-dessous.
@link_suppresion@


',
	'thead_fiche' => 'Fiche',
	'thead_fiche_mod' => 'Modifiable',
	'thead_form' => 'Formulaire',
	'thead_obligatoire' => 'Obligatoire',
	'thead_table' => 'Table',
	'titre_modifier_auteur' => 'Modifier le profil de cet utilisateur',
	'titre_modifier_auteur_nom' => 'Modifier le profil de @nom@',
	'titre_modifier_profil' => 'Modifier votre profil',
	'titre_supprimer_compte' => 'Supprimer votre compte',

	// V
	'vos_articles_auteur' => 'Vos articles',
	'vos_contacts_personnels' => 'Vos contacts personnels',
	'votre_adresse' => 'Votre adresse personnelle',
	'votre_login_mail' => 'Votre login ou email :',
	'votre_mail' => 'Votre email :',
	'votre_nom_complet' => 'Votre nom complet'
);

?>
