<?php
$telemovel 	= NULL;
$pin 		= NULL;
$errors 	= [];

function MostrarErros()
{
	foreach ($GLOBALS['errors'] as &$erro)
		echo $erro.'.<br>';
}

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	// Efetuar um sanity check aos dados inseridos
	$telemovel 	= $_POST["inputTelemovel"]; 
	$pin 		= $_POST["inputPIN"];

	// Verificar na base de dados se o número de telemóvel existe
	if(!empty($telemovel))
	{
		if(!empty($pin))
		{
			if(Utilizador::Existe($telemovel))
			{
				$user = new Utilizador($telemovel, $pin);

				if($user->carregado)
				{
					$_SESSION['user'] = $user;
					header('Location: index.php');
				}
				else
					$errors[] = "O PIN que introduziu está incorrecto";
			}
			else
				$errors[] = "O número '$telemovel' não consta na base de dados";
		}
		else
			$errors[] = "Tem de inserir um PIN";
	}
	else
	{
		$errors[] = "Tem de inserir um Número de Telemóvel";

		if(empty($pin))
			$errors[] = "Tem de inserir um PIN";
	}
}

?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<title>OPortoBus - Plataforma de Gestão</title>

	<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link
		href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
		rel="stylesheet">
	<link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-warning">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-xl-4 col-lg-6 col-md-9">
				<div class="card o-hidden border-0 shadow-lg my-5">
					<div class="card-body p-0">
						<div class="row">
							<div class="col-lg-12">
								<div class="p-5">
									<div class="text-center">
										<h1 class="h4 text-gray-900 font-weight-bold mb-4">OPortoBus.pt</h1>
										<h1 class="h6 text-gray-400 font-italic mb-4">Plataforma de Gestão</h1>
									</div>
									<?php
											if(!empty($errors)) 
											{
												echo'	
													<div class="card shadow mb-4">
														<div class="card-header py-3">
															<h6 class="m-0 font-weight-bold text-danger">Erros:</h6>
														</div>
													<div class="card-body">';
													echo MostrarErros();
												echo '</div>
												</div>';
											} 
										?>
									<form id="login" action="index.php" method="POST">
										<div class="form-group">
											<input type="tel" name="inputTelemovel" class="form-control form-control-user" aria-describedby="userHelp" placeholder="Número de Telemóvel" value="<?= $telemovel ? $telemovel : '' ?>" pattern="[9]{1}[1-6]{1}[0-9]{7}" maxlength="9" required>
										</div>
										<div class="form-group">
											<input type="password" id="inputPIN" name="inputPIN" class="form-control form-control-user" title="O PIN tem de ter obrigatoriamente 4 números." placeholder="PIN" pattern="[0-9]{4}" maxlength="4" autocomplete="off" required>
										</div>
										<button id="btnIniciar" type="submit" class="btn btn-warning btn-block">Iniciar Sessão</button>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="vendor/jquery/jquery.min.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
	<script src="js/sb-admin-2.min.js"></script>
	<script type="text/javascript">
		$('#inputPIN').keyup(function () {
			if (this.value.length == 4) {
				$('#btnIniciar').click();
			}
		});
	</script>
</body>
</html>