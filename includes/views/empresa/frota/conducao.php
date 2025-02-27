<?php
require 'includes/frota/frota.class.php';
require 'includes/geo.class.php';

define("TODAY", date("d-m"));

// Carregar a sessão activa se existir
$result = $database->query("SELECT viatura_matricula, kms_iniciais, obs FROM viaturas_sessoes WHERE funcionario_telemovel = '{$_SESSION['user']->telemovel}' AND ativa = true");

if (!$result)
	trigger_error('Query Inválida: ' . $database->error);
else
{
	if ($result->num_rows > 0)
	{
		$session = $result->fetch_assoc();

		$_SESSION['user']->session = 
		[
			'viatura' 		=> $session['viatura_matricula'], 
			'kms' 			=> $session['kms_iniciais'], 
			'gps' 			=> NULL, 
			'observacoes' 	=> $session['obs']
		];
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	// Verificar se está a abrir ou fechar condução
	if(!empty($_SESSION['user']->session)) // Fechar Condução
	{
		if($_POST['viaturaKms'] > $_SESSION['user']->session['kms'])
		{
			if(strpos($_POST['localizacao'], ",")) // Verificar se foi fornecida posição GPS
			{
				$coords = explode(',',$_POST['localizacao']);
				$coords = json_encode(['latitude' => $coords[0], 'longitude' => $coords[1]]); // Criar um JSON com os dados GPS

				$_SESSION['user']->session['kms'] 			= $_POST['viaturaKms'];
				$_SESSION['user']->session['gps'] 			= $coords;
				$_SESSION['user']->session['observacoes'] 	= $_POST['conducaoObservacoes'];

				$session = $_SESSION['user']->session; // Só para ser mais fácil...

				$result = $database->query("
					UPDATE viaturas_sessoes 
					SET data_final = current_timestamp(), kms_finais = '{$session['kms']}', localizacao_final = NULLIF('{$session['gps']}', ''), obs = '{$session['observacoes']}', ativa = false
					WHERE funcionario_telemovel = '{$_SESSION['user']->telemovel}' AND ativa = true"
				);

				if (!$result)
					trigger_error('Query Inválida: ' . $database->error);
				else
					Alerta('Sessão de condução <span class="font-weight-bold">terminada</span> para a viatura <span class="font-weight-bold">' . Viatura::FormatarMatricula($session['viatura']) . '</span>');

				// Destruir a Sessão de Condução
				$_SESSION['user']->session = [];
			}
			else
				Alerta('Tem de ativar o GPS no seu dispositivo antes de poder <span class="font-weight-bold">fechar</span> a sessão!', ALERTA_ERRO, "map-marker-alt");
		}
		else
			Alerta('Não pode fechar com os mesmos ou menos KMs com que iniciou a sessão!', ALERTA_ERRO, $icon = "road");
	}
	else // Abrir Condução
	{
		if(strpos($_POST['localizacao'], ",")) // Verificar se foi fornecida posição GPS
		{
			$coords = explode(',',$_POST['localizacao']);
			$coords = json_encode(['latitude' => $coords[0], 'longitude' => $coords[1]]); // Criar um JSON com os dados GPS

			// Construir o array com os dados de condução na SESSION
			$_SESSION['user']->session = 
			[
				'viatura' 		=> $_POST['conducaoViatura'], 
				'tipo' 			=> $_POST['tipoConducao'], 
				'kms' 			=> $_POST['viaturaKms'], 
				'gps' 			=> $coords, 
				'observacoes' 	=> $_POST['conducaoObservacoes']
			];

			$session = $_SESSION['user']->session; // Só para ser mais fácil...

			$result = $database->query("
				INSERT INTO viaturas_sessoes (viatura_matricula, tipo, funcionario_telemovel, kms_iniciais, localizacao_inicial, obs) 
				VALUES('{$session['viatura']}', '{$session['tipo']}', '{$_SESSION['user']->telemovel}', '{$session['kms']}', NULLIF('{$session['gps']}', ''), '{$session['observacoes']}')
			");

			if (!$result)
				trigger_error('Query Inválida: ' . $database->error);
			else
				Alerta('Sessão de condução <span class="font-weight-bold">iniciada</span> para a viatura <span class="font-weight-bold">' . Viatura::FormatarMatricula($session['viatura']) . '</span>', ALERTA_SUCESSO, "road");
		}
		else
			Alerta('
				Tem de ativar o GPS no seu dispositivo antes de poder <span class="font-weight-bold">abrir</span> a sessão!<br>
				<small class="form-text text-muted">Assim que ativar o GPS carregue na barra da localização para assumir a mesma.</small>', 
				ALERTA_ERRO, $icon = "map-marker-alt"
			);
	}
}

$sessaoAtiva = isset($_SESSION['user']->session['viatura']) ? true : false;
?>
<h1 class="h3 mb-2 text-gray-800">Sessões de Condução</h1>
<div class="alert alert-warning"><span class="font-weight-bolder">Atenção:</span> As sessões são para '<i>Iniciar</i>' e '<i>Fechar</i>' <u>sempre</u> que se começa ou deixa de <u>conduzir</u>.</div>
<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-<?= $sessaoAtiva ? "danger":"success" ?>"><?= $sessaoAtiva ? 'Fecho de Condução - <span class="font-weight-bolder">Viatura '.Viatura::FormatarMatricula($_SESSION['user']->session['viatura']).'</span>' : 'Início de Condução' ?></h6>
	</div>
	<div class="card-body">
		<form action="index.php?ver=empresa&categoria=frota&subcategoria=conducao" method="POST">
			<?php if(!$sessaoAtiva)	{ ?>
			<div class="form-row">
				<div class="form-group col-md-auto">
					<label for="tipoConducao">Tipo de Condução</label>
					<select id="tipoConducao" name="tipoConducao" class="form-control form-control-sm" required>
						<option value="OUTRO">Outro</option>
						<option value="OFICINA">Oficina</option>
						<option value="ABASTECIMENTO">Abastecimento</option>
						<option value="SERVICO" selected>Serviço</option>
					</select>
				</div>
				<div class="col-md-auto">
					<label for="conducaoViatura">Viatura</label>
					<select id="conducaoViatura" name="conducaoViatura" class="form-control form-control-sm" required>
						<option value="" selected>Escolher...</option>
						<?php 
						foreach(Frota::Viaturas() as $matricula => $viatura)
							echo '<option value="'.$matricula.'">'.$viatura["nome"].' '.substr($matricula,2,2).'</option>';
						?>
					</select>
				</div>
			</div>
			<?php } ?>
			<div class="form-group">
				<label for="viaturaKms">KMs <?= $sessaoAtiva ? "Finais":"Iníciais"?></label>					
				<input type="number" id="viaturaKms" name="viaturaKms" class="form-control form-control-sm"<?= $sessaoAtiva ? ' value="'.$_SESSION['user']->session['kms'].'"' : NULL ?>required>
			</div>
			<div class="form-group">
				<label for="conducaoObservações">Observações</label>					
				<textarea id="conducaoObservacoes" name="conducaoObservacoes" class="form-control form-control-sm" rows="4" maxlength="255" placeholder="Qualquer tipo de observação sobre a viatura ou o serviço em si..."><?= $sessaoAtiva ? $_SESSION['user']->session['observacoes'] : NULL ?></textarea>
			</div>
			<div class="form-group">
				<label for="localizacao">Localização</label>					
				<input type="text" id="localizacao" name="localizacao" class="form-control form-control-sm" onclick="getLocation()" required readonly>
			</div>
			<button type="submit" name="formConducao" class="btn btn-<?= $sessaoAtiva ? "danger":"success" ?> btn-icon-split my-1">
				<span class="icon text-white-50">
					<i class="fas fa-road"></i>
				</span>
				<span class="text"><?= $sessaoAtiva ? "Terminar":"Iniciar" ?> Condução</span>
			</button>
		</form>
	</div>
</div>
<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary">Listagem de Sessões de Condução</h6>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table id="sessoes" class="display table table-sm table-borderless table-hover" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th></th>
						<th class="text-left">Dia</th>
						<th class="text-center">Viatura</th>
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
						<th class="text-center">Início</th>
						<th class="text-center">Fim</th>
						<th class="text-right">KMs Percorridos</th>
					</tr>
				</tfoot>
				<tbody>
					<?php
					// This function will only be used in this page. No need to place it anywhere else
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
						SELECT s.id, s.viatura_matricula, s.tipo, v.tipo as viatura_tipo, s.funcionario_telemovel, s.data_inicial, s.kms_iniciais, s.localizacao_inicial, s.data_final, s.kms_finais, s.localizacao_final, s.obs, s.ativa 
						FROM viaturas_sessoes s
						LEFT JOIN utilizadores u ON s.funcionario_telemovel = u.telemovel
						LEFT JOIN viaturas v ON s.viatura_matricula = v.matricula 
						WHERE s.funcionario_telemovel = '{$_SESSION['user']->telemovel}'
						ORDER BY s.ativa DESC, s.data_inicial DESC
						LIMIT 15
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
							echo '<td class="text-center" nowrap><i class="'.Viatura::Icon($session["viatura_tipo"]).'"></i> '.Viatura::FormatarMatricula($session["viatura_matricula"]).'</td>';
							// Start Location
							echo '<td class="text-center" nowrap><a href="' . @($location["start"] ? FormatLocationURL($location["start"]) : '#') . '" target="_blank">'.date('H:i', strtotime($session['data_inicial'])).'<br/><small class="text-xs">(' . @GEO::ObterEnderecoPorCoords($location["start"]) . ')</small></a></td>';
							// Finish Location
							echo '<td class="text-center" nowrap><a href="' . @($location["finish"] ? FormatLocationURL($location["finish"]) : '#') . '" target="_blank">'.date('H:i', strtotime($session['data_final'])).'<br/><small class="text-xs">(' . @GEO::ObterEnderecoPorCoords($location["finish"]) . ')</small></a></td>';
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