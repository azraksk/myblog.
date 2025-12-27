document.addEventListener("DOMContentLoaded", function () {
    const profile = document.getElementById("profileToggle");
    const menu = document.getElementById("profileMenu");

    if (profile && menu) {
        profile.addEventListener("click", function (e) {
            e.stopPropagation();
            menu.classList.toggle("active");
        });

        document.addEventListener("click", function () {
            menu.classList.remove("active");
        });
    }
});
