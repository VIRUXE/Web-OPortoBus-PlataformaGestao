<?php
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

if($database->connect_error)
	die("ERRO FATAL: " . $database->connect_error);

if(isset($_GET['logout']))
{
	session_destroy();
	header('Location: index.php');
}

$view = $category = $action = null;

if(isset($_GET['ver'])) 
{
	$view = $_GET['ver'];

	if(isset($_GET['categoria']))
		$category = $_GET['categoria'];

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
		case 'clientes':
			require 'includes/views/clientes.php';
			break;
		case 'servicos':
			switch ($action) {
				case 'adicionar':
					require 'includes/views/addservico.php';
					break;
				default:
					require 'includes/views/servicos.php';
					break;
			}
			break;
		case 'frota':
			switch ($category) 
			{
				default:
					require 'includes/views/frota/viaturas.php';
					break;
				case 'abastecimentos':
					require 'includes/views/frota/abastecimentos.php';
					break;
				case 'conducao':
					require 'includes/views/frota/conducao.php';
					break;
			}
			break;
		case 'utilizadores':
			require 'includes/views/utilizadores.php';
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