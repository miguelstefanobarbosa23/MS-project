

document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.getElementById('form-contato');
    if (!formulario) return; 

    const campos = {
        nome:     formulario.querySelector('#nome'),
        email:    formulario.querySelector('#email'),
        assunto:  formulario.querySelector('#assunto'),
        mensagem: formulario.querySelector('#mensagem'),
    };

    Object.values(campos).forEach(function (campo) {
        if (!campo) return;
        campo.addEventListener('blur', function () { validarCampo(campo); });
        
        campo.addEventListener('input', function () {
            if (campo.closest('.campo').classList.contains('campo--erro')) {
                validarCampo(campo);
            }
        });
    });

    formulario.addEventListener('submit', function (evento) {
        let formularioValido = true;

        Object.values(campos).forEach(function (campo) {
            if (!campo) return;
            const valido = validarCampo(campo);
            if (!valido) formularioValido = false;
        });

        if (!formularioValido) {
            evento.preventDefault(); 
            const primeiroComErro = formulario.querySelector('.campo--erro input, .campo--erro textarea');
            if (primeiroComErro) primeiroComErro.focus();
            return;
        }

        const botaoEnviar = formulario.querySelector('button[type="submit"]');
        if (botaoEnviar) botaoEnviar.classList.add('enviando');
    });

    function validarCampo(campo) {
        const valor = campo.value.trim();
        let mensagemErro = '';

        switch (campo.id) {
            case 'nome':
                if (valor.length === 0) {
                    mensagemErro = 'Digite o seu nome.';
                } else if (valor.length < 3) {
                    mensagemErro = 'O nome deve ter pelo menos 3 caracteres.';
                }
                break;

            case 'email': {
                
                const padraoEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (valor.length === 0) {
                    mensagemErro = 'Digite o seu e-mail.';
                } else if (!padraoEmail.test(valor)) {
                    mensagemErro = 'Digite um e-mail válido (ex: nome@email.com).';
                }
                break;
            }

            case 'assunto':
                if (valor.length === 0) {
                    mensagemErro = 'Digite um assunto.';
                } else if (valor.length < 3) {
                    mensagemErro = 'O assunto deve ter pelo menos 3 caracteres.';
                }
                break;

            case 'mensagem':
                if (valor.length === 0) {
                    mensagemErro = 'Escreva sua mensagem.';
                } else if (valor.length < 10) {
                    mensagemErro = 'A mensagem deve ter pelo menos 10 caracteres.';
                }
                break;
        }

        const wrapperCampo = campo.closest('.campo');
        const elementoErro = wrapperCampo.querySelector('.campo__mensagem-erro');

        if (mensagemErro) {
            wrapperCampo.classList.add('campo--erro');
            wrapperCampo.classList.remove('campo--valido');
            if (elementoErro) elementoErro.textContent = mensagemErro;
            return false;
        }

        wrapperCampo.classList.remove('campo--erro');
        wrapperCampo.classList.add('campo--valido');
        if (elementoErro) elementoErro.textContent = '';
        return true;
    }
});
