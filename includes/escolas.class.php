<?php
class Escolas
{
	public static function Obter()
	{
		global $database;

		$escolas = [];

		$result = $database->query("SELECT id, nome FROM localizacoes WHERE tipo = 1 ORDER BY nome ASC");
		if($result && $result->num_rows)
		{
			while ($escola = $result->fetch_assoc())
				$escolas[] = $escola;
		}

		return $escolas;
	}
}

class Criancas
{
	public static function Obter()
	{
		global $database;

		$criancas = [];

		$result = $database->query("
			SELECT c.id as id, c.nome_primeiro as nome_primeiro, c.nome_ultimo as nome_ultimo, loc.nome AS escola 
			FROM escolas_criancas c
			LEFT JOIN localizacoes loc ON c.escola_id = loc.id
			ORDER BY escola ASC, c.nome_primeiro ASC
			");

		if($result && $result->num_rows)
		{
			while ($crianca = $result->fetch_assoc())
				$criancas[] = $crianca;
		}

		return $criancas;
	}
}
?>