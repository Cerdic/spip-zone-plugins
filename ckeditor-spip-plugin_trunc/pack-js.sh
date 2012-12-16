#!/bin/sh

JS_SRC=ckeditor-plugin
JS_DST=ckeditor-plugin-packed
CSS_SRC=css
CSS_DST=css-packed
VERSION=2.4.6

if [ -f "${HOME}/.packer-root" ] ; then
	. "${HOME}/.packer-root"
else
	echo "Il faut créer un fichier ${HOME}/.packer-root contenant : PACKER_ROOTPATH=/chemin/vers/yuicompressor-${VERSION}"
	exit
fi

PACKER="java -jar ${PACKER_ROOTPATH}yuicompressor-${VERSION}/build/yuicompressor-${VERSION}.jar"

for JS_SRCFILE in $(find ${JS_SRC} -type f) ; do
	if echo $JS_SRCFILE | grep -v "/.svn" &>/dev/null ; then
		JS_DSTFILE=${JS_SRCFILE/${JS_SRC}/${JS_DST}}
		JS_DSTDIR=$(dirname ${JS_DSTFILE})
		mkdir -p "${JS_DSTDIR}"
		if echo $JS_SRCFILE | grep -E "\.js\$" &>/dev/null ; then
			if [ "$1" = "-v" ] ; then
				echo "$PACKER -o \"${JS_DSTFILE}\" \"${JS_SRCFILE}\""
			fi
			$PACKER -o "${JS_DSTFILE}" "${JS_SRCFILE}"
		else
			if [ "$1" = "-v" ] ; then
				echo "cp \"${JS_SRCFILE}\" \"${JS_DSTFILE}\""
			fi
			cp "${JS_SRCFILE}" "${JS_DSTFILE}"
		fi
		chmod a+rX "${JS_DSTFILE}"
	fi
done

for CSS_SRCFILE in ${CSS_SRC}/*.css ; do
	CSS_DSTFILE=${CSS_SRCFILE/${CSS_SRC}/${CSS_DST}}
	if [ ! -d "$CSS_DST" ] ; then
		mkdir -p "$CSS_DST"
	fi
	if [ "$1" = "-v" ] ; then
		echo "$PACKER -o \"$CSS_DSTFILE\" \"$CSS_SRCFILE\""
	fi
	$PACKER -o "$CSS_DSTFILE" "$CSS_SRCFILE"
done
