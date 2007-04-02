<?
// ---------------------------------------------
//	Useful time tool to estimate duration
//  of a program. It pushes to improve programs
//   last modification: 25/02/04 - Alain	
// ---------------------------------------------

// ---------------------------------------------
//	get time in microsecond, and format in second
// ---------------------------------------------
function getmicrotime(){
	// get micro time and format in second
	list($sec, $usec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

// ---------------------------------------------
//	calculate duration from 2 times
// ---------------------------------------------
function exetime($t_start,$t_end) {
	// evaluate exe time in second
	$e_time = $t_end - $t_start;
	$e_time = sprintf("%01.4f",$e_time);
	return $e_time;
}

// ---------------------------------------------
//	combine both previous to deliver a formated result
// ---------------------------------------------
function display_exe_time ($time_start, $comment) {
	$time_end = getmicrotime();
	$duration = exetime($time_start,$time_end);
	print "$comment : $duration sec</i><br><br>";
}

// ---------------------------------------------
//	return date in future, by month increment	
// ---------------------------------------------
function month_in_future($increment) {
	$day = date("d");
	$month = date("m");
	$year = date("Y");
	
	$month_future = $month + $increment;
	$today_future = date("Y-m-d", mktime(0,0,0,$month_future, $day, $year));
	return $today_future;
}
?>