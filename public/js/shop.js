(function () {
  "use strict";
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  // Menu mobile
  var menu = $("#mobile-nav");
  $$("[data-open-menu]").forEach(function (b) { b.addEventListener("click", function () { menu.hidden = false; }); });
  $$("[data-close-menu]").forEach(function (b) { b.addEventListener("click", function () { menu.hidden = true; }); });
  document.addEventListener("keydown", function (e) { if (e.key === "Escape" && menu) menu.hidden = true; });

  // Recherche
  var sb = $("#search-bar");
  $$("[data-toggle-search]").forEach(function (b) {
    b.addEventListener("click", function () { sb.hidden = !sb.hidden; if (!sb.hidden) $("input", sb).focus(); });
  });

  // Toast
  var t = $(".toast"); if (t) setTimeout(function () { t.remove(); }, 5000);

  // Quantité +/-
  $$(".qty").forEach(function (q) {
    var input = $("input", q);
    $$("button[data-step]", q).forEach(function (b) {
      b.addEventListener("click", function () {
        var v = Math.max(+input.min || 0, Math.min(+input.max || 20, (+input.value || 0) + (+b.dataset.step)));
        input.value = v;
        if (q.dataset.autosubmit !== undefined) q.closest("form").submit();
      });
    });
  });

  // Galerie produit
  var main = $("[data-gallery-main]");
  $$("[data-gallery-thumb]").forEach(function (b) {
    b.addEventListener("click", function () {
      main.src = b.dataset.src;
      $$("[data-gallery-thumb]").forEach(function (x) { x.setAttribute("aria-current", String(x === b)); });
    });
  });

  // Onglets accueil
  $$("[data-tabs]").forEach(function (box) {
    var btns = $$("[data-tab]", box);
    btns.forEach(function (b) {
      b.addEventListener("click", function () {
        btns.forEach(function (x) { x.setAttribute("aria-selected", String(x === b)); });
        $$("[data-panel]", box).forEach(function (p) { p.hidden = p.dataset.panel !== b.dataset.tab; });
      });
    });
  });

  // Frais de livraison selon la ville (commande)
  var city = $("#city");
  if (city) {
    var fc = +city.dataset.feeConakry, fo = +city.dataset.feeOther, sub = +city.dataset.subtotal;
    var fmt = function (n) { return new Intl.NumberFormat("fr-FR").format(n).replace(/ | /g, " ") + " GNF"; };
    var upd = function () {
      var fee = /conakry/i.test(city.value) ? fc : fo;
      $("#fee").textContent = fee ? fmt(fee) : "Offerte";
      $("#total").textContent = fmt(sub + fee);
    };
    city.addEventListener("input", upd); upd();
  }

  // Bouton de commande : un seul envoi
  $$("form[data-once]").forEach(function (f) {
    f.addEventListener("submit", function () { var b = $("button[type=submit]", f); if (b) { b.disabled = true; b.dataset.label && (b.textContent = b.dataset.label); } });
  });
})();
