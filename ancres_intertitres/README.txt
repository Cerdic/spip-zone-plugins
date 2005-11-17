Nom : ancres_intertitres
Version : 0.1
Version spip minimale requise : 1.9alpha

Objectif : ajouter des ancres html calculées en fonction du texte de l'intertitre. Fournir une balise pour afficher la "table des matieres" d'un article (contenu de #TEXTE)

Fonctionnement : 

- les intertitres ( notes {{{intertitre}}} ) seront transformes en <h3 class="spip"><a name="intertitre"></a>intertitre</h3>
- le contenu de l'attribut name est calcule selon une methode approchant celle des urls propres de spip. Aussi un intertitre {{{ceci est un intertitre spécial}}} sera transforme en <h3 class="spip"><a name="ceci-est-un-intertitre-special"></a>ceci est un intertitre spécial</h3>
- chaque intertitre est memorise avec son ancre correspondante pour etre reproduite dans la balise #TABLE_MATIERE

Installation :

- copier le repertoire ancres_intertitres dans le repertoire plugins de votre site spip.
- creer un fichier ecrire/mes_options.php3 s'il n'existe pas
- ajouter dans ce fichier les lignes :

---rien avant les lignes ci-dessous---
<?php

$plugins[] = 'ancres_intertitres';
$plugins[] = 'ancres'; //plugin d'exemple de la distribution de spip

?>
---rien apres la ligne ci-dessus---

Utilisation : les intertitres seront calcules automatiquement. Pour afficher la table des matieres, ajouter la balise #TABLE_MATIERE dans votre squelette.

Note : si vous voulez afficher la table des matieres avant le #TEXTE, procedez de la facon suivante.

[(#TEXTE|?{' ', ''})]
#TABLE_MATIERE
#TEXTE

ChangeLog :

2005-11-17 : version initiale 0.1

Todo :

- Trouver un moyen de remplir la table des matieres avant l'appel a la balise #TEXTE
