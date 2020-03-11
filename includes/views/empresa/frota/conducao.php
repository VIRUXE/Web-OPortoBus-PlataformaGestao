<?php
require 'includes/frota/frota.class.php';
require 'includes/geo.class.php';

// Carregar a sessão activa se existir
$result = $database->query("SELECT viatura_matricula, kms_iniciais, obs FROM viaturas_sessoes WHERE funcionario_telemovel = '{$_SESSION['user']->telemovel}' AND ativa = true");

if (!$result)
	trigger_error('Query Inválida: ' . $database->error);
else
{
	if ($result->num_rows > 0)
	{
		$conducao = $result->fetch_assoc();

		$_SESSION['user']->conducao = 
		[
			'viatura' 		=> $conducao['viatura_matricula'], 
			'kms' 			=> $conducao['kms_iniciais'], 
			'gps' 			=> NULL, 
			'observacoes' 	=> $conducao['obs']
		];
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	// Verificar se está a abrir ou fechar condução
	if(!empty($_SESSION['user']->conducao)) // Fechar Condução
	{
		if($_POST['viaturaKms'] != $_SESSION['user']->conducao['kms'])
		{
			$coords = explode(',',$_POST['localizacao']); // Separar por latitude e longitude

			$_SESSION['user']->conducao['kms'] 			= $_POST['viaturaKms'];
			$_SESSION['user']->conducao['gps'] 			= ['latitude' => $coords[0], 'longitude' => $coords[1]];
			$_SESSION['user']->conducao['observacoes'] 	= $_POST['conducaoObservacoes'];

			$sessao = $_SESSION['user']->conducao; // Só para ser mais fácil...

			$result = $database->query("
				UPDATE viaturas_sessoes 
				SET data_final = current_timestamp(), kms_finais = '{$sessao['kms']}', localizacao_final = '".json_encode($sessao['gps'])."', obs = '{$sessao['observacoes']}', ativa = false
				WHERE funcionario_telemovel = '{$_SESSION['user']->telemovel}' AND ativa = true"
			);

			if (!$result)
				trigger_error('Query Inválida: ' . $database->error);
			else
				echo '
					<div class="alert alert-success text-center">
					<i class="fas fa-road"></i> Sessão de condução terminada para a viatura <span class="font-weight-bold">' . Viatura::FormatarMatricula($sessao['viatura']) . '</span>
					</div>
				';

			// Destruir a Sessão de Condução
			$_SESSION['user']->conducao = [];
		}
		else
			echo '
				<div class="alert alert-danger text-center">
				<i class="fas fa-road"></i> Não pode fechar com os mesmos KMs com que iniciou a sessão!
				</div>
			';
	}
	else // Abrir Condução
	{
		$coords = explode(',',$_POST['localizacao']); // Separar por latitude e longitude

		// Construir o array com os dados de condução na SESSION
		$_SESSION['user']->conducao = 
		[
			'viatura' 		=> $_POST['conducaoViatura'], 
			'tipo' 			=> $_POST['tipoConducao'], 
			'kms' 			=> $_POST['viaturaKms'], 
			'gps' 			=> ['latitude' => $coords[0], 'longitude' => $coords[1]], 
			'observacoes' 	=> $_POST['conducaoObservacoes']
		];

		$sessao = $_SESSION['user']->conducao; // Só para ser mais fácil...

		$result = $database->query("
			INSERT INTO viaturas_sessoes (viatura_matricula, tipo, funcionario_telemovel, kms_iniciais, localizacao_inicial, obs) 
			VALUES('{$sessao['viatura']}', '{$sessao['tipo']}', '{$_SESSION['user']->telemovel}', '{$sessao['kms']}', '".json_encode($sessao['gps'])."', '{$sessao['observacoes']}')"
		);

		if (!$result)
			trigger_error('Query Inválida: ' . $database->error);
		else
		{
			$sessaoID = $database->insert_id;
			echo '<div class="alert alert-success text-center"><i class="fas fa-road"></i> Sessão de condução iniciada para a viatura <span class="font-weight-bold">' . Viatura::FormatarMatricula($sessao['viatura']) . '</span> com ID '.$sessaoID.'</div>';
		}
	}
}

$sessaoAtiva = isset($_SESSION['user']->conducao['viatura']) ? true : false;
?>
			<h1 class="h3 mb-2 text-gray-800">Sessões de Condução</h1>
			<div class="alert alert-warning"><span class="font-weight-bolder">Atenção:</span> As sessões são para '<i>Iniciar</i>' e '<i>Fechar</i>' <u>sempre</u> que se está ou deixa de <u>trabalhar</u>.</div>
			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-<?= $sessaoAtiva ? "danger":"success" ?>"><?= $sessaoAtiva ? 'Fecho de Condução - <span class="font-weight-bolder">Viatura '.Viatura::FormatarMatricula($_SESSION['user']->conducao['viatura']).'</span>' : 'Início de Condução' ?></h6>
				</div>
				<div class="card-body">
					<form class="form-horizontal" action="index.php?ver=empresa&categoria=frota&subcategoria=conducao" method="POST">
						<?php
						if(!$sessaoAtiva)
						{
							echo '
								<div class="row">
									<div class="form-group col-lg-6">
										<legend for="conducaoViatura">Viatura</legend>
										<select id="conducaoViatura" name="conducaoViatura" class="form-control form-control-sm" required>
											<option value="" selected>Escolher...</option>
											';
									foreach(Frota::Viaturas() as $matricula => $viatura)
										echo '<option value="'.$matricula.'">'.$viatura["nome"].' '.substr($matricula,2,2).'</option>';
									echo '
										</select>
									</div>
									<div class="form-group col-lg-6">
										<legend for="tipoConducao">Tipo de Condução</legend>
										<select id="tipoConducao" name="tipoConducao" class="form-control form-control-sm" required>
											<option value="OUTRO">Outro</option>
											<option value="OFICINA">Oficina</option>
											<option value="ABASTECIMENTO">Abastecimento</option>
											<option value="SERVICO" selected>Serviço</option>
										</select>
									</div>
								</div>
								';
						}
						?>
						<div class="row">
							<div class="form-group col-lg-6">
								<legend for="localizacao">Localização</legend>					
								<input type="text" id="localizacao" name="localizacao" class="form-control form-control-sm" onclick="getLocation()" required readonly>
							</div>
							<div class="form-group col-lg-6">
								<legend for="viaturaKms">KMs de <?= $sessaoAtiva ? "Fecho":"Abertura"?></legend>					
								<input type="number" id="viaturaKms" name="viaturaKms" class="form-control form-control-sm"<?= $sessaoAtiva ? ' value="'.$_SESSION['user']->conducao['kms'].'"' : NULL ?>required>
							</div>
						</div>
						<div class="form-group">
							<div class="col-lg-6"></div>
							<legend for="conducaoObservações">Observações</legend>					
							<textarea id="conducaoObservacoes" name="conducaoObservacoes" class="form-control form-control-sm" rows="4" maxlength="255" placeholder="Qualquer tipo de observação sobre a viatura ou o serviço em si..."><?= $sessaoAtiva ? $_SESSION['user']->conducao['observacoes'] : NULL ?></textarea>
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
<?php if($_SESSION['user']->Admin()) { ?>			
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
									SELECT id, viatura_matricula, s.tipo, v.tipo as viatura_tipo, funcionario_telemovel, CONCAT(u.nome_primeiro, ' ',SUBSTRING(u.nome_ultimo,1,1), '.') as motorista, data_inicial, kms_iniciais, localizacao_inicial, data_final, kms_finais, localizacao_final, obs, ativa 
									FROM viaturas_sessoes s
									LEFT JOIN utilizadores u ON s.funcionario_telemovel = u.telemovel
									LEFT JOIN viaturas v ON s.viatura_matricula = v.matricula 
									ORDER BY ativa DESC, data_inicial DESC
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


							$locInicial 		= json_decode($conducao['localizacao_inicial'], true);
							$enderecoInicial 	= GEO::ObterEnderecoPorCoords($locInicial['latitude'], $locInicial['longitude']);
							$locFinal 			= json_decode($conducao['localizacao_final'], true);
							$enderecoFinal		= GEO::ObterEnderecoPorCoords($locFinal['latitude'], $locFinal['longitude']);
							$kmsPercorridos 	= $conducao['kms_finais']-$conducao['kms_iniciais'];

							echo '<tr'.($conducao['ativa'] ? ($foiHoje ? ' class="table-success font-weight-bold"' : ' class="table-success"') : ($foiHoje ? ' class="font-weight-bold"' : NULL)).'>';
							echo '<td><i style="color: Tomato;" class="fa'.($conducao['obs'] ? 's' : 'l').' fa-exclamation-circle" title="Observações:" data-toggle="popover" data-placement="top" data-content="'.($conducao['obs'] ? $conducao['obs'] : "Sem observações...").'"></i> <i class="'.ServicoIcon($conducao['tipo']).'"></i></td>';
							echo '<td class="text-left" nowrap>'.date('d-m', strtotime($conducao['data_inicial'])).'</td>';
							echo '<td class="text-center" nowrap>'.'<i class="'.Viatura::Icon($conducao["viatura_tipo"]).'"></i> '.Viatura::FormatarMatricula($conducao["viatura_matricula"]).'</td>';
							echo '<td class="text-center" nowrap><i class="'.Utilizador::Icon($conducao["funcionario_telemovel"]).'"></i> '.$conducao['motorista'].'</td>';
							echo '<td class="text-left" nowrap><a href="'. (!empty($locInicial) ? 'https://www.google.com/maps/search/'.$locInicial['latitude'].','.$locInicial['longitude'].'/' : '#') .'" target="_blank">'.date('H:i', strtotime($conducao['data_inicial'])).' <small>('.$enderecoInicial['rua'].', '.$enderecoInicial['cidade'].')</small></a></td>';
							echo '<td class="text-left" nowrap><a href="'. (!empty($locFinal) ? 'https://www.google.com/maps/search/'.$locFinal['latitude'].','.$locFinal['longitude'].'/' : '#') .'" target="_blank">'.date('H:i', strtotime($conducao['data_final'])).' <small>('.(!empty($locFinal) ? $enderecoFinal['rua'].', '.$enderecoFinal['cidade'] : 'Indefinido').')</small></a></td>';
							echo '<td class="text-right" title="Quilometros" data-toggle="popover" data-placement="top" data-content="Iniciais: '.$conducao['kms_iniciais'].' Finais: '.($kmsPercorridos	> 0 ? $conducao['kms_finais'] : "Indefinido").'">'.($kmsPercorridos > 0 ? $kmsPercorridos : NULL).'</td>';
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