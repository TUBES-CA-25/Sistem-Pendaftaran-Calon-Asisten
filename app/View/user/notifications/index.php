<?php
/**
 * Notification View
 */
?>

<!-- Page Header -->
<?php
    $title = 'Notifikasi';
    $subtitle = 'Daftar pesan dan pemberitahuan';
    $icon = 'bx bx-bell';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<div class="container-fluid px-4 py-4" style="margin-top: -30px; position: relative; z-index: 10;">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">
                <i class='bx bx-message-square-dots'></i> Pesan Notifikasi
            </h5>
        </div>
        <div class="card-body p-4">
            <ul id="messageList" class="list-group list-group-flush">
                <!-- Messages will be loaded here via JavaScript -->
            </ul>
            <div id="emptyState" class="text-center py-5 text-muted d-none">
                <i class='bx bx-inbox display-1 opacity-50'></i>
                <p class="mt-3">Tidak ada pesan notifikasi</p>
            </div>
        </div>
    </div>
</div>

<script>
// Message list functionality can be added here
const messageList = document.getElementById('messageList');
const emptyState = document.getElementById('emptyState');

function loadMessages() {
    // TODO: Load messages from backend
    // If no messages, show empty state
    if (!messageList.children.length) {
        emptyState.classList.remove('d-none');
    }
}

loadMessages();
</script>
