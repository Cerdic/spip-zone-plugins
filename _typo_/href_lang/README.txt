Nom : href_lang
Version : 0.1
Version spip minimale requise : 1.9alpha

Objectif : ajouter un attribut hreflang sur un lien externe quand on le specifie dans le titre du lien

Fonctionnement : 

le filtre est applique en post traitement de propre. il remplace toute chaine
<a href="lien">titre|en</a> par <a href="lien" hreflang="en">titre</a>. Puis il supprime dans toute chaine
se terminant par "|en" la barre vertical et les caracteres suivants (cas de la balise #INTRODUCTION)

Installation :

- copier le repertoire href_lang dans le repertoire plugins de votre site spip.
- creer un fichier ecrire/mes_options.php3 s'il n'existe pas
- ajouter dans ce fichier les lignes :

---rien avant les lignes ci-dessous---
<?php

$plugins[] = 'href_lang';

?>
---rien apres la ligne ci-dessus---

Utilisation : 

les rédacteurs préciseront s'il le souhaite le code de langue dans le titre du lien externe.
Exemple [titre|lang->http://lien.externe.net]

TODO :

changer la notation en [titre[lang]->http://lien.externe.net] pour faire référence à la notation multi ?

ChangeLog :

2005-11-18 : version initiale 0.1