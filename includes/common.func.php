<?php
	function timeago($date) 
	{
	   $timestamp = strtotime($date);	
	   $currentTime = time();
	   
	   $strTime = array("segundo", "minutos", "hora", "dia", "mese", "ano");
	   $length = array("60","60","24","30","12","10");

	   if($currentTime >= $timestamp) 
	   {
			$diff = time()-$timestamp;

			for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) 
				$diff = $diff / $length[$i];

			$diff = round($diff);
			return $diff . " " . $strTime[$i] . "(s) atrás";
	   }
	}
?>	