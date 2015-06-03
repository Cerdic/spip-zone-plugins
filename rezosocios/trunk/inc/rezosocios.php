<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function rezosocios_liste(){
	$rezosocios = array(
					'facebook' => array(
									'nom' => 'Facebook',
									'url' => 'https://www.facebook.com/'
								),
					'twitter' => array(
									'nom' => 'Twitter',
									'url' => 'https://twitter.com/'
								),
					'google+' => array(
									'nom' => 'Google+',
									'url' => 'https://plus.google.com/+'
								),
					'youtube' => array(
									'nom' => 'Youtube',
									'url' => 'https://www.youtube.com/user/'
								),
					'dailymotion' => array(
									'nom' => 'Dailymotion',
									'url' => 'http://www.dailymotion.com/'
								),
					'pinterest' => array(
									'nom' => 'Pinterest',
									'url' => 'https://www.pinterest.com/'
								),
					'flickr' => array(
									'nom' => 'Flickr',
									'url' => 'https://www.flickr.com/photos/'
								),
					'storify' => array(
									'nom' => 'Storify',
									'url' => 'https://storify.com/'
								),
					'linkedin' => array(
									'nom' => 'Linkedin',
									'url' => 'https://www.linkedin.com/profile/view?id='
								),
					'weibo' => array(
									'nom' => 'Weibo',
									'url' => 'http://www.weibo.com/'
								),
					'VK' => array(
									'nom' => 'VK',
									'url' => 'http://vk.com/'
								)
				);
	$rezosocios = pipeline('rezosocios_liste',$rezosocios);
	return $rezosocios;
}
?>
