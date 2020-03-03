<?php
require_once 'config.php';

class Frota
{

	function Viaturas()
	{
		global $database;
		$viaturas = [];

		$result = $database->query("SELECT * FROM viaturas ORDER BY nome ASC");
		if($result)
		{
			while ($viatura = $result->fetch_assoc())
			{
				$matricula = $viatura['matricula'];
				unset($viatura['matricula']);

				$viaturas[$matricula] = $viatura;
			}
		}

		return $viaturas;
	}
}

class Viatura
{
	static function RegistoAnterior($matricula, $combustivelTipo, $dataInicial = NULL)
	{
		global $database;

		$registo = ['kms' => '0', 'litros' => '0'];

		if(is_null($dataInicial))
			$dataInicial =  date("Y-m-d");

		$result = $database->query("
								SELECT viatura_kms as kms, combustivel_litros as litros FROM viaturas_abastecimentos 
								WHERE viatura_matricula = '{$matricula}' AND combustivel_tipo = '{$combustivelTipo}' AND abastecimento_data < '{$dataInicial}' 
								ORDER BY abastecimento_data DESC
							");

		if($result && $result->num_rows)
			$registo = $result->fetch_assoc();

		return $registo;
	}

	static function FormatarMatricula($data)
	{
		$matricula = null;

		if (strlen($data) == 6) {
			$matricula = substr_replace($data, '-', 2, 0);
			$matricula = substr_replace($matricula, '-', 5, 0);
		}

		return $matricula;
	}

	static function Icon($tipoViatura)
	{
		$icon = "fas fa-car-crash";
		
		switch ($tipoViatura) {
			case 'MOTA':
				$icon = "fas fa-motorcycle";
				break;
			case 'LIGEIRO':
				$icon = "fas fa-car";
				break;
			case 'LIGEIROPAX':
				$icon = "fas fa-shuttle-van";
				break;
			case 'PESADOPAX':
				$icon = "fas fa-bus-alt";
				break;
		}
		return $icon;
	}
}