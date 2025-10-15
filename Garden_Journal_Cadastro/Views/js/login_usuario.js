const FormApp = {
    formData: {
        email: '',
        senha: ''
    },
    mensagemRetorno: '',
    async autenticar() {
        try {
            const response = await fetch("../../../Controllers/usuarioController.php?acao=autenticar", {
                method: "POST",
                
                body: JSON.stringify(this.formData),
                headers: { "Content-Type": "application/json" }
            });
            
            const dados = await response.json();
            
            if (response.ok) {
                window.location.href = "../Html/Tela_do_Usuario.php";
            } else {
                this.mensagemRetorno = dados.mensagem;
            }
        } catch (error) {
            this.mensagemRetorno = "Erro na comunicação com o servidor";
        }
    }
}

document.addEventListener("DOMContentLoaded", () => {
    PetiteVue.createApp({ FormApp }).mount();
});