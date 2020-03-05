<?php
require_once 'includes/escolas.class.php';

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$primeiroNome 	= $_POST['primeiroNome'];
	$ultimoNome 	= (!empty($_POST['ultimoNome']) ? $_POST['ultimoNome'] : NULL);
	$escolaID 		= $_POST['escola'];
	$obs 			= $_POST['obs'];

	if($database->query("INSERT INTO escolas_criancas (nome_primeiro, nome_ultimo, escola_id, obs) VALUES ('$primeiroNome', '$ultimoNome', '$escolaID', '$obs')"))
	    echo "Inserido com sucesso";
	else
	    echo "Erro: ".$database->error;

}
?>
<div class="card o-hidden border-0 shadow-lg my-5">
	<div class="card-body p-0">
		<div class="row">
			<div class="col-lg-12">
				<div class="p-5">
					<div class="text-center">
						<h1 class="h4 text-gray-900 mb-4">Inserir Criança</h1>
					</div>
					<form method="POST">
						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<input type="text" class="form-control" name="primeiroNome" placeholder="Primeiro Nome" required>
							</div>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="ultimoNome" placeholder="Último Nome">
							</div>
						</div>
						<div class="form-group">
							<legend>Escola</legend>
							<select class="form-control" name="escola" required>
								<option value="" selected>Escolher...</option>
								<?php
								foreach(Escolas::Obter() as $escola)
									echo '<option value="'.$escola['id'].'">'.$escola['nome'].'</option>';
								?>
							</select>
						</div>
						<div class="form-group">
							<legend>Observações</legend>
							<textarea class="form-control" name="obs" placeholder="Qualquer observação sobre a criança..."></textarea>
						</div>
						<button class="btn btn-success">Inserir Criança</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- SELECT e.id, nome_primeiro, nome_ultimo, loc.nome AS escola 
FROM escolas_criancas e
LEFT JOIN localizacoes loc ON e.escola_id = loc.id -->