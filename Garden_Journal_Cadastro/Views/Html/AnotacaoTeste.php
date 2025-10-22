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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Editor Milkdown - Versão Simplificada</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <script src="https://unpkg.com/petite-vue" defer></script>
  <link rel="stylesheet" href="../Css/AnotacaoTeste.css">
  </link>
  <!-- <script src="../js/nota.js" defer></script> -->
  <script src="../js/salvarNota.js" defer></script>
</head>

<body>

  <?php include_once("partialsmenu.php"); ?>

    <div class="container" v-scope="App()" @vue:mounted="mounted">
    <header>
      <div class="mb-4">
        <input type="text" 
               v-model="formData.titulo" 
               class="form-control form-control-lg border-0 fs-1 fw-bold"
               placeholder="Digite o título da sua anotação..."
               style="background: transparent; outline: none;">
      </div>
      
    </header>

    <!-- Seção de Categorias -->
    <div class="mb-4">
      <label class="form-label fw-bold">Categorias</label>
      <div class="categorias-wrapper">
        <div class="categorias-list">
          <div v-for="categoria in categoriasDisponiveis" :key="categoria.id" class="form-check">
            <input class="form-check-input" 
                   type="checkbox" 
                   :value="categoria.id" 
                   :id="'cat-' + categoria.id" 
                   v-model="formData.categoriasSelecionadas">
            <label class="form-check-label" :for="'cat-' + categoria.id">
              {{ categoria.nome }}
            </label>
          </div>
        </div>
      </div>
    </div>

    <div class="editor-wrapper">
      <div class="editor-header">
        <h2>Editor</h2>
        <span>Digite seu Markdown aqui</span>
      </div>
      <div class="editor-content">
        <textarea id="editor" 
                  placeholder="# Comece a escrever seu Markdown aqui..." 
                  v-model="formData.conteudo"
                  @input="atualizarPreview"></textarea>
      </div>
    </div>

    <div class="preview-wrapper">
      <div class="preview-header">
        <h2>Visualização</h2>
        <span>Resultado</span>
      </div>
      <div id="preview" v-html="preview"></div>
    </div>

    <div class="actions">
      <button @click="limparEditor" type="button">🧹 Limpar Editor</button>
      <button @click="carregarExemplo" type="button" class="secondary">📋 Texto de Exemplo</button>
      <button @click="salvarNota" type="button">💾 Salvar Nota</button>
      <button @click="downloadHTML" type="button">📥 Download HTML</button>
    </div>

    <!-- Mensagens de feedback -->
    <div v-if="mensagem" class="alert" :class="{'alert-success': sucesso, 'alert-danger': !sucesso}">
      {{ mensagem }}
    </div>

    <div class="tips">
      <h3>📚 Guia Rápido de Markdown</h3>
      <div class="tips-grid">
        <div class="tip-card">
          <h4>Títulos</h4>
          <p>
            <code># Título 1</code><br />
            <code>## Título 2</code><br />
            <code>### Título 3</code>
          </p>
        </div>
        <div class="tip-card">
          <h4>Ênfase</h4>
          <p>
            <code>**negrito**</code><br />
            <code>*itálico*</code><br />
            <code>~~tachado~~</code>
          </p>
        </div>
        <div class="tip-card">
          <h4>Listas</h4>
          <p>
            <code>- Item não ordenado</code><br />
            <code>1. Item ordenado</code><br />
          </p>
        </div>
        <div class="tip-card">
          <h4>Código</h4>
          <p>
            <code>`código inline`</code><br />
          </p>
        </div>
        <div class="tip-card">
          <h4>Links e Imagens</h4>
          <p>
            <code>[texto](url)</code><br />
            <code>![alt](url)</code>
          </p>
        </div>
        <div class="tip-card">
          <h4>Outros</h4>
          <p>
            <code>> Citação</code><br />
            <code>--- Linha horizontal</code><br />
          </p>
        </div>
      </div>
    </div>

    <footer>
      <p>
        Criado com ❤️ | Inspirado no Milkdown |
        <a href="https://www.markdownguide.org/" target="_blank">Aprenda Markdown</a>
      </p>
    </footer>
  </div>
</body>

</html>