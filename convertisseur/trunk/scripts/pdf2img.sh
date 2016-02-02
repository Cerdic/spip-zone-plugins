#!/bin/sh

# Conversion d'un PDF en images

nom=${1%.*}
nom=${nom##*/}
nom=${nom/\//}

shave="$3"
[[ "$shave" == "" ]] && shave=0

echo "conversion shave $3 de $1 dans $2"

# infos sur le pdf : identify -format "%[pdf:HiResBoundingBox]" testpdf.pdf
# Multipages ? "$2/$nom-%04d.jpg"
# rogner : -shave 40x40
# redimensionner : -resize 1500

# une seule page
convert -verbose -colorspace RGB -resize 1500 -interlace none -density 300 -background white -alpha remove -quality 80 -shave "$shave" "$1" "$2/$nom.jpg"

