<?php

function phpcurl_get($url, $data_string = NULL, $content_type = 'Content-Type: application/json', $silent=false){

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if(!is_null($data_string)) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			$content_type,
			'Content-Length: ' . strlen($data_string)) 
		);
	}
	$result = curl_exec($ch);
	curl_close($ch);
	if(!$silent) {
		return $result;
	}
}

function phpcurl_post($url, $data_string = NULL, $content_type = 'Content-Type: application/json', $silent=false){

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		$content_type,
		'Content-Length: ' . strlen($data_string)) 
	); 
	 
	$result = curl_exec($ch);
	curl_close($ch);
	if(!$silent) {
		return $result;
	}
}

function phpcurl_put($url, $data_string = NULL, $content_type = 'Content-Type: application/json', $silent=false){

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		$content_type,
		'Content-Length: ' . strlen($data_string)) 
	); 
	 
	$result = curl_exec($ch);
	curl_close($ch);
	if(!$silent) {
		return $result;
	}
}

function phpcurl_delete($url, $data_string = NULL, $content_type = 'Content-Type: application/json', $silent=false){

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); 
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if(!is_null($data_string)) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			$content_type,
			'Content-Length: ' . strlen($data_string)) 
		); 
	}
	$result = curl_exec($ch);
	curl_close($ch);
	if(!$silent) {
		return $result;
	}
}