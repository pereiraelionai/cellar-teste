var Mascara =  {
    setMoeda: function (id) {
        var campo = document.getElementById(id);
        campo.addEventListener('input', function(event) {
            var valor = event.target.value.replace(/\D/g, ''); 
            valor = (valor / 100).toFixed(2); 
            valor = valor.toString().replace('.', ','); 
            valor = 'R$ ' + valor; 
            event.target.value = valor; 
        });
    }

}

function formatarMoeda(valor) {
    valor = parseFloat(valor);
    var valorString = valor.toFixed(2).replace('.', ',');
    return 'R$ ' + valorString.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
}