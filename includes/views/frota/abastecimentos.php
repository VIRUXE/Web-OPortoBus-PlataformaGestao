<?php
require_once 'includes/frota/frota.class.php';

function CorMedia($media)
{
	$cor = "success";

	if ($media >= 10.0)
		$cor = "warning";

	if ($media >= 13.0)
		$cor = "danger";

	return $cor;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Form para Adicionar Abastecimento
	if (isset($_POST['adicionarAbastecimento'])) {
		$viatura 		= $_POST['viaturaAbastecida'];
		$combustivel 	= $_POST['combustivelTipo'];
		$kms 			= $_POST['kmsViatura'];
		$litros 		= $_POST['combustivelLitros'];
		$precoLitro		= $_POST['combustivelPreco'];
		$localizacao	= $_POST['localizacao'];
		$responsavel 	= $_SESSION['utilizador']['telemovel'];

		// Validar os dados primeiro


		// Enviar para a base de dados
		$result = $database->query("
			INSERT INTO viaturas_abastecimentos (viatura_matricula, abastecimento_localizacao, viatura_kms, combustivel_tipo, combustivel_litros, combustivel_valor, responsavel_telemovel, criador_telemovel)
			VALUES('$viatura', '$localizacao', '$kms', '$combustivel', '$litros', '$precoLitro', '$responsavel', '{$_SESSION['utilizador']['telemovel']}')");

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

				<label>Preço p/Litro</label><!-- 1.33 para a MaiaTransportes -->
				<input type="number" name="combustivelPreco" class="form-control" placeholder="12.34" step="0.01" onkeypress="return isNumberKey(event,this)" placeholder="1.33" required>
				<small id="emailHelp" class="form-text text-muted">O preço por defeito para a MaiaTransportes é de 1.33€</small>

				<label>Localização</label>
				<input name="localizacao" type="text" class="form-control" placeholder="Ex.: Maia Transportes" required>

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
<?php if($_SESSION['user']->cargo == 'DONO' || $_SESSION['user']->cargo == 'DESENVOLVEDOR') { ?>
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
						<th class="text-left">Data</th>
						<th class="text-center">Viatura</th>
						<th class="text-right">KMs Totais</th>
						<th class="text-center">KMs Percorridos</th>
						<th class="text-center">Combustível</th>
						<th class="text-center">Litros</th>
						<th class="text-center">Custo</th>
						<th class="text-right">L/100KM</th>
						<th class="text-right">Responsável</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th class="text-left">Data</th>
						<th class="text-center">Viatura</th>
						<th class="text-right">KMs Totais</th>
						<th class="text-center">KMs Percorridos</th>
						<th class="text-center">Combustível</th>
						<th class="text-center">Litros</th>
						<th class="text-center">Custo</th>
						<th class="text-right">L/100KM</th>
						<th class="text-right">Responsável</th>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$result = $database->query("
									SELECT viatura_matricula, v.nome as label, v.tipo as tipo, abastecimento_data, abastecimento_localizacao, viatura_kms, CONCAT(UCASE(LEFT(combustivel_tipo, 1)), LCASE(SUBSTRING(combustivel_tipo, 2))) AS combustivel_tipo, combustivel_litros, combustivel_valor, CONCAT(r.nome_primeiro, '. ',SUBSTRING(r.nome_ultimo,1,1)) AS responsavel, responsavel_telemovel, CONCAT(c.nome_primeiro, '. ',SUBSTRING(c.nome_ultimo,1,1)) AS criador, criador_telemovel FROM viaturas_abastecimentos 
									LEFT JOIN viaturas v ON viaturas_abastecimentos.viatura_matricula = v.matricula 
									LEFT JOIN utilizadores r ON viaturas_abastecimentos.responsavel_telemovel = r.telemovel 
									LEFT JOIN utilizadores c ON viaturas_abastecimentos.criador_telemovel = c.telemovel
									ORDER BY abastecimento_data DESC
								");

					if (!$result)
						trigger_error('Query Inválida: ' . $database->error);
					else {
						while ($abast = $result->fetch_assoc()) {
							$registoAnterior = Viatura::RegistoAnterior($abast["viatura_matricula"], $abast["combustivel_tipo"], $abast["abastecimento_data"]);

							$kmsTotais = $abast["viatura_kms"];
							$kmsPercorridos = $registoAnterior['kms'] ? $kmsTotais - $registoAnterior['kms'] : 0;
							$mediaCons = $kmsPercorridos ? round(100 / ($kmsPercorridos / $abast["combustivel_litros"]), 2) : "0.0";
							$custoAbastecimento = round($abast["combustivel_valor"] * $abast["combustivel_litros"], 2, PHP_ROUND_HALF_EVEN);
							$custoKM = $kmsPercorridos ? round($abast["combustivel_litros"] / $kmsPercorridos, 2) : 0;

							echo '<tr>';
							echo '<th class="text-left" title="' . date('d-m-Y H:i', strtotime($abast["abastecimento_data"])) . '" nowrap>' . date('d-m', strtotime($abast["abastecimento_data"])) . '</th>';
							echo '<td class="text-center" nowrap><i class="' . Viatura::Icon($abast["tipo"]) . '" title="' . $abast["label"] . '"></i> <a href="index.php?ver=frota&categoria=abastecimentos&viatura=' . $abast["viatura_matricula"] . '">' . Viatura::FormatarMatricula($abast["viatura_matricula"]) . '</a></td>';
							echo '<td class="text-right">' . $kmsTotais . '</td>';
							echo '<td class="text-center" title="' . $registoAnterior['kms'] . 'KMS em '.date('d-m-Y H:i', strtotime($abast["abastecimento_data"])).'">' . $kmsPercorridos . '</td>';
							echo '<td class="text-center">' . $abast["combustivel_tipo"] . '</td>';
							echo '<td class="text-center">' . $abast["combustivel_litros"] . '</td>';
							echo '<td class="text-center" title="' . $abast["combustivel_valor"] . '€ ('.$abast["abastecimento_localizacao"].')">' . $custoAbastecimento . '€</td>';
							echo '<td class="text-right text-' . CorMedia($mediaCons) . '" title="'.$custoKM.'€ por KM">' . $mediaCons . 'L</td>';
							echo '<td class="text-center text-gray-800" title="Registado por: ' . $abast["criador"] . '" nowrap><i class="' . $_SESSION['user']->Icon($abast["responsavel_telemovel"]) . '"></i> ' . $abast["responsavel"] . '</td>';
							echo '</tr>';
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php } ?>