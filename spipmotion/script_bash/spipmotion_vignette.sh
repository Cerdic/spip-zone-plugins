#!/bin/bash
# SPIPMotion vignette : 
# Un programme shell de récupération de vignettes de vidéos
#
# Version 0.0.1
#
# Dependances :
#   * ffmpeg
#
# Pour l'installation des binaires nécessaires :
# -* Compilation de librairies nécessaires à FFMpeg : 
#    http://technique.arscenic.org/compilation-de-logiciel/article/compilation-et-installation-de
# -* Compilation de FFMpeg lui-même : 
#    http://technique.arscenic.org/compilation-de-logiciel/article/compiler-ffmpeg
#

VERSION="0.0.1"

################ LOCALISATION #####################
messageaide="
-----------------------------
SPIPmotion vignette v$VERSION
-----------------------------

Utilisation : ./spipmotion_vignette.sh arguments
ou arguments doit inclure le fichier source et le fichier de sortie et éventuellement :
* la taille de la video ex : --size 320x240
* le chemin vers l'executable ffmpeg (--p). /usr/local/bin/ffmpeg est la valeur par défaut.


Exemple :
./spipmotion_vignette.sh --e fichier-entree.avi --s fichier-sortie.jpg --size 320x240 --ss 9 --p /usr/local/bin/ffmpeg

#########################################################################################
##  Ce programme recquiert ffmpeg                                                      ##
##  Voir http://technique.arscenic.org/compilation-de-logiciel/article/compiler-ffmpeg ##
#########################################################################################
"
		formatsortie="SPIPmotion vignette : le fichier de sortie doit se terminer par une extension reconnue : flv flac ogg ogv oga mp3 mp4 mov m4v webm"
		mauvaisarg="SPIPmotion vignette : argument ${1} non reconnu
Pour visualiser le manuel de spipmotion, faîtes : \"./spipmotion_vignette.sh --help\""
		pasfichierentree="SPIPmotion vignette : aucun fichier source n'a été spécifié
Pour visualiser le manuel de spipmotion, faîtes : \"./spipmotion_vignette.sh --help\""
		pasfichiersortie="SPIPmotion vignette : aucun fichier de sortie n'a été spécifié
Pour visualiser le manuel de spipmotion, faîtes : \"./spipmotion_vignette.sh --help\""
		titredejala="Fichier de sortie existant"
		textedejala="Attention, le fichier de sortie que vous avez spécifié existe déjà.
Voulez-vous l'écraser ?
Si non, le fichier déjà présent sera renommé."
		oui="oui"
		non="non"
		succes="Succès ! La vignette a été exportée."

#################################################

################ ARGUMENTS ######################

FORCE="false";

if [ -z "$log" ];then
	log="/dev/null"
fi

while test -n "${1}"; do
	case "${1}" in
		--help|-h) echo "$messageaide";
		exit 0;;
		--version|-v) echo "SPIPmotion v."${VERSION}"";
		exit 0;;
		--force|-f) FORCE="true"
		shift;;
		--e) entree="${2}"
		shift;;
		--s) sortie="${2}"
			case "$sortie" in
			*".png"|*".jpg");;
			*) echo "$formatsortie";
			exit 1;;
			esac
		shift;;
		--size) size="-s ${2}"
		shift;;
		--ss) position="-ss ${2}"
		shift;;
		--params_supp) params_sup=" ${2}"
		shift;;
		--p) chemin="${2}"
		shift;;
		--log) log="${2}"
		shift;;
	esac
	shift
done

case "$chemin" in
	"") 
  	chemin=$(which ffmpeg)
esac
        
spipmotion_vignette (){

	########## TRAITEMENT DES ARGUMENTS ###############
	
	case "$entree" in
	  "") echo "$pasfichierentree"; exit 1;;
	esac
	
	case "$sortie" in
	  "") 
		sortie="$entree.jpg"
		echo $sortie
	esac
	
	########### Arguments spécifiques aux videos
	
	case "$size" in
	  "") size="-s 320x240"
	esac
	
	########### SI LA SORTIE EXISTE DÉJÀ #############
	if [ -e "$sortie" ] && [ ${FORCE} != "true" ]
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
	
	echo "SPIPmotion vignette v$VERSION

	"
	echo "$chemin -i $entree -vframes 1 $size $position $params_sup -y $sortie 2>> $log >> $log"
	$chemin -i $entree -vframes 1 $size $position $params_sup -y $sortie 2>> $log >> $log
	exit $?
}

spipmotion_vignette

exit $?