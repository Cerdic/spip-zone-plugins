# Videos

Nouvelle version / Fork du plug-in Vidéo pour SPIP, compatible avec la nouvelle version de l'API V3 de YouTube.

Documentation du plug-in : 
<https://contrib.spip.net/Plugin-Video-s>



## Utilisation :

Actuellement, le plug-in est testé et fonctionnel avec les services suivants :

**YouTube :**

*   L'API V3 YouTube nécessite d'obtenir une API KEY qui doit être renseignée dans la configuration du plug-in.
    
    [Console Developers Google](https://console.developers.google.com/)  
    [Documentation API V3](https://developers.google.com/youtube/v3/docs/?hl=fr)

Ajout d'une vidéo :

Copier l'URL de partage depuis l'onglet "partager", exemple : `https://youtu.be/PPZ5nnWWW`

**DailyMotion :**

Ajouter l'URL de la page de la vidéo, exemple : `http://www.dailymotion.com/video/titre_de_la_video`

## Tester / Contribuer

Si vous souhaitez tester le plug-in :

*   Vous pouvez installer le plug-in dans votre site SPIP depuis GitHub :
    Allez dans gestion des plugins > Ajouter des plugins et collez l'adresse de l'archive :
    `https://github.com/mistergraphx/videos/archive/master.zip`, dans le champ du formulaire.

Si vous souhaitez apporter une modification, proposer une évolution, n'hésitez pas à soumettre une 'issue',
ou proposez directement une pull-request depuis votre fork du projet.

## Todo

@todo - Recharger le bloc d'affichage des documents  
@todo - [?] Tester si la vidéo est déjà présente dans la médiathèque ?  
@todo - Le service Vimeo n'est plus fonctionnel : <https://developer.vimeo.com/api>  
@todo - Utilisation de curl plutôt que file_get_content
@todo - Skel d'affichage de channel, playlist

## Changements

1.10.15

*	Ajout d'une fonction `url_get_contents($url)`, utilisant en priorité `curl`, puis `file_get_contents` ou `fopen`, principalement pour s'adapter suivant les serveurs et versions de php. Utilisée uniquement pour le service Youtube en test.
*	Ajout d'un test sur le retour des données json.

1.10.14 :

*   Ajoute un champ de configuration API KEY YOUTUBE, et la réception des données json
*   Les vignettes de vidéos sont "mieux" gérées en mutu ou en site seul
*   On n’utilise plus la pipeline `affiche_gauche`, mais `formulaire_fond` : le formulaire d’ajout de vidéo est accessible quand on a la présence d’ajouter document, donc plus besoin d’éditer un objet pour ajouter une vidéo : on peut ajouter des vidéos, depuis la médiathèque, ou depuis la page vue de l’objet. Le formulaire reste groupé avec l’ajout de document et donc n'apparait plus en dessous des documents.
*   Ajout de l'export de configuration du plugin avec le plugin IEconfig