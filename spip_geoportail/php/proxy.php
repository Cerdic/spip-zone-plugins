<?php
// proxy http
// auteur: Marc Gauthier
// 02/09/2009
// les erreurs dans les log du serveur
// http://www.papygeek.com/download/53/
// Transfer-Encoding: chunked
//
// Didier Richard - IGN - dérivation pour publication
// (c) IGN 2010
// License : http://creativecommons.org/licenses/by-nc-sa/2.0/fr/

    // ----------------------------------------------------------
    // global variables :
    $debug= 1;
    $debug_html= 0;
    $sUrl= '';
    $sReponse= '';
    // via un proxy d'entreprise/IP
    $proxy_host= '';
    $proxy_port= 80;
    $content_types= array(
        'application/vnd.google-earth.kml+xml',
        'application/vnd.google-earth.kml',
        //'application/vnd.google-earth.kmz', # TODO needs to unzip response ...
        'application/vnd.ogc.se_xml',
        'application/vnd.ogc.wms_xml',
        'application/vnd.ogc.wfs_xml',
        'application/vnd.ogc.gml',
        'application/soap+xml',
        'application/xml',
        'text/xml',
        'text/plain',
        'text/html');
    // ----------------------------------------------------------

    //
    // écriture d'un message de log
    function carp($msg) {
        global $debug;
        if ($debug) {
            if ($debug_html) {
                print "__FILE__.:$msg.<br>\n";
            }
            error_log(__FILE__.": $msg", 0);
        }
    }

    //
    // écriture d'un message de log et erreur http
    function confess($msg) {
        carp($msg);
        header("HTTP/1.0 500 $msg");
        exit;
    }

    //
    // mode chunk
    function unchunk($result) {
        return preg_replace_callback(
            '/(?:(?:\r\n|\n)|^)([0-9A-F]+)(?:\r\n|\n){1,2}(.*?)'.
            '((?:\r\n|\n)(?:[0-9A-F]+(?:\r\n|\n))|$)/si',
            create_function(
                '$matches',
                'return hexdec($matches[1]) == strlen($matches[2]) ? $matches[2] : $matches[0];'
            ),
            $result
        );
    }

    //
    // la fonction proxy
    // 2 grandes étapes:
    // - émission de la requête
    // - réception de la réponse
    function proxy($sUrl) {
        global $proxy_host, $proxy_port, $chunked, $content_types;
        //
        // analyse de l'url et construction de la requête
        $sUrl= urldecode($sUrl);
        $referrer= $_SERVER["HTTP_REFERER"];
        $userAgent= $_SERVER["HTTP_USER_AGENT"];
        $aUrl= @parse_url($sUrl);
        if (!isset($aUrl['scheme'])) {
            //try absolute and relative path:
            confess("proxy scheme missing");
        }
        // construction de la requete
        $acceptH= $_SERVER['HTTP_ACCEPT'];
        $pragmaH= $_SERVER['HTTP_PRAGMA'];
        $cacheControlH= $_SERVER['HTTP_CACHE_CONTROL'];
        $acceptLanguageH= $_SERVER['HTTP_ACCEPT_CHARSET'];
        $keepAliveH= $_SERVER['HTTP_KEEP_ALIVE'];
        #$acceptEncodingH= $_SERVER['HTTP_ACCEPT_ENCODING'];//TODO decompression
        #$connectionH= $_SERVER['HTTP_CONNECTION'];
        $SOAPActionH= $_SERVER['HTTP_SOAPACTION'];
        $acceptCharsetH= $_SERVER['HTTP_ACCEPT_CHARSET'];
        $gppKeyH= $_SERVER['HTTP_GPPKEY'];#if gppKey in HTTP Header
        $Hs= "Host: ".$aUrl['host'].($aUrl['port']? ":".$aUrl['port'] : "")."\r\n"
            . (strlen($acceptH)>0? "Accept: ".$acceptH : join(",", $content_types))."\r\n"
            . (strlen($pragmaH)>0? "Pragma: ".$pragmaH."\r\n" : "")
            . (strlen($cacheControlH)>0? "Cache-Control: ".$cacheControlH."\r\n" : "")
            . (strlen($acceptLanguageH)>0? "Accept-Language: ".$acceptLanguageH."\r\n" : "")
            . (strlen($keepAliveH)>0? "Keep-Alive: ".$keepAliveH."\r\n" : "")
            #. (strlen($acceptEncodingH)>0? "Accept-Encoding: ".$acceptEncodingH."\r\n" : "")
            #. (strlen($connectionH)>0? "Connection: ".$connectionH."\r\n" : "")
            . (strlen($SOAPActionH)>0? "SOAPAction: ".$SOAPActionH."\r\n" : "")
            . (strlen($acceptCharsetH)>0? "Accept-Charset: ".$acceptCharsetH."\r\n" : "")
            . (strlen($gppKeyH)>0? "gppkey: ".$gppKeyH."\r\n" : "")
            . (strlen($referrer)>0? "Referer: ".$referrer."\r\n" : "")
            . (strlen($userAgent)>0? "User-Agent: ".$userAgent."\r\n" : "")
            ;
        carp($Hs);
        if ($_SERVER["REQUEST_METHOD"]==='GET') {
            $sReq= "GET $sUrl HTTP/1.0\r\n"
                 . $Hs
                 ;
        } else {
            if ($_SERVER["REQUEST_METHOD"]==='POST') {
                $data= '';
                if (count($_POST)){
                    while (list($key, $val)= each($_POST)){
                        $data.="$key : $val\n";
                    }
                } else {
                    $data= trim(file_get_contents('php://input'));
                }
                $sReq= "POST $sUrl HTTP/1.0\r\n"
                     . $Hs
                     . "Content-Type: text/xml\r\n"
                     . "Content-length: ".strlen($data)."\r\n"
                     . "\r\n"
                     . $data
                     ;
            }
        }
        $sReq.= "\r\n";
        if (empty($proxy_host)) {
            $host= $aUrl["host"];
            $port= $aUrl["port"] ? $aUrl["port"] : 80;
        } else {
            $host= $proxy_host;
            $port= $proxy_port;
        }
        // envoi de la requête
        carp("host:$host port:$port url:$sUrl");
        $fp= @fsockopen($host, $port, $errno, $errstr, 5);
        if (!$fp) {
            confess("fsockopen failed: $errstr ($errno)");
        }
        carp("sReq:$sReq");
        fwrite($fp, $sReq);
        // attente de la réponse
        $headers= '';
        $sReponse= '';
        ob_start();
        while (!feof($fp)) {
            $sReponse.= fread($fp, 4096);
        }
        fclose ($fp);
        $eoh= strpos($sReponse, "\r\n\r\n");
        $headers= substr($sReponse, 0, $eoh);
        $sReponse= substr($sReponse, $eoh+4);
        $Hs= preg_split('/(?:\r\n|\n)/', $headers);
        carp("Hs=[".count($Hs)."]");
        for ($i= 0, $l= count($Hs); $i<$l; $i++) {
            if (preg_match('/^Content-Length/i', $Hs[$i])) {
                continue;
            }
            if (preg_match('/^Transfer-Encoding: chunked/i', $Hs[$i])) {
                // Transfer-Encoding: chunked
                carp("chunked response");
                $sReponse= unchunck($sReponse);
                continue;
            }
            #carp("header=[$Hs[$i]]");
            header($Hs[$i]);
        }
        header("Content-Length: ".strlen($sReponse));
        print $sReponse;
    }

    // ----------------------------------------------------------
    // programme principal:
    // on accept que GET/POST (pour l'instant)
    if (($_SERVER["REQUEST_METHOD"]==='GET' or $_SERVER["REQUEST_METHOD"]==='POST') &&
        (isset($_REQUEST["url"]) && strlen($_REQUEST["url"])>0)) {
        $sUrl= substr($_SERVER["QUERY_STRING"],4);
        carp("Proxying:$sUrl");
        proxy($sUrl);
        exit;
    }
    // on ne traite pas la demande :
    carp("REQUEST_METHOD:".$_SERVER["REQUEST_METHOD"]." QUERY_STRING:".$_SERVER["QUERY_STRING"] );
    if ($debug) {
        phpinfo(INFO_VARIABLES);
    }
    exit;
    // ----------------------------------------------------------
?>
