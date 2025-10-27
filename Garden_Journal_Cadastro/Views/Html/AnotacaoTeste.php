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

    <link rel="stylesheet" href="../Css/AnotacaoTeste.css"></link>
    <script src="../js/nota.js" defer></script>
    <script src="salvarNota.js" defer></script>
  </head>
  <body>
    <?php include_once("partialsmenu.php"); ?>

    <div class="container">
      <!-- Inicio da área de alteração pelo usuário -->
      <header>
        <!-- Aqui eu quero que seja o nome da anotação que o usuário quiser inserir -->
        <h1>Editor Markdown Interativo</h1>
        <p class="description">
          Um editor Markdown simples e elegante com visualização em tempo real.
          Escreva no editor e veja o resultado formatado instantaneamente.
        </p>
      </header>

      <div class="editor-wrapper">
        <div class="editor-header">
          <h2>Editor</h2>
          <span>Digite seu Markdown aqui</span>
        </div>
        <div class="editor-content">
          <textarea
            id="editor"
            placeholder="# Comece a escrever seu Markdown aqui..."
          >
# teste
          </textarea
          >
        </div>
      </div>

      <div class="preview-wrapper">
        <div class="preview-header">
          <h2>Visualização</h2>
          <span>Resultado</span>
        </div>
        <div id="preview">
          <!-- A visualização será gerada aqui pelo JavaScript -->
        </div>
      </div>
<!-- Fim da visualização -->

      <div class="actions">
        <button id="clear-btn">🧹 Limpar Editor</button>
        <button id="sample-btn" class="secondary">📋 Texto de Exemplo</button>
        <button id="download-btn">💾 Download HTML</button>
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
              <code>```bloco de código```</code>
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
          <a href="https://www.markdownguide.org/" target="_blank"
            >Aprenda Markdown</a
          >
        </p>
      </footer>
    </div>
  </body>
</html>