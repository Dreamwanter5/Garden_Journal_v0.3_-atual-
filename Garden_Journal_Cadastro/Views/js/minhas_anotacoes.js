console.log('Debug: minhas_anotacoes.js loaded');

const notesApp = {
  notas: [],
  loading: true,
  selecionada: null,

  // filtros
  termo: '',
  categoriaSelecionada: '',
  categoriasDisponiveis: [],

  async mounted() {
    console.log('[notesApp] mounted');
    await this.carregarCategorias();
    await this.carregarNotas();
  },

  async carregarNotas() {
    this.loading = true;
    try {
      const url = '/Programacao_web/Garden_Journal_v0.3_(atual)/Controllers/AnotacaoController.php?acao=listar';
      console.log('[notesApp] fetching', url);
      const resp = await fetch(url, { method: 'GET', credentials: 'same-origin' });
      console.log('[notesApp] fetch status:', resp.status, resp.statusText);
      if (!resp.ok) {
        const text = await resp.text();
        console.error('[notesApp] erro no servidor:', resp.status, text);
        this.notas = [];
        return;
      }
      const data = await resp.json();
      console.log('[notesApp] response json:', data);
      this.notas = data.notas || [];
    } catch (e) {
      console.error("Erro ao carregar notas (fetch):", e);
      this.notas = [];
    } finally {
      this.loading = false;
    }
  },

  async carregarCategorias() {
    try {
      const url = '/Programacao_web/Garden_Journal_v0.3_(atual)/Controllers/AnotacaoController.php?acao=categorias';
      const resp = await fetch(url, { credentials: 'same-origin' });
      if (!resp.ok) throw new Error('Falha ao buscar categorias');
      const data = await resp.json();
      // usar NOME de categoria (as notas trazem nomes concatenados)
      const nomes = (data.categorias || []).map(c => (c.nome || '').trim()).filter(Boolean);
      this.categoriasDisponiveis = Array.from(new Set(nomes)).sort((a,b)=>a.localeCompare(b));
      console.log('[notesApp] categorias para filtro:', this.categoriasDisponiveis);
    } catch (e) {
      console.error('Erro ao carregar categorias para filtro:', e);
      this.categoriasDisponiveis = [];
    }
  },

  filteredNotas() {
    const q = (this.termo || '').toLowerCase().trim();
    const cat = (this.categoriaSelecionada || '').toLowerCase().trim();

    return (this.notas || []).filter(n => {
      const okTitle = q ? (n.titulo || '').toLowerCase().includes(q) : true;

      if (!cat) return okTitle;

      const list = this.categoriesList(n).map(s => s.toLowerCase());
      const okCat = list.includes(cat);

      return okTitle && okCat;
    });
  },

  limparFiltros() {
    this.termo = '';
    this.categoriaSelecionada = '';
  },

  abrir(nota) {
    this.selecionada = nota;
    const modalEl = document.getElementById('previewModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
  },

  excluir(nota) {
    if (!confirm(`Excluir a anotação "${nota.titulo}"? Esta ação não pode ser desfeita.`)) return;
    const url = `/Programacao_web/Garden_Journal_v0.3_(atual)/Controllers/AnotacaoController.php?acao=excluir&titulo=${encodeURIComponent(nota.titulo)}`;
    fetch(url, { method: 'DELETE', credentials: 'same-origin' })
      .then(async resp => {
        const data = await resp.json().catch(()=>({}));
        if (resp.ok) {
          this.notas = this.notas.filter(n => n.titulo !== nota.titulo);
          console.log('[notesApp] anotação excluída');
        } else {
          alert(data.mensagem || 'Falha ao excluir');
        }
      })
      .catch(e => {
        console.error('Erro ao excluir:', e);
        alert('Erro de conexão ao excluir');
      });
  },

  categoriesList(nota) {
    if (!nota) return [];
    // notas.listar retorna nomes concatenados
    if (Array.isArray(nota.categorias)) return nota.categorias;
    const s = (nota.categorias || '').toString();
    return s.split(',').map(x => x.trim()).filter(Boolean);
  },

  renderMarkdown(markdown = '') {
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
      .replace(/!\[([^\]]*)\]\((\S+?)(?:\s+"([^"]*)")?\)/gim, function (_, alt, url, title) {
        return '<img src="' + url + '" alt="' + (alt||'') + (title ? '" title="' + title : '') + '">';
      })
      .replace(/\[([^\[]+)\]\(([^\)]+)\)/gim, '<a href="$2" target="_blank">$1</a>')
      .replace(/^\s*(\n)?(.+)/gim, function (m) {
        return /\<(\/)?(h\d|ul|ol|li|blockquote|pre|img)/.test(m) ? m : "<p>" + m + "</p>";
      })
      .replace(/\n$/gim, "<br>");
  }
};

document.addEventListener("DOMContentLoaded", () => {
  console.log('Debug: DOM loaded, mounting app...');
  PetiteVue.createApp({ notesApp }).mount('#app-root');
  console.log('Debug: App mounted');
});