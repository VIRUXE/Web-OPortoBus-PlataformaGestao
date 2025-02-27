			<h1 class="h3 mb-4 text-gray-800">Utilizadores</h1>

			<!-- Adicionar Utilizador -->
			<div class="card shadow mb-4">
				<a href="#adicionarUtilizador" class="d-block card-header py-3 collapsed" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="adicionarUtilizador">
					<h6 class="m-0 font-weight-bold text-success">Adicionar Utilizador</h6>
				</a>
				<div class="collapse" id="adicionarUtilizador">
					<div class="card-body"><!-- telemovel, nivel, primeiro nome, ultimo nome, morada, nib, nif, mail, cargo, activo-->
						<form id="adicionarUtilizador" class="form-horizontal" action="index.php?pagina=viaturas&categoria=utilizadores" method="POST">
							<label for="telemovel">Telemóvel</label>
							<input type="tel" name="telemovel" class="form-control" pattern="[9]{1}[1-6]{1}[0-9]{7}" maxlength="9" required>
							<label for="nomePrimeiro">Primeiro Nome</label>
							<input type="text" name="nomePrimeiro" class="form-control" required>
							<label for="nomeUltimo">Último Nome</label>
							<input type="text" name="nomeUltimo" class="form-control" required>
							<label for="morada">Morada</label>
							<input type="text" name="morada" class="form-control" required>
							<label for="nif">NIF</label>
							<input type="number" name="nif" class="form-control" pattern="[9]{1}[1-6]{1}[0-9]{7}" maxlength="9" required>
							<label for="nib">NIB</label>
							<input type="number" name="nib" class="form-control" pattern="[9]{1}[1-6]{1}[0-9]{7}" maxlength="9" required>
							<label for="email">E-Mail</label>
							<input type="email" name="email" class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required>

							<label class="my-1 mr-2" for="cargoUser">Cargo</label>
							<select id="cargoUser" class="form-control">
								<option value="OUTRO">				Outro</option>
								<option value="VIGILANTE">			Vigilante</option>
								<option value="MOTORISTA">			Motorista</option>
								<option value="MOTORISTAPESADOS">	Motorista de Pesados</option>
								<option value="DONO">				Dono</option>
							</select>
							
							<button type="submit" class="btn btn-success btn-icon-split my-1">
								<span class="icon text-white-50">
									<i class="fas fa-check"></i>
								</span>
								<span class="text">Inserir Utilizador</span>
							</button>
						</form>
					</div>
				</div>
			</div>

			<!-- Listagem de Utilizadores -->
			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-primary">Listagem de Utilizadores</h6>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table id="dataTable" class="table table-sm table-borderless" width="100%" cellspacing="0">
							<thead>
								<tr>
									<th>Telemóvel</th>
									<th>Primeiro Nome</th>
									<th>Último Nome</th>
									<th>Cargo</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th>Telemóvel</th>
									<th>Primeiro Nome</th>
									<th>Último Nome</th>
									<th>Cargo</th>
								</tr>
							</tfoot>
							<tbody>
								<?php
								$result = $database->query("SELECT telemovel, nome_primeiro, nome_ultimo, CONCAT(UCASE(LEFT(cargo, 1)), LCASE(SUBSTRING(cargo, 2))) as cargo FROM utilizadores ORDER BY nome_primeiro");

								if ($result->num_rows) 
								{
									while ($user = $result->fetch_assoc()) 
									{
										$cargo = $user['cargo'];

										if($cargo == "Motoristapesados")
											$cargo = "Motorista de Pesados";

										echo '<tr>';
										echo '<td><a href="index.php?ver=empresa&categoria=utilizadores&utilizador='.$user["telemovel"].'">'. $user["telemovel"] . '</a></td>';
										echo '<td>' . $user["nome_primeiro"] . '</td>';
										echo '<td>' . $user["nome_ultimo"] . '</td>';
										echo '<td><i class="'.Utilizador::Icon($user["telemovel"]).'"></i> '.$cargo.'</td>';
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