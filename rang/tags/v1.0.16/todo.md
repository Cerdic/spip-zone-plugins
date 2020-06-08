## TODO 
**1 - En priorité** 

- ~~gérer le rang des liaisons (objets liés à un autre objet)(ex. : pouvoir ordonner la liste des mots clés d'un article)~~
-> fait avec https://core.spip.net/issues/4305
- l'option de configuration 'Donner un rang aux objets au moment de leur création/publication' : pouvoir choisir de mettre la nouvelle instance au début ou à la fin du tableau.

- au moment du choix d'un objet dans la config du plugin,  affecter automatiquement un rang aux instances de l'objet existant
- corrolaire : si un objet à des "num titre", traiter.

**2 - l'ordonnancement des rubriques**
A moins d’une modif du core, ça va pas être possible car cette partie n'a pas encore été transformée en squelettes, la liste est générée au moyen d'une fonction PHP :
https://core.spip.net/projects/spip/repository/entry/spip/ecrire/inc/presenter_enfants.php#L179
https://core.spip.net/projects/spip/repository/entry/spip/ecrire/inc/presenter_enfants.php#L21
donc à moins de surcharger tout le fichier (pas conseillé), point de salut.

**3 - priorité basse**
- gérer le reclassement d'un tableau suite à la suppression d'un item (genre mot-cle) ou à la dépublication d'un objet avec statut. 
- gérer les objets éditoriaux historiques de SPIP : brèves + sites ;
- que se passe t-il si on déplace un article d’une rubrique à une autre via Plan ?
- API (?) pour les plugins (Pages uniques, Albums, Menus, Bank, etc.) ;