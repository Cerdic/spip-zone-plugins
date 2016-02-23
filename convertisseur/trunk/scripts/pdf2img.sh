#!/bin/sh

# Conversion d'un PDF en images
pdf="$1"
nom=${pdf%.*}
nom=${nom##*/}
nom=${nom/\//}

dest=${2-0} # 0 par défaut
shave="${3-0}"

regex="\.jpg$"

[[ "$dest" =~ $regex ]] && fichier_dest="$dest"
[[ "$dest" =~ $regex ]] || fichier_dest="$dest/$nom.jpg"

echo "conversion (shave $shave) de $pdf dans $fichier_dest"

# infos sur le pdf : identify -format "%[pdf:HiResBoundingBox]" testpdf.pdf
# Multipages ? "$2/$nom-%04d.jpg"
# rogner : -shave 40x40
# redimensionner : -resize 1500

# une seule page
convert -verbose -colorspace RGB -resize 1500 -interlace none -density 300 -background white -alpha remove -quality 80 -shave "$shave" "$pdf" "$fichier_dest"
