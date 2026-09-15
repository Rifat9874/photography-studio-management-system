// Booking Manager module JS - Riahna
function managerModuleReady() {
    var note = document.getElementById('manager_module_note');
    if (note != null) {
        note.innerText = 'Booking Manager module loaded with AJAX photographer loading and approval workflow.';
    }
}
document.addEventListener('DOMContentLoaded', managerModuleReady);
