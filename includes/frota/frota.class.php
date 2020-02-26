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
	static function KmsAnteriores($matricula, $combustivelTipo, $dataInicial = NULL)
	{
		global $database;

		$kms = 0;

		if(is_null($dataInicial))
			$dataInicial =  date("Y-m-d");

		$result = $database->query("
								SELECT viatura_kms FROM viaturas_abastecimentos 
								WHERE viatura_matricula = '{$matricula}' AND combustivel_tipo = '{$combustivelTipo}' AND abastecimento_data < '{$dataInicial}' 
								ORDER BY abastecimento_data DESC
							");

		if($result->num_rows)
			$kms = $result->fetch_assoc()['viatura_kms'];

		return $kms;
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
}