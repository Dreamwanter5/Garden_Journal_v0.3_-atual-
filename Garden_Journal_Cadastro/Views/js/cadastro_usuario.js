const FormApp = {
    formData: {
        nome: "",
        email: "",
        senha: ""
    },
    mensagemRetorno: '',
    statusRetorno: '',
    errors: {
    },
    validarData() {
        const dataNascimento = this.formData.data_nascimento;
        if (!dataNascimento) {
            this.errors.data_nascimento = "Campo obrigatório";
            return false;
        }

        const dataAtual = new Date();
        const dataNasc = new Date(dataNascimento);
        if (dataNasc >= dataAtual) {
            this.errors.data_nascimento = "Data de nascimento inválida";
            return false;
        }
        return true;
    },
    validarSenha() {
        if (this.formData.senha.length < 8) {
            this.errors.senha = "Digite no mínimo 8 caracteres"
            return false;
        }
        return true;
    },
    async enviarRequisicao() {
        // validarData() removido porque não existe campo data_nascimento no formulário
        if (!this.validarSenha()) {
            this.statusRetorno = 'error';
            this.mensagemRetorno = this.errors.senha || 'Senha inválida';
            return;
        }

        try {
            const response = await fetch("../../../Controllers/usuarioController.php?acao=inserir", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(this.formData)
            });

            const dados = await response.json();
            this.mensagemRetorno = dados.mensagem;
            this.statusRetorno = response.ok ? "success" : "error";

            if (this.statusRetorno === "success") {
                setTimeout(() => window.location.href = "tela_do_usuario.php", 700);
            }
        } catch (error) {
            this.mensagemRetorno = "Erro na comunicação com o servidor";
            this.statusRetorno = "error";
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    PetiteVue.createApp({ FormApp }).mount();
});

function validarSenha() {
    const inputSenha = document.getElementById("senhaCaixa");
    const senha = inputSenha.value;
    // const inputConfirmSenha = document.getElementById("confirmarSenha");
    // const confirmSenha = inputConfirmSenha.value;

    if (senha.length < 8) {
        inputSenha.classList.add('is-invalid');
        document.getElementById("erroSenha").innerText = "A senha deve possuir no mínimo 8 caracteres.";
        console.log("senha paia.");
        return false;
    } 
    // else if (senha.lenght >= 8 || !senha.includes(' !#$%&()*+,-./:;<=>?@[\]^_`{|}~')) {
    //     document.getElementById("erroSenha").innerText = "Adicione um caractér especial."
    //     console.log("Falta um caractér especial")
    //     return false;
    // }

    

    else {
        document.getElementById("erroSenha").innerText = "";
        inputSenha.classList.remove("is-invalid");
        return true;
    }
}

function validarNome() {
    const inputNome = document.getElementById("nomeCompletoCaixa");
    const nome = inputNome.value;

    if (nome === "") {
        inputNome.classList.add('is-invalid');
        document.getElementById("erroNome").innerText = "Favor inserir seu nome!";
        console.log ("Nome invalido");
        return false;
    } 
    else {
        inputNome.classList.remove('is-invalid');
        inputNome.classList.add('is-valid');
        document.getElementById("erroNome").innerText = "";
        return false;
    }
}

function validarEmail() {
    const inputEmail = document.getElementById("emailCaixa");
    const email = inputEmail.value;

    if (email === "" || !email.includes("@")) { 
        inputEmail.classList.add('is-invalid');
        document.getElementById("erroEmail").innerText = "Insira um e-mail válido!";
        console.log ("email invalido");
        return false;
    } 
    else {
        inputEmail.classList.remove('is-invalid');
        inputEmail.classList.add('is-valid');
        document.getElementById("erroEmail").innerText = "";
        return true;
    }
}
