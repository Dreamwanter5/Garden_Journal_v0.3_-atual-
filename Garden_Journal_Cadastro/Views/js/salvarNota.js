const app = {
    formData: {
        titulo: '',
        descricao: '',         // <--- adicionada
        conteudo: '',
        categoriasSelecionadas: []
    },
    
    categoriasDisponiveis: [
        { id: 1, nome: 'Trabalho' },
        { id: 2, nome: 'Estudos' },
        { id: 3, nome: 'Pessoal' },
        { id: 4, nome: 'Ideias' },
        { id: 5, nome: 'Projetos' },
        { id: 6, nome: 'Lembretes' }
    ],
    
    preview: '',
    mensagem: '',
    sucesso: false,
    errors: {},

    // Validações
    validarTitulo() {
        if (this.formData.titulo.trim() === "") {
            this.errors.titulo = "Favor inserir um título para a anotação";
            return false;
        } else {
            this.errors.titulo = "";
            return true;
        }
    },

    validarConteudo() {
        if (this.formData.conteudo.trim() === "") {
            this.errors.conteudo = "A anotação não pode estar vazia";
            return false;
        } else {
            this.errors.conteudo = "";
            return true;
        }
    },

    // Método para coletar dados do formulário (alternativa)
    coletarDadosFormulario() {
        const form = document.getElementById('form-anotacao');
        const formData = new FormData(form);
        
        // Converte para objeto simples
        const dados = {
            titulo: formData.get('titulo'),
            conteudo: formData.get('conteudo'),
            categorias: formData.getAll('categorias[]'), // Para checkboxes múltiplos
            conteudo_html: this.preview
        };
        
        return dados;
    },

    // Parser Markdown (mantido igual)
    parseMarkdown(markdown) {
        return markdown
            .replace(/^###### (.*$)/gim, "<h6>$1</h6>")
            .replace(/^##### (.*$)/gim, "<h5>$1</h5>")
            .replace(/^#### (.*$)/gim, "<h4>$1</h4>")
            .replace(/^### (.*$)/gim, "<h3>$1</h3>")
            .replace(/^## (.*$)/gim, "<h2>$1</h2>")
            .replace(/^# (.*$)/gim, "<h1>$1</h1>")
            .replace(/\*\*(.*)\*\*/gim, "<strong>$1</strong>")
            .replace(/\*(.*)\*/gim, "<em>$1</em>")
            .replace(/~~(.*)~~/gim, "<del>$1</del>")
            .replace(/`(.*?)`/gim, "<code>$1</code>")
            .replace(/```([\s\S]*?)```/gim, "<pre><code>$1</code></pre>")
            .replace(/^> (.*$)/gim, "<blockquote>$1</blockquote>")
            .replace(/^\-\-\-$/gim, "<hr>")
            .replace(/^\s*[\-\*\+] (.*$)/gim, "<ul><li>$1</li></ul>")
            .replace(/^\s*\d+\. (.*$)/gim, "<ol><li>$1</li></ol>")
            .replace(/<\/ul>\s*<ul>/gim, "")
            .replace(/<\/ol>\s*<ol>/gim, "")
            .replace(/\[([^\[]+)\]\(([^\)]+)\)/gim, '<a href="$2" target="_blank">$1</a>')
            .replace(/!\[([^\[]+)\]\(([^\)]+)\)/gim, '<img src="$2" alt="$1">')
            .replace(/^\s*(\n)?(.+)/gim, function (m) {
                return /\<(\/)?(h\d|ul|ol|li|blockquote|pre|img)/.test(m)
                    ? m
                    : "<p>" + m + "</p>";
            })
            .replace(/\n$/gim, "<br>");
    },

    atualizarPreview() {
        this.preview = this.parseMarkdown(this.formData.conteudo);
    },

    limparEditor() {
        this.formData.titulo = '';
        this.formData.descricao = '';         // <--- limpa
        this.formData.conteudo = '';
        this.formData.categoriasSelecionadas = [];
        this.preview = '';
        this.mensagem = '';
        this.errors = {};
    },

    carregarExemplo() {
        this.formData.conteudo = `# Bem-vindo ao Editor Markdown!

Este é um exemplo de **editor Markdown** com visualização em tempo real.

## Funcionalidades

- ✨ **Visualização em tempo real**
- 🎨 **Design moderno e responsivo**
- 📱 **Compatível com dispositivos móveis**
- 🚀 **Rápido e leve**

## Exemplos de Sintaxe

### Texto Formatado

**Negrito**, *itálico*, ~~tachado~~, e \`código inline\`.

### Listas

1. Item ordenado 1
2. Item ordenado 2
3. Item ordenado 3

- Item não ordenado
- Outro item
  - Subitem

### Código

\`\`\`javascript
function exemplo() {
    console.log("Olá, Mundo!");
    return true;
}
\`\`\`

### Citações

> Esta é uma citação elegante que destaca informações importantes.

### Links e Imagens

Visite o [Markdown Guide](https://www.markdownguide.org) para aprender mais.

---

*Divirta-se escrevendo!*`;
        this.atualizarPreview();
    },

    // Método principal de salvamento
    async salvarNota() {
        // Validações
        const tituloValido = this.validarTitulo();
        const conteudoValido = this.validarConteudo();

        if (!tituloValido || !conteudoValido) {
            this.mostrarMensagem('Por favor, corrija os erros antes de salvar.', false);
            return;
        }

        try {
            // OPÇÃO 1: Usando FormData tradicional (para envio de arquivos também)
            const formData = new FormData();
            formData.append('titulo', this.formData.titulo);
            formData.append('descricao', this.formData.descricao);   // <--- enviado
            formData.append('conteudo', this.formData.conteudo);
            formData.append('conteudo_html', this.preview);
            this.formData.categoriasSelecionadas.forEach(categoria => {
                formData.append('categorias[]', categoria);
            });

            // OPÇÃO 2: Usando JSON (mantendo a abordagem atual)
            const response = await fetch('../../../Controllers/AnotacaoController.php?acao=salvar', {
                method: "POST",
                body: JSON.stringify({
                    titulo: this.formData.titulo,
                    descricao: this.formData.descricao,   // <--- enviado
                    conteudo: this.formData.conteudo,
                    conteudo_html: this.preview,
                    categorias: this.formData.categoriasSelecionadas
                }),
                headers: {
                    "content-type": "application/json"
                }
            });

            let responseBody;
            let isJson = false;
            try {
                responseBody = await response.clone().json();
                isJson = true;
            } catch (e) {
                responseBody = await response.text();
            }

            if (response.ok) {
                if (isJson && responseBody.mensagem) {
                    this.mostrarMensagem(responseBody.mensagem, true);
                } else {
                    this.mostrarMensagem("Nota salva com sucesso!", true);
                }
                
                // redireciona para a tela do usuário após salvar
                setTimeout(() => {
                    window.location.href = 'tela_do_usuario.php';
                }, 700);
            } else {
                if (isJson && responseBody.error) {
                    this.mostrarMensagem(responseBody.error, false);
                } else if (isJson && responseBody.mensagem) {
                    this.mostrarMensagem(responseBody.mensagem, false);
                } else {
                    this.mostrarMensagem("Erro ao salvar nota", false);
                }
            }
        } catch (error) {
            console.error('Erro:', error);
            this.mostrarMensagem('Erro de conexão', false);
        }
    },

    downloadHTML() {
        const htmlContent = `
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>${this.formData.titulo}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            color: #333;
        }
        h1, h2, h3 { color: #2d3748; }
        code { background: #f7fafc; padding: 2px 6px; border-radius: 4px; }
        pre { background: #2d3748; color: #e2e8f0; padding: 1em; border-radius: 8px; overflow-x: auto; }
        blockquote { border-left: 4px solid #4c6ef5; padding-left: 1em; margin-left: 0; color: #4a5568; font-style: italic; }
    </style>
</head>
<body>
    <h1>${this.formData.titulo}</h1>
    <div>${this.preview}</div>
</body>
</html>`;

        const blob = new Blob([htmlContent], { type: "text/html" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `${this.formData.titulo}.html`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    },

    mostrarMensagem(texto, ehSucesso) {
        this.mensagem = texto;
        this.sucesso = ehSucesso;
        setTimeout(() => {
            this.mensagem = '';
        }, 5000);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const mountedApp = PetiteVue.createApp({ app }).mount();

});