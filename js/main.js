
document.addEventListener('DOMContentLoaded', function () {
    inicializarMenuHamburguer();
    atualizarAnoRodape();
    inicializarRevelarAoRolar();
    inicializarFiltroProjetos();
    inicializarModalProjeto();
    inicializarConfirmacaoExclusao();
    inicializarAutoFecharAlertas();
    inicializarAlertaPorUrl();
});

function inicializarMenuHamburguer() {
    const botao = document.querySelector('.nav__hamburguer');
    const lista = document.querySelector('.nav__lista');
    if (!botao || !lista) return;

    botao.addEventListener('click', function () {
        const estaAberto = lista.classList.toggle('aberto');
        botao.classList.toggle('ativo', estaAberto);
        botao.setAttribute('aria-expanded', estaAberto ? 'true' : 'false');
    });

   
    lista.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            lista.classList.remove('aberto');
            botao.classList.remove('ativo');
            botao.setAttribute('aria-expanded', 'false');
        });
    });
}

function atualizarAnoRodape() {
    const elementoAno = document.getElementById('ano-atual');
    if (elementoAno) {
        elementoAno.textContent = new Date().getFullYear();
    }
}

function inicializarRevelarAoRolar() {
    const elementos = document.querySelectorAll('.revelar');
    if (elementos.length === 0) return;

    const observador = new IntersectionObserver(function (entradas) {
        entradas.forEach(function (entrada) {
            if (entrada.isIntersecting) {
                entrada.target.classList.add('visivel');

               
                const barra = entrada.target.querySelector('.habilidade__progresso');
                if (barra) {
                    const nivel = barra.getAttribute('data-nivel') || '0';
                    barra.style.setProperty('--alvo', nivel + '%');
                    barra.classList.add('preencher');
                }

                observador.unobserve(entrada.target); 
            }
        });
    }, { threshold: 0.15 });

    elementos.forEach(function (elemento) { observador.observe(elemento); });
}


function inicializarFiltroProjetos() {
    const botoesFiltro = document.querySelectorAll('.filtro');
    const cards = document.querySelectorAll('.card-projeto');
    if (botoesFiltro.length === 0 || cards.length === 0) return;

    botoesFiltro.forEach(function (botao) {
        botao.addEventListener('click', function () {
            
            botoesFiltro.forEach(function (b) { b.classList.remove('ativo'); });
            botao.classList.add('ativo');

            const tecnologiaEscolhida = botao.getAttribute('data-tecnologia');

            cards.forEach(function (card) {
                const tecnologiasDoCard = (card.getAttribute('data-tecnologias') || '').split(',');
                const deveMostrar = tecnologiaEscolhida === 'todos' || tecnologiasDoCard.includes(tecnologiaEscolhida);
                card.classList.toggle('escondido', !deveMostrar);
            });
        });
    });
}

function inicializarModalProjeto() {
    const modal = document.getElementById('modal-projeto');
    const botoesDetalhes = document.querySelectorAll('.abrir-detalhes');
    if (!modal || botoesDetalhes.length === 0) return;

    const imagemModal    = document.getElementById('modal-projeto-imagem');
    const tituloModal    = document.getElementById('modal-projeto-titulo');
    const tagsModal      = document.getElementById('modal-projeto-tags');
    const descricaoModal = document.getElementById('modal-projeto-descricao');
    const linkModal      = document.getElementById('modal-projeto-link');
    const avisoSemLink   = document.getElementById('modal-projeto-sem-link');

    let elementoQueAbriu = null; 

    botoesDetalhes.forEach(function (botao) {
        botao.addEventListener('click', function () {
            const card = botao.closest('.card-projeto');
            if (!card) return;

            const imagemCard = card.querySelector('.card-projeto__imagem img');
            const tagsCard   = card.querySelector('.card-projeto__tags');
            const link       = card.getAttribute('data-link') || '';

            imagemModal.src = imagemCard ? imagemCard.src : '';
            imagemModal.alt = imagemCard ? imagemCard.alt : '';
            tituloModal.textContent = card.querySelector('h3').textContent;
            descricaoModal.textContent = card.getAttribute('data-descricao-completa') ||
                                         card.querySelector('.card-projeto__corpo p').textContent;

            
            tagsModal.innerHTML = tagsCard ? tagsCard.innerHTML : '';

            if (link && link !== '#') {
                linkModal.href = link;
                linkModal.style.display = '';
                avisoSemLink.style.display = 'none';
            } else {
                linkModal.style.display = 'none';
                avisoSemLink.style.display = 'inline-block';
            }

            elementoQueAbriu = botao;
            abrirModal();
        });
    });

    modal.querySelectorAll('[data-fechar-modal]').forEach(function (elemento) {
        elemento.addEventListener('click', fecharModal);
    });

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape' && modal.classList.contains('aberto')) {
            fecharModal();
        }
    });

    function abrirModal() {
        modal.classList.add('aberto');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-aberto');
        modal.querySelector('.modal-projeto__fechar').focus();
    }

    function fecharModal() {
        modal.classList.remove('aberto');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-aberto');
        if (elementoQueAbriu) elementoQueAbriu.focus();  
    }
}

function inicializarConfirmacaoExclusao() {
    document.querySelectorAll('.link-excluir').forEach(function (link) {
        link.addEventListener('click', function (evento) {
            const mensagem = link.getAttribute('data-confirmar') || 'Tem certeza que deseja excluir este registro?';
            if (!window.confirm(mensagem)) {
                evento.preventDefault(); 
            }
        });
    });
}


function inicializarAutoFecharAlertas() {
    const alertas = document.querySelectorAll('.alerta[data-auto-fechar]');
    alertas.forEach(function (alerta) {
        setTimeout(function () {
            alerta.style.transition = 'opacity 0.5s ease';
            alerta.style.opacity = '0';
            setTimeout(function () { alerta.remove(); }, 500);
        }, 5000);
    });
}

function inicializarAlertaPorUrl() {
    const areaAlerta = document.getElementById('area-alerta');
    if (!areaAlerta) return;

    const parametros = new URLSearchParams(window.location.search);
    const status = parametros.get('status');
    if (!status) return; 

    const mensagens = {
        sucesso: 'Mensagem enviada com sucesso! Em breve retornaremos o contato.',
        erro: 'Não foi possível enviar sua mensagem. Verifique os dados e tente novamente.',
    };
    const textoAlerta = mensagens[status] || null;
    if (!textoAlerta) return;

    const divAlerta = document.createElement('div');
    divAlerta.className = status === 'sucesso' ? 'alerta alerta--sucesso' : 'alerta alerta--erro';
    divAlerta.setAttribute('role', 'status');
    divAlerta.setAttribute('data-auto-fechar', '');
    divAlerta.textContent = textoAlerta;
    areaAlerta.appendChild(divAlerta);

    setTimeout(function () {
        divAlerta.style.transition = 'opacity 0.5s ease';
        divAlerta.style.opacity = '0';
        setTimeout(function () { divAlerta.remove(); }, 500);
    }, 5000);

    const urlLimpa = window.location.pathname;
    window.history.replaceState({}, '', urlLimpa);
}
