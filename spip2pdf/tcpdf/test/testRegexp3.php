<?php
echo "testing spliting of html link \n";
$html = <<<EOT
Go to 
<a href="http://bla.com">bla site</a> and 

<br/> we are pleased to propose you a new <a href="qjsk.ki">ki link</a> about the 

<a href="/zou/df/sqsq.ji">ZOU ZOU</a>, enjoy !!
EOT;
//extract the links
$exploded = array();
$pattern = '/<a[^>]+href="[^>]+"[^>]*>[^>]+<\/a>/';
preg_match_all($pattern, $html, $exploded);
//parse the link and fill the link buffer
$link_buffer = array();
$pattern = '/<a[^>]+href="([^>]+)"[^>]*>([^>]+)<\/a>/';
foreach($exploded[0] as $key=>$element){
	preg_match($pattern, $element, $link);
	$link_index = $key + 1;
	$link_url = $link[1];
	$link_content = $link[2];
	$link_buffer[$link_index]['url'] = $link_url;
	$link_buffer[$link_index]['content'] = $link_content;
	$html = str_replace($element, $link_content. "[".$link_index."]",$html);
}
print_r($link_buffer);
echo $html;

?>