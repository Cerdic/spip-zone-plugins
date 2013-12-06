<?php

/* On verifie que l'extension php pour imagemagick est bien installée et activée */
function photosafe_ext_test(){
	return extension_loaded('imagick');
}

function photosafe_rm_exif($file){
	if (photosafe_ext_test()){
		$i = new Imagick($file);
		spip_log($i->getImageProperties('exif:*'), 'photosafe');
		$i->stripImage();
		$i->writeImage($file);
		/*debug*/
		$i->readImage($file);
		spip_log($i->getImageProperties('exif:*'), 'photosafe');
		/*fin debug*/
		return 1;
	}
	return 0;
}
 




?>
