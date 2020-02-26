<?php
if (isset($_GET['nomePrimeiro'])) {
    $nomePrimeiro = $_GET['nomePrimeiro'];
}
if (isset($_GET['nomeUltimo'])) {
    $nomeUltimo = $_GET['nomeUltimo'];
}
if (isset($_GET['escola'])) {
    $escola = $_GET['escola'];
}


$conn = new mysqli("localhost", "root", null, "oportobus_plataforma");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT nome FROM escolas_locais ORDER BY nome ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        // echo $row["nome"] . "<br>";
    }
} else {
    echo "0 results";
}
// $conn->close();

// Carregar crianças num array de javascript

// Carregar datalist

?>
<!DOCTYPE html>
<html lang="pt-PT">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>SB Admin 2 - Register</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

  <div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
      <div class="card-body p-0">
        <!-- Nested Row within Card Body -->
        <div class="row">
          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
          <div class="col-lg-7">
            <div class="p-5">
              <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Introduzir Criança!</h1>
              </div>
              <form class="user" method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                <div class="form-group row">
                  <div class="col-sm-6 mb-3 mb-sm-0">
                    <input type="text" class="form-control form-control-user" id="primeiroNome" placeholder="Primeiro Nome">
                  </div>
                  <div class="col-sm-6">
                    <input type="text" class="form-control form-control-user" id="ultimoNome" placeholder="Último Nome">
                  </div>
                </div>
                <div class="from-group">
                    <input type="text" class="form-control form-control-user" id="escola" placeholder="Escola" list="escolas">
                    <datalist id="escolas">
                    <?php
                        $sql = "SELECT id, nome FROM escolas_locais ORDER BY nome ASC";
                        $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="'.$row["nome"].'">'.$row["id"].'</option>';
                        }
                    }
                    ?>
                    </datalist>
                </div>
                <div class="form-group">
                  <input type="text" class="form-control form-control-user" id="observacoes" placeholder="Observações...">
                </div>
                <a href="login.html" class="btn btn-primary btn-user btn-block">
                  Introduzir Criança
                </a>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

</body>

</html>