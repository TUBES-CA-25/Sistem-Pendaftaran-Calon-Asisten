<?php
// Menerima variabel $soalArray dari Controller
if (empty($soalArray)) {
    echo '<div class="text-center py-12 flex flex-col items-center">
        <i class="bx bx-file-blank text-slate-300 text-6xl mb-4"></i>
        <h5 class="text-slate-600 font-bold text-lg mb-1">Belum Ada Soal</h5>
        <p class="text-slate-400 text-sm max-w-sm">Klik tombol "Tambah Soal" untuk menambahkan soal baru ke bank ini</p>
    </div>';
    return;
}

foreach ($soalArray as $index => $soal) {
    $isPG = (($soal['status_soal'] ?? '') === 'pilihan_ganda');
    $questionType = $isPG ? 'PILIHAN GANDA' : 'ESSAY';
    $points = 5; // Default 5 points per question
    $timeLimit = '45 detik'; // Mock
    $soalId = $soal['id'];
    $deskripsi = $soal['deskripsi'] ?? '';
    $imageUrl = $soal['image_url'] ?? '';
    $jawaban = $soal['jawaban'] ?? '';
    $pilihan = $soal['pilihan'] ?? '';
    
    // Parse pilihan for PG
    $optionsHtml = '';
    if ($isPG && !empty($pilihan)) {
        $options = [];
        $parsed = json_decode($pilihan, true);
        if (is_array($parsed)) {
            // Check if associative or sequential
            if (array_keys($parsed) !== range(0, count($parsed) - 1)) {
                foreach ($parsed as $k => $v) {
                    $options[] = ['key' => $k, 'value' => $v];
                }
            } else {
                foreach ($parsed as $idx => $v) {
                    $options[] = ['key' => chr(65 + $idx), 'value' => $v];
                }
            }
        } else {
            // Legacy parsing (fallback)
            $decoded = strip_tags($pilihan);
            preg_match_all('/([A-E])\.\s*(.*?)(?=(?:,\s*[A-E]\.)|$)/s', $decoded, $matches, PREG_SET_ORDER);
            if (count($matches) > 0) {
                foreach ($matches as $m) {
                    $options[] = ['key' => $m[1], 'value' => trim($m[2])];
                }
            } else {
                $parts = array_map('trim', explode(',', $decoded));
                foreach ($parts as $idx => $part) {
                    $options[] = ['key' => chr(65 + $idx), 'value' => $part];
                }
            }
        }
        
        // Correct Key
        $correctKey = null;
        if (!empty($jawaban)) {
            $jwb = strtoupper(trim($jawaban));
            foreach ($options as $opt) {
                if ($jwb === strtoupper($opt['key']) || str_starts_with($jwb, strtoupper($opt['key']) . '.')) {
                    $correctKey = $opt['key'];
                    break;
                }
            }
            if (!$correctKey && preg_match('/^([A-E])/', $jwb, $m)) {
                $correctKey = $m[1];
            }
        }
        
        $optionsHtml = '<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">';
        foreach ($options as $opt) {
            $val = $opt['value'];
            $key = $opt['key'];
            
            // basic check for image
            $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)[\'"]?\s*$/i', $val) || str_starts_with(trim($val), 'http');
            if ($isImage && !str_contains($val, '<img')) {
                $content = '<img src="' . htmlspecialchars(trim($val)) . '" class="max-h-24 object-contain rounded-md" onerror="this.style.display=\'none\'">';
            } else {
                $content = '<div class="text-slate-700 text-sm">' . nl2br(htmlspecialchars($val)) . '</div>';
            }
            
            // Mark correct answer with JS class for toggling later if needed, but since we render it server-side, 
            // we will let JS toggle the visibility of correct answers if needed, or we just render it.
            // By default, admin view shows the correct answer marked.
            $isCorrect = ($key === $correctKey);
            $bgColor = $isCorrect ? 'bg-blue-50 border-blue-200' : 'bg-slate-50 border-slate-200';
            $badgeColor = $isCorrect ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-500 border border-slate-200';
            $correctMark = $isCorrect ? '<i class="bx bxs-check-circle text-blue-600 text-lg ml-auto"></i>' : '';
            
            // Optional correct-answer-element class so JS can hide/show them
            $correctClass = $isCorrect ? 'is-correct-option' : '';

            $optionsHtml .= '
            <div class="flex items-center gap-3 p-3 rounded-xl border ' . $bgColor . ' ' . $correctClass . ' transition-colors">
                <div class="w-8 h-8 shrink-0 flex items-center justify-center rounded-lg text-sm font-bold ' . $badgeColor . '">' . $key . '</div>
                <div class="flex-grow">' . $content . '</div>
                ' . $correctMark . '
            </div>';
        }
        $optionsHtml .= '</div>';
    }
?>
    <div class="bg-white border-b border-slate-100 last:border-0 p-6 sm:px-8 hover:bg-slate-50/50 transition duration-300 group" data-id="<?= $soalId ?>" data-type="<?= htmlspecialchars($soal['status_soal'] ?? 'essay') ?>">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <?= $index + 1 ?>. <?= $questionType ?> &bull; <?= $timeLimit ?> &bull; <?= $points ?> poin
            </div>
            <!-- Action Buttons -->
            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-200 text-slate-500 transition" onclick="window.editSoal(<?= $soalId ?>)" title="Edit">
                    <i class='bx bx-edit'></i>
                </button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-200 text-slate-500 transition" onclick="window.deleteSoal(<?= $soalId ?>)" title="Hapus">
                    <i class='bx bx-trash'></i>
                </button>
            </div>
        </div>
        
        <!-- Question Content -->
        <div class="flex flex-col gap-4">
            <!-- Image if any -->
            <?php if (!empty($imageUrl)): ?>
            <div class="w-full">
                <img src="<?= htmlspecialchars((str_starts_with($imageUrl, 'http') ? '' : '/Sistem-Pendaftaran-Calon-Asisten/') . $imageUrl) ?>" 
                     alt="Gambar Soal" 
                     class="w-full h-48 sm:h-64 object-contain bg-slate-50 rounded-xl border border-slate-200 cursor-zoom-in hover:opacity-95 transition-opacity" 
                     onerror="this.style.display='none'"
                     onclick="showImageModal(this.src)">
            </div>
            <?php endif; ?>
            
            <!-- Text -->
            <div class="w-full">
                <!-- We output raw deskripsi inside a hidden textarea or custom attribute, and let JS parse it via marked, OR we just let JS parse the innerHTML.
                     In exam.js, it looks for elements with 'condition-render-markdown' and parses them. So we put raw markdown here. -->
                <div class="text-slate-800 text-[15px] font-medium leading-relaxed mb-4 condition-render-markdown" style="display:none;"><?= htmlspecialchars($deskripsi) ?></div>
                <div class="markdown-rendered-content text-slate-800 text-[15px] font-medium leading-relaxed mb-4"></div>
                
                <?= $optionsHtml ?>
                
                <?php if (!$isPG && !empty($jawaban)): ?>
                <div class="mt-4 p-4 bg-emerald-50 rounded-xl border border-emerald-100 essay-correct-answer">
                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-1 flex items-center gap-1.5"><i class='bx bxs-check-circle'></i> Jawaban Benar</div>
                    <div class="text-emerald-800 text-sm font-medium"><?= nl2br(htmlspecialchars($jawaban)) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php
}
?>
