#!/bin/sh

# Site Archive (SiA)

# Script batch d'archive de site

# $LastChangedRevision: 35 $
# $LastChangedBy: cpaulus $
# $LastChangedDate: 2010-09-01 14:23:47 +0200 (Mer 01 sep 2010) $

# @author Christian Paulus <cpaulus at quesaco.org>

# Script lancé en batch par le plugin.

# Peut etre lancé directement du terminal à
# partir de la racine du site.

# options par défaut
LOGGER="/usr/bin/logger"
LOGGER_TAG="[SIA]"
WGET="/opt/local/bin/wget"
WGET_LOG_MAX_SIZE=200 # en Ko

# LOG_DIR et LOG_SUFFIX de SPIP
LOG_DIR="tmp/"
LOG_SUFFIX=".log"

IP_HOST=""

# journaux de wget. Ne devraient pas arriver ici.
# défini par sia_fonctions.php
SIA_LOGS_DIR="tmp/sia/"

syslog_log()
{
	#/usr/bin/syslog -s -l $2 "[SIA] $1"
	$LOGGER -t "$LOGGER_TAG" "$1"
	
	# Ecriture dans le journal (si existe) en double
	if [ "$SIA_LOG_FILE" != "" ] && [ -f "$SIA_LOG_FILE" ]
	then
		NOW=`date '+%b %d %X'`
		echo "$NOW $IP_HOST (pid ${PPID}) [SIA] $1" >> "$SIA_LOG_FILE"
	fi
}

notice_log()
{
	syslog_log "$1" 5
}

error_log()
{
	syslog_log "$1" 3
}

help_usage()
{
	echo "help_usage: `basename $0` [-n nom_archive] [-p chemin_destination] [-u url_cible]
	
		-n nom du fichier
		-p chemin du répertoire de destination
		-t type de cible (unique, multi ou  texte)
		-u url du document cible utilisé par wget
	
	Si appelé sans argument, recherche dans le chemin courant :
		tmp/<site_archive_name>*.lock
		
	Si fichier trouvé, charge du todo correspondant
	les paramètres de l'archivage demandé.
	
	Exemple de contenu d'un fichier tmp/site_archive-unique.todo
	
		sia: LastChangedRevision: 35 
		siajobname: quesaco-Archive-de-site-Website-archiver-u
		destdir: img/zip/
		tmpdir: tmp/
		lockfile: tmp/quesaco-Archive-de-site-Website-archiver_u.lock
		logdir: tmp/
		logsuf: .log
		sialogsdir: tmp/sia/
		type: texte
		level: 1
		targeturl: http://127.0.0.1:8011/spip.php?page=site_archive-texte&id_rubrique=2
		iphost: 127.0.0.1
		wget: /opt/local/bin/wget 

	"
	
	# Cet exemple peut différer si options choisies (strict, etc.)
}

# Vérifier position en racine SPIP
if [ -d "ecrire" ] && [ -d "squelettes-dist" ] && [ -d "tmp" ]
then 
	syslog_log "`basename $0` starting"
else
	error_log "`basename $0` doit être lancé à la racine du site SPIP"
	exit -1
fi

NOTICE="Calling shell site_archive"

if [ ! -n "$1" ]
then
	# via at ou batch, les params sont dans un todo
	NOTICE="$NOTICE whithout arguments"
else
	# en direct, les paramètres sont disponibles
	while getopts :n:p:t:u:h argument
	do
		case $argument in
		
			h) help_usage; exit 64 ;;
			
			n) target_name="$OPTARG";; ##
			:) help_usage; exit 64 ;;
			
			p) path_dest="$OPTARG";;
			:) help_usage; exit 64 ;;
			
			t) type_archive="$OPTARG";;
			:) help_usage; exit 64 ;;
			
			u) url_objet=$OPTARG;;
			:) help_usage; exit 64 ;;
	
		esac
	done
	NOTICE="$NOTICE with arguments"
fi

