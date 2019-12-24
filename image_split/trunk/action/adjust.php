<?php
    
function action_adjust_dist() {
	// need to test authorisations
	if($GLOBALS['visiteur_session']['id_auteur']=='') die('You are not authorized here...');
	
	$objet = _request("objet");
	$id_objet = _request("id_objet");
	$id_document = _request("id_document");
	$sid_document = _request("id_document2");

	$res = sql_select("doc.fichier, doc.largeur, doc.extension", "spip_documents doc", "doc.id_document=".intval($id_document), "", "", 1);
	if($row = spip_fetch_array($res)){
		$fichier = $row["fichier"];
		$largeur = $row["largeur"];
		$extension = $row["extension"];
		$src = $_SERVER['DOCUMENT_ROOT']."/"._DIR_IMG.$fichier;
	}
	else die("Error: first document not found in DB");

	$res = sql_select("doc.fichier, doc.largeur, doc.extension", "spip_documents doc", "doc.id_document=".intval($sid_document), "", "", 1);
	if($row = spip_fetch_array($res)){
		$fichier = $row["fichier"];
		$largeur = $row["largeur"];
		$extension = $row["extension"];
		$ssrc = $_SERVER['DOCUMENT_ROOT']."/"._DIR_IMG.$fichier;
	}
	else die("Error: second document not found in DB");

  	// use the crop coordinates to create the destination file in the /tmp folder
	$quality = 100;
	$targ_w = _request('firstWidth');
	$targ_h = _request('firstHeight');  
	$src_x = round(_request('firstX'));
	$src_y = round(_request('firstY'));
	$src_w = $targ_w = round(_request('firstWidth'));
	$src_h = $targ_h = round(_request('firstHeight'));

	$starg_w = _request('secondWidth');
	$starg_h = _request('secondHeight');  
	$ssrc_x = round(_request('secondX'));
	$ssrc_y = round(_request('secondY'));
	$ssrc_w = $starg_w = round(_request('secondWidth'));
	$ssrc_h = $starg_h = round(_request('secondHeight'));
	
	if($extension == 'jpg') $img_r = imagecreatefromjpeg($src);
	if($extension == 'png') $img_r = imagecreatefrompng($src);
	if($extension == 'gif') $img_r = imagecreatefromgif($src);
	$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

	if($extension == 'jpg') $simg_r = imagecreatefromjpeg($ssrc);
	if($extension == 'png') $simg_r = imagecreatefrompng($ssrc);
	if($extension == 'gif') $simg_r = imagecreatefromgif($ssrc);

	$sdst_r = ImageCreateTrueColor( $starg_w, $starg_h );

	imagecopyresampled($dst_r, $img_r, 0, 0, $src_x, $src_y, $targ_w, $targ_h, $src_w, $src_h);
	imagecopyresampled($sdst_r, $simg_r, 0, 0, $ssrc_x, $ssrc_y, $starg_w, $starg_h, $ssrc_w, $ssrc_h);

	$output_filename_tmp = substr($src, 0, strrpos($src, '.')).'_croptmp.jpg';
	$output_filename = $output_base = 'crop.jpg';
	$arr = explode("/", $output_filename);
	$output_filename = $arr[sizeof($arr)-1];

	$soutput_filename_tmp = substr($ssrc, 0, strrpos($ssrc, '.')).'_crop2tmp.jpg';
	$soutput_filename = $soutput_base = 'crop.jpg';
	$sarr = explode("/", $soutput_filename);
	$soutput_filename = $sarr[sizeof($sarr)-1];

	//create cropped image
	if($extension == 'jpg') imagejpeg($dst_r, $output_filename_tmp, $quality);

	//second
	if($extension == 'jpg') imagejpeg($sdst_r, $soutput_filename_tmp, $quality);
	

	include_spip('base/abstract_sql');
	$ajouter_documents = charger_fonction('ajouter_documents', 'action');
		$file = array('tmp_name' => $output_filename_tmp, 'name' => $output_filename);
		$files = array($file);
		if($x = $ajouter_documents('new', $files, $objet, $id_objet, 'document')){
			unlink($output_filename_tmp);		
		}
		else{
			unlink($output_filename_tmp);
			die("Error: first document not saved.");
	}

	$sfile = array('tmp_name' => $soutput_filename_tmp, 'name' => $soutput_filename);
	$sfiles = array($sfile);
	if($sx = $ajouter_documents('new', $sfiles, $objet, $id_objet, 'document')){
		unlink($soutput_filename_tmp);		
		header("Location: ".$GLOBALS["meta"]["adresse_site"].'/ecrire/?exec='.$objet.'&id_article='.$id_objet."#doc".$sx[0]);
		die("<html><head><body>You will be redirected soon...<script>window.location.href='".$GLOBALS["meta"]["adresse_site"].'/ecrire/?exec='.$objet.'&id_article='.$id_objet."#doc".$sx[0]."';</script></body></html>");
	}
	else{
		unlink($soutput_filename_tmp);
		die("Error: second document not saved");
}
}
?>