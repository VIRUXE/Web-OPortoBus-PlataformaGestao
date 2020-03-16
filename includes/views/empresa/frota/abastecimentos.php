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
	$cor = "pink";//'GASOLEO','GASOLINA','ELETRICO','GPL','ADBLUE'

	switch ($tipo) 
	{
		case 'GASOLEO':
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
						while ($abast = $result->fetch_assoc())
						{
							$registoAnterior = Viatura::RegistoAnterior($abast["viatura_matricula"], $abast["combustivel_tipo"], $abast["abastecimento_data"]);

							$kmsTotais = $abast["viatura_kms"];
							$kmsPercorridos = $registoAnterior['kms'] ? $kmsTotais - $registoAnterior['kms'] : 0;
							$mediaCons = $kmsPercorridos ? round(100 / ($kmsPercorridos / $abast["combustivel_litros"]), 2) : "0.0";
							$custoAbastecimento = round($abast["combustivel_valor"] * $abast["combustivel_litros"], 2, PHP_ROUND_HALF_EVEN);
							$custoKM = $kmsPercorridos ? round($abast["combustivel_litros"] / $kmsPercorridos, 2) : 0;

							echo '<tr>';
							echo '<th class="text-left" title="' . date('d-m-Y H:i', strtotime($abast["abastecimento_data"])) . '" nowrap>' . date('d-m', strtotime($abast["abastecimento_data"])) . '</th>';
							echo '<td class="text-center" nowrap><i class="' . Viatura::Icon($abast["tipo"]) . '" title="' . $abast["label"] . '"></i> <a href="index.php?ver=frota&categoria=abastecimentos&viatura=' . $abast["viatura_matricula"] . '">' . Viatura::FormatarMatricula($abast["viatura_matricula"]) . '</a></td>';
							echo '<td class="text-right" nowrap>' . $kmsTotais . ' <span class="text-xs">KMs</span></td>';
							echo '<td class="text-center" title="' . $registoAnterior['kms'] . 'KMS em '.date('d-m-Y H:i', strtotime($abast["abastecimento_data"])).'" nowrap>' . $kmsPercorridos . ' <span class="text-xs">KMs</span></td>';
							echo '<td class="text-center"><i style="color:'.CorCombustivel(strtoupper($abast["combustivel_tipo"])).'" class="fas fa-gas-pump"></i> '.$abast["combustivel_tipo"].'</td>';
							echo '<td class="text-center">' . $abast["combustivel_litros"] . 'L</td>';
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