<?php

class Utilizador
{

	static function FormatarNome()
	{
		$nome = NULL;

		if(isset($_SESSION['utilizador']))
			$nome = $_SESSION['utilizador']['nome_primeiro'].' '.$_SESSION['utilizador']['nome_ultimo'];

		return $nome;
	}

	static function Icon($userTelemovel = NULL)// 'OUTRO','VIGILANTE','MOTORISTA','MOTORISTAPESADOS','DONO','DESENVOLVEDOR'
	{
		$icon = "fas fa-user-alien";
		$cargo = $_SESSION['utilizador']['cargo'];

		if(!is_null($userTelemovel))
		{
			global $database;

			$result = $database->query("SELECT cargo FROM utilizadores WHERE telemovel = '$userTelemovel' LIMIT 1");
				if($result && $result->num_rows)
					$cargo = $result->fetch_assoc()['cargo'];
		}

		switch ($cargo)
		{
			case 'DESENVOLVEDOR':
				$icon = "fas fa-user-secret";
				break;

			case 'DONO':
				$icon = "fas fa-user-crown";
				break;

			case 'MOTORISTAPESADOS':
				$icon = "fas fa-user-tie";
				break;
		}

		return $icon;
	}
}
