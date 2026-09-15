// Customer module JS - Rifat
function customerModuleReady() {
    var note = document.getElementById('customer_module_note');
    if (note != null) {
        note.innerText = 'Customer module loaded with JS validation and AJAX/JSON support.';
    }
}
document.addEventListener('DOMContentLoaded', customerModuleReady);
