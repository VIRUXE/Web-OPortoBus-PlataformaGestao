<?php
include 'includes/frota/frota.class.php';
?>
		<h1 class="h3 mb-4 text-gray-800">Viaturas</h1>

		<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-primary">Lista de Viaturas</h6>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-sm table-borderless table-hover" id="dataTable" width="100%" cellspacing="0">
							<thead>
								<tr>
									<th>Matrícula</th>
									<th>Nome</th>
									<th>Tipo</th>
									<th>Passageiros</th>
									<th>Côr</th>
									<th><i class="fas fa-wheelchair"></i></th>
									<th title="Cartão de Tacografo"><i class="fas fa-id-card"></i></th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th>Matrícula</th>
									<th>Nome</th>
									<th>Tipo</th>
									<th>Passageiros</th>
									<th>Côr</th>
									<th><i class="fas fa-wheelchair"></i></th>
									<th title="Cartão de Tacografo"><i class="fas fa-id-card"></i></th>
								</tr>
							</tfoot>
							<tbody>
								<?php
								$result = $database->query("SELECT * FROM viaturas ORDER BY nome ASC");

								if($result)
								{
									while ($viatura = $result->fetch_assoc()) 
									{
										echo '<tr>';
										echo '<td><a href="index.php?ver=frota&categoria=abastecimentos&viatura='.$viatura["matricula"].'">' . Viatura::FormatarMatricula($viatura["matricula"]) . '</a></td>';
										echo '<td>' . $viatura["nome"] . '</td>';
										echo '<td><i class="fas fa-' . ($viatura["tipo"] == "LIGEIRO" ? "car" : "bus") . '"></i></td>';
										echo '<td>' . $viatura["pax"] . '</td>';
										echo '<td>' . ucfirst(strtolower($viatura["cor"])) . '</td>';
										echo '<td>' . $viatura["cadeiras"] . '</td>';
										echo '<td><i class="fas fa-'.($viatura["cartao"] == "S" ? "check" : "times") . '"></i></td>';
										echo '</tr>';
									}
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>          