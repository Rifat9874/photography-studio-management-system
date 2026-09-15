// Admin module JS - Jubayer
function adminModuleReady() {
    var note = document.getElementById('admin_module_note');
    if (note != null) {
        note.innerText = 'Admin module loaded with role check, PHP validation and AJAX dashboard support.';
    }
}
document.addEventListener('DOMContentLoaded', adminModuleReady);
