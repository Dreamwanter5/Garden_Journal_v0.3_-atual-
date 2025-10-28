<?php
session_start();
if (!isset($_SESSION["id"])) {
    header('Location: login.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Usuário</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../Css/Tela_do_Usuario.css">
</head>

<body>
    <?php include_once("partialsmenu.php"); ?>

    <div class="container py-5 position-relative">
        <img src="../img/planta_vaso.gif" alt="Plant background" class="plant-bg">

        <div class="user-card">
            <h1 class="welcome-text">Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h1>
            <p class="lead text-muted mb-5">Estamos felizes em vê-lo de volta ao Garden Journal</p>

            <div class="user-info-card">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div class="info-content">
                        <h5>Nome Completo</h5>
                        <p><?php echo htmlspecialchars($_SESSION['nome']); ?></p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h5>Email</h5>
                        <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                    </div>
                </div>


            </div>

            <div class="row mt-5">
                <div class="col-md-4 mb-4">
                    <div class="action-card">
                        <div class="action-icon">
                            <i class="bi bi-journal"></i>
                        </div>
                        <h4>Nota diária</h4>
                        <p class="text-muted">Registre suas observações e progresso</p>
                        <a href="Anotacao.php" class="btn btn-outline-success mt-2">Acessar</a>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="action-card">
                        <div class="action-icon">
                            <i class="bi bi-flower1"></i>
                        </div>
                        <h4>Minhas Anotações</h4>
                        <p class="text-muted">Gerencie sua plantação de conhecimento</p>
                        <a href="minhas_anotacoes.php" class="btn btn-outline-success mt-2">Visualizar</a>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="action-card">
                        <div class="action-icon">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <h4>Calendário</h4>
                        <p class="text-muted">Veja o que vem a seguir</p>
                        <a href="#" class="btn btn-outline-success mt-2">Ver Agenda</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>