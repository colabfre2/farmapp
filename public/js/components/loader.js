// Script untuk animasi chevron / panah pada menu dropdown sidebar
document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function (el) {
    var target = document.querySelector(el.getAttribute("href"));
    // Mencari element chevron tanpa mempedulikan nama class spesifiknya (disamakan jadi .icon-chevron)
    var icon = el.querySelector(".icon-chevron");
    if (!target || !icon) return;

    target.addEventListener("show.bs.collapse", function () {
        icon.style.transform = "rotate(180deg)";
    });
    target.addEventListener("hide.bs.collapse", function () {
        icon.style.transform = "rotate(0deg)";
    });
});
