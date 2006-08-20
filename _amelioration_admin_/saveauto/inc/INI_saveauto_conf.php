<?php

$base = "portail";

//optionnel : envoi de la sauvegarde par mail si vous mettez l'email du destinataire
$destinataire_save = "test1.iufm@laposte.net";

//d�termine � partir de combien de jours une archive est consid�r�e comme obsol�te et automatiquement supprim�e du serveur
//mettez -1 pour d�sactiver cette fonctionnalit�
$jours_obso = 120;

//true=affiche un message de succ�s dans l'interface
$ecrire_succes = true;

//true=demande la compression des sauvegardes
$gz = true;

//true=sauvegarde la structure des tables
$structure = true;

//true=sauvegarde les donnees des tables
$donnees = true;

//optionnel : sauver que les tables avec une cha�ne dans le nom, ex : annuaire_, important, machin 
//ne mettez rien pour accepter toutes les tables
//s�parez les diff�rents noms par le symbole ;
$accepter = "";

//optionnel : si la table contient dans son nom la chaine sp�cifi�e : les donn�es sont ignor�e (pas la structure)
//s�parez les diff�rents noms par le symbole ;
$eviter = "_index;_temp;_cache";

//r�pertoire prot�g� o� stocker les fichiers (chemin � partir de la racine du SPIP, ecrire/data/ par ex)
//$rep_bases = "save_base_auto/"; 
$rep_bases = "ecrire/data/";

//en jours. XX ->faire la sauvegarde tous les XX jours, 1=quotidien
$frequence_maj = 1;

//vous pouvez mettre un pr�fixe au nom de la sauvegarde si vous le d�sirez
$prefixe_save = "save_auto_";


?>