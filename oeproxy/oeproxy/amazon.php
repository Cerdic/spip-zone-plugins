<?php
/*
 * Plugin oEmebed The Web
 * (c) 2011 Cedric Morin
 * Distribue sous licence GPL
 *
 * http://oembed.com/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function oeproxy_amazon_dist($url,$options,$html=null){

	if (!$url
		OR !preg_match(',^http://(?:www.)?amazon.fr/[^/]+/[^/]+/([^/]+)/,i',$url,$m))
		return 404;


	$asin = $m[1];

	/* Amazon Product Advertising API */
	include_spip("oeproxy/AmazonAPI/amazon_api_class");

	$obj = new AmazonProductAPI();

	try
	{
			/* Returns a SimpleXML object */
			 $result = $obj->getItemByAsin($asin,'Large');
	}
	catch(Exception $e)
	{
			echo $e->getMessage();
	}

	$item = $result->Items->Item;

	$asin = $item->ASIN;
	$url_detail = $item->DetailPageURL;
	$image = $item->LargeImage;

	$author = $item->ItemAttributes->Author;
	$title = $item->ItemAttributes->Title;
	
	
	print_r($result->Items->Item);

	die();
}
