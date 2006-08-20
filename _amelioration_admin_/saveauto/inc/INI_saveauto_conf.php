<?php

$base = "portail";

//optionnel : envoi de la sauvegarde par mail si vous mettez l'email du destinataire
$destinataire_save = "test1.iufm@laposte.net";

//détermine à partir de combien de jours une archive est considérée comme obsolète et automatiquement supprimée du serveur
//mettez -1 pour désactiver cette fonctionnalité
$jours_obso = 120;

//true=affiche un message de succès dans l'interface
$ecrire_succes = true;

//true=demande la compression des sauvegardes
$gz = true;

//true=sauvegarde la structure des tables
$structure = true;

//true=sauvegarde les donnees des tables
$donnees = true;

//optionnel : sauver que les tables avec une chaîne dans le nom, ex : annuaire_, important, machin 
//ne mettez rien pour accepter toutes les tables
//séparez les différents noms par le symbole ;
$accepter = "";

//optionnel : si la table contient dans son nom la chaine spécifiée : les données sont ignorée (pas la structure)
//séparez les différents noms par le symbole ;
$eviter = "_index;_temp;_cache";

//répertoire protégé où stocker les fichiers (chemin à partir de la racine du SPIP, ecrire/data/ par ex)
//$rep_bases = "save_base_auto/"; 
$rep_bases = "ecrire/data/";

//en jours. XX ->faire la sauvegarde tous les XX jours, 1=quotidien
$frequence_maj = 1;

//vous pouvez mettre un préfixe au nom de la sauvegarde si vous le désirez
$prefixe_save = "save_auto_";


?>