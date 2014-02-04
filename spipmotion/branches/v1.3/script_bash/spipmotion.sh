#!/bin/bash
# SPIPMotion : 
# Un programme shell de conversion de fichiers audios et vidéos
#
# Version 0.3.4
#
# Dependances :
#   * ffmpeg avec le support de libmp3lame
#	* ffmpeg2theora
#
# Credits préalables : aozeo - http://www.aozeo.com/blog/40-linux-convertir-videos-flv-ffmpeg-telephone-portable
#
# Pour l'installation des binaires nécessaires :
# -* Compilation de librairies nécessaires à FFMpeg : 
#    http://technique.arscenic.org/compilation-de-logiciel/article/compilation-et-installation-de
# -* Compilation de FFMpeg lui-même : 
#    http://technique.arscenic.org/compilation-de-logiciel/article/compiler-ffmpeg
# -* Compilation de FFMpeg2theora :
#    http://technique.arscenic.org/compilation-de-logiciel/article/ffmpeg2theora
#

VERSION="0.3.4"

################ LOCALISATION #####################
messageaide="
SPIPmotion v$VERSION

Utilisation : ./spipmotion.sh arguments
ou arguments doit inclure le fichier source et le fichier de sortie et éventuellement :
* la taille de la video ex : --size 320x240
* le bitrate de la video ex : --bitrate 448
* le nombre d'image par seconde ex : --fps 15
* le bitrate audio ex : --audiobitrate 64
* la fréquence d'echantillonnage sonore ex : --audiofreq 22050
* le chemin vers l'executable ffmpeg (--p). /usr/local/bin/ffmpeg est la valeur par défaut.


Exemples :
./spipmotion.sh --e fichier-entree.avi --s fichier-sortie.flv --size 320x240 --bitrate 448 --fps 15 --audiobitrate 64kbs --audiofreq 22050 --p /usr/local/bin/ffmpeg
./spipmotion.sh --e fichier-entree.wav --s fichier-sortie.mp3 --audiobitrate 64 --audiofreq 22050

#########################################################################################
##  Ce programme recquiert une version de ffmpeg                                       ##
##        compilée avec le support libmp3lame                                          ##
## Voir http://technique.arscenic.org/compilation-de-logiciel/article/compiler-ffmpeg ##
#########################################################################################
"
		formatsortie="SPIPmotion : le fichier de sortie doit se terminer par une extension reconnue : flv flac ogg ogv oga mp3 mp4 mov m4v webm"
		mauvaisarg="SPIPmotion : argument ${1} non reconnu
Pour visualiser le manuel de spipmotion, faîtes : \"./spipmotion --help\""
		pasfichierentree="SPIPmotion : aucun fichier source n'a été spécifié
Pour visualiser le manuel de spipmotion, faîtes : \"./spipmotion --help\""
		pasfichiersortie="SPIPmotion : aucun fichier de sortie n'a été spécifié
Pour visualiser le manuel de spipmotion, faîtes : \"./spipmotion --help\""
		assemblage="Conversion en .flv"
		titredejala="Fichier de sortie existant"
		textedejala="Attention, le fichier de sortie que vous avez spécifié existe déjà.
Voulez-vous l'écraser ?
Si non, le fichier déjà présent sera renommé."
		oui="oui"
		non="non"
		succes="Succès ! Le fichier a bien été converti !"

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
		--version|-v) echo "SPIPmotion v. "${VERSION}"";
		exit 0;;
		--force|-f) FORCE="true"
		shift;;
		--e) entree="${2}"
		shift;;
		--s) sortie="${2}"
			case "$sortie" in
			*".mp3"|*".flac"|*".flv"|*".mp4"|*".ogg"*|".oga"|*".ogv"|*".m4v"|*".webm");;
			*) echo "$formatsortie";
			exit 1;;
			esac
		shift;;
		--pass) pass="-pass ${2}"
		shift;;
		--size) size="${2}"
		shift;;
		--bitrate) 
			bitrate_ffmpeg="-vb ${2}.k"
			bitrate_ffmpeg2theora="-V ${2}"
		shift;;
		--acodec) acodec="-acodec ${2}"
		shift;;
		--vcodec) vcodec="-vcodec ${2}"
		shift;;
		--params_supp) params_sup=" ${2}"
		shift;;
		--audiobitrate) 
			audiobitrate_quality_ffmpeg="-ab ${2}.k"
			audiobitrate_quality_ffmpeg2theora="--audiobitrate ${2}"
		shift;;
		--audioquality) 
			audiobitrate_quality_ffmpeg="-aq ${2}"
			audiobitrate_quality_ffmpeg2theora="--audioquality ${2}"
		shift;;
		--videoquality) videoquality="${2}"
		shift;;
		--audiofreq) 
			audiofreq_ffmpeg="-ar ${2}"
			audiofreq_ffmpeg2theora="-H ${2}"
		shift;;
		--fps) 
			fps_ffmpeg="-r ${2}"
			fps_ffmpeg2theora="-F ${2}"
		shift;;
		--ac) 
			ac_ffmpeg="-ac ${2}"
			ac_ffmpeg2theora="-c ${2}"
		shift;;
		--p) chemin="${2}"
		shift;;
		--fpre) fpre="-fpre ${2}"
		shift;;
		--two-pass) deux_passes="--two-pass"
		;;
		--info) info="${2}"
		shift;;
		--log) log="${2}"
		shift;;
		--encodeur) encodeur="${2}"
		shift;;
	esac
	shift
