<?php
echo "testing cleaning the br and p \n";
$html = <<<EOT
some content before 

with 

plenty line 

break

<li>kjksjdk 
<li>sdsd
sdsdsdsd
<img src="sdlsdk" width="45" /> content in the middle <li>jhhzsja</li><li>Hop

<h3>A big title</h3>

<img src="hsdghs.jyu" height="34">

Some content at the end

<h3 class="spip">Another big title</h3>

EOT;

$pattern = '/(<[^>]+>)/Uu';
$html = str_replace("</li>","||",$html);
$html = str_replace("</h3>","||",$html);
$html = preg_replace('/(<[^>]*>)/Uu','||$1||',$html);
$html = str_replace("\n","\n||",$html);
$html = str_replace("\r","\r||",$html);
$a = explode("||",$html);
print_r($a);
$tokens = array();
$token_counter = 0;
for($i=0; $i<count($a); $i++){
	if(trim($a[$i])==""){
		//ignore
		continue;
	}elseif($a[$i]=="<li>"){
		$token_counter++;
		$tokens[$token_counter]['type'] = "li";
		$tokens[$token_counter]['content'] = $a[$i+1];
		//pass the next
		$i++;
	}elseif(preg_match('/<h3[^>]*>/',$a[$i])){
		$token_counter++;
		$tokens[$token_counter]['type'] = "h3";
		$tokens[$token_counter]['content'] = $a[$i+1];
		//pass the next
		$i++;
	}elseif(preg_match('/<img[^>]+src="([^>]+)"[^>]*>/',$a[$i],$src)){
		$token_counter++;
		$tokens[$token_counter]['type'] = "img";
		$tokens[$token_counter]['src'] = $src[1];
		//extract the width if applicable  
		if(preg_match('/<img[^>]+width="([^>]+)"[^>]*>/',$a[$i],$width)){
			$tokens[$token_counter]['width'] = $width[1];						
		}
		//extract the height if applicable
		if(preg_match('/<img[^>]+height="([^>]+)"[^>]*>/',$a[$i],$height)){
			$tokens[$token_counter]['height'] = $height[1];						
		}	
	}else{
		$token_counter++;
		$tokens[$token_counter]['type'] = "plain";
		$tokens[$token_counter]['content'] = $a[$i];
	}
}
print_r($tokens);
?>