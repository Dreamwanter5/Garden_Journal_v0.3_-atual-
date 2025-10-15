document.addEventListener('DOMContentLoaded', function() {
    const images = [
        {
            src: '../img/Placeholder1.webp',
            desc: 'Com o Garden Journal você pode juntar  anotações de qualquer lugar e ainda mantê-las organizadas sem que se misturem com outras',
            alt: 'Organização'
        },
        {
            src: '../img/Placeholder2.jpeg',
            desc: 'Que o seu jardim a cada instante que você trabalhar nele adquira a sua cara e suas preferências.',
            alt: 'Personalização'
        },
        {
            src: '../img/Placeholder3.webp',
            desc: 'Com o sistema de tags inseridas pelo o usuário, é possível deixar todas suas notas devidamente organizadas com seus pares.',
            alt: 'Produtividade'
        }
    ];

    const buttons = document.querySelectorAll('.filter-btn');
    const img = document.getElementById('filter-image');
    const desc = document.getElementById('filter-desc');

    function activateButton(idx) {
        img.src = images[idx].src;
        img.alt = images[idx].alt;
        desc.textContent = images[idx].desc;
        buttons.forEach(b => b.classList.remove('active'));
        buttons[idx].classList.add('active');
    }

    buttons.forEach((btn, idx) => {
        btn.addEventListener('click', function() {
            activateButton(idx);
        });
    });

    activateButton(0);
});
