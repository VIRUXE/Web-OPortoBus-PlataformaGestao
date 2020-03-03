<?php

class Utilizador
{
	const Cargos = ['OUTRO','VIGILANTE','MOTORISTA','MOTORISTAPESADOS','DONO','DESENVOLVEDOR'];

	public 	$carregado 		= false;

	public 	$telemovel 		= NULL;
	public 	$nome 			= ["primeiro" => NULL, "ultimo" => NULL];
	public 	$morada 		= NULL;
	public 	$nib 			= NULL;
	public 	$nif 			= NULL;
	public 	$mail			= NULL;
	public 	$cargo 			= NULL;
	public 	$ativo 			= false;

	public $conducao		= [];

	public function __construct($userTelemovel, $userPIN)
	{
		global $database;

		$pin = $this->HashPIN($userPIN);

		$result = $database->query("SELECT * FROM utilizadores WHERE telemovel = '$userTelemovel' AND (pin IS NULL OR pin = '$pin') LIMIT 1");

		if($result && $result->num_rows)
		{
			$user = $result->fetch_assoc();

			$this->telemovel 		= $user['telemovel'];
			$this->nome['primeiro'] = $user['nome_primeiro'];
			$this->nome['ultimo'] 	= $user['nome_ultimo'];
			$this->morada 			= $user['morada'];
			$this->nib 				= $user['nib'];
			$this->nif 				= $user['nif'];
			$this->mail 			= $user['mail'];
			$this->cargo 			= $user['cargo'];
			$this->ativo 			= $user['ativo'];

			if(is_null($user['pin']))// Se ainda não tiver PIN, então definir com o inserido no form
				$this->DefinirPIN($userPIN);

			$this->carregado = true;
		}
	}

	private function HashPIN($pin)
	{
		return hash('sha256', $pin);
	}

	private function DefinirPIN($userPIN)
	{
		global $database;

		$pin = HashPIN($userPIN);

		$database->query("UPDATE utilizadores SET pin = '$pin' WHERE telemovel = '$this->telemovel'");
	}

	private function PINDefinido()
	{
		if(!is_null($this->pin))
			return true;

		return false;
	}

	public static function Existe($userTelemovel)
	{
		global $database;

		$result = $database->query("SELECT NULL FROM utilizadores WHERE telemovel = '$userTelemovel' LIMIT 1");

		if($result && $result->num_rows)
			return TRUE;

		return false;
	}

	public function NomeFormatado()
	{
		return $this->nome['primeiro'].' '.$this->nome['ultimo'];
	}

	public static function Icon($userTelemovel = NULL)
	{// 'OUTRO','VIGILANTE','MOTORISTA','MOTORISTAPESADOS','DONO','DESENVOLVEDOR'
		$cargo = NULL;
		$icon = "fas fa-user-alien";

		if(!is_null($userTelemovel))
		{
			global $database;

			$result = $database->query("SELECT cargo FROM utilizadores WHERE telemovel = '$userTelemovel' LIMIT 1");

			if($result && $result->num_rows)
				$cargo = $result->fetch_assoc()['cargo'];
		}
		else
			$cargo = $_SESSION['user']->cargo;

		switch ($cargo)
		{
			case 'DESENVOLVEDOR':
				$icon = "fas fa-user-secret";
				break;

			case 'DONO':
				$icon = "fas fa-user-crown";
				break;

			case 'MOTORISTA':
			case 'MOTORISTAPESADOS':
				$icon = "fas fa-user-tie";
				break;
		}

		return $icon;
	}
}
