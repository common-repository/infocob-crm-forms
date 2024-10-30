<?php
	namespace Infocob\CrmForms\Admin;
	
	use DateInterval;
	use DateTime;
	
	if(!defined('ABSPATH') && !is_admin()) {
		die();
	}
	class DateTimeFr extends DateTime {
		
		public function __construct($time = "now", $timezone = null) {
			
			parent::__construct($time, $timezone);
			
		}
		
		#[\ReturnTypeWillChange]
		public static function createFromFormat($format, $datetime = "now", $timezone = null) {
			$datetimeobject = parent::createFromFormat($format, $datetime);
			return new DateTimeFr($datetimeobject->format("Y-m-d H:i:s"));
		}
		
		
		public static function ChangeFormat($dateString, $format = "d/m/Y") {
			if(empty($dateString) || $dateString == "0000-00-00" || $dateString == '0000-00-00 00:00:00' || !self::IsValideDateSql($dateString)) {
				return "";
			}
			$d = new DateTimeFr($dateString);
			
			return $d->format($format);
		}
		
		public static function IsValideDateSql($date, $format = 'Y-m-d H:i:s') {
			//return $date;
			$d = DateTime::createFromFormat($format, $date);
			
			return $d && $d->format($format) == $date;
		}
		
		/**
		 * Return french date format
		 *
		 * @param $format
		 * Format accept&eacute; by date() function
		 */
		#[\ReturnTypeWillChange]
		public function format($format) {
			$date = parent::format($format);
			$date = $this->translateDay($date);
			$date = $this->translateShortDay($date);
			$date = $this->translateMonth($date);
			$date = $this->translateShortMonth($date);
			
			return $date;
		}
		
		/**
		 * Translate days (format : "l")
		 *
		 * @param $date
		 * Date format&eacute;e in English
		 *
		 * @return string
		 * Date format&eacute;e in French
		 */
		protected function translateDay($date) {
			$en = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
			$fr = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
			foreach($en as $i => $jour) {
				$date = preg_replace("#" . $jour . "#", $fr[ $i ], $date);
			}
			
			return $date;
		}
		
		/**
		 * Translate days (format : "D")
		 *
		 * @param $date
		 * Date format&eacute;e in English
		 *
		 * @return string
		 * Date format&eacute;e en Fench
		 */
		protected function translateShortDay($date) {
			$en = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
			$fr = array('Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim');
			foreach($en as $i => $jour) {
				$date = preg_replace("#" . $jour . "#", $fr[ $i ], $date);
			}
			
			return $date;
		}
		
		/**
		 * Translate month (format : "F")
		 *
		 * @param $date
		 * Date format&eacute;e in English
		 *
		 * @return string
		 * Date format&eacute;e in French
		 */
		protected function translateMonth($date) {
			$en = array(
				'January',
				'February',
				'March',
				'April',
				'May',
				'June',
				'July',
				'August',
				'September',
				'October',
				'November',
				'December'
			);
			$fr = array(
				'Janvier',
				'Février',
				'Mars',
				'Avril',
				'Mai',
				'Juin',
				'Juillet',
				'Août',
				'Septembre',
				'Octobre',
				'Novembre',
				'Décembre'
			);
			foreach($en as $i => $jour) {
				$date = preg_replace("#" . $jour . "#", $fr[ $i ], $date);
			}
			
			return $date;
		}
		
		/**
		 * Traduit le nom des mois cours (format : "M")
		 *
		 * @param $date
		 * La date format&eacute;e en Anglais
		 *
		 * @return string
		 * La date format&eacute;e en Français
		 */
		protected function translateShortMonth($date) {
			$en = array('Feb', 'Apr', 'May', 'Jun', 'Jul', 'Aug');
			$fr = array('Fev', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû');
			foreach($en as $i => $jour) {
				$date = preg_replace("#" . $jour . "#", $fr[ $i ], $date);
			}
			
			return $date;
		}
		
		public static function getTimestampFromString($dateString) {
			if(empty($dateString) || $dateString == "0000-00-00" || $dateString == '0000-00-00 00:00:00' || !self::IsValideDateSql($dateString)) {
				return "";
			}
			$d = new DateTimeFr($dateString);
			
			return $d->getTimestamp();
		}
		
		public static function DateOfTheDay($format = "d/m/Y") {
			$d = new DateTimeFr();
			
			return $d->format("l j F Y");
		}
		
		public static function GetSemestre() {
			$date = new DateTimeFr();
			if(((int) $date->format("m")) > 6) {
				return 2;
			} else {
				return 1;
			}
		}
		
		public static function CountDays($date1, $date2, $absolute = true) {
			if(!is_string($date1)) {
				$datetime1 = clone $date1;
			} else {
				$datetime1 = new DateTimeFr($date1);
			}
			
			if(!is_string($date2)) {
				$datetime2 = clone $date2;
			} else {
				$datetime2 = new DateTimeFr($date2);
			}
			
			
			$interval = $datetime1->diff($datetime2, false);
			
			$days = (int) $interval->format('%R%a');
			
			return ($absolute && $days < 0) ? $days * (- 1) : $days;
		}
		
		public function semestre() {
			if(((int) $this->format("m")) > 6) {
				return 1;
			} else {
				return 2;
			}
		}
		
		public function setOnPreviousMonday() {
			return $this->setOnPreviousDay(1);
		}
		
		public function setOnPreviousDay($dayGoal) {
			$day = (int) $this->format("N");
			if($day === $dayGoal) {
				return true;
			} elseif($day > $dayGoal) {
				$dayToSub = $day - $dayGoal;
			} else {
				$dayToSub = 7 - ($dayGoal - $day);
			}
			$this->sub(new DateInterval("P" . $dayToSub . "D"));
			
			return true;
		}
		
		public function setOnNextWeek($weekGoal) {
			$week         = (int) $this->format("W");
			$weekInterval = new DateInterval("P7D");
			
			if($week == $weekGoal) {
				if((int) $this->format("N") == 1) {
					return true;
				} else {
					$this->sub($weekInterval);
					$this->setOnNextMonday();
					
					return true;
				}
			}
			
			
			$this->setOnNextMonday();
			
			$z = 0;
			while($week !== $weekGoal) {
				
				$this->add($weekInterval);
				$week = (int) $this->format("W");
				
				$z ++;
				if($z > 55) {
					return false;
				}
			}
			
			return true;
		}
		
		public function setOnNextMonday() {
			return $this->setOnNextDay(1);
		}
		
		public function setOnNextDay($dayGoal) {
			$day = (int) $this->format("N");
			if($day !== $dayGoal) {
				$dayToAdd = 8 - $day;
				$this->add(new DateInterval("P" . $dayToAdd . "D"));
			}
			
			return true;
		}
	}

?>