# tente de lire un todo dans le tmp/
for ii in `ls tmp/*todo`
do
	SIA=""
	
	# le todo est composé d'une clé:valeur par ligne
	while read key val
	do
		case $key in
			"sia:") 	SIA="$val";;
			"siajobname:")	SIA_JOB_NAME="$val";;
			"destdir:")	SIA_DEST_FOLDER="$val";;
			"tmpdir:")	SIA_SPIP_TEMP="$val";;
			"lockfile:")	SIA_LOCK_FILE="$val";;
			"logdir:")	LOG_DIR="$val";;
			"logsuf:")	LOG_SUFFIX="$val";;
			"sialogsdir:") SIA_LOGS_DIR="$val";;
			"type:")	SIA_TYPE="$val";;
			"level:")	SIA_LEVEL="$val";;
			"targeturl:")	SIA_TARGET_URL="$val";;
			"iphost:")	IP_HOST="$val";;
			"wget:")	WGET="$val";;
			"randomwait:")	RANDOM_WAIT="$val";;
			"strict:")	STRICT_MODE="$val";;
			"useragent:")	USER_AGENT="$val";;
		esac
	done < "$ii"

	# si fichier valide, quitter la boucle
	if [ "$SIA" != "" ]
	then
		# le fichier lock est vide = tache disponible
		# Prendre la main
		if [ -f "$SIA_LOCK_FILE" ] && [ ! -s "$SIA_LOCK_FILE" ]
		then
			echo "$PPID" > "$SIA_LOCK_FILE"
			SIA_TODO_FILE="$ii"
			NOTICE="$NOTICE using $ii"
			break 2
		fi
		# sinon, passer au suivant
	fi
done

# les surcharges passées via la console
# (options du script)
if [ ! -z "$target_name" ]
then
	SIA_JOB_NAME="$target_name"
fi
if [ ! -z "$path_dest" ]
then
	SIA_DEST_FOLDER="$path_dest"
fi
if [ ! -z "$type_archive" ]
then
	SIA_TYPE="$type_archive"
fi
if [ ! -z "$url_objet" ]
then
	SIA_TARGET_URL="$url_objet"
fi

# le log géré par SPIP
SIA_LOG_FILE="${LOG_DIR}sia${LOG_SUFFIX}"

# le log de wget
if [ -d "${SIA_LOGS_DIR}" ]
then
	WGET_LOG_FILE="${SIA_JOB_NAME}${LOG_SUFFIX}"
else
	WGET_LOG_FILE="/dev/null"
fi

# a partir d'ici, notice_log et error_log sont utilisables
notice_log "$NOTICE"
notice_log "sia log file: $SIA_LOG_FILE"
notice_log "wget log file: $WGET_LOG_FILE"

#######
WGET_OPTIONS=""

# Pas de hiérarchie de répertoires
WGET_OPTIONS="$WGET_OPTIONS --no-directories "

# -nH --no-host-directories
# Ne pas créer le répertoire destination
WGET_OPTIONS="$WGET_OPTIONS --no-host-directories "

# Cacher le premier composant
WGET_OPTIONS="$WGET_OPTIONS --cut-dirs=1 "

# Ne pas remonter dans les répertoires parents
WGET_OPTIONS="$WGET_OPTIONS --no-parent "

# En fin de traitement, convertir les liens internes 
# pour une consultation locale.
WGET_OPTIONS="$WGET_OPTIONS --convert-links "

# Renommer les fichiers, compatibilité Windows.
# (les '?' sont sources à problèmes)
WGET_OPTIONS="$WGET_OPTIONS --restrict-file-names=windows "

# Ajouter les messages au journal
WGET_OPTIONS="$WGET_OPTIONS -a $WGET_LOG_FILE "

# Options complémentaires
if [ ! -z "$USER_AGENT" ]
then
	WGET_OPTIONS="$WGET_OPTIONS --user-agent=\"$USER_AGENT\" "
fi
if [ ! -z "$RANDOM_WAIT" ]
then
	WGET_OPTIONS="$WGET_OPTIONS --random-wait "
fi

WGET_RECUR_OPTIONS=""
# -r: recursif
WGET_RECUR_OPTIONS="-r "

# -l 1: profondeur récursif, 1 pour ramener les images, a minima
if [ ! -z "$SIA_LEVEL" ] && [ "$SIA_LEVEL" -gt "1" ] && [ "$SIA_LEVEL" -le "5" ]
then
	WGET_RECUR_OPTIONS="$WGET_RECUR_OPTIONS -l $SIA_LEVEL "
else
	WGET_RECUR_OPTIONS="$WGET_RECUR_OPTIONS -l 1 "
fi

# -E --adjust-extension --html-extension: forcer l'extension html si besoin
WGET_RECUR_OPTIONS="$WGET_RECUR_OPTIONS -E "

# -k: convert-links: convertir les liens relatifs
WGET_RECUR_OPTIONS="$WGET_RECUR_OPTIONS -k "

if [ "$SIA_TYPE" = "multi" ]
then
	WGET_OPTIONS="$WGET_RECUR_OPTIONS $WGET_OPTIONS"
