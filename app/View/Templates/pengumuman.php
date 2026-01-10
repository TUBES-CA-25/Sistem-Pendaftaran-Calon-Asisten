
<div class="content-wrapper" style="padding: 20px;"></div>


<div class="content-wrapper" style="padding: 20px;">
    
    <div style="margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
        <h2>Papan Pengumuman</h2>
        <p style="color: #666;">Informasi terbaru seputar kegiatan laboratorium.</p>
    </div>

    <div class="pengumuman-list">
        
        <?php if (!empty($data['pengumuman'])): ?>
            <?php foreach ($data['pengumuman'] as $p): ?>
                
                <div class="card" style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; overflow: hidden;">
                    
                    <div style="background: #f8f9fa; padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="margin: 0; color: #333; font-size: 1.2rem;">
                            <i class='bx bx-info-circle' style="color: #007bff; margin-right: 5px;"></i>
                            <?= $p['judul_pengumuman']; ?>
                        </h3>
                        <span style="font-size: 0.85rem; color: #888; background: #eee; padding: 4px 8px; border-radius: 4px;">
                            <i class='bx bx-calendar'></i> <?= date('d M Y, H:i', strtotime($p['created_at'])); ?>
                        </span>
                    </div>

                    <div style="padding: 20px; color: #444; line-height: 1.6;">
                        <?= nl2br($p['pengumuman']); ?>
                    </div>

                </div>

            <?php endforeach; ?>
        <?php else: ?>
            
            <div style="text-align: center; padding: 50px; background: white; border-radius: 8px;">
                <img src="<?= APP_URL; ?>/Assets/icon/billboard.png" alt="Empty" style="width: 80px; opacity: 0.5;">
                <h3 style="color: #999; margin-top: 15px;">Belum ada pengumuman saat ini.</h3>
            </div>

        <?php endif; ?>

    </div>
</div>