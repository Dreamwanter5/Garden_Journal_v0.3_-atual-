const app = {
    formData: {
        titulo: '',
        tituloOriginal: '',
        descricao: '',
        conteudo: '',
        categoriasSelecionadas: []
    },
    // estado adicional exigido pelo template/métodos
    preview: '',
    mensagem: '',
    sucesso: false,
    errors: { titulo: '', descricao: '', conteudo: '' },

    // controle de UI
    editorColapsado: false,

    categoriasDisponiveis: [],
    novaCategoriaNome: '',
    criandoCategoria: false,
    erroCategoria: '',

    // novo: carregar nota ao montar componente
    async mounted() {
        const urlParams = new URLSearchParams(window.location.search);
        const titulo = urlParams.get('titulo');
        console.log('[Anotacao] mounted, titulo param =', titulo);

        // restaura preferência
        const persisted = localStorage.getItem('gj_editor_collapsed');
        if (persisted === '1') this.editorColapsado = true;

        // 1) carregar categorias do usuário
        await this.carregarCategorias();

        // 2) se veio título, carregar nota e aplicar seleção
        if (titulo) {
            await this.carregarNota(titulo);
        }
    },

    toggleEditor() {
        this.editorColapsado = !this.editorColapsado;
        localStorage.setItem('gj_editor_collapsed', this.editorColapsado ? '1' : '0');
    },

    async carregarCategorias() {
        try {
            const url = `/Programacao_web/Garden_Journal_v0.3_(atual)/Controllers/AnotacaoController.php?acao=categorias`;
            const resp = await fetch(url, { credentials: 'same-origin' });
            if (!resp.ok) throw new Error('Falha ao buscar categorias');
            const data = await resp.json();
            this.categoriasDisponiveis = (data.categorias || []).map(c => ({
                id: Number(c.id_categoria),
                nome: c.nome
            }));
            console.log('[Anotacao] categorias carregadas:', this.categoriasDisponiveis);
        } catch (e) {
            console.error('Erro ao carregar categorias:', e);
            this.categoriasDisponiveis = [];
        }
    },

    async carregarNota(titulo) {
        try {
            const url = `/Programacao_web/Garden_Journal_v0.3_(atual)/Controllers/AnotacaoController.php?acao=buscar&titulo=${encodeURIComponent(titulo)}`;
            console.log('[Anotacao] buscando nota:', url);
            const response = await fetch(url, { credentials: 'same-origin' });
            if (!response.ok) throw new Error('Nota não encontrada');

            const data = await response.json();
            const nota = data.nota;

            this.formData.titulo = nota.titulo || '';
            this.formData.tituloOriginal = nota.titulo || '';
            this.formData.descricao = nota.descricao || '';
            this.formData.conteudo = nota.texto || '';
            // aplica seleção convertendo para números
            this.formData.categoriasSelecionadas = Array.isArray(nota.categorias)
                ? nota.categorias.map(Number)
                : [];

            this.atualizarPreview();
            console.log('[salvarNota] Nota carregada:', nota, 'selecionadas=', this.formData.categoriasSelecionadas);
        } catch (error) {
            console.error('Erro ao carregar nota:', error);
            this.mostrarMensagem('Erro ao carregar nota para edição', false);
        }
    },

    async adicionarCategoria() {
        this.erroCategoria = '';
        const nome = (this.novaCategoriaNome || '').trim();
        if (!nome) return;

        try {
            this.criandoCategoria = true;
            const url = `/Programacao_web/Garden_Journal_v0.3_(atual)/Controllers/AnotacaoController.php?acao=categorias`;
            const resp = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({ nome })
            });

            const data = await resp.json();
            if (!resp.ok) {
                this.erroCategoria = data.mensagem || 'Falha ao criar categoria';
                return;
            }

            const c = data.categoria;
            const nova = { id: Number(c.id_categoria), nome: c.nome };
            // adiciona na lista e já marca como selecionada
            this.categoriasDisponiveis.push(nova);
            if (!this.formData.categoriasSelecionadas.includes(nova.id)) {
                this.formData.categoriasSelecionadas.push(nova.id);
            }
            this.novaCategoriaNome = '';
        } catch (e) {
            this.erroCategoria = 'Erro ao criar categoria';
        } finally {
            this.criandoCategoria = false;
        }
    },

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
        // Conteúdo agora é opcional
        this.errors.conteudo = "";
        return true;
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
            .replace(/!\[([^\]]*)\]\((\S+?)(?:\s+"([^"]*)")?\)/gim, function(_, alt, url, title) {
                return '<img src="' + url + '" alt="' + (alt||'') + (title ? '" title="' + title : '') + '">';
            })
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
        this.formData.descricao = '';
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
        // Valida apenas o título (conteúdo é opcional)
        const tituloValido = this.validarTitulo();
        if (!tituloValido) {
             this.mostrarMensagem('Por favor, corrija os erros antes de salvar.', false);
             return;
         }

        try {
            const url = '/Programacao_web/Garden_Journal_v0.3_(atual)/Controllers/AnotacaoController.php?acao=salvar';
            console.log('[Anotacao] salvando em:', url, 'payload:', {
              titulo: this.formData.titulo, titulo_original: this.formData.tituloOriginal, categorias: this.formData.categoriasSelecionadas
            });
            const response = await fetch(url, {
                method: "POST",
                body: JSON.stringify({
                    titulo: this.formData.titulo,
                    titulo_original: this.formData.tituloOriginal || null,
                    descricao: this.formData.descricao,
                    conteudo: this.formData.conteudo,
                    conteudo_html: this.preview,
                    // envia ids numéricos
                    categorias: (this.formData.categoriasSelecionadas || []).map(Number)
                }),
                headers: { "content-type": "application/json" },
                credentials: 'same-origin'
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
                this.mostrarMensagem(
                    isJson && responseBody.mensagem ? responseBody.mensagem : "Nota salva com sucesso!",
                    true
                );
                setTimeout(() => {
                    window.location.href = 'minhas_anotacoes.php'; // retorna à listagem
                }, 700);
            } else {
                if (isJson && (responseBody.error || responseBody.mensagem)) {
                    this.mostrarMensagem(responseBody.error || responseBody.mensagem, false);
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