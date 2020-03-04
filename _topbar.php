<?php
include_once 'includes/common.func.php';
?>				
				<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

					<!-- Toggle do Sidebar -->
					<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>

					<!-- Input de Pesquisa -->
					<form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
						<div class="input-group">
							<input type="text" class="form-control bg-light border-0 small" placeholder="Pesquisar..." aria-label="Search" aria-describedby="basic-addon2">
							<div class="input-group-append">
								<button class="btn btn-primary" type="button"><i class="fas fa-search fa-sm"></i></button>
							</div>
						</div>
					</form>

					<!-- Navegação do Topo -->
					<ul class="navbar-nav ml-auto">
						<!-- Lupa para activar o Input de Pesquisa para Mobile -->
						<li class="nav-item dropdown no-arrow d-sm-none">
							<a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fas fa-search fa-fw"></i>
							</a>
							<!-- Input de Pesquisa para Mobile -->
							<div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
								<form class="form-inline mr-auto w-100 navbar-search">
									<div class="input-group">
										<input type="text" class="form-control bg-light border-0 small" placeholder="Pesquisar..." aria-label="Search" aria-describedby="basic-addon2">
										<div class="input-group-append">
											<button class="btn btn-primary" type="button"><i class="fas fa-search fa-sm"></i></button>
										</div>
									</div>
								</form>
							</div>
						</li>

						<!-- Alertas -->
						<li class="nav-item dropdown no-arrow mx-1">
							<a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fas fa-bell fa-fw"></i>
								<span class="badge badge-danger badge-counter">3+</span>
							</a>
							<!-- Alertas - Dropdown -->
							<div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
								<h6 class="dropdown-header">Alertas</h6>
								<a class="dropdown-item d-flex align-items-center" href="#">
									<div class="mr-3">
										<div class="icon-circle bg-primary"><i class="fas fa-file-alt text-white"></i></div>
									</div>
									<div>
										<div class="small text-gray-500">1 Dezembro 2019</div>
										<span class="font-weight-bold">O serviço 'Transporte Escolar' foi concluído</span>
									</div>
								</a>
								<a class="dropdown-item d-flex align-items-center" href="#">
									<div class="mr-3">
										<div class="icon-circle bg-success">
											<i class="fas fa-donate text-white"></i>
										</div>
									</div>
									<div>
										<div class="small text-gray-500">1 Dezembro 2019</div>
										Foram faturados 100000EUR
									</div>
								</a>
								<a class="dropdown-item d-flex align-items-center" href="#">
									<div class="mr-3">
										<div class="icon-circle bg-warning">
											<i class="fas fa-exclamation-triangle text-white"></i>
										</div>
									</div>
									<div>
										<div class="small text-gray-500">1 Dezembro 2019</div>
										A viatura '00-xx-00' está com um consumo exagerado
									</div>
								</a>
								<a class="dropdown-item text-center small text-gray-500" href="#">Mostrar todos</a>
							</div>
						</li>
						<!-- Mensagens -->
						<li class="nav-item dropdown no-arrow mx-1">
							<a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fas fa-envelope fa-fw"></i>
								<span class="badge badge-danger badge-counter"><?= $_SESSION['user']->MensagensPorLer() ?></span>
							</a>
							<!-- Mensagens - Dropdown -->
							<div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
								<h6 class="dropdown-header">Mensagens</h6>
								<?php
									foreach ($_SESSION['user']->ObterMensagens(4) as $msg) 
									{
										echo '
											<a class="dropdown-item d-flex align-items-center" href="#">
											<div>
												<div class="text-truncate">'.$msg['nome_primeiro'].' '.$msg['nome_ultimo'].'</div>
												<div class="small text-gray-500">'.$msg['titulo'].' · '.timeago($msg['data']).'</div>
											</div>
											</a>
										';
									}
									
								?>
								<!-- <a class="dropdown-item d-flex align-items-center" href="#">
									<div class="font-weight-bold">
										<div class="text-truncate">Mensagem 1</div>
										<div class="small text-gray-500">Flávio Pereira · 58m</div>
									</div>
								</a> -->
								<!-- <a class="dropdown-item d-flex align-items-center" href="#">
									<div>
										<div class="text-truncate">Mensagem 2</div>
										<div class="small text-gray-500">Flávio Pereira · 1d</div>
									</div>
								</a>
								<a class="dropdown-item d-flex align-items-center" href="#">
									<div>
										<div class="text-truncate">Mensagem 3</div>
										<div class="small text-gray-500">Flávio Pereira · 2d</div>
									</div>
								</a>
								<a class="dropdown-item d-flex align-items-center" href="#">
									<div>
										<div class="text-truncate">Mensagem 4</div>
										<div class="small text-gray-500">Marco Patinha · 2sem</div>
									</div>
								</a> -->
								<a class="dropdown-item text-center small text-gray-500" href="#">Mostrar todas</a>
							</div>
						</li>
						<div class="topbar-divider d-none d-sm-block"></div>
						<!-- Opções do Utilizador -->
						<li class="nav-item dropdown no-arrow">
							<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Cargo: <?= $_SESSION['user']->cargo ?>">
								<span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $_SESSION['user']->NomeFormatado(); ?></span>
								<i class="<?= $_SESSION['user']->Icon(); ?>"></i>
							</a>
							<!-- Utilizador - Dropwdown -->
							<div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
								<a class="dropdown-item" href="#">
									<i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>Perfil
								</a>
								<a class="dropdown-item" href="#">
									<i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>Definições
								</a>
								<a class="dropdown-item" href="#">
									<i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>Registo de Actividade
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="index.php?logout">
									<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Terminar Sessão
								</a>
							</div>
						</li>
					</ul>
				</nav>
				<!-- Conteúdo de cada página  -->
				<div class="container">