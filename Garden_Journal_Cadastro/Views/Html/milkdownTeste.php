<?php
$conteudo = "# Olá!\n\nEste é um teste do Milkdown Crepe com PHP.";
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Milkdown Crepe + PHP</title>
    <link rel="stylesheet" href="https://esm.run/@milkdown/crepe@7.17.1/style.css">
    <style>
        body {
            font-family: system-ui, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 2rem;
        }

        h2 {
            text-align: center;
            margin-bottom: 1rem;
        }

        #editor {
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: auto;
            background: white;
        }

        #status {
            text-align: center;
            margin-top: 1rem;
            color: #555;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <h1>Editor Markdown</h1>
    <div id="editor"></div>
    <div id="status">Carregando editor...</div>

    <script type="module">
        import { Crepe } from "https://esm.run/@milkdown/crepe@7.17.1";

        const crepe = new Crepe({
            root: document.getElementById("app"),
            defaultValue: "# Hello, Crepe!\n\nStart writing your markdown...",
        });
        await crepe.create();

        const status = document.getElementById('status');
        status.textContent = "Editor carregado com sucesso!";

        async function salvar() {
            const markdown = editor.getMarkdown();
            status.textContent = "Salvando...";
            try {
                await fetch("salvar_nota.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "conteudo=" + encodeURIComponent(markdown)
                });
                status.textContent = "Salvo em " + new Date().toLocaleTimeString();
            } catch {
                status.textContent = "Erro ao salvar";
            }
        }

        document.addEventListener("keydown", (e) => {
            if (e.ctrlKey && e.key === "s") {
                e.preventDefault();
                salvar();
            }
        });

        setInterval(salvar, 10000);
    </script>
</body>

</html>