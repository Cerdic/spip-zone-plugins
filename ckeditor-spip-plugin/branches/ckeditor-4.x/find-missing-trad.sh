#!/bin/sh
SRC_LANG="$1"
CMP_LANG="$2"

if [ -z "$SRC_LANG" ] ; then
	SRC_LANG=fr
fi

TESTFILE=test_lang_$SRC_LANG.php

echo "<?php " > $TESTFILE
echo "	error_reporting(E_ERROR | E_PARSE);" >> $TESTFILE
echo "	\$GLOBALS['idx_lang'] = 'spiplang' ; " >> $TESTFILE
echo "	define('_ECRIRE_INC_VERSION',1) ; " >> $TESTFILE
echo "	include 'lang/ckeditor_$SRC_LANG.php' ;" >> $TESTFILE
if [ -z "$CMP_LANG" ] ; then
	echo "	\$GLOBALS['langstrs'] = array(" >> $TESTFILE
	# grep -R "ckeditor:" . | grep -v "ckeditor:tool_" | perl -ne "/([:'\"])ckeditor:(\w+)\1/ && print \"\t\t'\$CMP_LANG',\n\";" | sort | uniq >> $TESTFILE
	grep -R --exclude=*.diff --exclude=*~ "ckeditor:" . | grep -v "ckeditor:tool_" | perl -ne "@a = /([:'\"])ckeditor:(\w+)\1/g;foreach(@a){print \$_;print \"\n\";}"|grep -vE "[:'\"]"|perl -ne "chop;print \"\t\t'\" ; print ; print \"',\n\";" >> $TESTFILE
	echo "	) ; " >> $TESTFILE
	echo "	\$GLOBALS['langmsgs'] = array();" >> $TESTFILE
else
	echo "	\$GLOBALS['idx_lang'] = 'langmsgs' ;" >> $TESTFILE
	echo "	include 'lang/ckeditor_$CMP_LANG.php' ;" >> $TESTFILE
	echo "	\$GLOBALS['langstrs'] = array_keys(\$GLOBALS['langmsgs']);" >> $TESTFILE
fi
echo "	include 'lang.inc.php' ;" >> $TESTFILE
echo "?>" >> $TESTFILE

php $TESTFILE > lang_$SRC_LANG.php
#rm $TESTFILE
