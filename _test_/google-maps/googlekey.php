<?php
//Enregistre la nouvelle cl�
if(!empty($_POST['key']))
{
	$fp = fopen('../../tmp/googlekey.txt','w');
	fwrite($fp,$_POST['key']);
	fclose($fp);
}
//Retourne l'ancienne cl�
$filename='../../tmp/googlekey.txt';
if($handle=@fopen($filename,'r'))
{	echo @fread($handle, filesize($filename));
	@fclose($handle);
}
?>