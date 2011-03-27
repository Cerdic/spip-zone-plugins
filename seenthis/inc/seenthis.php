<?php

## a definir dans mes_options (ou cfg)
# define('_SEENTHIS_LOGIN', 'xxx');
# define('_SEENTHIS_PASS', 'xxx');


// (c) Fil base sur le code d'ARNO* / Seenthis.net 2011
// Licence LGPL / MIT (je suppose)

class Seenthis {
	var $login=null;
	var $pass=null;
	var $post_url; # URL de l'API

	function Seenthis($login=null, $pass=null) {

		if (!is_null($login))
			$this->login = $login;
		else
			$this->login = _SEENTHIS_LOGIN;

		if (!is_null($pass))
			$this->pass = $pass;
		else
			$this->pass = _SEENTHIS_PASS;

		$this->post_url = "https://seenthis.net/api/";
	}

	function create_message($titre, $link, $quote, $comment, $tags) {
		$ret = "";
		if (strlen($titre) > 0) $ret .= "$titre\n";
		if (strlen($link) > 0) $ret .= "$link\n";
		$ret .= "\n";
	
		if (strlen($quote) > 0) $ret .= "❝".$quote."❞\n\n";
		if (strlen($comment) > 0) $ret .= "$comment\n";
	
		if (!is_array($tags)) {
			$tags = explode(" ", $tags);
		}
		foreach($tags as $tag) {
			$tag = trim($tag);
			$tag = preg_replace(",^#,", "", $tag);
			if (strlen($tag) > 0) {
				$l .= "#$tag ";
			}
		}
		if (strlen($l) > 0) $ret .= "\n$l\n";
		
		return $ret;
	}
	
	
	function create_xml($message, $id=null, $inreplyto=null) {

		$xml = "<?xml version='1.0' encoding='UTF-8'?>\n"
				."<entry xmlns='http://www.w3.org/2005/Atom' xmlns:thr='http://purl.org/syndication/thread/1.0'>\n";

		if ($id = $this->numeric($id))
			$xml .= "<id>message:$id</id>\n";

		$xml .= "<summary><![CDATA[".trim($message)."]]></summary>\n";

		if ($inreplyto = $this->numeric($inreplyto))
			$xml .= "<thr:in-reply-to ref='message:$inreplyto'/>\n";

		$xml .= "</entry>\n";
	
		return $xml;
	}
	
	function curl($xml, $method, $uri) {
		$request = curl_init(); // initiate curl object
		curl_setopt($request, CURLOPT_URL, $this->post_url.$uri);
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
		curl_setopt($request, CURLOPT_HTTPHEADER, array("Content-Type: application/atom+xml;type=entry"));
		curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method );	
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true );	
		curl_setopt($request, CURLOPT_USERPWD, $this->login.':'.$this->pass);
		curl_setopt($request, CURLOPT_POSTFIELDS, "$xml");

		$post_response = curl_exec($request); // execute curl post and store results in $post_response
		curl_close ($request); // close curl object

		if (is_object($r = new SimpleXmlIterator($post_response))
		AND !$r->body  # le controle d'erreur n'est pas proprement indique dans la reponse de l'API, on se rabat sur la presence d'un body
		) {
			$r->xml = $post_response;
			unset($r->error);
		}
		else {
			$r = new stdClass();
			$r->xml = $post_response;
			$r->error = trim(strip_tags($post_response)); # pas beau
			if (!$r->error)
				$r->error = "Empty response";
		}

		return $r;
	}
	
	function read($id = null) {
		// si on ne précise pas le message, c'est le plus récent de notre feed
		// sinon c'est le message demandé
		if (is_null($id)) {
			$a = $this->feed();
			if (!$a->error) {
				$b = $a->entry[0];
				$b->xml = (string) $a->xml;
				return $b;
			}
			else
				return $a;
		}

		else
			return $this->curl("", 'GET', 'messages/'.$this->numeric($id));
	}

	function post($message, $inreplyto=null) {
		$xml = $this->create_xml($message, null, $inreplyto) ;
		return $this->curl($xml, 'POST', 'messages');
	}
	
	function update($message, $id) {
		$xml = $this->create_xml($message, $id);
		return $this->curl($xml, 'PUT', 'messages');
	}

	function feed($login=null, $pagination=0) {
		if (!strlen($login))
			$login = $this->login;
		return $this->curl("", 'GET', 'people/'.rawurlencode($login).'/messages/'.$nb);
	}


	// supprimer le 'message:' d'un id
	private function numeric($id) {
		if ($id = intval(preg_replace('/^message:/', '', $id)))
			return $id;
	}

} // class Seenthis


?>