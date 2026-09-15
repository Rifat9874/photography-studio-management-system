// Photographer module JS - Mahin
function photographerModuleReady() {
    var note = document.getElementById('photographer_module_note');
    if (note != null) {
        note.innerText = 'Photographer module loaded with portfolio, availability and assigned shoot workflow.';
    }
}
document.addEventListener('DOMContentLoaded', photographerModuleReady);
