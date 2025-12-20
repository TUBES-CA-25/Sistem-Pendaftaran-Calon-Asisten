function showModal(message, gifUrl = null) {
    const modal = document.getElementById('customModal');
    const modalMessage = document.getElementById('modalMessage');
    const modalGif = document.getElementById('modalGif');
    const closeModal = document.getElementById('closeModal');

    modalMessage.textContent = message;

    if (gifUrl) {
        modalGif.src = gifUrl;
        modalGif.style.display = 'block';
    } else {
        modalGif.style.display = 'none';
    }

    modal.style.display = 'flex';

    closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}
$(document).ready(function() {
    $('#berkasPresentasiForm').submit(function(e) {
        e.preventDefault(); 

        $.ajax({
            url: '/Kelompok1_IC-ASSIST/public/judul', 
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                   showModal(response.message || 'Data berhasil disimpan', '/Kelompok1_IC-ASSIST/public/Assets/gif/success.gif');
                   document.querySelector('a[data-page="presentasi"]').click();
                } else {
                    showModal(response.message || 'Data gagal disimpan', '/Kelompok1_IC-ASSIST/public/Assets/gif/failed.gif');
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
            url: '/Kelompok1_IC-ASSIST/public/presentasi',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
               showModal(response.message || 'Data berhasil disimpan', '/Kelompok1_IC-ASSIST/public/Assets/gif/success.gif');
                document.querySelector('a[data-page="presentasi"]').click();
            },
            error: function (xhr, status, error) {
                console.error('Raw Response:', xhr.responseText);
                console.error('Error:', error);
            }
        });
    });        
});
