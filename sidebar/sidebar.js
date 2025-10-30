document.addEventListener("DOMContentLoaded", () => {
  const submenuToggles = document.querySelectorAll(".submenu-toggle");
  submenuToggles.forEach(toggle => {
    toggle.addEventListener("click", e => {
      e.preventDefault();
      const parent = toggle.parentElement;

      document.querySelectorAll(".submenu").forEach(item => {
        if (item !== parent) item.classList.remove("open");
      });

      parent.classList.toggle("open");
    });
  });
});
