
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
                secondary: "#4B70F5",
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
            },
            animation: {
                blob: "blob 18s ease-in-out infinite",
                "fade-up": "fade-up 0.5s cubic-bezier(0.65, 0, 0.35, 1) both",
                shake: "shake 0.3s ease-in-out",
                // Nama class tetap `animate-page-fade` agar markup <body> tidak berubah
                "page-fade": "page-fade-in 0.6s cubic-bezier(0.65, 0, 0.35, 1) forwards",
            }
        }
    },
    plugins: [
        function({ addVariant }) {
            addVariant("group-active", ".group.active &");
        }
    ]
}

