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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <script src="https://unpkg.com/petite-vue" defer></script>
  <link rel="stylesheet" href="../Css/Anotacao.css">
  <script src="../js/salvarNota.js" defer></script>
</head>

<body>
  <?php include_once("partialsmenu.php"); ?>

  <div class="container" v-scope="app">
    <!-- FORMULÁRIO PRINCIPAL -->
    <form @submit.prevent="salvarNota" id="form-anotacao">
      <header>
        <!-- Campo de título -->
        <div class="mb-4">
          <input type="text" 
             name="titulo"
             v-model="formData.titulo" 
             @blur="validarTitulo"
             :class="{'is-invalid': errors.titulo}"
             class="form-control form-control-lg border-0 fs-1 fw-bold"
             placeholder="Digite o título da sua anotação..."
             style="background: transparent; outline: none;">
          <div class="invalid-feedback">{{ errors.titulo }}</div>
        </div>
        
        <!-- Campo de descrição -->
        <div class="mb-4">
          <input type="text" 
             name="descricao"
             v-model="formData.descricao" 
             :class="{'is-invalid': errors.descricao}"
             class="form-control border-0"
             placeholder="Digite uma breve descrição da sua anotação..."
             style="background: transparent; outline: none;">
          <div class="invalid-feedback">{{ errors.descricao }}</div>
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
                     v-model="formData.categoriasSelecionadas"
                     :name="'categorias[]'"> <!-- Importante para formulário tradicional -->
              <label class="form-check-label" :for="'cat-' + categoria.id">
                {{ categoria.nome }}
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Área do Editor e Preview -->
      <div class="editor-preview-container">
        <div class="editor-wrapper">
          <div class="editor-header">
            <h2>Editor</h2>
            <span>Digite seu Markdown aqui</span>
          </div>
          <div class="editor-content">
            <textarea 
              name="conteudo"
              id="editor" 
              placeholder="# Comece a escrever seu Markdown aqui..." 
              v-model="formData.conteudo"
              @input="atualizarPreview"
              @blur="validarConteudo"
              :class="{'is-invalid': errors.conteudo}"></textarea>
            <div class="invalid-feedback">{{ errors.conteudo }}</div>
          </div>
        </div>

        <div class="preview-wrapper">
          <div class="preview-header">
            <h2>Visualização</h2>
            <span>Resultado</span>
          </div>
          <div id="preview" v-html="preview"></div>
        </div>
      </div>

      <!-- Campo hidden para o HTML gerado (opcional) -->
      <input type="hidden" name="conteudo_html" :value="preview">

      <!-- Mensagens de feedback -->
      <div v-if="mensagem" class="alert mt-3" :class="{'alert-success': sucesso, 'alert-danger': !sucesso}">
        {{ mensagem }}
      </div>

      <!-- Botões de ação -->
      <div class="actions mt-3">
        <button type="submit" class="btn btn-primary">💾 Salvar Nota</button>
        <button type="button" @click="downloadHTML" class="btn btn-success">📥 Download HTML</button>
      </div>
    </form>

    <!-- Algumas dicas -->
    <div class="tips mt-5">
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
              <small style="font-size:0.6rem; display:block; color:#6c757d;">
                No momento o código não suporta o formato original de inserção em MD, eis aqui uma alternativa
              </small>
              <code>&lt;img src="link" alt="descrição"/&gt;</code>
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
    </div>

    <footer class="mt-5">
      <p>
        Criado com ❤️ | Inspirado no Milkdown |
        <a href="https://www.markdownguide.org/" target="_blank">Aprenda Markdown</a>
      </p>
    </footer>
  </div>
</body>
</html>