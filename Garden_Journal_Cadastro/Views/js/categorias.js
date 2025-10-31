const catsApp = {
  categorias: [],
  loading: true,

  // criar
  nova: '',
  loadingCriar: false,
  erroCriar: '',

  // editar
  editId: 0,
  editNome: '',

  // ui
  termo: '',
  mensagem: '',
  sucesso: false,

  async mounted() {
    await this.carregar();
  },

  async carregar() {
    this.loading = true;
    try {
      const url = '/Programacao_web/Garden_Journal_v0.3_(atual)/Controllers/AnotacaoController.php?acao=categorias';
      const resp = await fetch(url, { credentials: 'same-origin' });
      const data = await resp.json();
      this.categorias = (data.categorias || []).map(c => ({
        id_categoria: Number(c.id_categoria),
        nome: c.nome
      }));
    } catch (e) {
      this.flash('Erro ao carregar categorias', false);
    } finally {
      this.loading = false;
    }
  },

  filtradas() {
    const q = (this.termo || '').toLowerCase().trim();
    return this.categorias.filter(c => (c.nome || '').toLowerCase().includes(q));
  },

  async criar() {
    this.erroCriar = '';
    const nome = (this.nova || '').trim();
    if (!nome) return;

    try {
      this.loadingCriar = true;
      const url = '/Programacao_web/Garden_Journal_v0.3_(atual)/Controllers/AnotacaoController.php?acao=categorias';
      const resp = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ nome })
      });
      const data = await resp.json();
      if (!resp.ok) {
        this.erroCriar = data.mensagem || 'Falha ao criar';
        return;
      }
      const c = data.categoria;
      this.categorias.push({ id_categoria: Number(c.id_categoria), nome: c.nome });
      this.nova = '';
      this.flash('Categoria criada', true);
    } catch (e) {
      this.erroCriar = 'Erro ao criar categoria';
    } finally {
      this.loadingCriar = false;
    }
  },

  iniciarEdicao(c) {
    this.editId = c.id_categoria;
    this.editNome = c.nome;
  },

  cancelarEdicao() {
    this.editId = 0;
    this.editNome = '';
  },

  async salvarEdicao() {
    const id = this.editId;
    const nome = (this.editNome || '').trim();
    if (!id || !nome) return;

    try {
      const url = '/Programacao_web/Garden_Journal_v0.3_(atual)/Controllers/AnotacaoController.php?acao=categorias';
      const resp = await fetch(url, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ id_categoria: id, nome })
      });
      const data = await resp.json().catch(() => ({}));
      if (!resp.ok) {
        this.flash(data.mensagem || 'Falha ao atualizar', false);
        return;
      }
      const idx = this.categorias.findIndex(x => x.id_categoria === id);
      if (idx >= 0) this.categorias[idx].nome = nome;
      this.cancelarEdicao();
      this.flash('Categoria atualizada', true);
    } catch (e) {
      this.flash('Erro ao atualizar categoria', false);
    }
  },

  async remover(c) {
    if (!confirm(`Excluir a categoria "${c.nome}"?`)) return;
    try {
      const url = `/Programacao_web/Garden_Journal_v0.3_(atual)/Controllers/AnotacaoController.php?acao=categorias&id=${c.id_categoria}`;
      const resp = await fetch(url, { method: 'DELETE', credentials: 'same-origin' });
      if (resp.status === 204) {
        this.categorias = this.categorias.filter(x => x.id_categoria !== c.id_categoria);
        this.flash('Categoria excluída', true);
      } else {
        const data = await resp.json().catch(() => ({}));
        this.flash(data.mensagem || 'Falha ao excluir', false);
      }
    } catch (e) {
      this.flash('Erro ao excluir categoria', false);
    }
  },

  flash(msg, ok) {
    this.mensagem = msg;
    this.sucesso = !!ok;
    setTimeout(() => (this.mensagem = ''), 3000);
  }
};

document.addEventListener('DOMContentLoaded', () => {
  PetiteVue.createApp({ catsApp }).mount();
});