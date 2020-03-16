<?php
require_once 'includes/escolas.class.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	/*echo '<pre>';
	print_r($_POST);
	echo '</pre>';*/

	$result = $database->query("
		INSERT INTO escolas_criancas_horarios (dia, crianca_id, recolha_localizacao_id, recolha_hora, entrega_localizacao_id, entrega_hora, obs) 
		VALUES('{$_POST['dia']}', '{$_POST['crianca']}', '{$_POST['localRecolha']}', '{$_POST['horaRecolha']}', '{$_POST['localEntrega']}', '{$_POST['horaEntrega']}', '{$_POST['obs']}')"
	);

	if (!$result)
		trigger_error('Query Inválida: ' . $database->error);
	else
		echo '<div class="alert alert-success text-center"><i class="fas fa-child"></i> Horário inserido com sucesso</div>';
}
if($_SESSION['user']->Admin()) {	
?>
<form method="POST">
	<div class="row">
		<div class="form-group col-lg-2">
			<legend for="dia" class="">Dia(s)</legend>
			<select id="dia" name="dia" class="form-control" required>
				<option value="" selected>Escolher...</option>
				<option value="SEGUNDA">Segunda-Feira</option>
				<option value="TERCA">Terça-Feira</option>
				<option value="QUARTA">Quarta-Feira</option>
				<option value="QUINTA">Quinta-Feira</option>
				<option value="SEXTA">Sexta-Feira</option>
				<option value="SABADO">Sábado</option>
				<option value="DOMINGO">Domingo</option>
				<option value="TODOS">Todos os Dias</option>
				<option value="TODOSUTEIS">Todos os Dias Úteis</option>
			</select>
		</div>
		<div class="form-group col-lg-4">
			<legend for="escola">Criança</legend>
			<select class="form-control" name="crianca" required>
				<option value="" selected>Escolher...</option>
				<option value="0">Quem estiver...</option>
				<?php
				$escola = NULL;
				$optFechado = true;

				foreach(Criancas::Obter() as $crianca)
				{
					if($crianca['escola'] != $escola)
					{
						if($optFechado)
							echo '<optgroup label="'.$crianca['escola'].'">';
						else
						{
							echo '</optgroup>';
							echo '<optgroup label="'.$crianca['escola'].'">';
						}

						$optFechado = !$optFechado;
						$escola = $crianca['escola'];
					}
					echo '<option value="'.$crianca['id'].'">'.$crianca['nome_primeiro'].' '.$crianca['nome_ultimo'].'</option>';
					// echo '<option value="'.$crianca['id'].'">'.$crianca['nome_primeiro'].' '.$crianca['nome_ultimo'].' ('.$crianca['escola'].')</option>';
				}
				?>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-lg-6">
			<legend>Local de Recolha</legend>
			<select name="localRecolha" class="form-control" required>
				<option value="" selected>Escolher...</option>
				<?php
				$result = $database->query("
					SELECT loc.id as id, loct.nome as tipo, loc.nome as nome, loc.morada as morada 
					FROM localizacoes loc
					LEFT JOIN localizacoes_tipo loct ON loc.tipo = loct.id
					WHERE loc.tipo = 1 OR loc.tipo = 2
					ORDER BY loc.nome ASC
				");

				if($result && $result->num_rows)
				{
					while ($loc = $result->fetch_assoc())
						echo '<option value="'.$loc['id'].'">'.$loc['nome'].'</option>';
				}
				?>
			</select>
		</div>
		<div class="col-lg-6">
			<legend>Hora de Recolha</legend>
			<input type="time" id="horaRecolha" name="horaRecolha" class="form-control" required>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-lg-6">
			<legend>Local de Entrega</legend>
			<select name="localEntrega" class="form-control" required>
				<option value="">Escolher...</option>
				<?php
				$result = $database->query("
					SELECT loc.id as id, loct.nome as tipo, loc.nome as nome, loc.morada as morada 
					FROM localizacoes loc
					LEFT JOIN localizacoes_tipo loct ON loc.tipo = loct.id
					WHERE loc.tipo = 1 OR loc.tipo = 2
					ORDER BY loc.nome ASC
				");

				if($result && $result->num_rows)
				{
					while ($loc = $result->fetch_assoc())
						echo '<option value="'.$loc['id'].'">'.$loc['nome'].'</option>';
				}
				?>
			</select>
		</div>
		<div class="col-lg-6">
			<legend>Hora de Entrega</legend>
			<input type="time" id="horaEntrega" name="horaEntrega" class="form-control" value="00:00" required>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-lg-6">
			<legend>Observações</legend>
			<textarea name="obs" class="form-control"></textarea>
		</div>
	</div>
	<button type="submit" class="btn btn-success btn-icon-split my-1">
		<span class="icon text-white-50">
			<i class="fas fa-clock"></i>
		</span>
		<span class="text">Inserir Horário</span>
	</button>	
</form>
<br>
<?php } ?>
<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary">Horários das Crianças</h6>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table id="horariosCriancas" class="table table-sm table-borderless table-hover" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>ID</th>
						<th>Dia</th>
						<th>Hora Recolha</th>
						<th>Criança</th>
						<th>Local de Recolha</th>
						<th>Local de Entrega</th>
						<th>Hora Entrega</th>
						<th></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>ID</th>
						<th>Dia</th>
						<th>Hora Recolha</th>
						<th>Criança</th>
						<th>Local de Recolha</th>
						<th>Local de Entrega</th>
						<th>Hora Entrega</th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$result = $database->query("
						SELECT horario.id, horario.dia, CONCAT(c.nome_primeiro, ' ',c.nome_ultimo) as crianca, recolha.nome as locRecolha, horario.recolha_hora, entrega.nome as locEntrega, horario.entrega_hora, horario.ativo
						FROM escolas_criancas_horarios horario
						LEFT JOIN escolas_criancas c ON c.id = horario.crianca_id
						LEFT JOIN localizacoes recolha ON horario.recolha_localizacao_id = recolha.id
						LEFT JOIN localizacoes entrega ON horario.entrega_localizacao_id = entrega.id
						ORDER BY horario.dia, horario.recolha_hora
					");

					if (!$result)
						trigger_error('Query Inválida: ' . $database->error);
					else
					{
						while ($horario = $result->fetch_assoc()) 
						{
							$dia = $horario['dia'];

							switch ($dia) 
							{
								case 'SEGUNDA':
									$dia = "2ª";
									break;
								case 'TERCA':
									$dia = "3ª";
									break;
								case 'QUARTA':
									$dia = "4ª";
									break;
								case 'QUINTA':
									$dia = "5ª";
									break;
								case 'SEXTA':
									$dia = "6ª";
									break;
								case 'SABADO':
									$dia = "Sab.";
									break;
								case 'DOMINGO':
									$dia = "Dom.";
									break;
								case 'TODOSUTEIS':
									$dia = "D. Úteis";
									break;
								case 'TODOS':
									$dia = "Todos";
									break;
							}

							echo '<tr'.(!$horario['ativo'] ? ' class="text-gray-400"' : NULL).'>';
							echo '<td>'.$horario['id'].'</td>';
							echo '<td nowrap>'.$dia.'</td>';
							echo '<td class="float-center">'.date('H:i', strtotime($horario['recolha_hora'])).'</td>';
							echo '<td nowrap>'.$horario['crianca'].'</td>';
							echo '<td nowrap>'.$horario['locRecolha'].'</td>';
							echo '<td nowrap>'.$horario['locEntrega'].'</td>';
							echo '<td class="float-center">'.($horario['entrega_hora'] != "00:00:00" ? date('H:i', strtotime($horario['entrega_hora'])) : NULL).'</td>';
							if($_SESSION['user']->Admin()) 
								echo '
								<td nowrap>
									<a href="#" class="btn btn-'.(!$horario['ativo'] ? 'success' : 'warning').' btn-circle btn-sm"><i class="fas fa-user-'.(!$horario['ativo'] ? 'check' : 'alt-slash').'"></i></a>
									<a href="#" class="btn btn-info btn-circle btn-sm"><i class="fas fa-user-edit"></i></a>
									<a href="#" class="btn btn-danger btn-circle btn-sm"><i class="fas fa-trash"></i></a>
								</td>';
							echo '</tr>';
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>