#!/bin/bash
## spipmotion : A shell program to convert videos in flv format (flash video)
## Version 0.1
## Dependancies :
##   * ffmpeg with mp3-lame support
## Credits prealables : aozeo - http://www.aozeo.com/blog/40-linux-convertir-videos-flv-ffmpeg-telephone-portable

version="0.1"

################ LOCALISATION #####################

################### English ########################
messageaide="
spipmotion $version

Usage: ./spipmotion args
where args must include source video (--e) and output video (--i) and possibly
* the conversion mode (--m) : m will use mplayer and f will use ffmpeg only
	m is slow, but light. Accepted sizes are the sizes supported by your version of mplayer
	f is quick but heavy. Accepted sizes are the sizes which ffmpeg was compiled
	-> default is m
* the path to the binary ffmpeg (--p). default is /usr/local/bin/ffmpeg.

Example :
./spipmotion --e input-video.avi --s output-video.flv --mode m --p /usr/local/bin/ffmpeg

####################################################
##       This program need a ffmpeg version       ##
##         compiled with mp3lame support !        ##
## See http://kent1.sklunk.net/spip.php?article71 ##
####################################################
"
		formatsortie="spipmotion: outfile name must end in .flv"
		mauvaisarg="spipmotion: unrecognised argument ${1}
For a proper use try : \"./spipmotion --help\""
		pasvideoentree="spipmotion: no source video specified.
For a proper use try : \"./spipmotion --help\""
		pasvideosortie="spipmotion: no output video specified.
For a proper use try : \"./spipmotion --help\""
		extractvid="Extracting Video"
		extractson="Extracting Sound"
		assemblage="Converting to flv"
		titredejala="Output file already exists"
		textedejala="The output file you specified already exists. 
Do you want to overwrite it ? 
If no, it will be renamed."
		oui="yes"
		non="no"
		succes="Success! Video has been converted in flv format!"
		nonamr="It seems that your version of ffmpeg was not compiled with AMR support. This program needs AMR support to work properly.
For a proper use try : \"./spipmotion --help\""

	case $LANG in
################### Français ######################
		fr* )
messageaide="
spipmotion $version

Utilisation : ./spipmotion arguments
ou arguments doit inclure la vidéo source et la vidéo de sortie au format flv et éventuellement :
* le mode de conversion : m se servira de mplayer et f n'utilisera que ffmpeg
	m est lent mais donne des fichiers légers. Les formats acceptés sont ceux supportés par votre version de mplayer
	f est plus rapide mais donne des fichiers plus lourd. Les formats acceptés sont ceux avec lesquels vous avez compilé ffmpeg
	-> par défaut, m est selectionné
* le chemin vers l'executable ffmpeg (--p). /usr/local/bin/ffmpeg est la valeur par défaut.


Exemple :
./spipmotion --e video-entree.avi --s video-sortie.flv --mode m --p /opt/ffmep/bin/ffmpeg

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
		extractvid="Extraction de la vidéo"
		extractson="Extraction de la bande son"
		assemblage="Conversion en .flv"
		titredejala="Fichier de sortie existant"
		textedejala="Attention, le fichier de sortie que vous avez spécifié existe déjà. 
Voulez-vous l'écraser ? 
Si non, le fichier déjà présent sera renommé."
		oui="oui"
		non="non"
		succes="Succès ! La vidéo a bien été convertie en flv !"
		nonamr="Il apparaît que votre version de ffmpeg n'a pas été compilée avec le support d'AMR. Ce programme en a besoin pour fonctionner correctement
Pour visualiser le manuel de spipmotion, faîtes : \"./spipmotion --help\""
	esac

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
		--mode) mode="${2}"
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

case "$mode" in
  "") "$mode" = "m"
esac

case "$chemin" in
  "") chemin="/usr/local/bin/ffmpeg"
esac


################# AMR OU PAS ? ##################
amroupas=`$chemin -version 2>&1`

if echo $amroupas | grep -v amr
then
echo "###################################################"
echo $nonamr;
echo "###################################################"
exit;
fi

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


############# SI ON UTILISE FFMPEG ################
if [ "$mode" = "f" ]
then
echo "
#######################
## "$assemblage" ##
#######################
"
echo "ah$chemin$mode"
"$chemin" -i $entree -acodec amr_nb -s 176x144 -ar 8000 -b 80 -vcodec h263 -ac 1 -y $sortie
else

################# SINON, MPLAYER ##################


echo "
#######################
## "$extracvid"  ##
#######################
"
mencoder $entree -nosound -ovc lavc -lavcopts vcodec=mpeg4 -vop expand=176:144,scale=176:-2 -o sortie.avi -ofps 12
echo "
#######################
## "$extractson"  ##
#######################
"
mplayer -vo null -ao pcm -af resample=8000,volume=+4db:sc $entree
echo "
#######################
## "$assemblage" ##
#######################
"
"$chemin" -i sortie.avi -i audiodump.wav -b 48 -ac 1 -ab 12 -map 0.0 -map 1.0 -y $sortie
rm sortie.avi
rm audiodump.wav
fi

echo "$succes"
echo "http://www.aozeo.com/"
