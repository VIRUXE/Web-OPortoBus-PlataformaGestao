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

		// echo '<pre>' . var_dump($escolas) . '</pre>';

		return $escolas;
	}
}

class Crianças
{
	public static function Obter()
	{

	}
}
?>