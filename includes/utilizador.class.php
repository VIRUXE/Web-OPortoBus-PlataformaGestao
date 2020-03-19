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

	public function Admin()
	{
		if($this->cargo == "DONO" || $this->cargo == "DESENVOLVEDOR")
			return true;

		return false;
	}

	private function HashPIN($pin)
	{
		return hash('sha256', $pin);
	}

	private function DefinirPIN($userPIN)
	{
		global $database;

		$pin = $this->HashPIN($userPIN);

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
	{
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
				$icon = "fas fa-user-shield";
				break;
			case 'MOTORISTA':
				$icon = "fad fa-user-tie";
				break;
			case 'MOTORISTAPESADOS':
				$icon = "fas fa-user-tie";
				break;
			case 'VIGILANTE':
				$icon = "fas fa-user-nurse";
				break;
		}

		return $icon;
	}

	public function ObterMensagens($quantidade = NULL)
	{
		global $database;

		$mensagens = [];

		$result = $database->query("
			SELECT msg.id, msg.data, de.nome_primeiro as nome_primeiro, de.nome_ultimo as nome_ultimo, msg.titulo, msg.lida 
			FROM utilizadores_mensagens msg
			LEFT JOIN utilizadores de ON msg.de_telemovel = de.telemovel
			WHERE msg.para_telemovel = '$this->telemovel'
			ORDER BY msg.data DESC
		");

		if($result && $result->num_rows)
		{
			while ($mensagem = $result->fetch_assoc())
				$mensagens[] = $mensagem;
		}

		// var_dump($mensagens);
		
		return $mensagens;
	}

/*	public function MensagensPorLer()
	{
		global $database;

		$count = 0;

		$result = $database->query("SELECT COUNT(*) as count FROM utilizadores_mensagens WHERE para_telemovel = '$this->telemovel' AND lida = false");

		if($result && $result->num_rows)
			$count = $result->fetch_assoc()['count'];

		return $count;
	}*/

	public static function Alerta($descricao, $utilizadorTelemovel = NULL)
	{
		global $database;

		if(is_null($utilizadorTelemovel))
			$utilizadorTelemovel = $this->telemovel;

		$result = $database->query("INSERT INTO `utilizadores_alertas` (`utilizador_telemovel`, `descricao`, `link`) VALUES ('$utilizadorTelemovel', '$descricao', '#')");

		if($result)
			var_dump($result);
	}

	public function ObterAlertas($quantidade = NULL)
	{
		global $database;

		$alertas = [];

		$result = $database->query("SELECT id, tipo, data, descricao, link, lido FROM utilizadores_alertas WHERE utilizador_telemovel = '$this->telemovel' ORDER BY data DESC");

		if($result && $result->num_rows)
		{
			while ($alerta = $result->fetch_assoc()) 
				$alertas[] = $alerta;
		}

		return $alertas;
	}
}
