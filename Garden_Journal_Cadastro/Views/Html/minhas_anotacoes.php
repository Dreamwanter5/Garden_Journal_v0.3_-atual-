<?php
session_start();
if (!isset($_SESSION["id"])) {
    header('Location: login.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Minhas Anotações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../Css/Anotacao.css">
</head>

<body>
    <?php include_once("partialsmenu.php"); ?>

    <div class="container py-4" v-scope="notesApp" @vue:mounted="mounted">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Minhas Anotações</h1>
            <a href="Anotacao.php" class="btn btn-success">+ Nova Anotação</a>
        </div>

        <div v-if="loading" class="text-center my-5">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
        </div>

        <div v-if="!loading && notas.length === 0" class="alert alert-info">
            Você ainda não tem anotações.
        </div>

        <div class="row g-3" v-if="!loading && notas.length > 0">
            <div class="col-12 col-md-6 col-lg-4" v-for="nota in notas" :key="nota.titulo">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-truncate">{{ nota.titulo }}</h5>
                        <p class="card-text text-muted mb-2">
                            {{ nota.descricao || 'Sem descrição' }}
                        </p>
                        <div class="mt-auto">
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-calendar3"></i> {{ nota.dt || 'Sem data' }}
                                </small>
                            </div>
                            <div class="mb-3" style="min-height: 28px;">
                                <span v-if="nota.categorias" class="badge bg-secondary me-1"
                                    v-for="cat in nota.categorias.split(',')" :key="cat">
                                    {{ cat.trim() }}
                                </span>
                                <small v-else class="text-muted fst-italic">Sem categorias</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary" @click="abrir(nota)">
                                    <i class="bi bi-eye"></i> Visualizar
                                </button>
                                <a :href="'Anotacao.php?titulo=' + encodeURIComponent(nota.titulo)"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de preview - remover aria-hidden para evitar warning -->
        <div class="modal fade" id="previewModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ selecionada?.titulo }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div v-html="renderMarkdown(selecionada?.texto || '')"></div>
                    </div>
                    <div class="modal-footer">
                        <a :href="'Anotacao.php?titulo=' + encodeURIComponent(selecionada?.titulo || '')"
                            class="btn btn-outline-primary">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/petite-vue"></script>
    <script
        src="/Programacao_web/Garden_Journal_v0.3_(atual)/Garden_Journal_Cadastro/Views/js/minhas_anotacoes.js"></script>
</body>

</html>