<?php
require_once 'includes/frota/frota.class.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
	// Form para Adicionar Abastecimento
	if (isset($_POST['adicionarAbastecimento'])) 
	{
		$viatura 		= $_POST['viaturaAbastecida'];
		$combustivel 	= $_POST['combustivelTipo'];
		$kms 			= $_POST['kmsViatura'];
		$litros 		= $_POST['combustivelLitros'];
		$precoLitro		= $_POST['combustivelPreco'];
		$localizacao	= $_POST['localizacao'];
		$responsavel 	= $_SESSION['user']->telemovel;

		// Validar os dados primeiro


		// Enviar para a base de dados
		$result = $database->query("
			INSERT INTO viaturas_abastecimentos (viatura_matricula, abastecimento_localizacao, viatura_kms, combustivel_tipo, combustivel_litros, combustivel_valor, responsavel_telemovel, criador_telemovel)
			VALUES('$viatura', '$localizacao', '$kms', '$combustivel', '$litros', '$precoLitro', '$responsavel', '{$_SESSION['user']->telemovel}')");

		if (!$result)
			trigger_error('Query Inválida: ' . $database->error);
		else
			Alerta('Abastecimento para a viatura <span class="font-weight-bold">' . Viatura::FormatarMatricula($viatura) . '</span> inserido com sucesso.', ALERTA_SUCESSO, "gas-pump");
	}
}
?>
<div class="card shadow mb-4">
	<a href="#adicionarAbastecimento" class="d-block card-header py-3">
		<h6 class="m-0 font-weight-bold text-success">Adicionar Abastecimento</h6>
	</a>
	<div id="adicionarAbastecimento">
		<div class="card-body">
			<form id="adicionarAbastecimento" action="index.php?ver=empresa&categoria=frota&subcategoria=abastecimento" method="POST">
				<div class="form-row">
					<div class="col-md-6">
						<label for="viaturaAbastecida">Viatura</label>
						<select id="viaturaAbastecida" name="viaturaAbastecida" class="form-control" required>
							<option value="" selected>Escolher...</option>
							<?php
							foreach (Frota::Viaturas() as $matricula => $viatura)
								echo '<option value="' . $matricula . '">' . $viatura["nome"] . ' ' . substr($matricula, 2, 2) . '</option>';
							?>
						</select>
					</div>
					<div class="col-md-6">
						<label>Odómetro</label>
						<input name="kmsViatura" type="text" class="form-control" minlength="4" maxlength="6" title="Apenas digitos. Mínimo de 4, máximo de 6." required>
					</div>
				</div>
				<div class="form-row">
					<div class="col-lg-2">
							<label for="combustivelTipo">Combustível</label>
							<select name="combustivelTipo" class="form-control" required>
								<option value="GASOLEO"> Gasóleo</option>
								<option value="GASOLINA"> Gasolina</option>
								<option value="ELETRICO"> Elétrico</option>
								<option value="GPL"> GPL</option>
								<option value="ADBLUE"> AdBlue</option>
							</select>
					</div>
					<div class="col-lg-2">
						<label>Litros</label>
						<input type="number" name="combustivelLitros" class="form-control" placeholder="12.34" step="0.01" onkeypress="return isNumberKey(event,this)" required>
					</div>
					<div class="col-lg-2">
						<label>Preço p/Litro</label><!-- 1.33 para a MaiaTransportes -->
						<input type="number" name="combustivelPreco" class="form-control" placeholder="12.34" step="0.01" onkeypress="return isNumberKey(event,this)" placeholder="1.33" required>
						<small id="emailHelp" class="form-text text-muted">(MaiaTransportes é 1.33€)</small>
					</div>
					<div class="col">
						<label>Localização</label>
						<input name="localizacao" type="text" class="form-control" placeholder="Ex.: Maia Transportes" required>
					</div>
				</div>
				<button type="submit" name="adicionarAbastecimento" class="btn btn-success btn-icon-split my-1">
					<span class="icon text-white-50">
						<i class="fas fa-check"></i>
					</span>
					<span class="text">Inserir Abastecimento</span>
				</button>
			</form>
		</div>
	</div>
</div>