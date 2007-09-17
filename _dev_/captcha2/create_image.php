<?php
// code repris de la contrib captcha  Bernard Blazin
// a ameliorer avec un outil config:
// choix de polices, font, distorsion
session_start();
create_image();
exit();
function create_image(){
    $md5_hash = md5(rand(0,999));     
    $security_code = substr($md5_hash, 15, 5);   
    $_SESSION['captcha2'] = $security_code;
	
    $width = 100;
    $height = 20; 

    $image = ImageCreate($width, $height); 
    $white = ImageColorAllocate($image, 255, 255, 255);
    $black = ImageColorAllocate($image, 0, 0, 0);
    $grey = ImageColorAllocate($image, 204, 204, 204);

    ImageFill($image, 0, 0, $black);
    ImageString($image, 3, 30, 3, $security_code, $white); 
    ImageRectangle($image,0,0,$width-1,$height-1,$grey); 
 
    header("Content-Type: image/jpeg"); 
    ImageJpeg($image);     
    ImageDestroy($image);
}



?>
