#dir=../../..
dir=/home/marcimat/www/spip

# -w n'affiche pas les differrences d'espacement ou de ligne vide
diff -w exec/mots_edit.php $dir/ecrire/exec/mots_edit.php > diff/exec_mots_edit.diff
diff -w exec/mots_tous.php $dir/ecrire/exec/mots_tous.php > diff/exec_mots_tous.diff
diff -w exec/mots_type.php $dir/ecrire/exec/mots_type.php > diff/exec_mots_type.diff
diff -w inc/editer_mot.php $dir/ecrire/inc/editer_mot.php > diff/inc_editer_mot.diff
diff -w action/instituer_groupe_mots.php $dir/ecrire/action/instituer_groupe_mots.php > diff/action_instituer_groupe_mots.diff
