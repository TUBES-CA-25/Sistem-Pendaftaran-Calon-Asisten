// Dependencies: common.js untuk showModal()

$(document).ready(function() {
    $('#berkasPresentasiForm').submit(function(e) {
        e.preventDefault(); 

        $.ajax({
            url: '/Sistem-Pendaftaran-Calon-Asisten/public/judul', 
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                   showModal(response.message || 'Data berhasil disimpan', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif');
                   document.querySelector('a[data-page="presentasi"]').click();
                } else {
                    showModal(response.message || 'Data gagal disimpan', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failed.gif');
                }
            },
            error: function(xhr, status, error) {
                console.log('Error:', xhr.responseText);
                alert('Terjadi kesalahan: ' + error);
            }
        });
    });

    $('#presentasiFormAccepted').on('submit', function (e) {
        e.preventDefault();
    
        var formData = new FormData(this);
        console.log("Form submitted");
        console.log("FormData entries:");
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
    
        $.ajax({
            url: '/Sistem-Pendaftaran-Calon-Asisten/public/presentasi',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
               showModal(response.message || 'Data berhasil disimpan', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif');
                document.querySelector('a[data-page="presentasi"]').click();
            },
            error: function (xhr, status, error) {
                console.error('Raw Response:', xhr.responseText);
                console.error('Error:', error);
            }
        });
    });        
});
