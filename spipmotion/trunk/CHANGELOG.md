#SPIPmotion - Changelog

## Versions 1.7.x

### Version 1.7.0

* Utiliser ecrire_config même pour notre table des métas différente (spipmotion_metas)
* En cas d'erreur d'encodage, il se peut que ce soit parce que FFmpeg ne sait détecter le format d'une piste, si on sait le format des différentes pistes, essayer à nouveau en passant les codecs utilisés pour l'entrée
* Amélioration du code HTML des tableaux dans le privé
* Si pas de codec audio pour le webm, utiliser libvorbis
* Les scripts d'encodage et de récupération de vignettes sont exécutables
* Compatible 3.2.x

## Versions 1.6.x

### Version 1.6.3

* Il arrive que mediainfo ne sache détecter les pistes vidéo et son. Ne pas empêcher ffprobe de les détecter et de les mettre à jour.

### Version 1.6.2

* Ajout du m4a comme extension d'encodage audio possible (aac)
* Prise en compte de la présence de libfdk_aac

### Version 1.6.1

* Lorsque l'on utilise le profile baseline pour la vidéo, forcer le format de pixels à yuv420p

### Version 1.6.0

* Version d'origine à la création de ce fichier de changelog