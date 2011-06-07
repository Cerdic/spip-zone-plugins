<?php
/***************************************************************
Version fran&ccedil;aise des chaine de caract&egrave;res

Format : $lang[''] = "";
***************************************************************/

$lang['ENCODING'] = "ISO-8859-1";

$lang['versionpsd'] = "PSD 1.1" ;
$lang['titrepsd'] = "Petit Serveur pour Didapages" ;
//url de la page d'accueil
$lang['urlaccueil'] = "http://www.fruitsdusavoir.org/psd/" ;

//textes des pages d'installation
$lang['dialoguehtaccess'] = "Acc&egrave;s prot&eacute;g&eacute; ! Veuillez saisir vos codes d'acc&egrave;s.";
$lang['defcodeadmin'] = "D&eacute;finissez les codes d'acc&egrave;s &agrave; la partie administration.";
$lang['utilisateur'] = "Nom d'utilisateur";
$lang['motdepasse'] = "Mot de passe";
$lang['enregistrer'] = "Enregistrer";


//aide contextuelles
$lang['aideaccescours'] = "Acc&egrave;s au cours";
$lang['aidepasaccescours'] = "Acc&egrave;s interdit, correction en cours";
$lang['aidevoirlog'] = "Voir/Cacher le d&eacute;tail des &eacute;v&egrave;nements";
$lang['aidechangercateg'] = "Changer la cat&eacute;gorie";
$lang['aidedebloquertravail'] = "Cliquez pour autoriser l'acc&egrave;s au livre";
$lang['aidebloquertravail'] = "Cliquez pour interdire l'acc&egrave;s au livre";
$lang['aidedeconnecterapp'] = "Cliquez pour deconnecter et annuler le travail en cours";
$lang['aidesuivitravail'] = "Suivi du travail de l'apprenant";
$lang['aidesupprimertravail'] = "Supprimer cette inscription";
$lang['aidechangermessageapp'] = "Changer le message d'accueil de cet apprenant";
$lang['aidemodifierdonnees'] = "Modifier le nom, prenom ou mot de passe";
$lang['aidevoirinscriptions'] = "Voir les inscriptions de cet apprenant";
$lang['aidedebloquercourslibre'] = "Cliquez pour que le cours soit en acc&egrave;s libre";
$lang['aidebloquercourslibre'] = "Cliquez pour que le cours ne soit pas en acc&egrave;s libre";
$lang['aidemodifiercours'] = "Modifier les donn&eacute;es de ce cours";
$lang['aidesupprimercours'] = "Supprimer ce cours";
$lang['aidevoircours'] = "Voir le contenu du cours";
$lang['aidesupprimerprof'] = "Supprimer ce compte";
$lang['gestdidaspip'] = "Gestion des projets Didapages";
$lang['explication'] = "* Champ obligatoire : Le nom doit &ecirc;tre uniquement compos&eacute; de caract&egrave;res alphanum&eacute;riques (pas d'espace, ni de tirets, ni underscores)";
$lang['recommandation'] = "<b>Rappel:</b> Pour un fonctionnement en ligne, les noms des fichiers m&eacute;dias du projet ne doivent en aucun cas comporter d'accent ou caract&egrave;res sp&eacute;ciaux !";

//textes de la page d'accueil
$lang['msgaccueil'] = "Bienvenue !";
$lang['connexion'] = "Connexion";
$lang['coursacceslibre'] = "Ressources en acc&egrave;s libre";
$lang['utilisateurinconnu'] = "Nom d'utilisateur inconnu !";
$lang['motdepasseincorrect'] = "Mot de passe incorrect !";

//items du menu
$lang['accueil'] = "Accueil";
$lang['travail'] = "Inscriptions et suivi du travail";
$lang['apprenants'] = "Gestion des apprenants";
$lang['cours'] = "Gestion des cours";
$lang['configuration'] = "Configuration";
$lang['deconnexion'] = "D&eacute;connexion";

