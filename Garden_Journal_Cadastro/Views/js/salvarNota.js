function parseMarkdown(markdown) {
  return (
    markdown
      // Headers
      .replace(/^###### (.*$)/gim, "<h6>$1</h6>")
      .replace(/^##### (.*$)/gim, "<h5>$1</h5>")
      .replace(/^#### (.*$)/gim, "<h4>$1</h4>")
      .replace(/^### (.*$)/gim, "<h3>$1</h3>")
      .replace(/^## (.*$)/gim, "<h2>$1</h2>")
      .replace(/^# (.*$)/gim, "<h1>$1</h1>")
      // Bold and Italic
      .replace(/\*\*(.*)\*\*/gim, "<strong>$1</strong>")
      .replace(/\*(.*)\*/gim, "<em>$1</em>")
      .replace(/~~(.*)~~/gim, "<del>$1</del>")
      // Code
      .replace(/`(.*?)`/gim, "<code>$1</code>")
      .replace(/```([\s\S]*?)```/gim, "<pre><code>$1</code></pre>")
      // Blockquotes
      .replace(/^> (.*$)/gim, "<blockquote>$1</blockquote>")
      // Horizontal Rule
      .replace(/^\-\-\-$/gim, "<hr>")
      // Lists
      .replace(/^\s*[\-\*\+] (.*$)/gim, "<ul><li>$1</li></ul>")
      .replace(/^\s*\d+\. (.*$)/gim, "<ol><li>$1</li></ol>")
      // Fix nested lists
      .replace(/<\/ul>\s*<ul>/gim, "")
      .replace(/<\/ol>\s*<ol>/gim, "")
      // Links
      .replace(
        /\[([^\[]+)\]\(([^\)]+)\)/gim,
        '<a href="$2" target="_blank">$1</a>'
      )
      // Images
      .replace(/!\[([^\[]+)\]\(([^\)]+)\)/gim, '<img src="$2" alt="$1">')
      // Paragraphs
      .replace(/^\s*(\n)?(.+)/gim, function (m) {
        return /\<(\/)?(h\d|ul|ol|li|blockquote|pre|img)/.test(m)
          ? m
          : "<p>" + m + "</p>";
      })
      // Line breaks
      .replace(/\n$/gim, "<br>")
  );
}

// Conteúdo de exemplo (copiado do nota.js)
const sampleContent = `# Bem-vindo ao Editor Markdown!

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

function App() {
  return {
    // ESTADO DA APLICAÇÃO
    formData: {
      titulo: '',
      conteudo: '',
      categoriasSelecionadas: []
    },
    
    // DADOS FIXOS
    categoriasDisponiveis: [
      { id: 1, nome: 'Trabalho' },
      { id: 2, nome: 'Estudos' },
      { id: 3, nome: 'Pessoal' },
      { id: 4, nome: 'Ideias' },
      { id: 5, nome: 'Projetos' },
      { id: 6, nome: 'Lembretes' }
    ],
    
    // ESTADO DE UI
    preview: '',
    mensagem: '',
    sucesso: false,

    // MÉTODOS
    mounted() {
      this.carregarExemplo();
    },

    atualizarPreview() {
      // Usa a função parseMarkdown completa do nota.js
      this.preview = parseMarkdown(this.formData.conteudo);
    },

    limparEditor() {
      this.formData.titulo = 'Nova Anotação';
      this.formData.conteudo = '';
      this.formData.categoriasSelecionadas = [];
      this.preview = '';
      this.mensagem = '';
    },

    carregarExemplo() {
      this.formData.conteudo = sampleContent;
      this.atualizarPreview();
    },

    async salvarNota() {
  // Validação simples
    if (!this.formData.titulo.trim()) {
        this.mostrarMensagem('Por favor, insira um título para a anotação', false);
        return;
    }

    if (!this.formData.conteudo.trim()) {
        this.mostrarMensagem('A anotação não pode estar vazia', false);
        return;
    }
    
    console.log('Dados que seriam enviados:', this.formData);
    this.mostrarMensagem('Nota salva com sucesso (simulação)!', true);

     setTimeout(() => {
    this.limparEditor();
    }, 2000);

    try {
        console.log('Enviando dados:', {
        titulo: this.formData.titulo,
        conteudo: this.formData.conteudo,
        categorias: this.formData.categoriasSelecionadas
        });

        const response = await fetch('../../../Controllers/AnotacaoController.php?acao=salvar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            titulo: this.formData.titulo,
            conteudo: this.formData.conteudo,
            categorias: this.formData.categoriasSelecionadas
        })
        });

        // Verifica se a resposta é JSON
        const contentType = response.headers.get('content-type');
        let resultado;

        if (contentType && contentType.includes('application/json')) {
        resultado = await response.json();
        } else {
        // Se não for JSON, pega o texto para debug
        const textResponse = await response.text();
        console.error('Resposta não é JSON:', textResponse);
        throw new Error('O servidor retornou uma resposta inválida. Verifique o controller.');
        }

        if (response.ok) {
        this.mostrarMensagem('Nota salva com sucesso!', true);
        console.log('Nota salva:', resultado);
        
        // Limpa o formulário após salvar com sucesso
        this.limparEditor();
        } else {
        // Se a resposta não foi ok, mas é JSON (erro do servidor)
        const errorMsg = resultado.mensagem || resultado.error || 'Erro ao salvar nota';
        this.mostrarMensagem(errorMsg, false);
        console.error('Erro do servidor:', resultado);
        }

    } catch (error) {
        console.error('Erro completo:', error);
        
        if (error.message.includes('JSON')) {
        this.mostrarMensagem('Erro no servidor: resposta inválida. Verifique o controller PHP.', false);
        } else if (error.message.includes('Network')) {
        this.mostrarMensagem('Erro de rede: não foi possível conectar ao servidor.', false);
        } else {
        this.mostrarMensagem('Erro inesperado: ' + error.message, false);
        }
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
}

document.addEventListener('DOMContentLoaded', () => {
  PetiteVue.createApp({ App }).mount();
});