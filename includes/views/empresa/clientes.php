			<h1 class="h3 mb-4 text-gray-800">Clientes</h1>
			
			<div class="row">
				<div class="col-lg-12 col-xs-4">
					<div class="card shadow mb-10">
						<div class="card-header py-3">
							<h6 class="m-0 font-weight-bold text-success">Adicionar Cliente</h6>
						</div>
						<div class="card-body">
							<form>
								<div class="form-row">
									<div class="col-4">
										<input type="text" class="form-control" placeholder="Nome do Cliente/Empresa">
									</div>
									<div class="col-2">
										<input type="text" class="form-control" placeholder="identificador@dominio.pt">
										<small class="form-text text-muted">
											Opcional, mas introduzir sempre que possível
										</small>
									</div>
									<div class="col-1">
										<input type="text" class="form-control" placeholder="NIF">
										<small class="form-text text-muted">Opcional</small>
									</div>
									<div class="col">
										<input type="text" class="form-control" maxlength="34" placeholder="IBAN">
										<small class="form-text text-muted">Opcional</small>
									</div>
									<button type="submit" class="btn btn-success mb-2">Inserir Cliente</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="card shadow mb-10">
						<div class="card-header py-3">
							<h6 class="m-0 font-weight-bold text-primary">Listagem de Clientes</h6>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-sm table-borderless table-hover table-striped" id="dataTable" width="100%" cellspacing="0">
									<thead>
										<tr>
											<th>ID</th>
											<th>Nome</th>
											<th>E-Mail</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>ID</th>
											<th>Nome</th>
											<th>E-Mail</th>
										</tr>
									</tfoot>
									<tbody>
										<?php
										$result = $database->query("SELECT id, nome, email FROM clientes");

										if ($result->num_rows) 
										{
											while ($cliente = $result->fetch_assoc()) 
											{
												echo '<tr>';
												echo '<td>' . $cliente["id"] . '</td>';
												echo '<td>' . $cliente["nome"] . '</td>';
												echo '<td>' . $cliente["email"] . '</td>';
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
				</div>
			</div>