<?php

// Creates a transparent image rather than the default black image
function imageCreateTransparent($x, $y) {
    $imageOut = imagecreate($x, $y);
    $colourBlack = imagecolorallocate($imageOut, 0, 0, 0);
    imagecolortransparent($imageOut, $colourBlack);
    return $imageOut;
}
 
?>
