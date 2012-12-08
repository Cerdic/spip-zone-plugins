#!/bin/sh
mv inc/ckeditor_constantes_inc.php inc/ckeditor_constantes_inc.php.bak
echo "<?php /* Généré automatiquement à partir des sources de ckeditor */" > inc/ckeditor_constantes_inc.php
grep -RhoE '^ *CKEDITOR\.[A-Z_]*[[:space:]]*=.*;' ../../lib/ckeditor/_source/ | perl -pe "s/CKEDITOR\.(.*?)\s*=\s*(.*?)\s*;/\tdefine('CKEDITOR_\$1', \$2);/;" | sort >> inc/ckeditor_constantes_inc.php
echo "?>" >> inc/ckeditor_constantes_inc.php
