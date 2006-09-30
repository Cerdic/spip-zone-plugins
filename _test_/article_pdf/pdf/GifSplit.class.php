<?php
class GifSplit
{
/*===========================================*/
/*== V A R I A B L E S ==*/
/*===========================================*/
var $image_count = 0;
var $buffer = array();
var $global = array();
var $fileframe = array();
var $gif = array(0x47, 0x49, 0x46);
var $header = "\x47\x49\x46\x38\x39\x61"; //GIF89a
var $logical_screen_size;
var $logical_screen_param;
var $global_color_table_size;
var $global_color_table_code;
var $global_color_table_flag;
var $global_image_data;
var $extension_lenght;
var $extension_type;
var $image_descriptor;
var $global_sorted;
var $fin;
var $fou;
var $sp;
var $fm;
var $es;
var $imnbr;
var $rsz;

function GifSplit($image, $format, $path, $im_number, $resize)
{
error_reporting(0);
$this->fm = $format;
$this->sp = $path;
$this->imnbr = ($im_number=='')? '-1' : $im_number ;
$this->rsz = ($resize=='')? '0' : $resize ;
if($this->fin = fopen($image, "rb"))
{
$this->getbytes(6);
if(!$this->arrcmp($this->buffer, $this->gif, 3))
{
$this->es = "error #1";
return(0);
}
/*étude du Logical Screen Descriptor
      7 6 5 4 3 2 1 0        Field Name                    Type
     +---------------+
  0  |               |       Logical Screen Width          Unsigned
     +-             -+
  1  |               |
     +---------------+
  2  |               |       Logical Screen Height         Unsigned
     +-             -+
  3  |               |
     +---------------+
  4  | |     | |     |       <Packed Fields>               See below
     +---------------+
  5  |               |       Background Color Index        Byte
     +---------------+
  6  |               |       Pixel Aspect Ratio            Byte
     +---------------+
     <Packed Fields>  =      Global Color Table Flag       1 Bit
                             Color Resolution              3 Bits
                             Sort Flag                     1 Bit
                             Size of Global Color Table    3 Bits
*/
//echo "début </br>" ;
$this->getbytes(4);
$this->logical_screen_size = $this->buffer;

//$this->buffer = array();
$this->getbytes(3);
$this->logical_screen_descriptor = $this->buffer;

$this->global_color_table_flag = ($this->buffer[0] & 0x80) ? TRUE : FALSE;
$this->global_color_table_code = ($this->buffer[0] & 0x07);
$this->global_color_table_size = pow(2,($this->global_color_table_code+1));
//$this->global_color_table_size = 2 << $this->global_color_table_code;
$this->global_sorted = ($this->buffer[4] & 0x08) ? TRUE : FALSE;
if($this->global_color_table_flag)
{
$this->getbytes(3 * $this->global_color_table_size);
for($i = 0; $i < ((3 * $this->global_color_table_size)); $i++)
$this->global[$i] = $this->buffer[$i];
}


$this->fou = '';

for($loop = true; $loop; )
{
$this->getbytes(1);
switch($this->buffer[0])
{
case 0x21:
$this->read_extension();
break;
case 0x2C:
$this->read_image_descriptor();
break;
case 0x3B:
$loop = false;
break;
default:
$this->es = sprintf("Unrecognized byte code %u\n<br>", $this->buffer[0]); 
}
if (($this->image_count > $this->imnbr)and($this->imnbr > -1))
{
$loop = false;
}

}
fclose($this->fin);
}
else
{
$this->es = "error #2";
return(0);
}
$this->es = "ok";

}
/*///////////////////////////////////////////////*/
/*// Function :: read_extension() //*/
/*///////////////////////////////////////////////*/
function read_extension()
{
/* Reset global variables */
/* 0x21 puis 4 bytes discriminants: 
  0xF9 : Graphic Control Extension puis 3ème byte Block Size
  0xFE : Comment Extension
  0x01 : Plain Text Extension puis 3ème byte Block Size
  0xFF : Application Extension puis 3ème byte Block Size
      7 6 5 4 3 2 1 0        Field Name                    Type
     +---------------+
  0  |     0x21      |       
     +-             -+
  1  |     ????      |    |  0xF9  | 0xFE  |  0x01  |
     +---------------+
  2  |               |    | Taille | data  | Taille |
     +-             -+
  3  |               |    |  Data  | Data  |  Data  |
     |               |      
     |               |       
     +---------------+
     |     0x00      |     Block Terminator  
     +---------------+

      7 6 5 4 3 2 1 0        Field Name                  
     +---------------+
  0  |     0x21      |       
     +-             -+
  1  |     0xFF      |      
     +---------------+
  2  |     Taille    |      Taille 1ère extension
     +-             -+
  3  |               |        
     |               |        Data
     |               |        
     +-             -+
     |     Taille    |       Taille 2ème extension (structure à confirmer)
     +-             -+
     |               |        
     |               |        Data
     |               |      
     +---------------+
     |     0x00      |       Block Terminator
     +---------------+

*/  
$this->fou .="\x21";
$this->buffer = array();
$this->getbytes(2);
$this->putbytes($this->buffer, 2);
$this->extension_type = $this->buffer[0];
$this->extension_lenght=$this->buffer[1]; 
if (array_search($this->extension_type, array (1=>0xF9,2=>0x01,3=>0xFF)))
{
$this->getbytes($this->extension_lenght);
$this->putbytes($this->buffer, $this->extension_lenght);
if ($this->extension_type == 0xFF)
  {
 	$this->getbytes(1);
 	$this->putbytes($this->buffer, 1);
  $this->extension_lenght=$this->buffer[0]; 
 	$this->getbytes($this->extension_lenght);
  $this->putbytes($this->buffer, $this->extension_lenght);
  }
}
 for(;;)
  {
 	$this->getbytes(1);
  $this->putbytes($this->buffer, 1);
// byte == 0 : fin du data de l'extension
  if ($this->buffer[0] == 0)
  {
  break ;
  }
 }
 

}

/*///////////////////////////////////////////////*/
/*// Function :: read_image_descriptor() //*/
/*///////////////////////////////////////////////*/
function read_image_descriptor()
{
/* Reset global variables */
$this->buffer = array();

//Lecture du descripteur de l'image: Image Descriptor
/*
      7 6 5 4 3 2 1 0        Field Name                    Type
     +---------------+
  0  |     0x2C      |       Image Separator               Byte
     +---------------+
  1  |               |       Image Left Position           Unsigned
     +-             -+
  2  |               |
     +---------------+
  3  |               |       Image Top Position            Unsigned
     +-             -+
  4  |               |
     +---------------+
  5  |               |       Image Width                   Unsigned
     +-             -+
  6  |               |
     +---------------+
  7  |               |       Image Height                  Unsigned
     +-             -+
  8  |               |
     +---------------+
  9  | | | |0 0|     |       <Packed Fields>               See below
     +---------------+
     <Packed Fields>  =      Local Color Table Flag        1 Bit
                             Interlace Flag                1 Bit
                             Sort Flag                     1 Bit
                             Reserved                      2 Bits
                             Size of Local Color Table     3 Bits
*/

$this->fou .="\x2C";

$this->getbytes(9);
for($i = 0; $i < 9; $i++)
{
$this->image_descriptor[$i] = $this->buffer[$i];
}

if ($this->rsz==1) // new screen sizes and image edges
{
// new logical screen size
$this->logical_screen_size[0] = $this->image_descriptor[4];
$this->logical_screen_size[1] = $this->image_descriptor[5];
$this->logical_screen_size[2] = $this->image_descriptor[6];
$this->logical_screen_size[3] = $this->image_descriptor[7];
// reset position
$this->image_descriptor[0] = $this->image_descriptor[1] = $this->image_descriptor[2] = $this->image_descriptor[3] = 0;
}
$this->putbytes($this->image_descriptor, 9);

$local_color_table_flag = ($this->buffer[8] & 0x80) ? TRUE : FALSE;

if($local_color_table_flag)
{
//il y a une table locale des couleurs
$code = ($this->buffer[8] & 0x07);
$sorted = ($this->buffer[8] & 0x20) ? TRUE : FALSE;
$size = pow(2,($code+1));
}

if($local_color_table_flag)
{
$this->getbytes(3 * $size);
$this->putbytes($this->buffer, 3 * $size);
}
/* LZW minimum code size */
$this->getbytes(1);
$this->putbytes($this->buffer, 1);

/* Image Data */
for(;;)
{
$this->getbytes(1);
$this->putbytes($this->buffer, 1);
if(($u = $this->buffer[0]) == 0)
break;
$this->getbytes($u);
$this->putbytes($this->buffer, $u);
}

$this->global_image_data = $this->fou;

//Construction de la structure de tête du fichier

// Header -> GIF89a //
$this->fou = $this->header;

//logical_screen_descriptor//
$this->putbytes($this->logical_screen_size,4);
$this->putbytes($this->logical_screen_descriptor,3);

//Global Color Table//
$this->putbytes($this->global, $this->global_color_table_size*3);

//Global_image_data

$this->fou .= $this->global_image_data;

/* trailer */
$this->fou .= "\x3B";



//Enregistrement du fichier gif
$framename = $this->sp . $this->image_count . ".gif";
 if (!$handle = fopen($framename, 'w')) {
         echo "Impossible d'ouvrir le fichier ($framename)";
         exit;
    }

if(!fwrite($handle,$this->fou))
{
$this->es = "error #3";
return(0);
}

$this->fileframe[]=$framename;

/* Write to file */
switch($this->fm)
{
/* Write as BMP */
case "BMP":
$im = imageCreateFromString($this->fou);
$framename = $this->sp . $this->image_count . ".bmp";
if(!$this->imageBmp($im, $framename))
{
$this->es = "error #3";
return(0);
}
imageDestroy($im);
break;
/* Write as PNG */
case "PNG":
$im = imageCreateFromString($this->fou);
$framename = $this->sp . $this->image_count . ".png";
if(!imagePng($im, $framename))
{
$this->es = "error #3";
return(0);
}
imageDestroy($im);
break;
/* Write as JPG */
case "JPG":
$im = imageCreateFromString($this->fou);
$framename = $this->sp . $this->image_count . ".jpg";
if(!imageJpeg($im, $framename))
{
$this->es = "error #3";
return(0);
}
imageDestroy($im);
break;
/* Write as GIF */
case "GIF":
$im = imageCreateFromString($this->fou);

imageDestroy($im);

break;
}
$this->image_count++;
$this->fou = '';
}
/*///////////////////////////////////////////////*/
/*// BMP creation group //*/
/*///////////////////////////////////////////////*/
/* ImageBMP */
function imageBmp($img, $file, $RLE=0)
{
$ColorCount = imagecolorstotal($img);
$Transparent = imagecolortransparent($img);
$IsTransparent = $Transparent != -1;
if($IsTransparent)
$ColorCount--;
if($ColorCount == 0)
{
$ColorCount = 0;
$BitCount = 24;
}
if(($ColorCount > 0) && ($ColorCount <= 2))
{
$ColorCount = 2;
$BitCount = 1;
}
if(($ColorCount > 2) && ($ColorCount <= 16))
{
$ColorCount = 16;
$BitCount = 4;
}
if(($ColorCount > 16) && ($ColorCount <= 256))
{
$ColorCount = 0;
$BitCount = 8;
}
$Width = imageSX($img);
$Height = imageSY($img);
$Zbytek = (4 - ($Width / (8 / $BitCount)) % 4) % 4;
if($BitCount < 24)
$palsize = pow(2, $BitCount) * 4;
$size = (floor($Width / (8 / $BitCount)) + $Zbytek) * $Height + 54;
$size += $palsize;
$offset = 54 + $palsize;
// Bitmap File Header
$ret = 'BM';
$ret .= $this->int_to_dword($size);
$ret .= $this->int_to_dword(0);
$ret .= $this->int_to_dword($offset);
// Bitmap Info Header
$ret .= $this->int_to_dword(40);
$ret .= $this->int_to_dword($Width);
$ret .= $this->int_to_dword($Height);
$ret .= $this->int_to_word(1);
$ret .= $this->int_to_word($BitCount);
$ret .= $this->int_to_dword($RLE);
$ret .= $this->int_to_dword(0);
$ret .= $this->int_to_dword(0);
$ret .= $this->int_to_dword(0);
$ret .= $this->int_to_dword(0);
$ret .= $this->int_to_dword(0);
// image data
$CC = $ColorCount;
$sl1 = strlen($ret);
if($CC == 0)
$CC = 256;
if($BitCount < 24)
{
$ColorTotal = imagecolorstotal($img);
if($IsTransparent)
$ColorTotal--;
for($p = 0; $p < $ColorTotal; $p++)
{
$color = imagecolorsforindex($img, $p);
$ret .= $this->inttobyte($color["blue"]);
$ret .= $this->inttobyte($color["green"]);
$ret .= $this->inttobyte($color["red"]);
$ret .= $this->inttobyte(0);
}
$CT = $ColorTotal;
for($p = $ColorTotal; $p < $CC; $p++)
{
$ret .= $this->inttobyte(0);
$ret .= $this->inttobyte(0);
$ret .= $this->inttobyte(0);
$ret .= $this->inttobyte(0);
}
}
if($BitCount <= 8)
{
for($y = $Height - 1; $y >= 0; $y--)
{
$bWrite = "";
for($x = 0; $x < $Width; $x++)
{
$color = imagecolorat($img, $x, $y);
$bWrite .= $this->decbinx($color, $BitCount);
if(strlen($bWrite) == 8)
{
$retd .= $this->inttobyte(bindec($bWrite));
$bWrite = "";
}
}
if((strlen($bWrite) < 8) and (strlen($bWrite) != 0))
{
$sl = strlen($bWrite);
for($t = 0; $t < 8 - $sl; $t++)
$sl .= "0";
$retd .= $this->inttobyte(bindec($bWrite));
}
for($z = 0; $z < $Zbytek; $z++)
$retd .= $this->inttobyte(0);
}
}
if(($RLE == 1) and ($BitCount == 8))
{
for($t = 0; $t < strlen($retd); $t += 4)
{
if($t != 0)
if(($t) % $Width == 0)
$ret .= chr(0).chr(0);
if(($t + 5) % $Width == 0)
{
$ret .= chr(0).chr(5).substr($retd, $t, 5).chr(0);
$t += 1;
}
if(($t + 6) % $Width == 0)
{
$ret .= chr(0).chr(6).substr($retd, $t, 6);
$t += 2;
}
else
$ret .= chr(0).chr(4).substr($retd, $t, 4);
}
$ret .= chr(0).chr(1);
}
else
$ret .= $retd;
if($BitCount == 24)
{
for($z = 0; $z < $Zbytek; $z++)
$Dopl .= chr(0);
for($y = $Height - 1; $y >= 0; $y--)
{
for($x = 0; $x < $Width; $x++)
{
$color = imagecolorsforindex($img, ImageColorAt($img, $x, $y));
$ret .= chr($color["blue"]).chr($color["green"]).chr($color["red"]);
}
$ret .= $Dopl;
}
}
if(fwrite(fopen($file, "wb"), $ret))
return true;
else
return false;
}
/* INT 2 WORD */
function int_to_word($n)
{
return chr($n & 255).chr(($n >> 8) & 255);
}
/* INT 2 DWORD */
function int_to_dword($n)
{
return chr($n & 255).chr(($n >> 8) & 255).chr(($n >> 16) & 255).chr(($n >> 24)
& 255); }
/* INT 2 BYTE */
function inttobyte($n)
{
return chr($n);
}
/* DECIMAL 2 BIN */
function decbinx($d,$n)
{
$bin = decbin($d);
$sbin = strlen($bin);
for($j = 0; $j < $n - $sbin; $j++)
$bin = "0$bin";
return $bin;
}
/*///////////////////////////////////////////////*/
/*// Function :: arrcmp() //*/
/*///////////////////////////////////////////////*/
function arrcmp($b, $s, $l)
{
for($i = 0; $i < $l; $i++)
{
if($s{$i} != $b{$i}) return false;
}
return true;
}
/*///////////////////////////////////////////////*/
/*// Function :: getbytes() //*/
/*///////////////////////////////////////////////*/
function getbytes($l)
{
for($i = 0; $i < $l; $i++)
{
$bin = unpack('C*', fread($this->fin, 1));
$this->buffer[$i] = $bin[1];
}
return $this->buffer;
}
/*///////////////////////////////////////////////*/
/*//           Function :: putbytes()          //*/
/*///////////////////////////////////////////////*/
function putbytes($s, $l)
{
for($i = 0; $i < $l; $i++)
{
$this->fou .= pack('C*', $s[$i]);
}
}
function getfilelist()
{
return $this->fileframe;
}

function getReport()
{
return $this->es;
}
}
?>
