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
</head>

<body>
    <?php @include_once("partialsmenu.php"); ?>

    <div id="app-root" class="container py-4" v-scope="notesApp" @vue:mounted="mounted">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
            <h1 class="h3 m-0">Minhas Anotações</h1>
            <a href="Anotacao.php" class="btn btn-success">+ Nova Anotação</a>
        </div>

        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Buscar por título..."
                                v-model="termo" />
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-tags"></i></span>
                            <select class="form-select" v-model="categoriaSelecionada">
                                <option value="">Todas as categorias</option>
                                <option v-for="cat in categoriasDisponiveis" :key="cat" :value="cat">{{ cat }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-2 d-grid d-md-block">
                        <button class="btn btn-outline-secondary w-100" @click="limparFiltros">
                            <i class="bi bi-eraser"></i> Limpar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="loading" class="text-center my-5">
            <div class="spinner-border text-success" role="status"></div>
        </div>

        <div v-if="!loading && filteredNotas().length === 0" class="alert alert-info">
            Nenhuma anotação encontrada com os filtros atuais.
        </div>

        <div v-if="!loading && filteredNotas().length > 0" class="mb-2">
            <small class="text-muted">Total: {{ filteredNotas().length }}</small>
        </div>

        <div class="row g-3" v-if="!loading && filteredNotas().length > 0">
            <div class="col-12 col-md-6 col-lg-4" v-for="nota in filteredNotas()" :key="nota.titulo">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-truncate">{{ nota.titulo }}</h5>
                        <p class="card-text text-muted mb-2">{{ nota.descricao || 'Sem descrição' }}</p>

                        <div class="mt-auto">
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-calendar3"></i> {{ nota.dt || 'Sem data' }}
                                </small>
                            </div>

                            <div class="mb-3" style="min-height:28px;">
                                <div v-if="categoriesList(nota).length">
                                    <span class="badge bg-secondary me-1" v-for="cat in categoriesList(nota)"
                                        :key="cat">
                                        {{ cat }}
                                    </span>
                                </div>
                                <small v-else class="text-muted fst-italic">Sem categorias</small>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-primary text-white" @click="abrir(nota)">
                                    <i class="bi bi-eye"></i> Visualizar
                                </button>
                                <a :href="'Anotacao.php?titulo=' + encodeURIComponent(nota.titulo)"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <button class="btn btn-sm btn-outline-danger" @click="excluir(nota)">
                                    <i class="bi bi-trash"></i> Excluir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="previewModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ selecionada && selecionada.titulo }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div v-html="renderMarkdown(selecionada ? selecionada.texto : '')"></div>
                    </div>
                    <div class="modal-footer">
                        <a :href="'Anotacao.php?titulo=' + encodeURIComponent(selecionada ? selecionada.titulo : '')"
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/petite-vue"></script>
    <script
        src="/Programacao_web/Garden_Journal_v0.3_(atual)/Garden_Journal_Cadastro/Views/js/minhas_anotacoes.js"></script>
</body>

</html>