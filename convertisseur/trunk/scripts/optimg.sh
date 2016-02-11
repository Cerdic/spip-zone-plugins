#!/bin/sh

# conversion noir et blanc + contrast luminosité : convert -colorspace Gray -brightness-contrast 15x10 LMDEN_1998-10_03.jpg 15x10.jpg
# convert -brightness-contrast 10x12

# Optimisation d'images trop lourdes ou trop grandes avec imagemagick
# Fabriquer la ligne de commande dans le spip cli copmme l'autre.

nom="${1##*/}"

# resize ?
if (( "${2}" > 0 )) ; then 
	l=" avec une largeur de $2 pixels"
	r="-resize $2x "
fi

opt=""
# resize ?
if (( "${4}" > 0 )) ; then 
	opt="-gaussian-blur 0.05 -quality ${4}% "
fi
# compression ?


# creer un fichier converti dans dest ?
if (( ${#3} > 0 )) ; then 
	d=" dans $3"
	dest="$3/$nom"
	ext="${1##*.}"
		
	filename="${dest%.*}" 
	#echo "\nOptimisation ($ext) de $1${l}${d}"
	echo "convert ${r}-strip -interlace Plane ${opt}$1 $filename.jpg"
	convert ${r}-strip -interlace Plane ${opt}"$1" "$filename.jpg"
	
	# pas de dest, on ecrase le fichier input avec sa version optimisée
	else
	echo "\nOptimisation de $1${l}"
	mogrify ${r}-strip -interlace Plane ${opt}"$1"
	
fi

