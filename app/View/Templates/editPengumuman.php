<div class="content-wrapper" style="padding: 20px;">
    <h2>Edit Pengumuman</h2>
    
    <div class="card" style="background:white; padding:20px; border-radius:10px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <form action="<?= APP_URL; ?>/pengumuman-admin/update" method="post">
            <input type="hidden" name="id" value="<?= $data['p']['id']; ?>">

            <div class="form-group" style="margin-bottom:15px;">
                <label>Judul</label>
                <input type="text" name="judul" value="<?= $data['p']['judul_pengumuman']; ?>" required style="width:100%; padding:8px; margin-top:5px; border:1px solid #ddd; border-radius:5px;">
            </div>
            
            <div class="form-group" style="margin-bottom:15px;">
                <label>Isi Informasi</label>
                <textarea name="isi" rows="6" required style="width:100%; padding:8px; margin-top:5px; border:1px solid #ddd; border-radius:5px;"><?= $data['p']['pengumuman']; ?></textarea>
            </div>
            
            <div style="margin-top: 20px;">
                <button type="submit" style="background:#28a745; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer;">Simpan Perubahan</button>
                
                <a href="#" data-page="pengumuman-admin" style="background:#6c757d; color:white; text-decoration:none; padding:10px 20px; border-radius:5px; margin-left:10px;">Batal</a>
            </div>
        </form>
    </div>
</div>