else
	if [ "$SIA_TYPE" = "unique" ]
	then
		WGET_OPTIONS="$WGET_RECUR_OPTIONS $WGET_OPTIONS"
	else
		# la version texte est un seul fichier, sans lien
		WGET_OPTIONS="$WGET_OPTIONS"
	fi
fi

notice_log "wget options: $WGET_RECUR_OPTIONS $WGET_OPTIONS"

# Créer si besoin le réceptacle des logs wget
# (a dû être créé par le plugin)
if [ ! -d "${SIA_TEMP_FOLDER}${SIA_LOGS_DIR}" ]
then
	notice_log "Create wget logs dir ${SIA_TEMP_FOLDER}${SIA_LOGS_DIR}"
	mkdir "${SIA_TEMP_FOLDER}${SIA_LOGS_DIR}"
	
	if [ "$?" -ne "0"]
	then
		error_log "${SIA_TEMP_FOLDER}${SIA_LOGS_DIR} not writable"
	else
		SIA_LOGS_DIR="${SIA_TEMP_FOLDER}${SIA_LOGS_DIR}"
	fi
fi

# Le download a lieu dans un dossier qui porte
# le nom de l'archive dans le temporaire.
SIA_TEMP_FOLDER="${SIA_SPIP_TEMP}${SIA_JOB_NAME}"

notice_log "sia temp folder: $SIA_TEMP_FOLDER"

