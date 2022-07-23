<?php
require_once 'includes/frota/frota.class.php';
require_once 'includes/geo.class.php';

define("TODAY", date("d-m"));
?>
<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary">Todas as Sessões de Condução</h6>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table id="sessoes" class="display table table-sm table-borderless table-hover" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th></th>
						<th class="text-left">Dia</th>
						<th class="text-center">Viatura</th>
						<th class="text-center">Funcionário</th>
						<th class="text-center">Início</th>
						<th class="text-center">Fim</th>
						<th class="text-right">KMs Percorridos</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th></th>
						<th class="text-left">Dia</th>
						<th class="text-center">Viatura</th>
						<th class="text-center">Funcionário</th>
						<th class="text-center">Início</th>
						<th class="text-center">Fim</th>
						<th class="text-right">KMs Percorridos</th>
					</tr>
				</tfoot>
				<tbody>
					<?php
					function ServicoIcon($servico)
					{
						$icon = "fas fa-question-circle";

						switch ($servico) 
						{
							case 'OFICINA':
								$icon = "fas fa-tools";
								break;
							case 'ABASTECIMENTO':
								$icon = "fas fa-gas-pump";
								break;
							case 'SERVICO':
								$icon = "fas fa-road";
								break;
						}

						return $icon;
					}

					$result = $database->query("
						SELECT s.id, s.viatura_matricula, s.tipo, v.tipo as viatura_tipo, s.funcionario_telemovel, CONCAT(u.nome_primeiro, ' ',SUBSTRING(u.nome_ultimo,1,1), '.') as motorista, s.data_inicial, s.kms_iniciais, s.localizacao_inicial, s.data_final, s.kms_finais, s.localizacao_final, s.obs, s.ativa 
						FROM viaturas_sessoes s
						LEFT JOIN utilizadores u ON s.funcionario_telemovel = u.telemovel
						LEFT JOIN viaturas v ON s.viatura_matricula = v.matricula 
						ORDER BY s.ativa DESC, s.data_inicial DESC
						LIMIT 30
					");

					if (!$result)
						trigger_error('Query Inválida: ' . $database->error);
					else
					{
						while ($session = $result->fetch_assoc()) 
						{
							$foiHoje = false;
							$location = [];
							$address = [];

							if(date('d-m', strtotime($session['data_inicial'])) == TODAY)
								$foiHoje = true;

							if(!is_null($session['localizacao_inicial']) && !is_null($session['localizacao_final'])) {
								$location["start"] 	= json_decode($session['localizacao_inicial'], true);
								$location["finish"] = json_decode($session['localizacao_final'], true);
							}
							
							// Get addresses from coordinatess

							$kmsPercorridos = $session['kms_finais']-$session['kms_iniciais'];

							// Driving Session State
							echo '<tr'.($session['ativa'] ? ($foiHoje ? ' class="table-success font-weight-bold"' : ' class="table-success"') : ($foiHoje ? ' class="font-weight-bold"' : NULL)).'>';
							// Observations and Session Type
							echo '<td nowrap><i style="color: Tomato;" class="fa'.($session['obs'] ? 's' : 'l').' fa-exclamation-circle" title="Observações:" data-toggle="popover" data-placement="top" data-content="'.($session['obs'] ? $session['obs'] : "Sem observações...").'"></i> <i class="'.ServicoIcon($session['tipo']).'"></i></td>';
							// Date
							echo '<td class="text-left" nowrap>'.date('d-m', strtotime($session['data_inicial'])).'</td>';
							// Type of Vehicle
							echo '<td class="text-center" nowrap>'.'<i class="'.Viatura::Icon($session["viatura_tipo"]).'"></i> '.Viatura::FormatarMatricula($session["viatura_matricula"]).'</td>';
							echo '<td class="text-center" nowrap><i class="'.Utilizador::Icon($session["funcionario_telemovel"]).'"></i> '.$session['motorista'].'</td>';
							// Start Location
							echo '<td class="text-center" nowrap><a href="' . ($location["start"] ? FormatLocationURL($location["start"]) : '#') . '" target="_blank">'.date('H:i', strtotime($session['data_inicial'])).'<br/><small class="text-xs">(' . GEO::ObterEnderecoPorCoords($location["start"]) . ')</small></a></td>';
							// Finish Location
							echo '<td class="text-center" nowrap><a href="' . ($location["finish"] ? FormatLocationURL($location["finish"]) : '#') . '" target="_blank">'.date('H:i', strtotime($session['data_final'])).'<br/><small class="text-xs">(' . GEO::ObterEnderecoPorCoords($location["finish"]) . ')</small></a></td>';
							// Distance Traveled
							echo '<td class="text-right" title="Quilometros" data-toggle="popover" data-placement="top" data-content="Iniciais: '.$session['kms_iniciais'].' Finais: '.($kmsPercorridos > 0 ? $session['kms_finais'] : "Desconhecido").'">'.($kmsPercorridos > 0 ? $kmsPercorridos.' <span class="text-xs">KMs</span>' : NULL).'</td>';
							echo '</tr>';
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>