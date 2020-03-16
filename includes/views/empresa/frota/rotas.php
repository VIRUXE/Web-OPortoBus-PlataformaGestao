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
<h1 class="h3 mb-4 text-gray-800">Definir Rotas</h1>
<form action="index.php?ver=empresa&categoria=frota&subcategoria=rotas" method="POST">
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
	<button type="submit" class="btn btn-success">Ver Rota</button>
	</div>
	<br>
	<div class="row">
		<label for="horariosEscolhidos" class="form-label">Horários Escolhidos</label>
		<select id="horariosEscolhidos" name="horariosEscolhidos[]" class="form-control" size="10" multiple="">
			<?php
			$result = $database->query("
					SELECT horario.id, horario.dia, horario.viatura_matricula, CONCAT(c.nome_primeiro, ' ',c.nome_ultimo) as crianca, recolha.nome as locRecolha, horario.recolha_hora, entrega.nome as locEntrega, horario.entrega_hora, horario.ativo
					FROM escolas_criancas_horarios horario
					LEFT JOIN escolas_criancas c ON c.id = horario.crianca_id
					LEFT JOIN localizacoes recolha ON horario.recolha_localizacao_id = recolha.id
					LEFT JOIN localizacoes entrega ON horario.entrega_localizacao_id = entrega.id
					WHERE horario.viatura_matricula = '$_viatura' AND horario.dia = '$_dia'
					ORDER BY horario.dia, horario.recolha_hora
				");

			if (!$result)
				trigger_error('Query Inválida: ' . $database->error);
			else
			{
				while ($horario = $result->fetch_assoc()) 
					echo "<option value=\"{$horario["id"]}\">[".date("H:i", strtotime($horario["recolha_hora"]))."] {$horario["locRecolha"]} -> {$horario["locEntrega"]} ({$horario["crianca"]})</option>";
			}
			?>
		</select>
	</div>
	<div class="row">
		<button id="btnInserir" name="btnInserir" class="btn btn-success" style="margin: 10px;"><i class="fas fa-arrow-to-top fa-2x"></i></button>
		<button id="btnRemover" name="btnRemover" class="btn btn-danger" style="margin: 10px;"><i class="fas fa-arrow-to-bottom fa-2x"></i></button>
	</div>
	<div class="row">
		<label for="horariosDisponiveis" class="form-label">Horários Disponíveis</label>
		<select id="horariosDisponiveis" name="horariosDisponiveis[]" class="form-control" size="10" multiple="">
			<?php
			$result = $database->query("
					SELECT horario.id, horario.dia, horario.viatura_matricula, CONCAT(c.nome_primeiro, ' ',c.nome_ultimo) as crianca, recolha.nome as locRecolha, horario.recolha_hora, entrega.nome as locEntrega, horario.entrega_hora, horario.ativo
					FROM escolas_criancas_horarios horario
					LEFT JOIN escolas_criancas c ON c.id = horario.crianca_id
					LEFT JOIN localizacoes recolha ON horario.recolha_localizacao_id = recolha.id
					LEFT JOIN localizacoes entrega ON horario.entrega_localizacao_id = entrega.id
					WHERE horario.viatura_matricula IS NULL AND horario.dia = '$_dia'
					ORDER BY horario.dia, horario.recolha_hora
				");

			if (!$result)
				trigger_error('Query Inválida: ' . $database->error);
			else
			{
				while ($horario = $result->fetch_assoc()) 
					echo "<option value=\"{$horario["id"]}\">[".date("H:i", strtotime($horario["recolha_hora"]))."] {$horario["locRecolha"]} -> {$horario["locEntrega"]} ({$horario["crianca"]})</option>";
			}
			?>
		</select>
	</div>
</form>