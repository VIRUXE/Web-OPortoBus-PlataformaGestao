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

	// 'OUTRO','VIGILANTE','MOTORISTA','MOTORISTAPESADOS','DONO','DESENVOLVEDOR'

	static function Icon($userTelemovel)
	{
		global $database;
	 
		$icon = "fas fa-user-alien";

		$result = $database->query("SELECT cargo FROM utilizadores WHERE telemovel = '$userTelemovel' LIMIT 1");
		if($result && $result->num_rows > 0)
		{
			switch ($result->fetch_assoc()['cargo'])
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
		}

		return $icon;
	}
}
