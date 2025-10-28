console.log('Debug: minhas_anotacoes.js loaded');

const notesApp = {
  notas: [],
  loading: true,
  selecionada: null,

  async mounted() {
    console.log('[notesApp] mounted');
    await this.carregarNotas();
  },

  async carregarNotas() {
    this.loading = true;
    try {
      // URL absoluta para evitar erros de path relativos
      const url = '/Programacao_web/Garden_Journal_v0.3_(atual)/Controllers/AnotacaoController.php?acao=listar';
      console.log('[notesApp] fetching', url);

      const resp = await fetch(url, {
        method: 'GET',
        credentials: 'same-origin' // garante envio do cookie de sessão
      });

      console.log('[notesApp] fetch status:', resp.status, resp.statusText);

      if (!resp.ok) {
        const text = await resp.text();
        console.error('[notesApp] erro no servidor:', resp.status, text);
        this.notas = [];
        alert('Erro ao carregar anotações: ' + resp.status + ' — veja console');
        return;
      }

      const data = await resp.json();
      console.log('[notesApp] response json:', data);
      this.notas = data.notas || [];
    } catch (e) {
      console.error("Erro ao carregar notas (fetch):", e);
      this.notas = [];
      alert('Erro ao comunicar com o servidor. Veja console.');
    } finally {
      this.loading = false;
    }
  },

  abrir(nota) {
    this.selecionada = nota;
    const modalEl = document.getElementById('previewModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
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
        return /\<(\/)?(h\d|ul|ol|li|blockquote|pre|img)/.test(m)
          ? m
          : "<p>" + m + "</p>";
      })
      .replace(/\n$/gim, "<br>");
  }
};

// Wait for DOM and mount once
document.addEventListener("DOMContentLoaded", () => {
  console.log('Debug: DOM loaded, mounting app...');
  // expose `notesApp` under the same name used in v-scope
  PetiteVue.createApp({ notesApp }).mount();
  console.log('Debug: App mounted');
});