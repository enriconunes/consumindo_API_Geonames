<?php

$username = 'enriconunes';
$baseUrl = 'http://api.geonames.org';
$url = "http://api.geonames.org/findNearbyJSON?lat=37.77493&lng=-122.41942&username=enriconunes"; //url padrao que será alterada posteriormente

// Query pela latitude e longitude do local
if (isset($_GET['inputFormulario']) && $_GET['inputFormulario'] == 'queryCoordenadas') {
    $queryCoordenadas = "lat=" . $_GET['latitude'] . "&" . "lng=" . $_GET['longitude'];
    $url = "{$baseUrl}/findNearbyJSON?{$queryCoordenadas}&username={$username}";
}

// Query pelo nome do local
if (isset($_GET['inputFormulario']) && $_GET['inputFormulario'] == 'queryNome') {
    $queryNome = "q=" . urlencode($_GET['nome']);
    $url = "{$baseUrl}/searchJSON?{$queryNome}&username={$username}";
}

// Fazer a chamada da API Geonames usando a função file_get_contents() a partir da url gerada
$resultados = file_get_contents($url);

// Converter o resultado da query JSON em um array
$dados = json_decode($resultados, true);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>App Geonames</title>

    <style>
        h1 {
            text-align: center;
            margin: 40px;
        }

        .card-header button {
            color: black !important;
        }

        .card {
            border-bottom: 1px solid rgba(0, 0, 0, .125) !important;
        }

        .resultQueryNome {
            max-height: 400px;
            overflow: scroll;
            overflow-x: hidden;
        }

        h6.url {
            color: #575757;
            font-size: 0.8em;
            position: fixed;
            bottom: 10px;
            left: 10px;
        }
    </style>

</head>

