// salvarNota.js - Versão Simplificada
class GerenciadorNotas {
    constructor() {
        this.ultimoSave = null;
        this.salvando = false;
        this.init();
    }

    init() {
        this.configurarAutoSave();
        this.configurarBotaoSalvar();
    }

    configurarAutoSave() {
        const editor = document.getElementById("editor");
        
        // Salva a cada 10 segundos (reduzido para evitar excesso)
        let timeoutSave;
        editor.addEventListener("input", () => {
            clearTimeout(timeoutSave);
            timeoutSave = setTimeout(() => {
                this.salvarNota();
            }, 10000); // 10 segundos
        });
    }
    
    configurarBotaoSalvar() {
        const actionsDiv = document.querySelector('.actions');
        if (actionsDiv && !document.getElementById('salvar-btn')) {
            const salvarBtn = document.createElement('button');
            salvarBtn.id = 'salvar-btn';
            salvarBtn.innerHTML = '💾 Salvar Nota';
            salvarBtn.className = 'btn btn-success';
            salvarBtn.addEventListener('click', () => this.salvarNota());
            actionsDiv.appendChild(salvarBtn);
        }
    }

    async salvarNota() {
        if (this.salvando) {
            console.log('Já está salvando...');
            return;
        }
        
        this.salvando = true;
        this.mostrarStatus('🔄 Salvando...');
        
        try {
            // Pega o título (precisa ser editável - vamos ajustar isso)
            const titulo = document.getElementById('titulo-nota')?.value || 'Nova Anotação';
            
            // Pega o conteúdo markdown do editor
            const conteudoMarkdown = document.getElementById("editor")?.value || '';
            
            const categorias = getSelectedCategories();

            const nota = {
                titulo,
                conteudoMarkdown,
                categorias,
                data_criacao: new Date().toISOString()
            };


            // Usa SEU parser para gerar o HTML
            const conteudoHTML = parseMarkdown(conteudoMarkdown);

            console.log('Enviando dados:', { titulo, conteudoMarkdown, conteudoHTML });

            const dados = {
                titulo: titulo,
                conteudo_markdown: conteudoMarkdown,
                conteudo_html: conteudoHTML,
                categorias: [] // Por enquanto vazio, pode adicionar depois
            };

            const response = await fetch('../../../../Controllers/salvar-nota.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(dados)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const resultado = await response.json();
            console.log('Resposta do servidor:', resultado);

            if (resultado.status === 'success') {
                this.mostrarFeedback('Nota salva com sucesso!', 'success');
                this.ultimoSave = new Date();
            } else {
                throw new Error(resultado.message);
            }

        } catch (error) {
            console.error('Erro ao salvar nota:', error);
            this.mostrarFeedback('Erro ao salvar nota: ' + error.message, 'error');
        } finally {
            this.salvando = false;
            this.esconderStatus();
        }
    }

    mostrarStatus(mensagem) {
        let statusDiv = document.getElementById('status-salvamento');
        if (!statusDiv) {
            statusDiv = document.createElement('div');
            statusDiv.id = 'status-salvamento';
            statusDiv.className = 'mt-2';
            document.querySelector('.container').appendChild(statusDiv);
        }
        statusDiv.innerHTML = `<small class="text-muted">${mensagem}</small>`;
        statusDiv.style.display = 'block';
    }

    esconderStatus() {
        const statusDiv = document.getElementById('status-salvamento');
        if (statusDiv) {
            statusDiv.style.display = 'none';
        }
    }

    mostrarFeedback(mensagem, tipo) {
        const feedbackAnterior = document.getElementById('feedback-nota');
        if (feedbackAnterior) {
            feedbackAnterior.remove();
        }

        const feedback = document.createElement('div');
        feedback.id = 'feedback-nota';
        feedback.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} mt-3`;
        feedback.textContent = mensagem;

        const actions = document.querySelector('.actions');
        if (actions) {
            actions.parentNode.insertBefore(feedback, actions);
        }

        setTimeout(() => {
            if (feedback.parentNode) {
                feedback.remove();
            }
        }, 3000);
    }
}

function getSelectedCategories() {
  const checkboxes = document.querySelectorAll('.categorias-list input[type="checkbox"]');
  const selected = [];
  checkboxes.forEach(checkbox => {
    if (checkbox.checked) {
      selected.push(checkbox.value);
    }
  });
  return selected;
}

document.getElementById('btn-add-categoria').addEventListener('click', function() {
  const novaCategoria = document.getElementById('nova-categoria').value.trim();
  if (novaCategoria) {
    const categoriasList = document.querySelector('.categorias-list');
    const randomId = 'cat-' + Math.random().toString(36).substr(2, 9);
    
    const newCategory = `
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="${novaCategoria}" id="${randomId}">
        <label class="form-check-label" for="${randomId}">${novaCategoria}</label>
      </div>
    `;
    
    categoriasList.insertAdjacentHTML('beforeend', newCategory);
    document.getElementById('nova-categoria').value = '';
  }
});

// Inicializa quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.gerenciadorNotas = new GerenciadorNotas();
});