//messages d'erreur de la page apprenants
$lang['erreursaisie1'] = "Le nom d'utilisateur doit avoir de 4 &agrave; 12 caract&egrave;res.";
$lang['erreursaisie2'] = "Le mot de passe doit avoir de 4 &agrave; 12 caract&egrave;res.";
$lang['erreursaisie3'] = "Le nom doit avoir de 1 &agrave; 20 caract&egrave;res.";
$lang['erreursaisie4'] = "Le pr&eacute;nom doit avoir de 1 &agrave; 20 caract&egrave;res.";
$lang['erreursaisie5'] = "Le nom de groupe doit avoir de 1 &agrave; 12 caract&egrave;res.";
$lang['erreursaisie6'] = "Seuls les caract&egrave;res a-z, A-Z, 0-9 et - sont autoris&eacute;s pour les noms d'utilisateur, de groupe, de cours et les mots de passe !";
$lang['erreursaisie7'] = "Ce nom d'utilisateur existe d&eacute;j&agrave;. Veuillez en choisir un autre.";

//messages d'erreur de la page cours
$lang['erreurimport1'] = "Vous ne pouvez pas importer un fichier d'une taille sup&eacute;rieure &agrave; ".ini_get("upload_max_filesize")." !";
$lang['erreurimport2'] = "Le fichier n'a &eacute;t&eacute; que partiellement re&ccedil;u, l'import a &eacute;chou&eacute; !";
$lang['erreurimport3'] = "Aucun fichier n'a &eacute;t&eacute; re&ccedil;u !";
$lang['erreurimport4'] = "Erreur cot&eacute; serveur : pas de dossier temporaire.";
$lang['erreurimport5'] = "Vous ne pouvez importer que les fichiers Zip correspondant aux cours cr&eacute;&eacute;s par Didapages (export MSP).";
$lang['erreurimport6'] = "Le nom du cours doit avoir de 1 &agrave; 12 caract&egrave;res (sans espace).";
$lang['erreurimport7'] = "Le titre du cours doit avoir de 1 &agrave; 100 caract&egrave;res.";
$lang['erreurimport8'] = "Ce nom de cours existe d&eacute;j&agrave;.";
$lang['erreurimport9'] = "Un probl&egrave;me est survenu lors de la d&eacute;compression du fichier Zip.";
$lang['erreurimport10'] = "Le cours a bien &eacute;t&eacute; import&eacute;, mais il contenait des fichiers, inhabituels pour un cours Didapages, qui ont &eacute;t&eacute; supprim&eacute;s par s&eacute;curit&eacute; : ";

//messages d'erreur de la page travail
$lang['erreurtravail1'] = "Erreur inconnue li&eacute;e au groupe ou &agrave; l'apprenant s&eacute;lectionn&eacute; !";
$lang['erreurtravail2'] = "Erreur inconnue li&eacute;e au cours s&eacute;lectionn&eacute; !";
$lang['erreurtravail3'] = "Cet apprenant est d&eacute;j&agrave; inscrit &agrave; ce cours !";
$lang['erreurtravail4'] = "Un ou plusieurs apprenants de ce groupe &eacute;taient d&eacute;j&agrave; inscrits &agrave; ce cours !";

//textes de la page apprenants
$lang['apptitrepage'] = "GESTION DES APPRENANTS";
$lang['apptitreinscript'] = "Inscrire un nouvel apprenant";
$lang['apptitreinscriptliste'] = "Inscrire une liste d'apprenants";
$lang['apptitreliste'] = "Liste des apprenants";
$lang['apptitremodifier'] = "Modifier les donn&eacute;es de";
$lang['appsupselection'] = "Supprimer la s&eacute;lection";
$lang['nom'] = "Nom";
$lang['prenom'] = "Pr&eacute;nom";
$lang['motdepasse'] = "Mot de passe";
$lang['modifier'] = "Modifier";
$lang['annuler'] = "Annuler";
$lang['groupe'] = "Groupe";
$lang['inscrire'] = "Inscrire";
$lang['tous'] = "Tous";
$lang['travail'] = "Travail";
$lang['aucunapprenant'] = "Aucun apprenant n'est inscrit !";
$lang['accueilapp'] = "Message";
$lang['messagedaccueil'] =" Message d'accueil";
$lang['fichiercsv'] = "Fichier CSV &agrave; importer : ";
$lang['consignecsv'] = " Format du fichier CSV : 
<br/>\"nomUtilisateur1\",\"motDePasse1\",\"nom1\",\"prenom1\"
<br/>\"nomUtilisateur2\",\"motDePasse2\",\"nom2\",\"prenom2\"<br />";
$lang['erreurimportcsv'] = "La liste doit être un fichier CSV !";
$lang['erreurlisteloginexiste'] = "Ce nom d'utilisateur existe d&eacute;j&agrave; !";
$lang['erreurlisteligne'] = "Erreur ligne";
$lang['erreurlisteformat'] = "le format est incorrect !";



