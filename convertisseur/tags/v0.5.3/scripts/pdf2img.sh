#!/bin/sh

# Conversion d'un PDF en images
pdf="$1"
nom=${pdf%.*}
nom=${nom##*/}
nom=${nom/\//}

dest=${2-0} # 0 par défaut
shave="${3-0}"

repdest=${dest}

# a t'on bien tout les outils qu'il faut sur le serveur ?
command -v convert >/dev/null 2>&1 || { echo >&2 "\nErreur. Installer imagemagick pour traiter les images. brew install imagemagick\n"; exit 1; }
command -v gs >/dev/null 2>&1 || { echo >&2 "\nErreur. Installer ghostscript pour traiter les PDF. brew install ghostscript\n"; exit 1; }
command -v pdfinfo >/dev/null 2>&1 || { echo >&2 "\nErreur. Installer poppler pour extraire le texte des PDF. brew install brew install poppler\n"; exit 1; }

# cas d'un pdf multipages avec format des pages indiqué en dest : pdf_%02d.jpg
regex="\.jpg$"
if [[ "$dest" =~ $regex ]] ; then 
	fichier_dest="$dest"
	repdest=${dest%/*}	
	format=" ($repdest)"
fi

[[ "$dest" =~ $regex ]] || fichier_dest="$dest/$nom.jpg"

#Si pas de dest
if [[ $dest == 0 ]] || [[ $dest = "" ]] ; then
	fichier_dest=${pdf/.pdf/.jpg}
fi

echo "Conversion (shave $shave) de $pdf dans $fichier_dest $format"

# infos sur le pdf : identify -format "%[pdf:HiResBoundingBox]" testpdf.pdf
# Multipages ? "$2/$nom-%04d.jpg"
# rogner : -shave 40x40
# redimensionner : -resize 1500

nb_pages=$(pdfinfo "$pdf" | grep Pages | awk '{print $2}')

# une seule page
# echo "convert -verbose -colorspace RGB -resize 1500 -interlace none -density 300 -background white -alpha remove -quality 80 -shave $shave $pdf $fichier_dest"
convert -verbose -colorspace RGB -resize 1500 -interlace none -density 300 -background white -alpha remove -quality 80 -shave "$shave" "$pdf" "$fichier_dest"
