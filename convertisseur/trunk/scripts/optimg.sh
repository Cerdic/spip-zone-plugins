#!/bin/sh

# conversion noir et blanc + contrast luminosité : convert -colorspace Gray -brightness-contrast 15x10 LMDEN_1998-10_03.jpg 15x10.jpg
# convert -brightness-contrast 10x12

# Optimisation d'images trop lourdes ou trop grandes avec imagemagick

#echo ${@}
#exit

command -v convert >/dev/null 2>&1 || { echo >&2 "\nErreur. Installer imagemagick. brew install imagemagick\n"; exit 1; }

nom="${1##*/}" # basename
resize=${2-0} # 0 par défaut
compress=${4-0} # 0 par défaut
dest="${3-0}"

# resize ?
if (( $resize > 0 )) ; then 
	l=" avec une largeur de ${resize} pixels"
	r="-resize ${resize}x "
	suffixe="-${resize}x"
fi

opt=""
# compression de x % ?
if (( $compress > 0 )) ; then 
	opt=" -gaussian-blur 0.05 -quality ${compress}% "
	suffixe="$suffixe-c${compress}"
fi
# compression ?


# creer un fichier converti dans dest ?
if [[ "$dest" != "0" ]] ; then 
	d=" dans $dest"
	dest="$dest/$nom"
	ext="${1##*.}"
	
	filename="${dest%.*}" 
	#echo "\nOptimisation ($ext) de $1${l}${d}"
	echo "convert ${r}-strip -interlace Plane ${opt}$1 $filename.jpg"
	convert "${r}-strip" -interlace Plane ${opt}"$1" "$filename.jpg"
	
	# pas de dest, on ecrase le fichier input avec sa version optimisée
	else
		filename="${1%.*}"
		# echo "$suffixe"
		
		ext="${1##*.}"
		dest="${filename}${suffixe}.$ext"
		echo "\nOptimisation de $1 vers $dest"
		echo "convert ${r}-strip -interlace Plane ${opt}${1} ${dest}"
		convert "${r}-strip" -interlace Plane"${opt}" "${1}" "${dest}"
fi
