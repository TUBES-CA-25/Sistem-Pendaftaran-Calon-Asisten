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

<main class="max-w-4xl mx-auto px-4 pb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col">
        <div class="flex items-center gap-2 p-6 border-b border-slate-100">
            <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class='bx bx-message-square-dots text-blue-600'></i> Pesan Notifikasi
            </h5>
        </div>
        <div class="p-6">
            <?php
            use App\Controllers\NotifikasiController;
            $notifications = NotifikasiController::getMessageById() ?? [];
            ?>
            <div id="messageList" class="space-y-4">
                <?php if (empty($notifications)): ?>
                    <div id="emptyState" class="text-center py-12 text-slate-400">
                        <i class='bx bx-inbox text-5xl mb-3 block opacity-50'></i>
                        <p class="text-sm font-medium">Tidak ada pesan notifikasi</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notif): ?>
                        <?php 
                            $date = isset($notif['created_at']) ? date('d M Y, H:i', strtotime($notif['created_at'])) : '-';
                        ?>
                        <div class="p-4 rounded-xl border-l-4 border-slate-200 bg-slate-50/50 hover:bg-slate-50 hover:border-blue-600 transition duration-200">
                            <div class="flex justify-between items-center gap-4 mb-2">
                                <strong class="text-blue-800 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="bx bx-envelope text-sm"></i> Admin
                                </strong>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200/50">
                                    <i class="bx bx-time-five"></i><?= $date ?>
                                </span>
                            </div>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                <?= htmlspecialchars($notif['pesan']) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
