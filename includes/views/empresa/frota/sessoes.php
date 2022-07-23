<?php
require_once 'includes/frota/frota.class.php';
require_once 'includes/geo.class.php';
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
						$dataHoje = date("d-m");

						while ($conducao = $result->fetch_assoc()) 
						{
							$foiHoje = false;

							if(date('d-m', strtotime($conducao['data_inicial'])) == $dataHoje)
								$foiHoje = true;

							$locInicial 		= (!is_null($conducao['localizacao_inicial']) 	? json_decode($conducao['localizacao_inicial'], true) : 							NULL);
							$enderecoInicial 	= (!is_null($locInicial) 						? GEO::ObterEnderecoPorCoords($locInicial['latitude'], $locInicial['longitude']) : 	NULL);
							$locFinal 			= (!is_null($conducao['localizacao_final']) 	? json_decode($conducao['localizacao_final'], true) : 								NULL);
							$enderecoFinal		= (!is_null($locFinal) 							? GEO::ObterEnderecoPorCoords($locFinal['latitude'], $locFinal['longitude']) : 		NULL);

							$kmsPercorridos 	= $conducao['kms_finais']-$conducao['kms_iniciais'];

							echo '<tr'.($conducao['ativa'] ? ($foiHoje ? ' class="table-success font-weight-bold"' : ' class="table-success"') : ($foiHoje ? ' class="font-weight-bold"' : NULL)).'>';
							echo '<td nowrap><i style="color: Tomato;" class="fa'.($conducao['obs'] ? 's' : 'l').' fa-exclamation-circle" title="Observações:" data-toggle="popover" data-placement="top" data-content="'.($conducao['obs'] ? $conducao['obs'] : "Sem observações...").'"></i> <i class="'.ServicoIcon($conducao['tipo']).'"></i></td>';
							echo '<td class="text-left" nowrap>'.date('d-m', strtotime($conducao['data_inicial'])).'</td>';
							echo '<td class="text-center" nowrap>'.'<i class="'.Viatura::Icon($conducao["viatura_tipo"]).'"></i> '.Viatura::FormatarMatricula($conducao["viatura_matricula"]).'</td>';
							echo '<td class="text-center" nowrap><i class="'.Utilizador::Icon($conducao["funcionario_telemovel"]).'"></i> '.$conducao['motorista'].'</td>';
							echo '<td class="text-center" nowrap><a href="'.(!is_null($enderecoInicial) ? 'https://www.google.com/maps/search/'.$locInicial['latitude'].','.$locInicial['longitude'].'/' : '#').'" target="_blank">'.date('H:i', strtotime($conducao['data_inicial'])).'<br/><small class="text-xs">('.(!is_null($enderecoInicial) ? $enderecoInicial['rua'].', '.$enderecoInicial['cidade'] : 'Desconhecido').')</small></a></td>';
							echo '<td class="text-center" nowrap>'.(!is_null($enderecoFinal) ? '<a href="https://www.google.com/maps/search/'.$locFinal['latitude'].','.$locFinal['longitude'].'/" target="_blank">'.date('H:i', strtotime($conducao['data_final'])).'<br/><small class="text-xs">('.$enderecoFinal['rua'].', '.$enderecoFinal['cidade'].')</small></a>' : 'Desconhecido').'</td>';
							echo '<td class="text-right" title="Quilometros" data-toggle="popover" data-placement="top" data-content="Iniciais: '.$conducao['kms_iniciais'].' Finais: '.($kmsPercorridos > 0 ? $conducao['kms_finais'] : "Desconhecido").'">'.($kmsPercorridos > 0 ? $kmsPercorridos.' <span class="text-xs">KMs</span>' : 'Desconhecido').'</td>';
							echo '</tr>';
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>