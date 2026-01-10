<div class="content-wrapper" style="padding: 20px;">
    <h2>Manajemen Pengumuman Laboratorium</h2>
    
    <div class="card" style="background:white; padding:20px; border-radius:10px; margin-bottom:20px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <h4>Buat Pengumuman Baru</h4>
        <form action="<?= APP_URL; ?>/pengumuman-admin/tambah" method="post">
            <div class="form-group" style="margin-bottom:15px;">
                <label>Judul</label>
                <input type="text" name="judul" required style="width:100%; padding:8px; margin-top:5px; border:1px solid #ddd; border-radius:5px;">
            </div>
            
            <div class="form-group" style="margin-bottom:15px;">
                <label>Isi Informasi</label>
                <textarea name="isi" rows="4" required style="width:100%; padding:8px; margin-top:5px; border:1px solid #ddd; border-radius:5px;"></textarea>
            </div>
            
            <button type="submit" style="background:#007bff; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer;">Terbitkan</button>
        </form>
    </div>

    <div class="card" style="background:white; padding:20px; border-radius:10px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <h4>Daftar Pengumuman Aktif</h4>
        <table border="1" cellpadding="10" cellspacing="0" width="100%" style="border-collapse:collapse; margin-top:15px;">
            <thead>
                <tr style="background:#f8f9fa; text-align:left;">
                    <th>No</th>
                    <th>Judul</th>
                    <th>Isi Singkat</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['pengumuman'])): ?>
                <?php $no = 1; foreach($data['pengumuman'] as $p) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $p['judul_pengumuman']; ?></td>
                        
                        <td><?= substr($p['pengumuman'], 0, 50) . '...'; ?></td>
                        
                        <td><?= $p['created_at']; ?></td> <td>
                            <form action="<?= APP_URL; ?>/pengumuman-admin/hapus" method="post">
                                <input type="hidden" name="id" value="<?= $p['id']; ?>">
                                <button type="submit" onclick="return confirm('Yakin hapus?');" style="background:#dc3545; color:white; border:none; padding:5px 10px; border-radius:3px; cursor:pointer;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" align="center">Belum ada pengumuman Yang Di publis.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>