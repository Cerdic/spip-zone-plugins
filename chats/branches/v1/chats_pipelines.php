<?php

function chats_objets_extensibles($objets){
		return array_merge($objets, array('chat' => _T('chats:chats')));
}