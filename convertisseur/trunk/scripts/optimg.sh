#!/bin/sh

# Optimisation d'images trop lourdes ou trop grandes avec imagemagick

nom=${1##*/}

# resize ?
if (( ${2} > 0 )) ; then 
	l=" avec une largeur de $2 pixels"
	r="-resize $2x "
fi

# creer un fichier converti dans dest ?
if (( ${#3} > 0 )) ; then 
	d=" dans $3"
	dest="$3/$nom"
	echo "Optimisation de $1${l}${d}"
	echo ">> convert ${r}-strip -interlace Plane -gaussian-blur 0.05 -quality 80% $1 $dest"
	convert ${r}-strip -interlace Plane -gaussian-blur 0.05 -quality 80% "$1" "$dest"
	
	# pas de dest, on ecrase le fichier input avec sa version optimisée
	else
	echo "Optimisation de $1${l}"
	mogrify ${r}-strip -interlace Plane -gaussian-blur 0.05 -quality 80% "$1"
	
fi

