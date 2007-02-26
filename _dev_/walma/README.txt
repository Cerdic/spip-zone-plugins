/************   ---------------------  **********/
          Walma en plugin pour spip

/************   --------------------  **********/

Pour activer le plugin walma
déposer le dossier walma (décompressé) dans un dossier nommé plugins à la racine de votre spip
puis rendez vous dans la partie privé /ecrire/?exec=admin_plugin

appeler ensuite l'article contenant des images de cette manière 
http://example.com/spip.php?page=walma&id_article=xx
en créant un lien dans un squelette cela donnerait
[(#URL_PAGE{walma}|parametre_url{id_article,xx})]	


/************  En savoir plus  **********/  

le developpement du plugin walma 
walma tend toujours à permettre de facilement installer une galerie pour spip 
et de la modifier encore plus simplement pour ceux qui le souhaitent.

Le  plugin walma découpe ainsi l'ancien fichier unique en plusieurs morceaux:

00-la page walma (walma.html) va donc inclure des "noisettes", on peut la renommer en article-xx.html du n° de sa rubrique de galerie
01-le head (inc-walma_head.html)  génère
	01A -le css dynamique (walmacss.html)
	01B -le javascript (walma.js)
04-la galerie en contenu principal passe en modele (modeles/walma_modele.html)
05- texte et forum lookés walma peuvent être ajouté ou retiré facilement (inc-walma_txtforum.html)

06-le multilinguisme est maintenant assuré par les fichiers de langues que l'on peut ajouter (lang/walma_fr.php)


/************       TODO     **********/  
le developpement du plugin walma prévoit des options:

 des menus lookés walma à gauche ou à droite à insérer
 une taille ajustable de la galerie
 un squelette de rubrique
 un modèle walma à insérer
 faciliter le relookage de walma

et des plus:

 le passage en jquery
 le fondu enchainé des images
 l’exportation zippé de la galerie
 lier un mot clef walma à l’article sans toucher au code
 détecter les articles ayant un certains nombres d'images pour automatiquement les afficher en walma
 avoir les crayons actifs