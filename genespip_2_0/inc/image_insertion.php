
<?
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/
echo "test2";
$chemin=_DIR_PLUGIN_GENESPIP.'IMG/';
$split = split('/',$_FILES['image']['type']);
echo $chemin;
if (is_uploaded_file($_FILES['image']['tmp_name'])) {
   echo _T('genespip:fichier'). . $_FILES['image']['name'] . ._T('genespip:telecharger_succes').".\n";
   move_uploaded_file ( $_FILES['image']['tmp_name'],$chemin."portrait".$_POST['id_individu'].".".$split[1]);
genespip_modif_fiche_portrait(1,$_POST['id_individu'],$split[1]);
}
?>
