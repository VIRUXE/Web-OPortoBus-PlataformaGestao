		<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">
			<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
				<div class="sidebar-brand-text mx-3">Plataforma de Gestão</div>
			</a>
			<hr class="sidebar-divider">
			<div class="sidebar-heading">Acesso Rápido</div>
			<li class="nav-item active">
				<a class="nav-link" href="index.php">
					<i class="fas fa-fw fa-tachometer-alt"></i>
					<span>Resumo Geral</span></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="index.php?ver=empresa&categoria=frota&subcategoria=conducao">
					<i class="fas fa-steering-wheel"></i>
					<span>Sessão de Condução</span></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="index.php?ver=empresa&categoria=frota&subcategoria=abastecimento">
					<i class="fas fa-fw fa-gas-pump"></i>
					<span>Abastecimento</span></a>
			</li>
			<hr class="sidebar-divider">
			<div class="sidebar-heading">Transporte Escolar</div>
			<li class="nav-item">
				<a class="nav-link collapsed" href="index.php?ver=transporteescolar&categoria=criancas" data-toggle="collapse" data-target="#collapseCriancas">
					<i class="fas fa-fw fa-child"></i>
					<span>Crianças</span>
				</a>
				<div id="collapseCriancas" class="collapse" aria-labelledby="headingServicos" data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">
						<a class="collapse-item" href="index.php?ver=transporteescolar&categoria=criancas&subcategoria=horarios"><i class="fas fa-clock"></i> Horários</a>
						<a class="collapse-item" href="index.php?ver=transporteescolar&categoria=criancas"><i class="fas fa-list-ol"></i> Listagem</a>
					</div>
				</div>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="index.php?ver=transporteescolar&categoria=rotas">
					<i class="fas fa-fw fa-route"></i>
					<span>Rotas Escolares</span>
				</a>
			</li>
<?php if($_SESSION['user']->Admin()) { ?>
			<hr class="sidebar-divider">
			<div class="sidebar-heading">Empresa</div>
			<li class="nav-item">
				<a class="nav-link" href="index.php?ver=empresa&categoria=clientes">
					<i class="fas fa-fw fa-user-tie"></i>
					<span>Clientes</span>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseServicos" aria-expanded="true" aria-controls="collapseServicos">
					<i class="fas fa-fw fa-road"></i>
					<span>Serviços</span>
				</a>
				<div id="collapseServicos" class="collapse" aria-labelledby="headingServicos" data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">
						<a class="collapse-item" href="index.php?ver=empresa&categoria=servicos&accao=adicionar"><i class="fas fa-plus-circle"></i> Adicionar</a>
						<a class="collapse-item" href="index.php?ver=empresa&categoria=servicos"><i class="fas fa-list-ol"></i> Consultar</a>
					</div>
				</div>
			</li>

			<li class="nav-item">
					<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFrota" aria-expanded="true" aria-controls="collapseFrota">
					<i class="fad fa-car-bus"></i>
					<span>Frota</span></a>
				</a>
				<div id="collapseFrota" class="collapse" aria-labelledby="headingFrota" data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">
						<a class="collapse-item" href="index.php?ver=empresa&categoria=frota"><i class="fas fa-chart-bar"></i> Estatísticas</a>
						<a class="collapse-item" href="index.php?ver=empresa&categoria=frota&subcategoria=viaturas"><i class="fas fa-garage-car"></i> Viaturas</a>
						<a class="collapse-item" href="index.php?ver=empresa&categoria=frota&subcategoria=abastecimentos"><i class="fas fa-gas-pump"></i> Abastecimentos</a>
						<a class="collapse-item" href="index.php?ver=empresa&categoria=frota&subcategoria=sessoes"><i class="fas fa-steering-wheel"></i> Sessões de Condução</a>
						<a class="collapse-item" href="index.php?ver=empresa&categoria=frota&subcategoria=rotas"><i class="fas fa-route"></i> Rotas</a>
					</div>
				</div>
			</li>

			<li class="nav-item">
				<a class="nav-link" href="index.php?ver=empresa&categoria=utilizadores">
					<i class="fas fa-fw fa-users"></i>
					<span>Utilizadores</span></a>
			</li>
<?php } ?>
			<hr class="sidebar-divider d-none d-md-block">
			<div class="text-center d-none d-md-inline">
				<button class="rounded-circle border-0" id="sidebarToggle"></button>
			</div>
		</ul>
