    </main>

    <footer>
        <p>&copy; 2025 Movie & Series Review</p>
    </footer>

    <script>
    const menuToggle = document.querySelector(".menu-toggle");
    const navMenu = document.querySelector(".nav-menu");
    const mainContent = document.querySelector("main");

    menuToggle.addEventListener("click", () => {
        navMenu.classList.toggle("active");
        if (navMenu.classList.contains("active")) {
            mainContent.style.marginTop = "100px";
        } else {
            mainContent.style.marginTop = "0px";
        }
    });
    </script>

</body>
</html>
