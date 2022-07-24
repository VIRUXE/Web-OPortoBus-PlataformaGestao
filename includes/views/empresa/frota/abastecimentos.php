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

function CorCombustivel($tipo)
{
	$cor = "pink";//'DIESEL','GASOLINA','ELETRICO','GPL','ADBLUE'

	switch ($tipo) 
	{
		case 'DIESEL':
			$cor = "black";
			break;
		case 'GASOLINA':
			$cor = "green";
			break;
		case 'ADBLUE':
			$cor = "blue";
			break;
	}

	return $cor;
}
?>
<h1 class="h3 mb-2 text-gray-800">Abastecimentos</h1>
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
						<th class="text-right">Odómetro</th>
						<th class="text-center">KMs</th>
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
						<th class="text-right">Odómetro</th>
						<th class="text-center">KMs</th>
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
						SELECT a.viatura_matricula, v.nome as label, v.tipo as tipo, a.abastecimento_data, a.abastecimento_localizacao, a.viatura_kms, CONCAT(UCASE(LEFT(combustivel_tipo, 1)), LCASE(SUBSTRING(combustivel_tipo, 2))) AS combustivel_tipo, a.combustivel_litros, a.combustivel_valor, CONCAT(r.nome_primeiro, '. ',SUBSTRING(r.nome_ultimo,1,1)) AS responsavel, a.responsavel_telemovel, CONCAT(c.nome_primeiro, '. ',SUBSTRING(c.nome_ultimo,1,1)) AS criador, a.criador_telemovel 
						FROM viaturas_abastecimentos a
						LEFT JOIN viaturas v ON a.viatura_matricula = v.matricula 
						LEFT JOIN utilizadores r ON a.responsavel_telemovel = r.telemovel 
						LEFT JOIN utilizadores c ON a.criador_telemovel = c.telemovel
						ORDER BY abastecimento_data DESC
					");

					if (!$result)
						trigger_error('Query Inválida: ' . $database->error, E_USER_ERROR);
					else 
					{
						while ($abastecimento = $result->fetch_assoc())
						{
							$registoAnterior = Viatura::RegistoAnterior($abastecimento["viatura_matricula"], $abastecimento["combustivel_tipo"], $abastecimento["abastecimento_data"]);

							echo var_dump($registoAnterior);

							$kmsTotais          = $abastecimento["viatura_kms"];
							$kmsPercorridos     = $registoAnterior['kms'] ? $kmsTotais - $registoAnterior['kms'] : 0;
							$mediaCons          = $kmsPercorridos ? round(100 / ($kmsPercorridos / $abastecimento["combustivel_litros"]), 2) : "0.0";
							$custoAbastecimento = round($abastecimento["combustivel_valor"] * $abastecimento["combustivel_litros"], 2, PHP_ROUND_HALF_EVEN);
							$custoKM            = $kmsPercorridos ? round($abastecimento["combustivel_litros"] / $kmsPercorridos, 2) : 0;

							echo '<tr>';
							// Date
							echo '<th class="text-left" title="' . date('d-m-Y H:i', strtotime($abastecimento["abastecimento_data"])) . '" nowrap>' . date('d-m', strtotime($abastecimento["abastecimento_data"])) . '</th>';
							// Vehicle
							echo '<td class="text-center" nowrap><i class="' . Viatura::Icon($abastecimento["tipo"]) . '" title="' . $abastecimento["label"] . '"></i> <a href="index.php?ver=frota&categoria=abastecimentos&viatura=' . $abastecimento["viatura_matricula"] . '">' . Viatura::FormatarMatricula($abastecimento["viatura_matricula"]) . '</a></td>';
							// Total Mileage
							echo '<td class="text-right" nowrap>' . $kmsTotais . ' <span class="text-xs">KMs</span></td>';
							// Last top-up
							echo '<td class="text-center" title="' . $registoAnterior['kms'] . 'KMS em '.date('d-m-Y H:i', strtotime($abastecimento["abastecimento_data"])).'" nowrap>' . $kmsPercorridos . ' <span class="text-xs">KMs</span></td>';
							// Fuel Type
							echo '<td class="text-center"><i style="color:'.CorCombustivel(strtoupper($abastecimento["combustivel_tipo"])).'" class="fas fa-gas-pump"></i> '.$abastecimento["combustivel_tipo"].'</td>';
							// Amount of Liters
							echo '<td class="text-center">' . $abastecimento["combustivel_litros"] . 'L</td>';
							// Fuel Cost
							echo '<td class="text-center" title="' . $abastecimento["combustivel_valor"] . '€ ('.$abastecimento["abastecimento_localizacao"].')">' . $custoAbastecimento . '€</td>';
							// Cost per Kilometer
							echo '<td class="text-right text-' . CorMedia($mediaCons) . '" title="'.$custoKM.'€ por KM">' . $mediaCons . 'L</td>';
							// People responsible for the top-up
							echo '<td class="text-right text-gray-800" title="Registado por: ' . $abastecimento["criador"] . '" nowrap><i class="' . $_SESSION['user']->Icon($abastecimento["responsavel_telemovel"]) . '"></i> ' . $abastecimento["responsavel"] . '</td>';
							echo '</tr>';
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>