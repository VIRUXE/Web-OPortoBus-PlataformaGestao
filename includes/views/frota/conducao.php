<?php
require 'includes/frota/frota.class.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	// Verificar se está a abrir ou fechar condução
	if(isset($_SESSION['utilizador']['conducao'])) // Fechar Condução
	{
		$sessao = $_SESSION['utilizador']['conducao']; // Só para ser mais fácil...

		$result = $database->query("
			UPDATE viaturas_sessoes 
			SET data_fim = current_timestamp(), kms_fim = '{$sessao['kms']}', localizacao_fim = POINT({$sessao['gps']['longitude']}, {$sessao['gps']['latitude']}), obs = '{$sessao['observacoes']}', activa = false
			WHERE funcionario_telemovel = '{$_SESSION['utilizador']['telemovel']}' AND activa = true"
		);

		if (!$result)
			trigger_error('Query Inválida: ' . $database->error);
		else
			echo '<div class="alert alert-danger text-center"><i class="fas fa-road"></i> Sessão de condução terminada para a viatura <span class="font-weight-bold">' . Viatura::FormatarMatricula($sessao['viatura']) . '</span></div>';

		// Destruir a Sessão de Condução
		unset($_SESSION['utilizador']['conducao']);
	}
	else // Abrir Condução
	{
		$coords = explode(',',$_POST['localizacao']); // Separar por latitude e longitude

		echo json_encode($coords);

		// Construir o array com os dados de condução na SESSION
		$_SESSION['utilizador']['conducao'] = 
		[
			'viatura' 		=> $_POST['conducaoViatura'], 
			'kms' 			=> $_POST['viaturaKms'], 
			'gps' 			=> ['latitude' => $coords[0], 'longitude' => $coords[1]], 
			'observacoes' 	=> $_POST['conducaoObservacoes']
		];

		$sessao = $_SESSION['utilizador']['conducao']; // Só para ser mais fácil...

		$result = $database->query("
			INSERT INTO viaturas_sessoes (viatura_matricula, funcionario_telemovel, kms_inicio, localizacao_inicio, obs) 
			VALUES('{$sessao['viatura']}', '{$_SESSION['utilizador']['telemovel']}', '{$sessao['kms']}', POINT({$sessao['gps']['longitude']}, {$sessao['gps']['latitude']}), '{$sessao['observacoes']}')"
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

$sessaoAtiva = isset($_SESSION['utilizador']['conducao']) ? true : false;
?>
			<h1 class="h3 mb-2 text-gray-800">Sessões de Condução</h1>

			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-<?= $sessaoAtiva ? "danger":"success" ?>"><?= $sessaoAtiva ? "Fecho de Sessão - Viatura ".Viatura::FormatarMatricula($_SESSION['utilizador']['conducao']['viatura']) : "Início de Sessão" ?></h6>
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
							<input type="number" id="viaturaKms" name="viaturaKms" class="col-md-10 form-control form-control-sm"<?= $sessaoAtiva ? ' value="'.$_SESSION['utilizador']['conducao']['kms'].'"' : NULL ?>required>
						</div>
						<div class="form-group row">
							<label for="conducaoObservações" class="col-md-2">Observações</label>					
							<textarea id="conducaoObservacoes" name="conducaoObservacoes" class="col-md-10 form-control form-control-sm" rows="4" maxlength="255" placeholder="Qualquer tipo de observação sobre a viatura ou o serviço em si..."><?= $sessaoAtiva ? $_SESSION['utilizador']['conducao']['observacoes'] : NULL ?></textarea>
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