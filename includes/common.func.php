<?php
define("ALERTA_ERRO", 			"danger");
define("ALERTA_AVISO", 			"warning");
define("ALERTA_SUCESSO", 		"success");

define("ALERTA_ICON_SOLID", 	"s");
define("ALERTA_ICON_REGULAR", 	"r");
define("ALERTA_ICON_LIGHT", 	"l");
define("ALERTA_ICON_DUO", 		"d");

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

function Alerta($mensagem, $tipoAlerta = ALERTA_SUCESSO, $icon = 'exclamation-circle', $iconStyle = ALERTA_ICON_SOLID, $alinhamento = "center")
{
	echo '<div class="alert alert-'.$tipoAlerta.' text-'.$alinhamento.'"><i class="fa'.$iconStyle.' fa-'.$icon.'"></i> '.$mensagem.'</div>';
}
?>	