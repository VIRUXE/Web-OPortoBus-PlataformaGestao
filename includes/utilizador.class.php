<?php
/**
 * 
 */
class Utilizador
{
	
	/*function __construct(argument)
	{
		# code...
	}*/

	function FormatarNome()
	{
		$nome = NULL;

		if(isset($_SESSION['utilizador']))
			$nome = $_SESSION['utilizador']['nome_primeiro'].' '.$_SESSION['utilizador']['nome_ultimo'];

		return $nome;
	}
}

?>