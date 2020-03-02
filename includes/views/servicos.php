			<h1 class="h3 mb-2 text-gray-800">Serviços</h1>

			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-primary">Tabela de Serviços</h6>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-sm table-borderless table-hover" id="dataTable" width="100%" cellspacing="0">
							<thead>
								<tr>
									<th>Data de Criação</th>
									<th>Data de Início</th>
									<th>Data de Fim</th>
									<th>Título</th>
									<th>Cliente</th>
									<th>Recorrente</th>
									<th>Estado</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th>Data de Criação</th>
									<th>Data de Início</th>
									<th>Data de Fim</th>
									<th>Título</th>
									<th>Cliente</th>
									<th>Recorrente</th>
									<th>Estado</th>
								</tr>
							</tfoot>
							<tbody>
								<?php
								$result = $database->query("SELECT date(data_criacao) as data_criacao, data_servico_inicio, data_servico_fim, descricao, titulo, clientes.nome as cliente, recorrente, estado FROM servicos LEFT JOIN clientes ON servicos.cliente_id = clientes.id ORDER BY data_servico_inicio DESC");

								if($result) 
								{
									while($svc = $result->fetch_assoc()) 
									{
										echo '<tr class="table-'.($svc["estado"] == 'EFETIVO' || $svc["estado"] == 'RESERVA' ? 'success' : 'warning').'">';
										echo '<td>' . $svc["data_criacao"] . '</td>';
										echo '<td>' . $svc["data_servico_inicio"] . '</td>';
										echo '<td>' . ($svc["data_servico_fim"] == NULL ? 'Sem fim' : $svc["data_servico_fim"]) . '</td>';
										echo '<td title="' . $svc["descricao"] . '">' . $svc["titulo"] . '</td>';
										echo '<td>' . $svc["cliente"] . '</td>';
										echo '<td style=font-weight: bold;>' . ($svc["recorrente"] == "S" ? 'Sim' : 'Não') . '</td>';
										echo '<td>' . $svc["estado"] . '</td>';
										echo '</tr>';
									}
								}
								else
									echo 'Erro: '.$database->error;
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>