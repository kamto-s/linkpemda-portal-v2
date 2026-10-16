class Config {
    adjustLayout() {
        const html = document.documentElement;
        if (window.innerWidth <= 1200) {
            html.setAttribute("data-sidebar-size", "full");
        } else {
            html.removeAttribute("data-sidebar-size");
        }
    }
    initTheme() {
        const savedTheme =
            sessionStorage.getItem("themeMode") ||
            (window.matchMedia &&
            window.matchMedia("(prefers-color-scheme: dark)").matches
                ? "dark"
                : "light");
        document.documentElement.setAttribute("data-bs-theme", savedTheme);
    }
    initWindowSize() {
        const self = this;
        window.addEventListener("resize", function () {
            self.adjustLayout();
        });
    }
    init() {
        this.initTheme();
        this.adjustLayout();
        this.initWindowSize();
    }
}
new Config().init();
