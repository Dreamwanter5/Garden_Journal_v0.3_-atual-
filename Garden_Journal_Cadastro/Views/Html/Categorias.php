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
  <title>Categorias</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <script src="https://unpkg.com/petite-vue" defer></script>
  <script src="../js/categorias.js" defer></script>
</head>
<body>
  <?php include_once("partialsmenu.php"); ?>

  <div class="container py-4" v-scope="catsApp" @vue:mounted="mounted">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
      <h1 class="h4 m-0">Categorias</h1>
      <a href="Anotacao.php" class="btn btn-outline-secondary">
        <i class="bi bi-journal-plus"></i> Nova Anotação
      </a>
    </div>

    <!-- Criar nova -->
    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-2 align-items-center">
          <div class="col-12 col-md-6">
            <input type="text" class="form-control" placeholder="Nova categoria..."
                   v-model="nova" @keyup.enter="criar" />
          </div>
          <div class="col-12 col-md-auto">
            <button class="btn btn-success" @click="criar" :disabled="loadingCriar">
              <i class="bi bi-plus-circle"></i> {{ loadingCriar ? 'Adicionando...' : 'Adicionar' }}
            </button>
          </div>
          <div class="col-12">
            <small class="text-danger" v-if="erroCriar">{{ erroCriar }}</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtro -->
    <div class="row g-2 mb-3">
      <div class="col-12 col-md-6">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input class="form-control" placeholder="Buscar categoria..." v-model="termo">
        </div>
      </div>
      <div class="col-12 col-md-6 text-md-end">
        <small class="text-muted">Total: {{ filtradas().length }}</small>
      </div>
    </div>

    <!-- Lista -->
    <div class="card">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:60%">Nome</th>
              <th class="text-end" style="width:40%">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in filtradas()" :key="c.id_categoria">
              <td>
                <div v-if="editId !== c.id_categoria">{{ c.nome }}</div>
                <div v-else class="d-flex gap-2">
                  <input type="text" class="form-control" v-model="editNome" />
                </div>
              </td>
              <td class="text-end">
                <div v-if="editId !== c.id_categoria" class="btn-group btn-group-sm">
                  <button class="btn btn-outline-primary" @click="iniciarEdicao(c)">
                    <i class="bi bi-pencil"></i> Renomear
                  </button>
                  <button class="btn btn-outline-danger" @click="remover(c)">
                    <i class="bi bi-trash"></i> Excluir
                  </button>
                </div>
                <div v-else class="btn-group btn-group-sm">
                  <button class="btn btn-primary" @click="salvarEdicao">
                    <i class="bi bi-check2-circle"></i> Salvar
                  </button>
                  <button class="btn btn-secondary" @click="cancelarEdicao">
                    <i class="bi bi-x-circle"></i> Cancelar
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!loading && filtradas().length === 0">
              <td colspan="2" class="text-center text-muted py-4">Nenhuma categoria encontrada.</td>
            </tr>
            <tr v-if="loading">
              <td colspan="2" class="text-center py-4">
                <div class="spinner-border text-success"></div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="mensagem" class="alert mt-3" :class="{'alert-success': sucesso, 'alert-danger': !sucesso}">
      {{ mensagem }}
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>