#!/usr/local/bin/bash


# env
changelog='changelog.txt'
tinymcedir="$(pwd)/tiny_mce/"
langdir=''
tmce_verbose=0
tmce_rsyncopts='r'

# options
while getopts ":vhco:d:" opt; do
  case $opt in
    v)
			tmce_verbose=1
			tmce_rsyncopts="v$tmce_rsyncopts"
      ;;
    o)
			tmce_rsyncopts="$tmce_rsyncopts$OPTARG"
      ;;
    d)
			langdir="$OPTARG"
      ;;
    h)
      echo " \
[add_lang.sh]\n \
Ajout des fichiers de langue par récursion dans la librairie TinyMCE du plugin pour SPIP \`TinyMCE\`.\n \
L'ajout doit être fait depuis une archive de fichiers de langue téléchargée depuis le site <tinymce.com>.\n \
Vous pouvez synchroniser plusieurs langues depuis la même source.\n\n \
USAGE\n \
    ~$ sh add_lang.sh [OPTIONS] [-d new/language/directory/] \n\n \
OPTIONS\n \
    -h          voir l'aide \n \
    -v          exécution du script \`rsync\` en mode \`verbose\` \n \
    -d PATH     chemin (absolu ou relatif) vers le répertoire à synchroniser contenant les langues \n \
    -o OPTS     options ajoutées à l'exécution du script \`rsync\` (sans le préfixe '-') \n \
    -c          voir le changelog actuel : versions de TinyMCE courante et langues installées \n \
" >&2
      exit 0
      ;;
    c)
      echo "[$changelog]"
      cat $changelog
			echo
      exit 0
      ;;
    \?)
      echo "Options inconnue : -$OPTARG" >&2
      exit 0
      ;;
  esac
done

# verif env
if [ ! -e "$tinymcedir" ]; then
	echo
	echo "!! le répertoire de la librairie est introuvable [$tinymcedir] !!"
	echo "> exécutez ce script depuis la racine du répertoire de la libraire ( ~\$ cd .../tinymce/jscripts/ )"
	exit 1
fi;

# arg1 : répertoire à synchroniser
if [ -z $langdir ]; then
		echo
		echo '> quel est le répertoire concerné (contenant les fichiers de langue) ? '
		read langdir
fi;

# langdir existe ?
if [ ! -e $langdir ]; then
	echo
	echo "!! le répertoire entré n'existe pas [$langdir] !!"
	exit 1
elif [ ! -d $langdir ]; then
	echo
	echo "!! le répertoire entré n'est pas un répertoire [$langdir] !!"
	exit 1
fi;

# on s'assure qu'on a le slash final
if [ ${langdir: -1} != '/' ]; then
	langdir="$langdir/"
fi;

# synchronisation
echo
echo "> exécution de la commande : \n \
	\`rsync -$tmce_rsyncopts $langdir $tinymcedir\`"
rsync "-$tmce_rsyncopts" "$langdir" "$tinymcedir"

# fin
echo '... OK'
echo
exit 0

# Endscript