<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * timeDiffRounded
 *
 * Génère le bloc de bouton pour gérer la position des objets d'un listing
 *
 * @access	public
 * @param	int
 * @param	int
 * @param	int
 * @param	string
 * @return	string
 */
if ( ! function_exists('timeDiffRounded'))
{
	function timeDiffRounded($time1, $time2, $suffix = '', $shortLabels = FALSE) {
		$d1 = date_create()->setTimestamp($time1);
		$d2 = date_create()->setTimestamp($time2);
		$interval = $d1->diff($d2);
		return readableInterval($interval, $suffix, $shortLabels);
	}
}

if ( ! function_exists('readableInterval'))
{
	function readableInterval($interval, $suffix = '', $shortLabels = FALSE) {
		if($shortLabels == FALSE) {
			if ( $v = $interval->y >= 1 ) return pluralize( $interval->y, 'année' ) . $suffix;
			if ( $v = $interval->m >= 1 ) return $interval->m.' mois' . $suffix;
			if ( $v = $interval->d >= 1 ) return pluralize( $interval->d, 'jour' ) . $suffix;
			if ( $v = $interval->h >= 1 ) return pluralize( $interval->h, 'heure' ) . $suffix;
			if ( $v = $interval->i >= 1 ) return pluralize( $interval->i, 'minute' ) . $suffix;
			return pluralize( $interval->s, 'seconde' ) . $suffix;
		}else{
			if ( $v = $interval->y >= 1 ) return pluralize( $interval->y, 'an' ) . $suffix;
			if ( $v = $interval->m >= 1 ) return $interval->m.' mois' . $suffix;
			if ( $v = $interval->d >= 1 ) return $interval->d.' j' . $suffix;
			if ( $v = $interval->h >= 1 ) return $interval->h.' h' . $suffix;
			if ( $v = $interval->i >= 1 ) return $interval->i.' m' . $suffix;
			return $interval->s.' s' . $suffix;
		}
	}
}

if ( ! function_exists('convertSecondsInReadableInterval'))
{
	function convertSecondsInReadableInterval($seconds, $suffix = '', $shortLabels = FALSE) {
		$d1 = new DateTime();
		$d2 = new DateTime();
		$d2->add(new DateInterval('PT'.$seconds.'S'));
		$interval = $d1->diff($d2);
		return readableInterval($interval, $suffix, $shortLabels);
	}
}


if ( ! function_exists('pluralize'))
{
	function pluralize( $count, $text ) 
	{ 
		return $count . ($count <= 1 ? ' '.$text : ' '.$text.'s');
	}
}

if ( ! function_exists('dateFrToYmd'))
{
	function dateFrToYmd( $date, $separatorTo = '-', $separatorFrom = '/' ) 
	{ 
		return implode($separatorTo, array_reverse(explode($separatorFrom, $date)));
	}
}


if ( ! function_exists('dateUsToFr'))
{
	function dateUsToFr( $date, $separatorTo = '/', $separatorFrom = '-' ) 
	{ 
		return implode($separatorTo, array_reverse(explode($separatorFrom, $date)));
	}
}

/**
 * getFirstdayOfWeekFromDateTime
 *
 * Get monday date from the week of the date in params in the format specified in the params
 *
 * @access	public
 * @param	DateTime
 * @param	string Format used by date()
 * @return	string
 */
if ( ! function_exists('getFirstdayOfWeekFromDateTime'))
{
	function getFirstdayOfWeekFromDateTime( $dateTime, $format = 'd/m/Y' ) 
	{
		$timestamp = $dateTime->getTimestamp();
		if($dateTime->format('w') == 0) { 
			 // for week considering to begin sunday...
			$timestamp -= 24 * 60 * 60; 
		}
		return date($format, strtotime('this week', $timestamp));
	}
}

/**
 * getFirstdayOfMonthFromDateTime
 *
 * Get the 1st of the the month of the date in params in the format specified in the params
 *
 * @access	public
 * @param	DateTime
 * @param	string Format used by date()
 * @return	string
 */
if ( ! function_exists('getFirstdayOfMonthFromDateTime'))
{
	function getFirstdayOfMonthFromDateTime( $dateTime, $format = 'd/m/Y' ) 
	{
		return $dateTime->format(str_replace('d', '01', $format));
	}
}