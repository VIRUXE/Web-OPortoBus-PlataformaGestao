<?php
require_once 'includes/frota/frota.class.php';
?>
Rota 1 = ZOE - Só tem 4a
<form method="POST">
	<div class="row justify-content-md-center">
		<div class="col col-lg-4">
		<legend for="viatura">Viatura</legend>
		<select id="viatura" name="viatura" class="form-control" required>
			<option value="" selected>Escolher...</option>
			<?php
			foreach (Frota::Viaturas() as $matricula => $viatura)
				echo '<option value="' . $matricula . '">' . $viatura["nome"] . ' ' . substr($matricula, 2, 2) . '</option>';
			?>
		</select>
		<legend for="dia" class="">Dia(s)</legend>
		<select id="dia" name="dia" class="form-control" required>
			<option value="" selected>Escolher...</option>
			<option value="SEGUNDA">Segunda-Feira</option>
			<option value="TERCA">Terça-Feira</option>
			<option value="QUARTA">Quarta-Feira</option>
			<option value="QUINTA">Quinta-Feira</option>
			<option value="SEXTA">Sexta-Feira</option>
		</select>
		<button type="submit" class="btn btn-success btn-icon-split my-1">
			<span class="icon text-white-50">
				<i class="fas fa-road"></i>
			</span>
			<span class="text">Ver Rota</span>
		</button>
		</div>
	</div>
</form>
<br>
<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary">Horários para</h6>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table id="horariosCriancas" class="table table-sm table-borderless table-hover" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Hora Recolha</th>
						<th>Criança</th>
						<th>Local de Recolha</th>
						<th>Local de Entrega</th>
						<th>Hora Entrega</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Hora Recolha</th>
						<th>Criança</th>
						<th>Local de Recolha</th>
						<th>Local de Entrega</th>
						<th>Hora Entrega</th>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$result = $database->query("SELECT grupo, horario_id FROM escolas_rotas");

					$rotaHorarios = [];

					if (!$result)
						trigger_error('Query Inválida: ' . $database->error);
					else
					{
						while ($horario = $result->fetch_assoc()) 
							$rotaHorarios[] = $horario;
					}

					// var_dump($rotaHorarios);

					foreach ($rotaHorarios as $horario) 
					{
						$result = $database->query("
							SELECT CONCAT(c.nome_primeiro, ' ',c.nome_ultimo) as crianca, recolha.nome as locRecolha, horario.recolha_hora, entrega.nome as locEntrega, horario.entrega_hora, horario.ativo
							FROM escolas_criancas_horarios horario
							LEFT JOIN escolas_criancas c ON c.id = horario.crianca_id
							LEFT JOIN localizacoes recolha ON horario.recolha_localizacao_id = recolha.id
							LEFT JOIN localizacoes entrega ON horario.entrega_localizacao_id = entrega.id
							WHERE horario.id = {$horario['horario_id']}
							ORDER BY horario.dia, horario.recolha_hora
							
						");

						if (!$result)
							trigger_error('Query Inválida: ' . $database->error);
						else
						{
							while ($horario = $result->fetch_assoc()) 
							{
								echo '<tr>';
								echo '<td class="float-center">'.date('H:i', strtotime($horario['recolha_hora'])).'</td>';
								echo '<td nowrap>'.$horario['crianca'].'</td>';
								echo '<td nowrap>'.$horario['locRecolha'].'</td>';
								echo '<td nowrap>'.$horario['locEntrega'].'</td>';
								echo '<td class="float-center">'.($horario['entrega_hora'] != "00:00:00" ? date('H:i', strtotime($horario['entrega_hora'])) : NULL).'</td>';
								echo '</tr>';
							}
						}

					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

