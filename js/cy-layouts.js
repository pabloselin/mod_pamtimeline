var gridlayout = cy.layout({
    name: 'grid',
    animate: true,
    randomize: false,
    fit: true,
    padding:40,
    animationEasing: 'ease-out'
});

var randomlayout = cy.layout({
    name: 'random',
    animate: true,
    randomize: false,
    fit: true,
    padding:30,
    animationEasing: 'ease-in',
    animationDuration: 500
});

var spreadlayout = cy.layout({
    name: 'spread',
    animate: true,
    randomize: true,
    fit: true,
    padding:30,
    minDist: 60,
    animationEasing: 'ease-in',
    animationDuration: 500
});

var artspreadlayout = minicy.layout({
    name: 'spread',
    animate: true,
    randomize: true,
    fit: true,
    padding:30,
    minDist: 60,
    animationEasing: 'ease-in',
    animationDuration: 500
});