// FUNCIONES PARA VALIDAR ENTRADAS

function validarN(e) { // 1
//patron = /\d/; // Solo acepta números
//patron = /\w/; // Acepta números y letras
//patron = /\D/; // No acepta números
//patron =/[A-Za-zñÑ\s]/; // igual que el ejemplo, pero acepta también las letras 
//onkeypress="return validar(event);"

    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8) return true; // 3
    patron =/\d/; // 4
    te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
} 

function validarL(e) { // 1
//patron = /\d/; // Solo acepta números
//patron = /\w/; // Acepta números y letras
//patron = /\D/; // No acepta números
//patron =/[A-Za-zñÑ\s]/; // igual que el ejemplo, pero acepta también las letras 
//onkeypress="return validar(event);"

    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8) return true; // 3
    patron =/[A-Za-zñÑáÁéÉíÍóÓúÚ\s]/; // 4
    te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
} 

function validarEM(e) { // 1
//patron = /\d/; // Solo acepta números
//patron = /\w/; // Acepta números y letras
//patron = /\D/; // No acepta números
//patron =/[A-Za-zñÑ\s]/; // igual que el ejemplo, pero acepta también las letras 
//onkeypress="return validar(event);"

    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8) return true; // 3
    patron =/[A-Za-zñÑáÁéÉíÍóÓúÚ@.\s]/; // 4
    te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
} 

function validarE(e) { // 1
//patron = /\d/; // Solo acepta números
//patron = /\w/; // Acepta números y letras
//patron = /\D/; // No acepta números
//patron =/[A-Za-zñÑ\s]/; // igual que el ejemplo, pero acepta también las letras 
//onkeypress="return validar(event);"

    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8) return true; // 3
    if (tecla==45) return true;
    if (tecla==48) return true;
    patron =/[1-9\s]/; // 4
    te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
} 

function validarF(e) { // 1
//patron = /\d/; // Solo acepta números
//patron = /\w/; // Acepta números y letras
//patron = /\D/; // No acepta números
//patron =/[A-Za-zñÑ\s]/; // igual que el ejemplo, pero acepta también las letras 
//onkeypress="return validar(event);"

    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8) return true; // 3
    if (tecla==47) return true;
    if (tecla==48) return true;
    patron =/[1-9\s]/; // 4
    te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
}

