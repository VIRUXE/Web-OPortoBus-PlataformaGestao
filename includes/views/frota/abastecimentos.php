<?php
require_once 'includes/frota/frota.class.php';
require_once 'includes/utilizador.class.php';

function CorMedia($media)
{
	$cor = "success";

	if ($media >= 8.0)
		$cor = "warning";

	if ($media >= 11.0)
		$cor = "danger";

	return $cor;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Form para Adicionar Abastecimento
	if (isset($_POST['adicionarAbastecimento'])) {
		$viatura 		= $_POST['viaturaAbastecida'];
		$combustivel 	= $_POST['combustivelTipo'];
		$litros 		= $_POST['combustivelLitros'];
		$kms 			= $_POST['kmsViatura'];

		// Validar os dados primeiro


		// Enviar para a base de dados
		$result = $database->query("
			INSERT INTO viaturas_abastecimentos (viatura_matricula, viatura_kms, combustivel_tipo, combustivel_litros, responsavel_telemovel, criador_telemovel)
			VALUES('$viatura', '$kms', '$combustivel', '$litros', '{$_SESSION['utilizador']['telemovel']}', '{$_SESSION['utilizador']['telemovel']}')");

		if (!$result)
			trigger_error('Query Inválida: ' . $database->error);
		else
			echo '<div class="alert alert-success text-center"><i class="fas fa-gas-pump"></i> O abastecimento para a viatura <span class="font-weight-bold">' . Viatura::FormatarMatricula($viatura) . '</span> foi inserido com sucesso.</div>';
	}
}
?>
<h1 class="h3 mb-2 text-gray-800">Abastecimentos</h1>

<!-- Adicionar Abastecimento -->
<div class="card shadow mb-4">
	<a href="#adicionarAbastecimento" class="d-block card-header py-3 collapsed" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="adicionarAbastecimento">
		<h6 class="m-0 font-weight-bold text-success">Adicionar Abastecimento</h6>
	</a>
	<div class="collapse" id="adicionarAbastecimento">
		<div class="card-body">
			<form id="adicionarAbastecimento" class="form-horizontal" action="index.php?ver=frota&categoria=abastecimentos" method="POST">
				<label class="my-1 mr-2" for="viaturaAbastecida">Viatura</label>
				<select id="viaturaAbastecida" name="viaturaAbastecida" class="form-control" required>
					<option value="" selected>Escolher...</option>
					<?php
					foreach (Frota::Viaturas() as $matricula => $viatura)
						echo '<option value="' . $matricula . '">' . $viatura["nome"] . ' ' . substr($matricula, 2, 2) . '</option>';
					?>
				</select>
				<label class="my-1 mr-2" for="combustivelTipo">Combustível</label>
				<select name="combustivelTipo" class="form-control" required>
					<option value="GASOLEO"> Gasóleo</option>
					<option value="GASOLINA"> Gasolina</option>
					<option value="ELETRICO"> Elétrico</option>
					<option value="GPL"> GPL</option>
					<option value="ADBLUE"> AdBlue</option>
				</select>
				<label>KMs Totais</label>
				<input name="kmsViatura" type="text" class="form-control" minlength="4" maxlength="6" title="Apenas digitos. Mínimo de 4, máximo de 6." required>

				<label>Litros</label>
				<input type="number" name="combustivelLitros" class="form-control" placeholder="12.34" step="0.01" onkeypress="return isNumberKey(event,this)" required>

				<label>Preço p/Litro</label>
				<input type="number" name="combustivelPreco" class="form-control" placeholder="12.34" step="0.01" onkeypress="return isNumberKey(event,this)" required>

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
<!-- Lista de Abastecimentos -->
<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary">Lista de Abastecimentos</h6>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-sm table-borderless table-hover" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Data</th>
						<th>Viatura</th>
						<th>KMs Totais</th>
						<th>KMs Percorridos</th>
						<th>Combustível</th>
						<th>Litros</th>
						<th>Preço p/Litro</th>
						<th>L/100KM</th>
						<th>Responsável</th>
						<th>Criador</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Data</th>
						<th>Viatura</th>
						<th>KMs Totais</th>
						<th>KMs Percorridos</th>
						<th>Combustível</th>
						<th>Litros</th>
						<th>Preço p/Litro</th>
						<th>L/100KM</th>
						<th>Responsável</th>
						<th>Criador</th>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$result = $database->query("
									SELECT viatura_matricula, viaturas.nome as label, viaturas.tipo as tipo, abastecimento_data, viatura_kms, CONCAT(UCASE(LEFT(combustivel_tipo, 1)), LCASE(SUBSTRING(combustivel_tipo, 2))) AS combustivel_tipo, combustivel_litros, combustivel_valor, CONCAT(utilizadores.nome_primeiro, '. ',SUBSTRING(utilizadores.nome_ultimo,1,1)) AS responsavel, responsavel_telemovel, CONCAT(utilizadores.nome_primeiro, '. ',SUBSTRING(utilizadores.nome_ultimo,1,1)) AS criador, criador_telemovel FROM viaturas_abastecimentos 
									LEFT JOIN viaturas ON viaturas_abastecimentos.viatura_matricula = viaturas.matricula 
									LEFT JOIN utilizadores ON viaturas_abastecimentos.responsavel_telemovel AND viaturas_abastecimentos.criador_telemovel = utilizadores.telemovel
									ORDER BY abastecimento_data DESC
								");

					if (!$result)
						trigger_error('Query Inválida: ' . $database->error);
					else {
						while ($abast = $result->fetch_assoc()) {
							$kmsAnteriores = Viatura::KmsAnteriores($abast["viatura_matricula"], $abast["combustivel_tipo"], $abast["abastecimento_data"]);

							$kmsTotais = $abast["viatura_kms"];
							$kmsPercorridos = $kmsAnteriores ? $kmsTotais - $kmsAnteriores : 0;
							$mediaCons = $kmsPercorridos ? round(100 / ($kmsPercorridos / $abast["combustivel_litros"]), 2) : "0.0";

							echo '<tr>';
							echo '<th title="' . date('d-m-Y H:i', strtotime($abast["abastecimento_data"])) . '" nowrap>' . date('d-m', strtotime($abast["abastecimento_data"])) . '</th>';
							echo '<td nowrap><i class="fas fa-' . ($abast["tipo"] == "LIGEIRO" ? "car" : "bus") . '"></i> <a href="index.php?ver=frota&categoria=abastecimentos&viatura=' . $abast["viatura_matricula"] . '" title="' . $abast["label"] . '">' . Viatura::FormatarMatricula($abast["viatura_matricula"]) . '</a></td>';
							echo '<td class="text-right">' . $kmsTotais . '</td>';
							echo '<td class="text-center" title="' . $kmsAnteriores . '">' . $kmsPercorridos . '</td>';
							echo '<td>' . $abast["combustivel_tipo"] . '</td>';
							echo '<td class="text-center">' . $abast["combustivel_litros"] . '</td>';
							echo '<td class="text-center">' . $abast["combustivel_valor"] . '€</td>';
							echo '<td class="text-' . CorMedia($mediaCons) . '">' . $mediaCons . 'L</td>';
							echo '<td title="'.$abast["responsavel_telemovel"].'" nowrap><i class="' . Utilizador::Icon($abast["responsavel_telemovel"]) . '"></i> ' . $abast["responsavel"] . '</td>';
							echo '<td title="'.$abast["criador_telemovel"].'" nowrap><i class="' . Utilizador::Icon($abast["criador_telemovel"]) . '"></i> ' . $abast["criador"] . '</td>';
							echo '</tr>';
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>