<body>
    <div class="container">

        <h1>Query de localizações com a API Geonames</h1>

        <!-- INICIO DO COLLAPSE BOOTSTRAP -->
        <div class="accordion" id="accordionExample">

            <!-- BUSCAR PELAS COORDENADAS -->
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Buscar local pelas coordenadas
                        </button>
                    </h5>
                </div>
                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">

                    <!-- INICIO DO FORMULARIO -->
                    <form class="form-inline" method="GET" action="index.php">
                        <div class="form-group mt-4 ml-4 mb-4">
                            <label for="inputLatitude">Latitude: </label>
                            <input type="number" step="0.0000001" id="inputLatitude" class="form-control mx-sm-3" placeholder="37.77493" name="latitude" required>

                            <label for="inputLongitude">Longitude: </label>
                            <input type="number" step="0.0000001" id="inputLongitude" class="form-control mx-sm-3" placeholder="-122.41942" name="longitude" required>

                            <button type="submit" name="inputFormulario" value="queryCoordenadas" class="btn btn-info">Buscar</button>
                        </div>
                    </form>

                    <!-- INICIO DO GRUPO DE CARDS -->
                    <div class="card-group">
                        <!-- INICIO DO CARD - LISTAGEM DE DADOS -->
                        <?php
                        // Faz a listagem somente se o formulário for enviado
                        if (isset($_GET['inputFormulario']) && $_GET['inputFormulario'] == 'queryCoordenadas') {
                            if (isset($dados['status']['message']) && $dados['status']['message'] == 'invalid lat/lng') {
                                echo "<div class='card-body'>";
                                echo "<div class='card' style='width: 18rem;'>";
                                echo "<div class='card-body'>";
                                echo "<h5 class='card-title'>" . "Dados inválidos" . "</h5>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                            // Conferir se algum local for encontrado
                            if (isset($dados['geonames']) && $dados['geonames'] == []) {
                                echo "<div class='card-body'>";
                                echo "<div class='card' style='width: 18rem;'>";
                                echo "<div class='card-body'>";
                                echo "<h5 class='card-title'>" . "Local não encontrado" . "</h5>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                            // Fazer a listagem se tudo der certo
                            if (isset($dados['geonames']) && $dados['geonames'] != []) {
                                foreach ($dados['geonames'] as $geoname) {
                                    echo "<div class='card-body'>";
                                    echo "<div class='card' style='width: 18rem;'>";
                                    echo "<div class='card-body'>";
                                    echo "<h5 class='card-title'>" . $geoname['name'] . "</h5>";
                                    echo "<p class='card-text'><b>Tipo:</b> " . $geoname['fclName'] . "</p>";
                                    echo "<p class='card-text'><b>País:</b> " . $geoname['countryName'] . "</p>";
                                    echo "<p class='card-text'><b>Estado:</b> " . $geoname['adminName1'] . "</p>";
                                    echo "<p class='card-text'><b>População:</b> " . $geoname['population'] . "</p>";
                                    echo "</div>";
                                    echo "</div>";
                                    echo "</div>";
                                }
                            }
                        }
                        ?>

                        <!-- FIM DO CARD - LISTAGEM DE DADOS-->
                    </div>
                </div>
            </div>
            <!-- FIM BUSCAR PELAS COORDENADAS -->

            <!-- BUSCAR PELO NOME -->
            <div class="card">
                <div class="card-header" id="headingTwo">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Buscar local pelo nome
                        </button>
                    </h5>
                </div>
                <div id="collapseTwo" class="collapse resultQueryNome shadow-sm rounded" aria-labelledby="headingTwo" data-parent="#accordionExample">
                    <div class="card-body">
                        <!-- INICIO DO FORMULARIO -->
                        <form class="form-inline" method="GET" action="index.php">
                            <div class="form-group mt-4 ml-4 mb-4">
                                <label for="inputName">Nome: </label>
                                <input type="text" id="inputName" class="form-control mx-sm-3" placeholder="Nome do local" name="nome" required>

                                <button type="submit" name="inputFormulario" value="queryNome" class="btn btn-info">Buscar</button>
                            </div>
                        </form>

                        <!-- INICIO DA LISTAGEM DE DADOS -->
                        <div class="card-group">
                            <?php
                            // fazer a listagem somente se o formulario for enviado
                            if (isset($_GET['inputFormulario']) && $_GET['inputFormulario'] == 'queryNome') {
                                $msg_erro = "Please add a username to each call in order for geonames to be able to identify the calling application and count the credits usage.";
                                if (isset($dados['status']['message']) && $dados['status']['message'] == $msg_erro) {
                                    echo "<div class='card-body'>";
                                    echo "<div class='card' style='width: 18rem;'>";
                                    echo "<div class='card-body'>";
                                    echo "<h5 class='card-title'>" . "Dados inválidos" . "</h5>";
                                    echo "</div>";
                                    echo "</div>";
                                    echo "</div>";
                                }
                                // conferir se algum local foi encontrado
                                if (isset($dados['geonames']) && $dados['geonames'] == []) {
                                    echo "<div class='card-body'>";
                                    echo "<div class='card' style='width: 18rem;'>";
                                    echo "<div class='card-body'>";
                                    echo "<h5 class='card-title'>" . "Local não encontrado" . "</h5>";
                                    echo "</div>";
                                    echo "</div>";
                                    echo "</div>";
                                }
                                // fazer a listagem dos locais se tudo der certo
                                if (isset($dados['geonames']) && $dados['geonames'] != []) {
                                    foreach ($dados['geonames'] as $geoname) {
                                        echo "<div class='card-body'>";
                                        echo "<div class='card' style='width: 18rem;'>";
                                        echo "<div class='card-body'>";
                                        echo "<h5 class='card-title'>" . $geoname['name'] . "</h5>";
                                        echo "<p class='card-text'><b>Tipo:</b> " . $geoname['fclName'] . "</p>";
                                        echo "<p class='card-text'><b>País:</b> " . $geoname['countryName'] . "</p>";
                                        echo "<p class='card-text'><b>Estado:</b> " . $geoname['adminName1'] . "</p>";
                                        echo "<p class='card-text'><b>População:</b> " . $geoname['population'] . "</p>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- FIM BUSCAR PELO NOME -->
        </div>
    </div>

    <!-- exibir a url da query -->
    <?php
    if (isset($_GET['inputFormulario'])) {
        echo '<h6 class="url"><b>Query: </b>' . $url . '></h6>';
    }
    ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>

</html>