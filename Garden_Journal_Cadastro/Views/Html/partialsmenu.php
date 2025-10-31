<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nomeUsuario = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Visitante';
$emailUsuario = isset($_SESSION['email']) ? $_SESSION['email'] : '';

$current = strtolower(basename($_SERVER['PHP_SELF'] ?? ''));

function isActive($files)
{
    global $current;
    foreach ((array) $files as $f) {
        if (strtolower($f) === $current)
            return 'active';
    }
    return '';
}
?>

<nav class="navbar navbar-expand-md navbar-dark bg-success sticky-top">
    <div class="container-fluid">
        <!-- Logo e título: redireciona para Tela_do_Usuario.php -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="Tela_do_Usuario.php">
            <img src="../img/pixelart plant.gif" alt="Logo" width="30" height="30"
                class="d-inline-block align-text-top">
            <span class="fw-semibold">Garden Journal</span>
        </a>

        <!-- Botão hamburguer (mobile) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
            aria-controls="navbarMain" aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Itens do menu -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive(['tela_do_usuario.php', 'Tela_do_Usuario.php']); ?>"
                        href="Tela_do_Usuario.php">
                        <i class="bi bi-house-door me-1"></i>
                        <span>Início</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive(['minhas_anotacoes.php']); ?>" href="minhas_anotacoes.php">
                        <i class="bi bi-journal-text me-1"></i>
                        <span>Minhas Anotações</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive(['anotacao.php', 'Anotacao.php']); ?>" href="Anotacao.php">
                        <i class="bi bi-plus-square me-1"></i>
                        <span>Nova Anotação</span>
                    </a>
                </li>
                <!-- novo: categorias -->
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive(['categorias.php', 'Categorias.php']); ?>"
                        href="Categorias.php">
                        <i class="bi bi-tags me-1"></i>
                        <span>Categorias</span>
                    </a>
                </li>
            </ul>

            <!-- Usuário (dropdown) -->
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2" type="button"
                        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-sm-inline"><?php echo htmlspecialchars($nomeUsuario); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li class="px-3 py-2">
                            <small class="text-muted d-block">Logado como</small>
                            <div class="fw-semibold"><?php echo htmlspecialchars($nomeUsuario); ?></div>
                            <div class="text-muted small"><?php echo htmlspecialchars($emailUsuario); ?></div>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo isActive(['tela_do_usuario.php', 'Tela_do_Usuario.php']); ?>"
                                href="Tela_do_Usuario.php">
                                <i class="bi bi-house-door me-2"></i>Início
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo isActive(['minhas_anotacoes.php']); ?>"
                                href="minhas_anotacoes.php">
                                <i class="bi bi-journal-text me-2"></i>Minhas Anotações
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo isActive(['anotacao.php', 'Anotacao.php']); ?>"
                                href="Anotacao.php">
                                <i class="bi bi-plus-square me-2"></i>Nova Anotação
                            </a>
                        </li>
                        <!-- novo: categorias -->
                        <li>
                            <a class="dropdown-item <?php echo isActive(['categorias.php', 'Categorias.php']); ?>"
                                href="Categorias.php">
                                <i class="bi bi-tags me-2"></i>Categorias
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person-gear me-2"></i>Meu Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-gear me-2"></i>Configurações
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>