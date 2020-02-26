<?php
require 'includes/frota/frota.class.php';

// Verificar se a sessão de condução está aberta ou não
if(isset($_SESSION['conducao']))
{

}
else
{

}
?>
			<h1 class="h3 mb-2 text-gray-800">Sessão de Condução</h1>

			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-success">Abertura de Sessão</h6>
				</div>
				<div class="card-body">
<!-- 					Escolher Viatura
					Localização
					Escolher Serviço(s) -->
					<form>
						<label class="my-1 mr-2" for="viaturaSessao">Viatura</label>
						<select id="viaturaSessao" name="viatuSessao" class="form-control" required>
							<option value="" selected>Escolher...</option>
							<?php
							foreach(Frota::Viaturas() as $matricula => $viatura)
								echo '<option value="'.$matricula.'">'.$viatura["nome"].' '.substr($matricula,2,2).'</option>';
							?>
						</select>
						<label for="localizacao">Localização</label>					
						<input type="text" id="localizacao" name="localizacao" class="form-control"  onclick="getLocation()" readonly>
					</form>
						<button class="form-control btn-block btn-success">GPS</button>
				</div>
			</div>