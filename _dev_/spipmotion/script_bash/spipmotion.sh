#!/bin/bash
## spipmotion : A shell program to convert videos in flv format (flash video)
## Version 0.1
## Dependancies :
##   * ffmpeg with mp3-lame support
## Credits prealables : aozeo - http://www.aozeo.com/blog/40-linux-convertir-videos-flv-ffmpeg-telephone-portable

version="0.1"

################ LOCALISATION #####################
messageaide="
spipmotion $version

Utilisation : ./spipmotion arguments
ou arguments doit inclure la vidéo source et la vidéo de sortie au format flv et éventuellement :
* la taille de la video ex : --size 320x240
* le bitrate de la video ex : --bitrate 448
* le nombre d'image par seconde ex : --fps 15
* le bitrate audio ex : --audiobitrate 64
* la fréquence d'echantillonnage sonnore ex : --bitrate 22050
* le chemin vers l'executable ffmpeg (--p). /usr/local/bin/ffmpeg est la valeur par défaut.


Exemple :
./spipmotion --e video-entree.avi --s video-sortie.flv --size 320x240 --bitrate 448 --fps 15 --audiobitrate 64 --audiofreq 22050 --p /usr/local/bin/ffmpeg

#####################################################
##  Ce programme recquiert une version de ffmpeg   ##
##        compilée avec le support mp3-lame        ##
## Voir http://kent1.sklunk.net/spip.php?article71 ##
#####################################################
"
		formatsortie="spipmotion : le fichier de sortie doit se terminer par flv"
		mauvaisarg="spipmotion : argument ${1} non reconnu
Pour visualiser le manuel de spipmotion, faîtes : \"./spipmotion --help\""
		pasvideoentree="spipmotion : aucune vidéo source n'a été spécifiée
Pour visualiser le manuel de spipmotion, faîtes : \"./spipmotion --help\""
		pasvideosortie="spipmotion : aucune vidéo de sortie n'a été spécifiée
Pour visualiser le manuel de spipmotion, faîtes : \"./spipmotion --help\""
		assemblage="Conversion en .flv"
		titredejala="Fichier de sortie existant"
		textedejala="Attention, le fichier de sortie que vous avez spécifié existe déjà. 
Voulez-vous l'écraser ? 
Si non, le fichier déjà présent sera renommé."
		oui="oui"
		non="non"
		succes="Succès ! La vidéo a bien été convertie en flv !"

#################################################

################ ARGUMENTS ######################

while test -n "${1}"; do
	case "${1}" in
		--help) echo "$messageaide";
		exit 0;;
		--e) entree="${2}"
		shift;;
		--s) sortie="${2}"
			case "$sortie" in
			*".flv");;
			*) echo "$formatsortie";
			exit 1;;
			esac
		shift;;
		--size) size="${2}"
		shift;;
		--bitrate) bitrate="${2}"
		shift;;
		--audiobitrate) audiobitrate="${2}"
		shift;;
		--audiofreq) audiofreq="${2}"
		shift;;
		--fps) fps="${2}"
		shift;;
		--p) chemin="${2}"
		shift;;
		*) echo "$mauvaisarg"; exit 1;;
	esac
	shift
done

########## TRAITEMENT DES ARGUMENTS ###############

case "$entree" in
  "") echo "$pasvideoentree"; exit 1;;
esac

case "$sortie" in
  "") "$sortie" = "$entree.flv"
esac

case "$size" in
  "") size="320x240"
esac

case "$bitrate" in
  "") bitrate="448"
esac

case "$audiobitrate" in
  "") audiobitrate="64"
esac

case "$audiofreq" in
  "") audiofreq="22050"
esac

case "$fps" in
  "") fps="15"
esac

case "$chemin" in
  "") chemin="/usr/local/bin/ffmpeg"
esac

########### SI LA SORTIE EXISTE DÉJÀ #############

if [ -f $sortie ];
	then
	PS3='> '
	echo "###############################################################"
	echo "$textedejala";
	LISTE=("[y] $oui" "[n] $non")  # liste de choix disponibles
	select CHOIX in "${LISTE[@]}" ; do
	    case $REPLY in
	        1|y)
	        rm $sortie
	        break
	        ;;
	        2|n)
	        mv $sortie $sortie-backup
	        break
	        ;;
	    esac
	done
fi

############# ON UTILISE FFMPEG ################

echo "ah$chemin"
"$chemin" -i $entree -acodec mp3 -f flv -s $size -b $bitrate.kb -ab $audiobitrate -ar $audiofreq -r $fps -y $sortie

echo "$succes"