done

case "$chemin" in
	"") 
	if [ "$encodeur" == "ffmpeg2theora" ]; then
		chemin=$(which ffmpeg2theora)
  	else
  		chemin=$(which ffmpeg)
	fi
esac
        
spipmotion_encodage_ffmpeg (){

	########## TRAITEMENT DES ARGUMENTS ###############
	
	case "$entree" in
	  "") echo "$pasfichierentree"; exit 1;;
	esac
	
	case "$sortie" in
	  "") sortie="$entree.flv"
	esac
	
	########### Arguments spécifiques aux videos
	
	case "$size" in
	  "") size="320x240"
	esac
	
	case "$bitrate_ffmpeg" in
	  "") bitrate_ffmpeg="-vb 448k"
	esac
	
	case "$fps_ffmpeg" in
	  "") fps_ffmpeg="15"
	esac
	
	case "$vcodec" in
		"libx264")
		case "$params_sup" in
	  		"") params_sup="-vpre default" ;;
	  	esac
	  	shift;;
		"")
		case "$sortie" in
	  		*".flv") vcodec="-vcodec flv" ;;
	  		*".ogg"|*".ogv") vcodec="-vcodec libtheora" ;;
	  	esac
	  	shift;;
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
	
	case "$sortie" in
	  *".mp3"|*".flac"|*".ogg"|*".oga" )
	  	echo "SPIPmotion v$VERSION
	
	On encode un son
	"
		echo "nice -19 $chemin -i $entree $acodec $audiobitrate_quality_ffmpeg $audiofreq_ffmpeg $ac_ffmpeg -y $sortie 2>> $log >> $log" >> $log
	  	nice -19 "$chemin" -i $entree $acodec $audiobitrate_quality_ffmpeg $audiofreq_ffmpeg $ac_ffmpeg -y $sortie 2>> $log >> $log ;;
	  *".flv"|*".mp4"|*".ogv"|*".mov"|*".m4v"|*".webm" )
	  	echo "SPIPmotion v$VERSION
	
	On encode une video
	"
	  	echo "nice -19 $chemin -i $entree $acodec $audiobitrate_quality_ffmpeg $ac_ffmpeg $audiofreq_ffmpeg $pass $fps_ffmpeg -s $size $vcodec $bitrate_ffmpeg $params_sup $fpre -y $sortie 2>> $log >> $log" >> $log
	  	nice -19 $chemin -i $entree $acodec $audiobitrate_quality_ffmpeg $ac_ffmpeg $audiofreq_ffmpeg $pass $fps_ffmpeg -s $size $vcodec $bitrate_ffmpeg $params_sup $fpre -y $sortie  2>> $log >> $log
	esac
	exit $?
}

spipmotion_encodage_ffmpeg2theora ()
{
	echo "SPIPmotion v$VERSION
	
	On encode une video via ffmpeg2theora
	"
	echo "$chemin $entree -v $videoquality $bitrate_ffmpeg2theora --soft-target $audiobitrate_quality_ffmpeg2theora $audiofreq_ffmpeg2theora $ac_ffmpeg2theora --max_size $size $fps_ffmpeg2theora $deux_passes --optimize --nice 9 -o $sortie 2> $log >> $log" >> $log
	$chemin $entree -v $videoquality $bitrate_ffmpeg2theora --soft-target $audiobitrate_quality_ffmpeg2theora $audiofreq_ffmpeg2theora $ac_ffmpeg2theora --max_size $size $fps_ffmpeg2theora $deux_passes --nosubtitles --optimize --nice 9 -o $sortie 2> $log >> $log
	exit $?	
}

ffmpeg_infos ()
{
	[ -z "$info" ] && return 1

	if [ "$info" == "-version" ];then
		$chemin $info 2>> $log >> $log
	else
		$chemin $info 2>> /dev/null >> $log
	fi
	return $?
}

if [ ! -z "$info" ];then
	ffmpeg_infos
elif [ "$encodeur" == "ffmpeg2theora" ];then
	spipmotion_encodage_ffmpeg2theora
else
	spipmotion_encodage_ffmpeg
fi

exit $?