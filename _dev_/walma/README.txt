/************   ---------------------  **********/
          Walma en plugin pour spip

/************   --------------------  **********/

Pour activer le plugin walma
d�poser le dossier walma (d�compress�) dans un dossier nomm� plugins � la racine de votre spip
puis rendez vous dans la partie priv� /ecrire/?exec=admin_plugin

appeler ensuite l'article contenant des images de cette mani�re 
http://example.com/spip.php?page=walma&id_article=xx
en cr�ant un lien dans un squelette cela donnerait
[(#URL_PAGE{walma}|parametre_url{id_article,xx})]	


/************  En savoir plus  **********/  

le developpement du plugin walma 
walma tend toujours � permettre de facilement installer une galerie pour spip 
et de la modifier encore plus simplement pour ceux qui le souhaitent.

Le  plugin walma d�coupe ainsi l'ancien fichier unique en plusieurs morceaux:

00-la page walma (walma.html) va donc inclure des "noisettes", on peut la renommer en article-xx.html du n� de sa rubrique de galerie
01-le head (inc-walma_head.html)  g�n�re
	01A -le css dynamique (walmacss.html)
	01B -le javascript (walma.js)
04-la galerie en contenu principal passe en modele (modeles/walma_modele.html)
05- texte et forum look�s walma peuvent �tre ajout� ou retir� facilement (inc-walma_txtforum.html)

06-le multilinguisme est maintenant assur� par les fichiers de langues que l'on peut ajouter (lang/walma_fr.php)


/************       TODO     **********/  
le developpement du plugin walma pr�voit des options:

 des menus look�s walma � gauche ou � droite � ins�rer
 une taille ajustable de la galerie
 un squelette de rubrique
 un mod�le walma � ins�rer
 faciliter le relookage de walma

et des plus:

 le passage en jquery
 le fondu enchain� des images
 l�exportation zipp� de la galerie
 lier un mot clef walma � l�article sans toucher au code
 d�tecter les articles ayant un certains nombres d'images pour automatiquement les afficher en walma
 avoir les crayons actifs