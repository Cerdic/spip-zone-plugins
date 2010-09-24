<?php
function cclass($c) {
	return strtok($c, '-');
}
function cnic($c) {
	return substr($c, strlen(cclass($c))+1);
}
?>