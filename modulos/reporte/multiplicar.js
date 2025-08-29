// Todo el código JavaScript va aquí, directamente en el HTML
const select1 = document.getElementById('partida');
const select2 = document.getElementById('valor');
const resultadoInput = document.getElementById('resultado');

function calcularMultiplicacion() {
    const valor1 = parseInt(select1.value);      // id de partidas (entero)
    const valor2 = parseFloat(select2.value);    // valor (decimal)

    if (!isNaN(valor1) && !isNaN(valor2)) {
        const multiplicacion = valor1 * valor2;
        resultadoInput.value = multiplicacion;
    } else {
        resultadoInput.value = '';
    }
}

select1.addEventListener('change', calcularMultiplicacion);
select2.addEventListener('change', calcularMultiplicacion);

// Ejecutar al cargar por si hay valores por defecto
calcularMultiplicacion();
