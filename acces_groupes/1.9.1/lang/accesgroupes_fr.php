<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
$GLOBALS[$GLOBALS['idx_lang']] = array(
// patch pour maj des tables
'erreur_patch0.7_etape1' => 'Erreur lors du renommage des tables : le passage &agrave; la version 1.0 n\'est pas fait !',
'erreur_patch0.7_etape2' => 'Erreur lors de la cr&eacute;ation du champ prive_public dans la table xxx_jpk_groupes_acces : le passage &agrave; la version 1.0 n\'est pas fait !',
'erreur_patch0.7_etape3' => 'Erreur lors de la conversion des champs "id_groupe" en "id_grpacces" : le passage &agrave; la version 1.0 n\'est pas fait !',
'OK_patch1.0' => 'La modification des tables pour le passage &agrave; la version 1.0 s\'est d&eacute;roul&eacute;e sans probl&egrave;me',
'erreur_patch0.7_1.0' => 'Erreur lors de la modification du champ dde_acces de la table accesgroupes_auteurs pour passer en version 1.0',  
'lancer_patch' => 'Lancer la modification des tables',
'titre_patch' => 'Mise &agrave; jour des tables utilis&eacute;es par le plugin acces_groupes',
'info_patch' => 'Ce script permet de modifier les tables de la base de donn&eacute;es de la contrib 
						 		 "<a href="http://contrib.spip.net/Creer-des-groupes-limiter-l-acces">acc&egrave;s restreints par groupes</a>"
								 afin de les rendre compatibles avec sa version en plugin (acces_groupes v1.0) .
						 		 <br><strong>Si vous n\'aviez pas install&eacute; cette contrib sur votre spip 1.8, il n\'est pas n&eacute;cessaire : passez votre chemin !</strong>
								 <br>Versions support&eacute;es : contrib version 0.6* et version 0.7
								 <br><br>Afin de pouvoir r&eacute;aliser cette mise &agrave; jour (et pour s&eacute;curit&eacute;)
								 vous devez renseigner les param&egrave;tres de connexion &agrave; la base de donn&eacute;es
								 <br>Pour le param&egrave;tre "<strong>Pr&eacute;fixe des tables SPIP</strong>", si vous avez une install "standard", laissez "<strong>spip</strong>"
								 sinon il s\'agit de la valeur de la variable "<strong>$table_prefix</strong>" renseign&eacute;e dans le fichier <strong>/ecrire/mes_options.php</strong>
								 (si ce fichier n\'existe pas, alors c\'est que votre install est "standard").',
'titre_formulaire_patch' => 'Param&egrave;tres de la base de donn&eacute;es',
'serveur_SQL' => 'Adresse serveur MySQL',
'base_SQL' => 'Base de donn&eacute du SPIP', 
'user_SQL' => 'Utilisateur serveur MySQL',
'pass_SQL' => 'Mot de passe serveur MySQL',
'prefixe_tables' => 'Préfixe des tables SPIP',

'module_titre'=>'Acc&egrave;s restreints par groupes',
'module_info'=>'Ce plugin permet de g&eacute;rer les restrictions d\'acc&egrave;s aux rubriques.<br />
								Si un groupe restreint l\'acc&egrave;s pour une rubrique elle sera invisible dans l\'espace public 
								et inaccessible dans l\'espace priv&eacute; pour les utilisateurs n\'appartenant pas &agrave; ce groupe.',
'select' => 'S&eacute;lection d\'un groupe',
'select_vide' => '-- liste des groupes --',
'nom' => 'NOM',
'description' => 'Description',
'actif' => 'activ&eacute;',
'inactif' => 'd&eacute;sactiv&eacute;',
'creer' => 'Cr&eacute;er',
'modifier' => 'Modifier',
'changer_proprio_groupe' => 'Propri&eacute;taire',
'tous_les_admins' => 'admins g&eacute;n&eacute;raux',
'changer_proprio_help' => 'Modifie le propri&eacute;pri&eacute;taire du groupe', 
'erreur_modif_proprio' => 'Erreur lors de la modification du proprio du groupe : ',
'erreur_modif_proprio_acces' => 'Erreur lors de la modification du proprio des acces aux rubriques du groupe : ',
'erreur_modif_proprio_auteurs' => 'Erreur lors de la modification du proprio des auteurs du groupe : ',
'membres' => 'Membres du groupe',
'choisir' => 'Vous devez choisir un groupe',
'ajouter' => 'Ajouter',
'accepter' => 'Accepter dans le groupe',
'rubriques_restreintes' => 'Rubriques &agrave; acc&egrave;s restreint',
'rubriques_autorisees_info' => 'Rubriques &agrave; acc&egrave;s restreint par ce groupe: ',
'prive_public' => 'priv&eacute; + public',
'prive_seul' =>  'priv&eacute;',
'public_seul' => 'public',
'prive_public_autres' => 'Rubriques &agrave; acc&egrave;s restreint par d\'autres groupes : ',
'prive_public_tous' => 'priv&eacute;+public/priv&eacute;/public',

'autoriser' => 'Limiter l\'acc&egrave;s &agrave; la rubrique',
'autoriser_info' => 'Rubriques restreintes accessibles par ce groupe... <br />(les sous rubriques sont automatiquement restreintes de fa&ccedil;on identique)',
'acces_double' => 'Le groupe a d&eacute;j&agrave; acc&egrave;s &agrave; cette rubrique',
'acces_rubrique_add_par' =>'La rubrique ajout&eacute;e est &eacute;galement accessible par le ou les groupes :',
'acces_rubrique_modif_par' =>'La rubrique modifi&eacute;e est &eacute;galement accessible par le ou les groupes :',
'non_connecte' => 'Votre profil ne vous donne pas acc&egrave;s &agrave; cette partie du site',
'bloque_rubrique' => 'Vous ne faites pas partie d\'un groupe ayant acc&egrave;s &agrave; cette rubrique',
'bloque_article' => 'Vous ne faites pas partie d\'un groupe ayant acc&egrave;s aux articles de cette rubrique',
'bloque_breve' => 'Vous ne faites pas partie d\'un groupe ayant acc&egrave;s aux breves de cette rubrique',
'groupes' => 'Groupes',
'titre_groupes' => 'Groupes et rubriques &agrave; acc&egrave;s restreint<br />',
'titre_page_groupe' => 'Gestion du groupe',
'organisation' => 'Organisation des groupes',
'groupes_rubriques' => 'Rubriques g&eacute;r&eacute;es par groupes',
'auteurs' => 'Auteurs',
'ss_groupes' => 'Sous-groupes',
'statuts' => 'Statuts',
'du_groupe' => ' inclus dans ce groupe',
'ajouter_auteur' => 'Ajouter un auteur ',
'ajouter_ss_groupe' => 'Ajouter un sous-groupe ',
'ajouter_statut' => 'Ajouter un statut ',
'auteurs_groupe' => 'Auteurs  appartenants &agrave; ce groupe',
'auteurs_en_attente' => 'Auteurs en attente d\'une demande d\'acc&egrave;s',
'ss_groupes_groupe' => 'Sous-groupes appartenants &agrave; ce groupe<br />( tous les utilisateurs du sous-groupe font partie du groupe )',
'statut_groupe' => 'Statuts  appartenants &agrave; ce groupe <br />( tous les utilisateurs ayant le statut font partie du groupe )',
'retirer_groupe' => 'Retirer le sous-groupe',
'retirer_statut' => 'Retirer ce statut',
'erreur_inclusion_recurrente' => '<span style="color: #f00;">Erreur : inclusion r&eacute;curente ! Un groupe ne peut &ecirc;tre inclu dans un de ses sous-groupes.</span>',
'arborescence_groupes' => 'Arborescence des sous-groupes',
'creation_table' => 'cr&eacute;ation de la table',
'installation' => 'Installation du gestionnaire de groupes et acc&egrave; restreints : <br />',
'install_ok' => 'aucune erreur',
'install_pas_ok' => 'il y a des erreurs !',
'duplicata_nom' => 'ce nom de groupe existe d&eacute;ja : veuillez en utiliser un autre !',
'portee_acces' => 'Limiter pour :',
'help_portee_acces' => 'les types de limitations disponibles d&eacute;pendent<br />des restrictions appliqu&eacute;s aux rubriques parents', 
'public' => 'partie publique',
'prive' => 'partie priv&eacute;e',
'les_2' => 'priv&eacute;e + publique',
'suppression' => 'Suppression',
'supprimer' => 'Supprimer',
'supprimer_tout' => 'D&eacute;truire le groupe',
'help_supprimer' => 'D&eacute;truire le groupe aura pour cons&eacute;quence de supprimer tous les utilisateurs/sous-groupes/statuts qu\'il contient ainsi que de remettre toutes les rubriques qu\'il contr&ocirc;le accessibles!',
'erreur_supr_auteurs' => 'Erreur lors lors de la suppression des utilisateurs du groupe ',
'erreur_supr_rubriques' => 'Erreur lors de la suppression des rubriques contr&ocirc;l&eacute;es par le groupe ',
'erreur_supr_ssgrpes' => 'Erreur lors de la suppression des sous-groupes inclus dans le groupe ',
'erreur_supr_groupe' => 'Erreur lors de la suppression du groupe ',
'supr_groupe_ok' => 'Suppression correcte du groupe ',
'autoriser_demandes' => 'Autoriser les inscriptions:',
'help_inscriptions' =>'(formulaire de demande d\'inscription si acc&egrave;s bloqu&eacute)',
'etat_groupe' => 'Etat du groupe',
'oui' => 'oui',
'non' => 'non',
'envoyer' => 'envoyer',
'demande_acces' => 'Vous pouvez demander &agrave; &ecirc;tre int&eacute;gr&eacute;(e) dans un des groupes autoris&eacute;: ',
'choix_groupe' => 'Choix du groupe :',
'proprio' => 'propri&eacute;taire',
'admins' => 'administrateurs',
'help_demande_acces' => 'Laissez un message pour expliquer votre demande :',
'msg_demande_acces1' => 'l\'auteur ',
'msg_demande_acces2' => ' souhaite &ecirc;tre int&eacute;gr&eacute; dans le groupe ',
'msg_demande_acces3' => ' pour pouvoir acc&eacute;der &agrave; la rubrique ',
'msg_demande_acces4' => '<br />Pour traiter sa demande, rendez-vous sur l\'interface de gestion des groupes ',
'msg_demande_acces5' => ' en cliquant sur ce lien.',
'msg_demande_acces6' => '(lorsque vous aurez trait&eacute; la demande de cet auteur, ce message sera automatiquement effac&eacute; et un message l\'informant de votre d&eacute;cision lui sera envoy&eacute;)',
'msg_demande_acces7' => 'Message de l\'utilisateur :',
'titre_demande_acces' => 'Demande d\'int&eacute;gration dans un groupe pour acc&eacute;der &agrave; une rubrique restreinte',
'demande_ok' => 'Votre demande à été envoy&eacute;e au(x) gestionnaire(s) du groupe',
'duplicata_demande_acces' => 'Il existe d&eacute;ja une demande d\'int&eacute;gration dans ce groupe &agrave; votre nom: merci d\'&ecirc;tre patient et d\'attendre que cette demande ait &eacute;t&eacute; trait&eacute;e...',
'erreur_creation_demande_acces' => 'Une erreur s\'est produite lors de la cr&eacute;ation de votre demande d\'acc&egrave;s',
'titre_msg_text' => 'Information',
'restriction_cree_par' => 'restriction g&eacute;r&eacute;e par',
'titre_message_retour' => 'R&eacute;ponse &agrave; une demande d\'acc&egrave;s',
'message_retour' => 'Votre demande d\'int&eacute;gration au groupe ',
'message_accepte' => ' est accept&eacute;e.',
'message_refuse' => ' est refus&eacute;e.',
'se_reconnecter' => 'se connecter sous un autre identifiant',
'retour_site' => 'Retour au site public'

);
?>