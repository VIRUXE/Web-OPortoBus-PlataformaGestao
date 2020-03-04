<?php
require_once 'includes/escolas.class.php';


?>
<div class="card o-hidden border-0 shadow-lg my-5">
	<div class="card-body p-0">
		<div class="row">
			<div class="col-lg-12">
				<div class="p-5">
					<div class="text-center">
						<h1 class="h4 text-gray-900 mb-4">Inserir Criança</h1>
					</div>
					<form class="user">
						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<input type="text" class="form-control" id="exampleFirstName" placeholder="Primeiro Nome" required>
							</div>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="exampleLastName" placeholder="Último Nome" required>
							</div>
						</div>
						<div class="form-group">
							<legend>Escola</legend>
							<select class="form-control" required>
								<option value="" selected>Escolher...</option>
								<?php
								foreach(Escolas::Obter() as $escola)
									echo '<option value="'.$escola['id'].'">'.$escola['nome'].'</option>';
								?>
							</select>
						</div>
						<div class="form-group">
							<legend>Observações</legend>
							<textarea class="form-control" placeholder="Qualquer observação sobre a criança..."></textarea>
						</div>
						<button class="btn btn-primary">Registar Criança</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>