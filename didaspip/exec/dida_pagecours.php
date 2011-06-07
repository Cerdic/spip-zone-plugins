<FORM method="POST" name="import" ENCTYPE="multipart/form-data"  action="?exec=importdidaspip" >

<table align="center"  cellspacing=0 width="100%" border="1">
<tr>
    <td><div align="center"><b>&nbsp;&nbsp;<?php 
if ($_GET['act']=="modifcours" or $_GET['act']=="modifcourssuite") echo $lang['courstitremodif']; 
else echo $lang['courstitreimport']; 
	?>
	</b></div></td>
<tr><td>
<?php
//affichage d'un eventuel message d'erreur
if ($erreurmsg!=false){
	echo '<table align="center" cellspacing=0><tr><td>'.$erreurmsg.'</td></tr></table>';
}
?>
<table align="center" cellspacing=0 >
  <tr>
    <td align="center" border="0"><br /><?php echo $lang['consigneimport']." : (Zip &lt; ".ini_get("upload_max_filesize").")"; ?>&nbsp;&nbsp;&nbsp;&nbsp;<INPUT type=file name="fichiercours" size="40"></td>
  </tr>
  <tr>
    <td align="center" border="0"><?php 
echo $lang['nom'].'&nbsp;&nbsp;'; 
if ($_GET['act']=="modifcours" or $_GET['act']=="modifcourssuite") echo ': '.$_GET['nom']; 
else {
	echo '<input type="text" name="nom" size="12" maxlength="12"';
	if (isset($_POST["nom"])) echo ' value="'.$_POST["nom"].'"';
	echo '>'; 
}
echo '&nbsp;&nbsp;&nbsp;&nbsp;';
echo $lang['explication']."<br />";

//vérification de l'accès à la suppression des projets 
@$acces_suppression=lire_config('didaspip/accessuppr');
if (!isset($acces_suppression)) $acces_suppression="oui";

?>
	</tr></td><tr><td><div align="center">
<input type="submit" value="<?php 
if ($_GET['act']=="modifcours" or $_GET['act']=="modifcourssuite") echo $lang['modifier']; 
else echo $lang['importer']; 
		?>" OnClick="check(this)">
	</div><br /></td>
  </tr>
  </td></tr>
  <tr><td>
  <?php
  echo $lang['recommandation'];
  ?></tr><td>
</table>
</tr></td>
</table>
</form>

<br />

<table align="center"  cellspacing="0">
  <tr>
    <td>
        <div align="left">&nbsp;&nbsp;<b><?php echo $lang["courstitreliste"];?>
        </b><br /></div></td>
  </tr>
  <tr><td>&nbsp;</td></tr>
</table>
<table align="center"  cellspacing=0 width="100%" border="1">
  <tr>
    <td width="12%"><div align="center"><?php echo $lang["taille"];?></div></td>
    <td width="18%"><div align="center"><?php echo $lang["nom"];?></div></td>
    <td><div align="center"><?php echo $lang["code"];?></div></td>
    <td width="10%"><div align="center"><?php echo $lang["voir"];?></div></td>
    <td width="10%"><div align="center"><?php if ($acces_suppression=="non") echo "suppression non autoris&eacute;e"; else echo $lang["supprimer"];?></div></td>
  </tr>

<?php
//liste des cours

$listecours=array();
recuplistecours($listecours);

if (count($listecours)==0){
	//message aucun cours
	echo '<tr><td align="center" colspan="7">'.$lang["aucuncours"].'</td></tr>';
} else {
	//lister les cours
	for ($i=0;$i<count($listecours);$i++){
		if ($i%2==1) echo '<tr>';
		else echo '<tr>';
		echo '<td><div align="center">'.$listecours[$i][3].' '.$lang["ko"].'</div></td>';
		echo '<td><div align="center">'.$listecours[$i][0].'</div></td>';
    	echo '<td><div align="center">didapages<b></b>@'.$listecours[$i][1].'<b></b>@</div></td>';
		
		/*echo '<td align="center"><a href="index.php?act=modifcours&nom='.$listecours[$i][0].'"><IMG SRC="style/modifcours.gif" class="image" title="'.$lang['aidemodifiercours'].'"></a></td>';
    	echo '<td align="center">';
		if (is_file('admin/cours/'.$listecours[$i][0].'/blocage')) echo '<a href="index.php?act=bloquercours&cours='.$listecours[$i][0].'"><IMG SRC="style/bloque.gif" class="image" title="'.$lang['aidedebloquercourslibre'].'"></a>';
		else echo '<a href="index.php?act=bloquercours&cours='.$listecours[$i][0].'"><IMG SRC="style/ok.gif" class="image" title="'.$lang['aidebloquercourslibre'].'"></a>';
		echo '</td>';*/
    	echo '<td><div align="center"><a href="'._DIR_IMG.'didapages/'.$listecours[$i][0].'/index.html" target="_blank">';
		echo ' <img src="'._DIR_PLUGIN_DIDA.'/img_pack/voirlivre.gif".></a></div></td>';
		if ($acces_suppression=="non") echo "<td> &nbsp;</td></tr>";
		else echo '<td><div align="center"><a href="?exec=supprdidaspip&cours='.$listecours[$i][0].'"><img src="'._DIR_PLUGIN_DIDA.'/img_pack/annuler.gif".></a></div></td></tr>';
	}
}
?>
</table>