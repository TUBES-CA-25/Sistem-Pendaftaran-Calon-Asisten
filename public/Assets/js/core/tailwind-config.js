
tailwind.config = {
    theme: {
        extend: {
            fontFamily: {
                sans: ["Poppins", "sans-serif"],
            },
            colors: {
                primary: {
                    light: "#e0f2fe",
                    DEFAULT: "#3dc2ec",
                    hover: "#2ba8d1",
                    dark: "#2563eb",
                },
                secondary: {
                    DEFAULT: "#4B70F5",
                    // Varian gelap untuk state hover gradasi. Tanpa ini,
                    // separuh kanan gradasi tidak berubah saat di-hover
                    // sehingga efeknya terlihat setengah jalan.
                    hover: "#3355d8",
                },
                success: {
                    DEFAULT: "#4caf50",
                    dark: "#1e7e34"
                },
                warning: {
                    DEFAULT: "#ffc107",
                },
                danger: {
                    DEFAULT: "#ff7782",
                    dark: "#ef4444"
                },
                info: {
                    DEFAULT: "#3dc2ec",
                }
            },
            backgroundImage: {
                "gradient-primary": "linear-gradient(135deg, #3dc2ec 0%, #2563eb 100%)",
                "gradient-success": "linear-gradient(135deg, #28a745 0%, #1e7e34 100%)",
                "gradient-header": "linear-gradient(135deg, #0099cc 0%, #0044aa 100%)",
                "gradient-sidebar-active": "linear-gradient(135deg, #3dc2ec 0%, #2563eb 100%)",
                "gradient-auth": "linear-gradient(135deg, #eff6ff 0%, #dbeafe 45%, #e0f2fe 100%)",
            },
            keyframes: {
                // Blob latar halaman auth. Menganimasikan transform + opacity saja
                // (bukan properti `background`) supaya benar-benar berjalan & hemat GPU.
                blob: {
                    "0%, 100%": { transform: "translate(0px, 0px) scale(1)" },
                    "33%": { transform: "translate(30px, -40px) scale(1.1)" },
                    "66%": { transform: "translate(-20px, 20px) scale(0.95)" },
                },
                "fade-up": {
                    from: { opacity: "0", transform: "translateY(24px)" },
                    to: { opacity: "1", transform: "translateY(0)" },
                },
                shake: {
                    "0%, 100%": { transform: "translateX(0)" },
                    "25%": { transform: "translateX(-5px)" },
                    "75%": { transform: "translateX(5px)" },
                },
                "page-fade-in": {
                    from: { opacity: "0" },
                    to: { opacity: "1" },
                },
                // Denyut halus pada titik timeline yang sedang berjalan.
                // Hanya box-shadow yang dianimasikan (bukan width/height) agar
                // tidak memicu reflow tiap frame.
                "dot-pulse": {
                    "0%, 100%": { boxShadow: "0 0 0 0 rgba(37, 99, 235, 0.45)" },
                    "70%": { boxShadow: "0 0 0 10px rgba(37, 99, 235, 0)" },
                },
                // Batang grafik tumbuh dari dasar saat halaman dimuat.
                "grow-up": {
                    from: { transform: "scaleY(0)" },
                    to: { transform: "scaleY(1)" },
                },
                // Bar horizontal tumbuh dari kiri (grafik angkatan di dashboard).
                "grow-right": {
                    from: { transform: "scaleX(0)" },
                    to: { transform: "scaleX(1)" },
                },
                // Kartu masuk dengan naik + membesar sedikit. Lebih hidup daripada
                // fade-up polos, dipakai untuk kartu statistik dashboard.
                "pop-in": {
                    from: { opacity: "0", transform: "translateY(12px) scale(0.97)" },
                    to: { opacity: "1", transform: "translateY(0) scale(1)" },
                },
                // Masuk menggeser dari kiri. Dipakai kartu timeline agar arah
                // masuknya berbeda dari kolom kiri (terasa berlapis, bukan seragam).
                "slide-in-right": {
                    from: { opacity: "0", transform: "translateX(20px)" },
                    to: { opacity: "1", transform: "translateX(0)" },
                },
                // Garis timeline tumbuh dari atas ke bawah, mengiringi titik-titiknya.
                "grow-down": {
                    from: { transform: "scaleY(0)" },
                    to: { transform: "scaleY(1)" },
                },
                // Angka statistik berputar masuk. Halus, tidak mengalihkan fokus.
                "count-in": {
                    from: { opacity: "0", transform: "translateY(8px)" },
                    to: { opacity: "1", transform: "translateY(0)" },
                },
                // Kilau melintas sekali di kartu highlight. translate saja,
                // supaya tidak memicu repaint properti background.
                sheen: {
                    "0%": { transform: "translateX(-120%) skewX(-18deg)" },
                    "100%": { transform: "translateX(320%) skewX(-18deg)" },
                },
                // Denyut lingkaran ring pada ikon aktif.
                "ring-pulse": {
                    "0%, 100%": { opacity: "0.55", transform: "scale(1)" },
                    "50%": { opacity: "0", transform: "scale(1.5)" },
                },
                // Kartu tahap yang SEDANG BERJALAN menyala berdenyut. Hanya
                // box-shadow yang dianimasikan (bukan width/height/background)
                // supaya tidak memicu reflow tiap frame.
                glow: {
                    "0%, 100%": { boxShadow: "0 0 0 0 rgba(37, 99, 235, 0.45), 0 8px 20px -6px rgba(37, 99, 235, 0.4)" },
                    "50%": { boxShadow: "0 0 0 8px rgba(37, 99, 235, 0), 0 12px 26px -6px rgba(37, 99, 235, 0.55)" },
                },
                // Titik penanda tahap berjalan: berkedip lembut naik-turun opacity.
                shimmer: {
                    "0%, 100%": { opacity: "1" },
                    "50%": { opacity: "0.45" },
                },
                // Denyut hijau pada titik tahap yang SUDAH SELESAI. Bentuknya sama
                // dengan dot-pulse (yang biru, untuk tahap berjalan), hanya warnanya
                // hijau - supaya kedua status sama-sama "hidup" tanpa tertukar arti.
                "dot-pulse-green": {
                    "0%, 100%": { boxShadow: "0 0 0 0 rgba(16, 185, 129, 0.5)" },
                    "70%": { boxShadow: "0 0 0 9px rgba(16, 185, 129, 0)" },
                },
                // Kilau lembut naik-turun untuk titik selesai: terang <-> agak redup.
                "dot-glow": {
                    "0%, 100%": { opacity: "1", transform: "scale(1)" },
                    "50%": { opacity: "0.72", transform: "scale(1.12)" },
                },
                // Gelombang mengalir. Pola SVG-nya digambar DUA KALI berdampingan
                // di wadah selebar 200%, lalu digeser -50% (tepat satu salinan)
                // sehingga saat perulangan kembali ke 0% sambungannya tidak
                // terlihat - alirannya mulus tanpa lompatan.
                "wave-flow": {
                    from: { transform: "translateX(0)" },
                    to: { transform: "translateX(-50%)" },
                },
                // Lapis kedua gelombang: naik-turun pelan supaya kedua lapisnya
                // tidak bergerak seragam - itu yang membuatnya terasa seperti air.
                "wave-bob": {
                    "0%, 100%": { transform: "translateY(0)" },
                    "50%": { transform: "translateY(3px)" },
                },
                // Sel kalender "timbul": naik sedikit sambil membesar dari kecil.
                // Dipakai bertingkat per sel sehingga tabel tersusun seperti
                // gelombang dari kiri-atas ke kanan-bawah.
                "cell-rise": {
                    from: { opacity: "0", transform: "translateY(6px) scale(0.9)" },
                    to: { opacity: "1", transform: "translateY(0) scale(1)" },
                },
            },
            animation: {
                blob: "blob 18s ease-in-out infinite",
                "fade-up": "fade-up 0.5s cubic-bezier(0.65, 0, 0.35, 1) both",
                shake: "shake 0.3s ease-in-out",
                // Nama class tetap `animate-page-fade` agar markup <body> tidak berubah
                "page-fade": "page-fade-in 0.6s cubic-bezier(0.65, 0, 0.35, 1) forwards",
                "dot-pulse": "dot-pulse 2s cubic-bezier(0.65, 0, 0.35, 1) infinite",
                "grow-up": "grow-up 0.7s cubic-bezier(0.65, 0, 0.35, 1) both",
                "grow-right": "grow-right 0.8s cubic-bezier(0.65, 0, 0.35, 1) both",
                "pop-in": "pop-in 0.45s cubic-bezier(0.34, 1.4, 0.64, 1) both",
                "slide-in-right": "slide-in-right 0.5s cubic-bezier(0.65, 0, 0.35, 1) both",
                "grow-down": "grow-down 0.9s cubic-bezier(0.65, 0, 0.35, 1) both",
                "count-in": "count-in 0.5s cubic-bezier(0.34, 1.4, 0.64, 1) both",
                sheen: "sheen 2.4s cubic-bezier(0.65, 0, 0.35, 1) 0.8s",
                "ring-pulse": "ring-pulse 2.2s cubic-bezier(0.65, 0, 0.35, 1) infinite",
                glow: "glow 2.2s cubic-bezier(0.65, 0, 0.35, 1) infinite",
                // Varian sheen yang BERULANG, untuk kartu tahap yang sedang berjalan.
                // Berbeda dari `sheen` (sekali jalan) yang dipakai kartu statistik.
                "sheen-loop": "sheen 3.2s cubic-bezier(0.65, 0, 0.35, 1) 1s infinite",
                shimmer: "shimmer 1.8s cubic-bezier(0.65, 0, 0.35, 1) infinite",
                "cell-rise": "cell-rise 0.4s cubic-bezier(0.34, 1.4, 0.64, 1) both",
                // linear + durasi panjang: aliran tenang, bukan berkedut.
                "wave-flow": "wave-flow 14s linear infinite",
                "wave-flow-slow": "wave-flow 22s linear infinite",
                "wave-bob": "wave-bob 5s ease-in-out infinite",
                "dot-pulse-green": "dot-pulse-green 2.4s cubic-bezier(0.65, 0, 0.35, 1) infinite",
                "dot-glow": "dot-glow 2.4s cubic-bezier(0.65, 0, 0.35, 1) infinite",
            }
        }
    },
    plugins: [
        function({ addVariant }) {
            addVariant("group-active", ".group.active &");
        }
    ]
}

