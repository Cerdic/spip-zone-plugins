#!/bin/sh

# Conversion d'un PDF en images

nom=${1%.*}
nom=${nom##*/}
nom=${nom/\//}

dest="$2/$nom"

echo "conversion de $1 dans $dest"

# infos sur le pdf
# identify -format "%[pdf:HiResBoundingBox]" testpdf.pdf


[ ! -d "$dest" ] && mkdir "$dest"

cd "$dest"
convert -verbose -colorspace RGB -resize 1200 -interlace none -density 300 -background white -alpha remove -quality 80 "$1" "$nom-%04d.jpg"

