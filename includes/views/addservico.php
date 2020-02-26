<?php
// $cliente = $dataInicio = $dataFim = $titulo = $estado = NULL;

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$cliente 	= $_POST["cliente"];
	$dataInicio = date("Y-m-d H:i:s",strtotime($_POST["dataInicio"]));
	$dataFim 	= date("Y-m-d H:i:s",strtotime($_POST["dataFim"]));
	$titulo 	= $_POST["titulo"];
	$descricao 	= $_POST["descricao"];
	$estado 	= $_POST["estado"];

if($database->query("INSERT INTO servicos (cliente_id, data_servico_inicio, data_servico_fim, titulo, descricao, estado, criador_telemovel) VALUES ('$cliente', '$dataInicio', '$dataFim', '$titulo', '$descricao', '$estado', '{$_SESSION['utilizador']['telemovel']}')"))
    echo "Inserido com sucesso";
else
    echo "Erro: ".$database->error;

}
?>
			<h1 class="h3 mb-4 text-gray-800">Clientes</h1>

			<form id="login" action="index.php?pagina=servicos&accao=adicionar" method="post" class="user">
				<div class="form-group row">
					<div class="col-sm-12 mb-3 mb-sm-0">
						<label for="nomeCliente">Nome do Cliente:</label>
						<input id="nomeCliente" name="cliente" type="text" class="form-control form-control-user" placeholder="ID do Cliente">
					</div>
				</div>
				<div class="form-group row"> <!-- Datas do Serviço -->
					<div class="col-sm-6">
						<label for="dataInicio">Data Início</label>
						<input id="dataInicio" name="dataInicio" type="datetime-local" class="form-control form-control-user" value="'<?= date("Y-m-j") ?>'">
					</div>
					<div class="col-sm-6">
						<label for="dataFim">Data de Fim</label>
						<input id="dataFim" name="dataFim" type="datetime-local" class="form-control form-control-user" value="'<?= date("Y-m-j") ?>'">
					</div>
				</div>
				<div class="form-group">
					<label for="titulo">Título do Serviço</label>
					<input id="titulo" name="titulo" type="text" class="form-control form-control-user" placeholder="Título do serviço" maxlength="50">
				</div>
				<div class="form-group">
					<label for="descricao">Descrição</label>
					<textarea id="descricao" name="descricao" class="form-control" rows="3"></textarea>
				</div>
				<div class="form-group">
					<label for="estado">Estado do Serviço</label>
					<select id="estado" name="estado" class="form-control">
						<option value="PRERESERVA">Pré-Reserva</option>
						<option value="EFETIVO">Efetivo</option>
						<option value="CONCLUIDO">Concluído</option>
					</select>
				</div>
				<div class="form-group">
					<label for="recorrente">Serviço recorrente?</label>
					<input type="radio" id="recorrenteSim" name="recorrente" value="S">
					<label for="female">Sim</label>
					<input type="radio" id="recorrenteNao" name="recorrente" value="N" checked>
					<label for="other">Não</label>
				</div>
				<a href="" class="btn btn-primary btn-user btn-block" onclick="this.closest('form').submit(); return false;">Registar Serviço</a>
			</form>