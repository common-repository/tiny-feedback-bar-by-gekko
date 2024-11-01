<?php

function tfb_return_date($date, $return_seconds=0) {
	
	if ($date != '' AND $date != '0') {
		$begin_gisteren = mktime(0, 0, 0, date("m") , date("d")-1, date("Y"));
		$begin_vandaag = mktime(0, 0, 0, date("m") , date("d"), date("Y"));
		$begin_morgen = mktime(0, 0, 0, date("m") , date("d")+1, date("Y"));
		$begin_overmorgen = mktime(0, 0, 0, date("m") , date("d")+2, date("Y"));
		$eind_overmorgen = mktime(0, 0, 0, date("m") , date("d")+3, date("Y"));
		
		setlocale(LC_ALL, 'nl_NL.ISO8859-1');
		
		if ($date >= $begin_gisteren AND $date < $begin_vandaag) {
	
			if ($return_seconds == '1') {
				$time = 'yesterday ' . strftime("%H:%M", $date);
			} else {
				$time = 'yesterday';
			}
		} elseif ($date >= $begin_vandaag AND $date < $begin_morgen) {
	
			if ($return_seconds == '1') {
				$time = 'today ' . strftime("%H:%M", $date);
			} else {
				$time = 'today';
			}
		} elseif ($date >= $begin_morgen AND $date < $begin_overmorgen) {
	
			if ($return_seconds == '1') {
				$time = 'tomorrow ' . strftime("%H:%M", $date);
			} else {
				$time = 'tomorrow';
			}
		} else {
	
			if ($return_seconds == '1') {
				$time = strftime("%e %B %Y om %H:%M", $date);
			} else {
				$time = strftime("%e %B %Y", $date);
			}
		}
	} else {
		$time = '-';
	}
	return $time;
}

?>