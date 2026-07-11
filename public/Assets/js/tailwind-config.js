
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
            }
        }
    },
    plugins: [
        function({ addVariant }) {
            addVariant("group-active", ".group.active &");
        }
    ]
}

