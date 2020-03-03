<?php
require 'includes/frota/frota.class.php';

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
					<div class="alert alert-danger text-center">
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
			'kms' 			=> $_POST['viaturaKms'], 
			'gps' 			=> ['latitude' => $coords[0], 'longitude' => $coords[1]], 
			'observacoes' 	=> $_POST['conducaoObservacoes']
		];

		$sessao = $_SESSION['user']->conducao; // Só para ser mais fácil...

		$result = $database->query("
			INSERT INTO viaturas_sessoes (viatura_matricula, funcionario_telemovel, kms_iniciais, localizacao_inicial, obs) 
			VALUES('{$sessao['viatura']}', '{$_SESSION['user']->telemovel}', '{$sessao['kms']}', '".json_encode($sessao['gps'])."', '{$sessao['observacoes']}')"
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

			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-<?= $sessaoAtiva ? "danger":"success" ?>"><?= $sessaoAtiva ? 'Fecho de Condução - <span class="font-weight-bolder">Viatura '.Viatura::FormatarMatricula($_SESSION['user']->conducao['viatura']).'</span>' : 'Início de Condução' ?></h6>
				</div>
				<div class="card-body">
					<form class="form-horizontal" action="index.php?ver=frota&categoria=conducao" method="POST">
						<?php
						if(!$sessaoAtiva)
						{
							echo '<div class="form-group row">';
							echo '<label class="col-md-2" for="conducaoViatura">Viatura</label>';
							echo '<select id="conducaoViatura" name="conducaoViatura" class="col-md-10 form-control form-control-sm" required>';
							echo '<option value="" selected>Escolher...</option>';
							foreach(Frota::Viaturas() as $matricula => $viatura)
								echo '<option value="'.$matricula.'">'.$viatura["nome"].' '.substr($matricula,2,2).'</option>';
							echo '</select>';
							echo '</div>';
						}
						?>

						<div class="form-group row">
							<label for="localizacao" class="col-md-2">Localização</label>					
							<input type="text" id="localizacao" name="localizacao" class="col-md-10 form-control form-control-sm" onclick="getLocation()" readonly>
						</div>
						<div class="form-group row">
							<label for="viaturaKms" class="col-md-2">KMs de <?= $sessaoAtiva ? "Fecho":"Abertura"?></label>					
							<input type="number" id="viaturaKms" name="viaturaKms" class="col-md-10 form-control form-control-sm"<?= $sessaoAtiva ? ' value="'.$_SESSION['user']->conducao['kms'].'"' : NULL ?>required>
						</div>
						<div class="form-group row">
							<label for="conducaoObservações" class="col-md-2">Observações</label>					
							<textarea id="conducaoObservacoes" name="conducaoObservacoes" class="col-md-10 form-control form-control-sm" rows="4" maxlength="255" placeholder="Qualquer tipo de observação sobre a viatura ou o serviço em si..."><?= $sessaoAtiva ? $_SESSION['user']->conducao['observacoes'] : NULL ?></textarea>
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
<?php if($_SESSION['user']->cargo == 'DONO' || $_SESSION['user']->cargo == 'DESENVOLVEDOR') { ?>			
			<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary">Listagem de Sessões de Condução</h6>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-sm table-borderless table-hover" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
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
					$result = $database->query("
									SELECT id, viatura_matricula, v.tipo as viatura_tipo, funcionario_telemovel, CONCAT(u.nome_primeiro, ' ',SUBSTRING(u.nome_ultimo,1,1), '.') as motorista, data_inicial, kms_iniciais, localizacao_inicial, data_final, kms_finais, localizacao_final, obs, ativa 
									FROM viaturas_sessoes 
									LEFT JOIN utilizadores u ON viaturas_sessoes.funcionario_telemovel = u.telemovel
									LEFT JOIN viaturas v ON viaturas_sessoes.viatura_matricula = v.matricula 
									ORDER BY ativa DESC, data_inicial DESC
								");

					if (!$result)
						trigger_error('Query Inválida: ' . $database->error);
					else
					{
						while ($conducao = $result->fetch_assoc()) 
						{
							$locInicial 	= json_decode($conducao['localizacao_inicial'], true);
							$locFinal 		= json_decode($conducao['localizacao_final'], true);
							$kmsPercorridos = $conducao['kms_finais']-$conducao['kms_iniciais'];

							echo '<tr'.($conducao['ativa'] ? ' class="table-success"' : NULL).' title="Observações: '.($conducao['obs'] ? $conducao['obs'] : "Sem observações...").'">';
							echo '<td class="text-left" nowrap>'.date('d-m', strtotime($conducao['data_inicial'])).'</td>';
							echo '<td class="text-center" nowrap>'.'<i class="'.Viatura::Icon($conducao["viatura_tipo"]).'"></i> '.Viatura::FormatarMatricula($conducao["viatura_matricula"]).'</td>';
							echo '<td class="text-center" nowrap><i class="'.Utilizador::Icon($conducao["funcionario_telemovel"]).'"></i> '.$conducao['motorista'].'</td>';
							echo '<td class="text-center"><a href="'. (!empty($locInicial) ? 'https://www.google.com/maps/search/'.$locInicial['latitude'].','.$locInicial['longitude'].'/' : '#') .'">'.date('H:i', strtotime($conducao['data_inicial'])).'</a></td>';
							echo '<td class="text-center"><a href="'. (!empty($locFinal) ? 'https://www.google.com/maps/search/'.$locFinal['latitude'].','.$locFinal['longitude'].'/' : '#') .'">'.date('H:i', strtotime($conducao['data_final'])).'</a></td>';
							echo '<td class="text-right" title="Iniciais: '.$conducao['kms_iniciais'].' Finais: '.($kmsPercorridos	? $conducao['kms_finais'] : "Indefinido").'">'.($kmsPercorridos ? $kmsPercorridos : "Indefinido").'</td>';
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