# Créer le répertoire temporaire de download
if [ ! -e "$SIA_TEMP_FOLDER" ]
then

	mkdir "$SIA_TEMP_FOLDER"

	if [ "$?" -ne 0 ]
	then
		# n'a pas réussi à le créer. Abandon.
		error_log "${SIA_TEMP_FOLDER} not writable"
		exit $?
	else
		notice_log "Runing wget in ${SIA_TEMP_FOLDER}/"
		
		# wget dans un sous shell, au bon endroit
		( cd "${SIA_TEMP_FOLDER}/"; $WGET $WGET_OPTIONS $SIA_TARGET_URL )
		
		ERR="$?"
		
		if [ "$ERR" -ne "0" ]
		then
			case $ERR in
			1) MSERR="No problems occurred.";;
			2) MSERR="Parse error---for instance, when parsing command-line options, the .wgetrc or .netrc...";;
			3) MSERR="File I/O error.";;
			4) MSERR="Network failure.";;
			5) MSERR="SSL verification failure.";;
			6) MSERR="Username/password authentication failure.";;
			7) MSERR="Protocol errors.";;
			8) MSERR="Server issued an error response.";;
			esac
			
			if [ "$STRICT_MODE" = "on" ] || [ "$ERR" -ne "8" ]
			then
				error_log "FATAL ERROR: wget error using: $WGET $WGET_OPTIONS $SIA_TARGET_URL"
				error_log "FATAL ERROR: wget error $ERR. $MSERR"
				exit "$ERR"
			fi
		fi

		if [ -c "${WGET_LOG_FILE}" ]
		then

			# device ?
			SIA_CURR_LOG="${WGET_LOG_FILE}"
			SIA_REAL_LOG="${WGET_LOG_FILE}"
		else

			# Le log de l'opération wget en cours
			# se trouve dans le rép en cours
			SIA_CURR_LOG="${SIA_TEMP_FOLDER}/${WGET_LOG_FILE}"
		
			# Le log wget officiel
			SIA_REAL_LOG="${SIA_LOGS_DIR}${SIA_JOB_NAME}.log"
		fi
		
		notice_log "Curr log: ${SIA_CURR_LOG}"
		notice_log "Real log: ${SIA_REAL_LOG}"
		
		# si vrai fichier (et pas device)
		if [ -f "$SIA_CURR_LOG" ]
		then

			# si log déjà présent, vérifier taille
			if [ -f "$SIA_REAL_LOG" ]
			then
				# obtenir la place occupée par le log, en ko
				size=`du -k "$SIA_REAL_LOG" | cut -f1`
				notice_log "Current log size: $size K"
				
				if [ "$size" -gt "$WGET_LOG_MAX_SIZE" ]
				then
					notice_log "Rotate log ${SIA_LOGS_DIR}${SIA_JOB_NAME}"
					for ii in 2 1
					do
						let nn=1+$ii
						f="${SIA_LOGS_DIR}${SIA_JOB_NAME}.$ii.log"
						if [ -f "${f}" ]
						then
							mv "${f}" "${SIA_LOGS_DIR}${SIA_JOB_NAME}.$nn.log"
						fi
					done
					
					# Archiver le log actuel
					cat ${SIA_REAL_LOG} > ${SIA_LOGS_DIR}${SIA_JOB_NAME}.1.log
				fi
			fi

			# créer le log officiel ou mettre à 0
			cat /dev/null > $SIA_REAL_LOG
			
			# recopier le log courant sur le log officiel
			cat $SIA_CURR_LOG >> $SIA_REAL_LOG
			
			# supprimer le log courant de l'archive
			rm $SIA_CURR_LOG
		fi
		
		# le nom du fichier principal (le premier fichier) rapatrié
		# par wget est composé de l'uri appelé en premier.
		SIA_FIRST_URI=`echo "$SIA_TARGET_URL" | sed "s/http\:\/\/[^/]*\///"`
		
		# Convertir ce nom en mode windows
		# car --restrict-file-names=window est utilisé
		# wget remplace '?' par '@'
		SIA_FIRST_INDEX=${SIA_FIRST_URI/\?/@}
		# relatif à son répertoire d'accueil + extension
		SIA_FIRST_INDEX="${SIA_TEMP_FOLDER}/${SIA_FIRST_INDEX}.html"
			
		if [ "$SIA_TYPE" = "unique" ]
		then
			# si type unique, le fichier index.html est manquant.
			# Le créer à partir du premier fichier chargé par wget
			SIA_NEW_INDEX="${SIA_TEMP_FOLDER}/index.html"
			
			notice_log "HELO4 ${SIA_FIRST_URI} $SIA_INDEX $SIA_NEW_INDEX"
			
			if [ -f "$SIA_FIRST_INDEX" ]
			then
				notice_log "Copying ${SIA_FIRST_INDEX} as ${SIA_NEW_INDEX}"
				cp "${SIA_FIRST_INDEX}" "${SIA_NEW_INDEX}"
			fi
		fi
		
		# Pour les versions html, archiver le répertoire d'accueil
		# dans le fichier compressé par zip
		if [ "$SIA_TYPE" = "multi" ] || [ "$SIA_TYPE" = "unique" ]
		then
			SIA_ZIP_TARGET="${SIA_SPIP_TEMP}${SIA_JOB_NAME}"
			
			notice_log "Compressing ${SIA_ZIP_TARGET} to ${SIA_SPIP_TEMP}${SIA_JOB_NAME}.zip"
			( cd ${SIA_SPIP_TEMP}; zip -q -r "${SIA_JOB_NAME}.zip" "${SIA_JOB_NAME}" )
			ZIPRESULT="$?"
		fi
		
		# Pour les versions texte, il n'y a qu'un seul fichier rapatrié.
		# Il est archivé au premier niveau, sans son répertoire.
		
		if [ "$SIA_TYPE" = "texte" ]
		then
			
			# le fichier à compresser porte le nom
			# de l'objet. Il sera placé près du répertoire pour le zip
			SIA_TXT_FILE="${SIA_JOB_NAME}.txt"
			
			SIA_ZIP_TARGET="${SIA_TXT_FILE}"
			
			if [ -f "$SIA_FIRST_INDEX" ]
			then
				notice_log "Copying text file to ${SIA_SPIP_TEMP}${SIA_TXT_FILE}"
				cp "$SIA_FIRST_INDEX" "${SIA_SPIP_TEMP}${SIA_TXT_FILE}"
				
				notice_log "Compressing text file to ${SIA_SPIP_TEMP}${SIA_JOB_NAME}.zip"
				( cd ${SIA_SPIP_TEMP}; zip -q "${SIA_JOB_NAME}.zip" "${SIA_TXT_FILE}" )
				ZIPRESULT="$?"
				
				# nettoyage
				rm "${SIA_SPIP_TEMP}${SIA_TXT_FILE}"
			fi
		fi
		
		if [ "$ZIPRESULT" -ne 0 ]
		then
			 error_log "Error compressing ${SIA_ZIP_TARGET}"
		else
			notice_log "Archive OK. Moving ${SIA_SPIP_TEMP}${SIA_JOB_NAME}.zip to $SIA_DEST_FOLDER"
			mv "${SIA_SPIP_TEMP}${SIA_JOB_NAME}.zip" "$SIA_DEST_FOLDER"
			
			# nettoyage
			notice_log "Removing temp files $SIA_TEMP_FOLDER $SIA_TODO_FILE $SIA_LOCK_FILE"
			rm -fR $SIA_TEMP_FOLDER
			rm $SIA_TODO_FILE $SIA_LOCK_FILE
		fi
	fi
fi