//textes de la page cours
$lang['courstitrepage'] = "GESTION DES PROJETS";
$lang['courstitreimport'] = "Importer un nouveau projet";
$lang['courstitremodif'] = "Modifier un cours existant";
$lang['courstitreliste'] = "Liste des projets en ligne";
$lang['taille'] = "Taille";
$lang['titre'] = "Titre";
$lang['code'] = "code &agrave; ins&eacute;rer dans l'article";
$lang['acces'] = "Acc&egrave;s";
$lang['voir'] = "Voir";
$lang['suppr'] = "Suppr.";
$lang['importer'] = "Importer";
$lang['categorie'] = "Cat&eacute;gorie";
$lang['consigneimport'] = "Projet Didapages &agrave; importer";
$lang['ko'] = "Ko";
$lang['aucuncours'] = "Aucun cours n'est install&eacute; !";
$lang['attentionmodifcours'] = "<b>--- Attention ! ---</b><br />Le travail de vos apprenants, ce sont les modifications qu'ils apportent &agrave; un livre par rapport &agrave; son &eacute;tat initial. Le remplacement du cours actuel par un autre trop diff&eacute;rent peut donc provoquer de graves probl&egrave;mes lors de l'acc&egrave;s &agrave; ce cours."; 
$lang['attentionmodifcours'] .= " Si les changements apport&eacute;s sont mineurs (correction de fautes d'orthographe, position d'&eacute;lements...), il ne devrait pas y avoir de probl&egrave;me. Mais en cas de modifications importantes (insertion de page, de nouveaux &eacute;l&eacute;ments...), vous devez imp&eacute;rativement supprimer les inscriptions sur ce cours.";
$lang['attentionmodifcours'] .= '<br /><br />';

//textes de la page travail
$lang['travailtitreinscrire'] = "Inscrire un apprenant ou un groupe &agrave; un cours";
$lang['travailtitremodif'] = "Modifier la cat&eacute;gorie d'une inscription &agrave; un cours";
$lang['aucours'] = "Au cours";
$lang['voirinscriptions'] = "Voir les inscriptions de";
$lang['pour'] = "pour";
$lang['touslesapprenants'] = "Tous les apprenants";
$lang['touslescours'] = "Tous les cours";
$lang['aucuneinscription'] = "Aucune inscription n'a &eacute;t&eacute; trouv&eacute;e !";
$lang['log'] = "Log";
$lang["nomducours"] = "Nom du cours";
$lang["tempstravail"] = "Temps de travail";
$lang["score"] = "Score";
$lang["minutes"] = "min";
$lang["travpbconnect1"] = " est connect&eacute; depuis ";
$lang["travpbconnect2"] = ". Vous devez le d&eacute;connecter pour acc&eacute;der au cours !";
$lang["deconnexionde"] = "D&eacute;connexion de ";
$lang["par"] = " par ";
$lang["cat"] = "Cat.";

//texte des fichiers log
$lang['log1'] = "Inscription au cours par";
$lang['log2'] = "Suivi du travail par";
$lang['log3'] = "Session de";
$lang['log4'] = "score de";
$lang['logpbdeconnect'] = "Fin d'une session non termin&eacute;e, d&eacute;marr&eacute;e le";
$lang['logpbenregitrement'] = "Echec d'une tentative d'enregistrement (apprenant d&eacute;connect&eacute;)";

//texte de la page de configuration
$lang['msgaccueilconf'] =" Messages d'accueils ( texte simple ou HTML)";
$lang['msgaccueilapp'] =" Message par d&eacute;faut pour les apprenants";
$lang['msgaccueilinvite'] =" Message de la page d'identification";
$lang['gereraccesprof']= " Cr&eacute;er/supprimer des acc&egrave;s enseignant/formateur";
$lang['aucunaccesprof']= " Aucun enseignant/formateur inscrit";
$lang['changerdonneesperso']= " Changer vos donn&eacute;es personnelles";
$lang['boitecateg']="Boîte &agrave; cat&eacute;gories";
$lang['ajouter']="Ajouter";
$lang['supprimer'] = "Supprimer";
$lang['exemplecateg'] ="Exemples : Cours/Maths/G&eacute;om&eacute;trie ; A faire avant vendredi ; Astronomie/Syst&egrave;me solaire";

//textes de l'acces apprenant
$lang['voscours'] = "Vos cours";
$lang['correctionencours'] = "Correction en cours...";
?>
