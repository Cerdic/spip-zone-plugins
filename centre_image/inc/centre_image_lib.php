<?php
/****************************************************************
  Smart Cropping Class
  Copyright 2014 Greg Schoppe (GPL Licensed)
  http://gschoppe.com
  
  Patché par ARNO*
  
  
  Desc: Takes a GD2 image reference and a target width/height,
        and produces a cropped resized image that puts the focus
        of the image at or close to a rule of thirds line.
  NOTE: THIS CLASS IS A PROOF OF CONCEPT AND RUNS SLOWLY.
        BE SURE TO CACHE RESULTS AND, IF POSSIBLE RUN AS A CHRON,
        BACKGROUND OR AJAX SCRIPT.
 ****************************************************************/
class _centre_image {
    protected $img, $orig_w, $orig_h, $x, $y, $x_weight, $y_weight;
    
    /* constructor - initializes the object
       takes: $img - gd2 image resource */
    function __construct($img) {
        $this->img = $img;
        $this->orig_w = imageSX($img);
        $this->orig_h = imageSY($img);
    }
    
    /* find_focus - identifies the focal point of an image,
                    based on color difference and image entropy
       takes: $slices - integer representing precision of focal
                        point. larger values are slower, but more
                        precise (optional, defaults to 20)
              $weight - float between 0 and 1 representing
                        weighting between entropy method (0) and
                        color method (1) (optional, defaults to .5)
              $sample - integer representing the downsampled
                        resolution of the image to test. larger
                        values are slower, but more precise
                        (optional, defaults to 200) */
    public function find_focus() {
	    // get a sample image to play with
        $temp = $this->rough_in_size(200, 200);
        $w = imagesx($temp);
        $h = imagesy($temp);
        $slices = 10;
        
        // smooth it a little to help reduce the effects of noise
        imagefilter($temp, IMG_FILTER_EDGEDETECT);
        imagefilter($temp, IMG_FILTER_GRAYSCALE);
        imagefilter($temp, IMG_FILTER_GAUSSIAN_BLUR);
        imagefilter($temp, IMG_FILTER_GAUSSIAN_BLUR);
        
        
        
        // get the mean color of the entire image
      //  $avgColor = $this->average_color($temp,0,0,$w,$h);
        $left = $top = 0;
        

        //find the vertical focus position
        $sliceArray   = array();
        // -get the width of each horizontal slice
        $slice_y = round($h/$slices);
        for($i=0;$i<$slices;$i++) {
        
        	// Ajouter une pondération dans 1/3 du haut
        	// (le centre d'intérêt est plus souvent dans le haut de l'image)
        	$pos = 1 - abs($i/$slices - 0.2)/1.5;
        	// echo "[$pos]";
        
        	// uniquement dans la tranche focus_x trouvée précédemment
        	// sinon on prend un x et un y qui ne concernent pas forcément le même endroit de l'image
            $sliceArray[$i]   = $this->get_entropy($temp, 0, $i*$slice_y, $w, $slice_y, $pos);
        }
        // -get the array index of the best slice
        $focus_y   = array_search(max($sliceArray), $sliceArray);
        // -get the pixel value corresponding with the center of that slice
        $y       = ($focus_y + 0.5)*$slice_y/$h;

        
        //find the horizontal focus position
        $sliceArray   = array();
        // -get the width of each vertical slice
        $slice_x = round($w/$slices);
        for($i=0;$i<$slices;$i++) {
             $sliceArray[$i]   = $this->get_entropy($temp, $i*$slice_x, $focus_y*$slice_y, $slice_x, $slice_y);
        }
        // -get the array index of the best slice
        $focus_x   = array_search(max($sliceArray), $sliceArray);
        // -get the pixel value corresponding with the center of that slice
        $x       = ($focus_x + 0.5)*$slice_x/$w;
        unset($sliceArray);
        
        
        


		//$y = 1 - $y;
        // set these values as the focus of the image
        return array("x"=>$x, "y"=>$y);
    }
    
    
    /* rough_in_size  - PROTECTED resizes image proportionally,
                        so that the given width and height are
                        covered. */
    protected function rough_in_size($newW, $newH) {
        $w = $this->orig_w;
        $h = $this->orig_h;
        // image must be valid
        if($w < 1 || $h < 1)
            return false;
        // first proportionally resize dimensions by width dimension
        $tempW = $newW;
        $tempH = ($h*$newW)/$w;
        // if it's too small, try resizing dimensions by height instead
        if($tempH<$newH) {
            $tempW = ($w*$newH)/$h;
            $tempH = $newH;
        }
        // if it's still too small for some reason,
        // just force dimensions to size (in case of rounding errors)
        if($tempW < $newW || $tempH < $newH) {
            $tempW = $newW;
            $tempH = $newH;
        }
        // make the resized image
        $temp = imagecreatetruecolor($tempW, $tempH);
        imagecopyresampled($temp, $this->img, 0, 0, 0, 0, $tempW, $tempH, $w, $h);
        return($temp);
    }

    /* get_entropy - PROTECTED gets the level of entropy present in a slice of an image */
    protected function get_entropy($img, $sx=0, $sy=0, $w=null, $h=null, $ponderation = 1) {
    
    
        if($w == null) $w = imageSX($img)-$sx;
        if($h == null) $h = imageSY($img)-$sy;
        if($w < 1 || $h < 1) return false;

        $levels = array();
        for($x=0;$x<$w;$x++) {
            for($y=0;$y<$h;$y++) {
                $color = imagecolorat($img,$sx+$x,$sy+$y);
                $grayVal = ($color >> 16) & 0xFF;
                if(!isset($levels[$grayVal]))$levels[$grayVal]=0;
                $levels[$grayVal]++;
            }
        }
        // get entropy value from histogram
        $entropy = 0;
        foreach($levels as $level) {
            $pl = $level/($w*$h);
            $pl = $pl*log($pl);
            $entropy -= $pl;
        }
        
        
    	//echo "<li>$sx, $sy, $w, $h, $ponderation - <b>$entropy</b></li>";
        
        return($entropy * $ponderation);
    }
    
}