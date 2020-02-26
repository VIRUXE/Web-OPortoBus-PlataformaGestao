<?php
require_once 'config.php';

class Viatura
{
	public $_matricula;
	public $_nome;
	public $_tipo;
	public $_pax;
	public $_cor;
	public $_cadeiras;
	private $_cartao;
	public $abastecimentos = [];

	function __construct($matricula, $nome, $tipo, $pax, $cor, $cadeiras, $cartao)
	{
		$this->_matricula = $matricula;
		$this->_nome = $nome;
		$this->_tipo = $tipo;
		$this->_pax = $pax;
		$this->_cor = $cor;
		$this->_cadeiras = $cadeiras;
		$this->_cartao = $cartao;
	}

	function UsaCartao()
	{
		if($cartao == "S")
			return true;

		return false;
	}

	function AdicionarAbastecimento()
	{

	}

	function FormatarMatricula($data)
	{
	    $matricula = null;
	    
	    if (strlen($data) == 6) {
	        $matricula = substr_replace($data, '-', 2, 0);
	        $matricula = substr_replace($matricula, '-', 5, 0);
	    }
	    
	    return $matricula;
	}

	function ObterKmsAnteriores($matricula)
	{
		require_once 'config.php';

		$database = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
	 
	if($database === false)
	    die("ERRO: Impossível estabelecer ligação com a base de dados. " . $database->connect_error);

		$kms = null;

		$result = $database->query("SELECT viatura_kms as kms FROM viaturas_abastecimentos WHERE viatura_matricula LIKE '$matricula' ORDER BY DATA DESC LIMIT 1,1");
		if (!$result)
		    trigger_error('Query Inválida: ' . $database->error);
		else
		{
			if ($result->num_rows > 0)
			{
				$row = $result->fetch_assoc();
				$kms = $row['kms'];
			}
		}

		return $kms;
	}
}

class Abastecimento
{
	function __construct()
	{
		
	}
}
?>