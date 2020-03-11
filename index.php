<?php
define('PATH_VIEWS', "includes/views/");

if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") 
{
	$location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: ' . $location);
	exit;
}
require 'includes/utilizador.class.php';

session_start();

include_once 'config.php';

$database = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$database->set_charset("utf8");
$database->query("SET time_zone='+00:00'");

if($database->connect_error)
	die("ERRO FATAL: " . $database->connect_error);

if(isset($_GET['logout']))
{
	session_destroy();
	header('Location: index.php');
}

// Inicializar vars de URL
$view = $category = $subcategory = $action = null;

if(isset($_GET['ver'])) 
{
	$view = $_GET['ver'];

	if(isset($_GET['categoria']))
	{
		$category = $_GET['categoria'];

		if(isset($_GET['subcategoria']))
			$subcategory = $_GET['subcategoria'];
	}

	if(isset($_GET['accao'])) 
		$action = $_GET['accao'];
}

if(isset($_SESSION['user'])) // Carregar o dashboard se o user já estiver carregado
{
	require '_header.php';
	require '_navbar.php';
?>
	<div id="content-wrapper" class="d-flex flex-column">
		<div id="content">
<?php
	require '_topbar.php';

	switch ($view) {

		case 'transporteescolar':
			switch ($category) 
			{
				case 'criancas':
					switch ($subcategory) 
					{
						case 'horarios':
							require PATH_VIEWS.'transporteescolar/horarios.php';
							break;
						
						default:
							require PATH_VIEWS.'transporteescolar/criancas.php';
							break;
					}
					break;
				case 'rotas':
					require PATH_VIEWS.'transporteescolar/rotas.php';
					break;
				default:

					break;
			}
			break;
		case 'empresa':
			switch ($category) 
			{
				case 'clientes':
					require PATH_VIEWS.'empresa/clientes.php';
					break;
				case 'servicos':
					switch ($action) 
					{
						case 'adicionar':
							require PATH_VIEWS.'empresa/addservico.php';
							break;
						default:
							require PATH_VIEWS.'empresa/servicos.php';
							break;
					}
					break;
				case 'frota':
				{
					switch ($subcategory)
					{
						case 'abastecimentos':
							require PATH_VIEWS.'empresa/frota/abastecimentos.php';
							break;
						case 'conducao':
							require PATH_VIEWS.'empresa/frota/conducao.php';
							break;
						case 'viaturas':
							require PATH_VIEWS.'empresa/frota/viaturas.php';
							break;
						default:
							require PATH_VIEWS.'empresa/frota/frota.php';
							break;
					}
					break;
				}
				case 'utilizadores':
					require PATH_VIEWS.'empresa/utilizadores.php';
					break;
				default:
					require PATH_VIEWS.'empresa/empresa.php';
					break;
			}
			break;
		
		default: // Carrega a vista de resumo, se não tiver escolhido, ou não existir uma página/categoria
			echo 'Escolha uma opção da barra de navegação...';
			// require 'includes/views/resumo.php';
			break;
	}
	require '_footer.php';
} 
else
	require 'login.php';

$database->close();
?>