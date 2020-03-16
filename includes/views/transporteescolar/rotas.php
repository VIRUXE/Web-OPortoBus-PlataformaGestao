<?php
require_once 'includes/frota/frota.class.php';

global $database;

$_viatura = (isset($_POST['viatura']) ? $_POST['viatura'] : NULL);
$_dia = (isset($_POST['dia']) ? $_POST['dia'] : NULL);

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	// var_dump($_POST);

	if(isset($_POST['btnInserir']) && isset($_POST['horariosDisponiveis']))
	{
		foreach ($_POST['horariosDisponiveis'] as $horario) 
			$database->query("UPDATE escolas_criancas_horarios SET viatura_matricula = '$_viatura' WHERE id = '$horario'");
	}
	elseif (isset($_POST['btnRemover']) && isset($_POST['horariosEscolhidos'])) 
	{
		foreach ($_POST['horariosEscolhidos'] as $horario) 
			$database->query("UPDATE escolas_criancas_horarios SET viatura_matricula = NULL WHERE id = '$horario'");
	}
}
?>
<h1 class="h3 mb-4 text-gray-800">Rotas</h1>
<form action="index.php?ver=transporteescolar&categoria=rotas" method="POST">
	<div class="form-row justify-content-lg-center">
		<div class="col-lg-auto form-inline">
			<label for="viatura">Viatura:</label>
			<select id="viatura" name="viatura" class="form-control" required>
				<option value="" selected>Escolher...</option>
				<?php
				foreach (Frota::Viaturas() as $matricula => $viatura)
					echo '<option value="' . $matricula . '"'.($matricula == $_viatura ? ' selected' : NULL).'>' . $viatura["nome"] . ' ' . substr($matricula, 2, 2) . '</option>';
				?>
			</select>
		</div>
		<div class="col-lg-auto form-inline">
			<label for="dia" class="">Dia(s):</label>
			<select id="dia" name="dia" class="form-control" required>
				<option value="" selected>Escolher...</option>
				<option value="SEGUNDA"	<?= ($_dia == "SEGUNDA" ? " selected" : NULL) ?>>Segunda-Feira</option>
				<option value="TERCA"	<?= ($_dia == "TERCA" 	? " selected" : NULL) ?>>Terça-Feira</option>
				<option value="QUARTA"	<?= ($_dia == "QUARTA" 	? " selected" : NULL) ?>>Quarta-Feira</option>
				<option value="QUINTA"	<?= ($_dia == "QUINTA" 	? " selected" : NULL) ?>>Quinta-Feira</option>
				<option value="SEXTA"	<?= ($_dia == "SEXTA" 	? " selected" : NULL) ?>>Sexta-Feira</option>
			</select>
		</div>
		<button type="submit" class="btn btn-success btn-block my-1"><i class="fas fa-route"></i> Ver Rota</button>
	</div>
</form>
<br>
<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary">Visualização de Rotas</h6>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table id="horariosCriancas" class="table table-sm table-borderless table-hover table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Hora Recolha</th>
						<th>Criança</th>
						<th nowrap>Local de Recolha</th>
						<th nowrap>Local de Entrega</th>
						<th>Hora Entrega</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Hora Recolha</th>
						<th>Criança</th>
						<th nowrap>Local de Recolha</th>
						<th nowrap>Local de Entrega</th>
						<th>Hora Entrega</th>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$result = $database->query("
						SELECT horario.id, horario.viatura_matricula, CONCAT(c.nome_primeiro, ' ',c.nome_ultimo) as crianca, recolha.nome as locRecolha, recolha.morada as locRecolhaEnd, horario.recolha_hora, entrega.nome as locEntrega, entrega.morada as locEntregaEnd, horario.entrega_hora, horario.ativo
						FROM escolas_criancas_horarios horario
						LEFT JOIN escolas_criancas c ON c.id = horario.crianca_id
						LEFT JOIN localizacoes recolha ON horario.recolha_localizacao_id = recolha.id
						LEFT JOIN localizacoes entrega ON horario.entrega_localizacao_id = entrega.id
						WHERE horario.viatura_matricula = '$_viatura' AND horario.dia = '$_dia' AND horario.ativo = true
						ORDER BY horario.dia, horario.recolha_hora
					");

					if (!$result)
						trigger_error('Query Inválida: ' . $database->error);
					else
					{
						while ($horario = $result->fetch_assoc()) 
						{
							$dia = $horario['dia'];

							echo '<tr>';
							echo '<td class="float-center">'.date('H:i', strtotime($horario['recolha_hora'])).'</td>';
							echo '<td nowrap>'.$horario['crianca'].'</td>';
							echo "<td nowrap><a href=\"https://www.google.pt/maps/place/{$horario['locRecolhaEnd']}\" target=\"_blank\">{$horario['locRecolha']}</a></td>";
							echo "<td nowrap><a href=\"https://www.google.pt/maps/place/{$horario['locEntregaEnd']}\" target=\"_blank\">{$horario['locEntrega']}</a></td>";
							echo '<td class="float-center">'.($horario['entrega_hora'] != "00:00:00" ? date('H:i', strtotime($horario['entrega_hora'])) : NULL).'</td>';
							echo '</tr>';
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>