<?php
session_start();

if (!isset($_SESSION["id"])) {
    header('Location: login.html');
    exit();
}

require_once('../../../Daos/baseDao.php');
require_once('../../../Daos/notaDao.php');
require_once('../../../Daos/categoriaDao.php');
require_once('../../../Entidades/Usuario.php');

$categoriaDAO = new CategoriaDAO();
$categorias = $categoriaDAO->buscarPorUsuario($_SESSION['id']);

include('partialsmenu.php');


?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Anotações - Garden Journal</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tagify -->
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet">

    <!-- Carregamento otimizado do Milkdown -->
    <script type="module">
        // Estratégia alternativa para carregar o Milkdown
        async function loadMilkdown() {
            try {
                // Método 1: Tentar carregar via CDN tradicional
                console.log('Tentando carregar Milkdown...');

                // Usando import maps para resolver os problemas de dependência
                const importMap = document.createElement('script');
                importMap.type = 'importmap';
                importMap.textContent = `
                {
                    "imports": {
                        "@milkdown/core": "https://cdn.jsdelivr.net/npm/@milkdown/core@6.5.4/+esm",
                        "@milkdown/plugin-commonmark": "https://cdn.jsdelivr.net/npm/@milkdown/plugin-commonmark@6.5.4/+esm",
                        "@milkdown/preset-gfm": "https://cdn.jsdelivr.net/npm/@milkdown/preset-gfm@6.5.4/+esm",
                        "@milkdown/plugin-listener": "https://cdn.jsdelivr.net/npm/@milkdown/plugin-listener@6.5.4/+esm"
                    }
                }
                `;
                document.head.appendChild(importMap);

                // Aguardar um momento para o import map ser processado
                setTimeout(async () => {
                    try {
                        const { createEditor } = await import('@milkdown/core');
                        const { commonmark } = await import('@milkdown/plugin-commonmark');
                        const { gfm } = await import('@milkdown/preset-gfm');
                        const { listener } = await import('@milkdown/plugin-listener');

                        window.milkdownLoaded = true;
                        window.milkdownModules = { createEditor, commonmark, gfm, listener };
                        console.log('Milkdown carregado com sucesso!');

                        // Inicializar o editor se a página já estiver carregada
                        if (document.readyState === 'complete' || document.readyState === 'interactive') {
                            initializeEditor();
                        }
                    } catch (e) {
                        console.error('Erro ao carregar Milkdown:', e);
                        // Método alternativo: carregar via script tradicional
                        loadMilkdownAlternative();
                    }
                }, 100);
            } catch (error) {
                console.error('Erro no carregamento inicial:', error);
                loadMilkdownAlternative();
            }
        }

        // Método alternativo de carregamento
        function loadMilkdownAlternative() {
            console.log('Usando método alternativo para carregar Milkdown...');
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/milkdown@6.5.4/dist/index.umd.js';
            script.onload = function () {
                window.milkdownLoaded = true;
                console.log('Milkdown (UMD) carregado com sucesso!');
                initializeEditor();
            };
            script.onerror = function () {
                console.error('Falha ao carregar Milkdown pelo método alternativo');
                document.getElementById('editor').innerHTML = `
                    <div class="alert alert-warning">
                        <h4>Problema ao carregar o editor</h4>
                        <p>O editor de texto não pôde ser carregado. Recarregue a página ou tente novamente mais tarde.</p>
                        <textarea class="form-control" rows="10" placeholder="Você pode digitar seu texto aqui enquanto o editor não carrega..."></textarea>
                    </div>
                `;
            };
            document.head.appendChild(script);
        }

        // Inicializar o editor
        function initializeEditor() {
            if (typeof window.milkdownModules !== 'undefined') {
                // Usando módulos ES
                window.milkdownModules.createEditor()
                    .use(window.milkdownModules.commonmark)
                    .use(window.milkdownModules.gfm)
                    .use(window.milkdownModules.listener)
                    .create()
                    .then(editor => {
                        window.editorInstance = editor;
                        console.log('Editor inicializado com módulos ES');
                        setTimeout(() => {
                            editor.focus();
                        }, 500);
                    });
            } else if (typeof window.milkdown !== 'undefined') {
                // Usando UMD
                const { createEditor } = window.milkdown;
                const { commonmark } = window.milkdown;
                const { gfm } = window.milkdown;
                const { listener } = window.milkdown;

                createEditor()
                    .use(commonmark)
                    .use(gfm)
                    .use(listener)
                    .create()
                    .then(editor => {
                        window.editorInstance = editor;
                        console.log('Editor inicializado com UMD');
                        setTimeout(() => {
                            editor.focus();
                        }, 500);
                    });
            }
        }

        // Iniciar carregamento do Milkdown
        loadMilkdown();

        // Inicializar quando a página estiver carregada
        document.addEventListener('DOMContentLoaded', function () {
            if (window.milkdownLoaded) {
                initializeEditor();
            }
        });
    </script>

    <style>
        .editor-container {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            height: 60vh;
        }

        #editor {
            height: 100%;
            padding: 15px;
            background-color: #fff;
            outline: none;
        }

        .editable-title {
            border: none;
            border-bottom: 2px solid #eee;
            font-size: 2rem;
            font-weight: bold;
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            transition: border-color 0.3s;
        }

        .editable-title:focus {
            border-color: #28a745;
            outline: none;
        }

        .tagify__tag {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .loading-editor {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            flex-direction: column;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <!-- Título editável -->
        <input type="text" id="titulo" class="editable-title" value="Nova anotação"
            placeholder="Dê um título à sua anotação">

        <!-- Categorias -->
        <div class="mb-4">
            <label class="form-label">Categorias (pressione Enter para adicionar)</label>
            <input id="categorias" class="form-control"
                value="<?= htmlspecialchars(implode(',', array_column($categorias, 'nome'))) ?>">
        </div>

        <!-- Editor com estado de carregamento -->
        <div class="editor-container">
            <div id="editor">
                <div class="loading-editor">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Carregando editor...</span>
                    </div>
                    <p class="mt-2">Carregando editor de texto...</p>
                </div>
            </div>
        </div>

        <!-- Feedback automático -->
        <div id="feedback" class="alert alert-info mt-3" style="display: none;">
            Salvando alterações...
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
    <script>
        // ========== CONFIGURAÇÃO DO TAGIFY ==========
        const tagify = new Tagify(document.getElementById('categorias'), {
            whitelist: <?= json_encode(array_column($categorias, 'nome')) ?>,
            dropdown: {
                maxItems: 10,
                enabled: 1,
                highlightFirst: true
            },
            enforceWhitelist: false, // Permite novas tags
            editTags: true,
            originalInputValueFormat: values => values.map(item => item.value).join(',')
        });

        // ========== SALVAMENTO AUTOMÁTICO ==========
        let saveTimer;
        let lastSavedContent = '';
        let savingInProgress = false;

        function saveNote() {
            if (savingInProgress) return;

            savingInProgress = true;
            document.getElementById('feedback').style.display = 'block';
            document.getElementById('feedback').textContent = 'Salvando alterações...';

            const titulo = document.getElementById('titulo').value;
            const categorias = tagify.value.map(tag => tag.value);
            const conteudo = window.editorInstance ? window.editorInstance.getMarkdown() : '';

            // Verifica se houve mudanças reais
            if (titulo + conteudo === lastSavedContent) {
                savingInProgress = false;
                document.getElementById('feedback').style.display = 'none';
                return;
            }

            fetch('../../../../Controllers/salvar-nota.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    titulo,
                    categorias,
                    conteudo
                })
            })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        lastSavedContent = titulo + conteudo;
                        document.getElementById('feedback').textContent = 'Alterações salvas com sucesso!';

                        // Atualiza título da página
                        document.title = titulo + ' - Garden Journal';

                        // Esconde o feedback após 2 segundos
                        setTimeout(() => {
                            document.getElementById('feedback').style.display = 'none';
                        }, 2000);
                    } else {
                        document.getElementById('feedback').textContent = 'Erro: ' + result.message;
                    }
                })
                .catch(error => {
                    document.getElementById('feedback').textContent = 'Erro de rede: ' + error.message;
                })
                .finally(() => {
                    savingInProgress = false;
                });
        }

        // ========== EVENT LISTENERS ==========
        document.getElementById('titulo').addEventListener('input', () => {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(saveNote, 1000);
        });

        tagify.on('change', () => {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(saveNote, 1000);
        });

        // Monitorar alterações no editor
        function setupEditorListeners() {
            if (window.editorInstance) {
                window.editorListener = window.editorInstance.listener;
                if (window.editorListener) {
                    window.editorListener.on('markdown-updated', () => {
                        clearTimeout(saveTimer);
                        saveTimer = setTimeout(saveNote, 1000);
                    });
                }
            } else {
                // Tentar novamente se o editor ainda não estiver pronto
                setTimeout(setupEditorListeners, 500);
            }
        }

        // Inicialização quando o editor estiver pronto
        const editorCheckInterval = setInterval(() => {
            if (window.editorInstance) {
                clearInterval(editorCheckInterval);
                setupEditorListeners();

                // Remover o indicador de carregamento
                const editorElement = document.getElementById('editor');
                if (editorElement.querySelector('.loading-editor')) {
                    editorElement.innerHTML = '';
                }
            }
        }, 100);
    </script>
</body>

</html>