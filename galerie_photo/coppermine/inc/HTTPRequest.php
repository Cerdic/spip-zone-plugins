<?
// Simple class to fetch a HTTP URL. Supports "Location:"-redirections. 
// Useful for servers with allow_url_fopen=false. Works with SSL-secured hosts.
// http://fr.php.net/fopen + http://fr.php.net/fsockopen
//
// Notes :
// - On peut réaliser la même chose plus simplement avec un fopen(URL,'r') mais cela nécessite que le paramètre allow_url_fopen de php.ini soit activé
// - Une autre solution consiste à utiliser curl, la librairie client URL si le package libcurl est installé
//
// Adapations effectuées :
// - les cookies spip et coopermine sont passés dans l'entête de fsockopen
// - les variables passées par la méthode post sont passées dans l'entête de fsockopen
// - les fichiers uploadés via le formulaire upload.php sont passées dans l'entête de fsockopen
// - le référant http est passé dans l'entête de fsockopen pour fonctionnement avec d'éventuelles conditions RewriteRule associées au réperertoire de coppermine

class HTTPRequest
{
   var $_fp;        // HTTP socket
   var $_url;        // full URL
   var $_host;        // HTTP host
   var $_protocol;    // protocol (HTTP/HTTPS)
   var $_uri;        // request URI
   var $_port;        // port
  
   // scan url
   function _scan_url()
   {
       $req = $this->_url;
      
       $pos = strpos($req, '://');
       $this->_protocol = strtolower(substr($req, 0, $pos));
      
       $req = substr($req, $pos+3);
       $pos = strpos($req, '/');
       if($pos === false)
           $pos = strlen($req);
       $host = substr($req, 0, $pos);
      
       if(strpos($host, ':') !== false)
       {
           list($this->_host, $this->_port) = explode(':', $host);
       }
       else
       {
           $this->_host = $host;
           $this->_port = ($this->_protocol == 'https') ? 443 : 80;
       }
      
       $this->_uri = substr($req, $pos);
       if($this->_uri == '')
           $this->_uri = '/';
   }
  
   // constructor
   function HTTPRequest($url)
   {
       $this->_url = $url;       
       $this->_scan_url();
   }
  
   // download URL to string
   function DownloadToString()
   {
       $crlf = "\r\n";
       $data="";
       
       srand((double)microtime()*1000000);
       $boundary = "---------------------".substr(md5(rand(0,32000)),0,10);
      
       // on récupère les cookies spip et coopermine
       // pour les faire passer dans l'entête de fsockopen       
       $spip_cookie = $_COOKIE['spip_session'];
       $coppermine_cookie= $_COOKIE['cpg143_data'];
       
       // on récupère les variables passées par la méthode post
       // pour les faire passer dans l'entête de fsockopen           
       foreach($_POST as $nom => $valeur) {
       	 if ($nom!="URI_array") {
         	$data .="--$boundary".$crlf;
         	$data .= 'Content-Disposition: form-data; name="'.$nom.'"'.$crlf;
         	$data .= $crlf.$valeur.$crlf;
         	$data .="--$boundary".$crlf;
        }
       }  
       
       // on récupère les variables passées dans le tableau URI_array : cas spécifique de upload.php
       if ($_POST['URI_array']!=NULL) {
       	for ($i=0;$i<3;$i++) {
         	$data .="--$boundary".$crlf;
         	$data .= 'Content-Disposition: form-data; name="URI_array[]"'.$crlf;
         	$data .= $crlf.$_POST['URI_array'][$i].$crlf;
         	$data .="--$boundary".$crlf;       		
       	}
      }

              
	     // on récupère les fichiers uploadés : traitement spécifique pour le formulaire upload.php de coppermine
	     if ( ($_GET['file']=="upload") AND ($_POST['control']='phase_1') AND ($_FILES!=NULL) ) { 
	     		for ($i=0;$i<5;$i++) {
			   		$file_name = $_FILES['file_upload_array']['name'][$i];  
			     	if ($file_name!="") {
			     	
			     		$tmp_name = $_FILES['file_upload_array']['tmp_name'][$i];   
			     		$content_type = $_FILES['file_upload_array']['type'][$i];	     					
	
							$data .="--$boundary".$crlf;				
							$data .="Content-Disposition: multipart/form-data; name=\"file_upload_array[]\"; filename=\"$file_name\"".$crlf;
							$data .= "Content-Type: $content_type".$crlf.$crlf;
							$data .= join("", file($tmp_name)).$crlf;
							$data .="--$boundary--".$crlf;       
						}
					}
	       }
	       
       
       // generate request
       $req = 'POST ' . $this->_uri . ' HTTP/1.0' . $crlf
           		.'Host: ' . $this->_host . $crlf             
           		. 'Referer: http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].$crlf      
           		."Cookie: cpg143_data=$coppermine_cookie;spip_session=$spip_cookie". $crlf
          		."Content-type: multipart/form-data; boundary=$boundary". $crlf
           		."Content-length: " . strlen($data).$crlf.$crlf.strlen($data).$crlf
           		.$data
           		."Connection: Close\n";

      
       // fetch
       $this->_fp = @fsockopen(($this->_protocol == 'https' ? 'ssl://' : '') . $this->_host, $this->_port);
       
       // Hack pour traiter les redirections des formulaires (cas du post d'un commentaire dans coppermine par exemple)       
       if (!($this->_fp)) echo '<script type="text/javascript">document.location="'.$_SERVER['HTTP_REFERER'].'"</script>';
       
       fwrite($this->_fp, $req);
       while(is_resource($this->_fp) && $this->_fp && !feof($this->_fp))
           $response .= fread($this->_fp, 1024);
       fclose($this->_fp);
      
       // split header and body
       $pos = strpos($response, $crlf . $crlf);
       if($pos === false)
           return($response);
       $header = substr($response, 0, $pos);
       $body = substr($response, $pos + 2 * strlen($crlf));
      
       // parse headers
       $headers = array();
       $lines = explode($crlf, $header);
       foreach($lines as $line)
           if(($pos = strpos($line, ':')) !== false)
               $headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1));
      
       // redirection?
       if(isset($headers['location']))
       {
           $http = new HTTPRequest($headers['location']);
           return($http->DownloadToString($http));
       }
       else
       {
           return($body);
       }
   }
}

?>

