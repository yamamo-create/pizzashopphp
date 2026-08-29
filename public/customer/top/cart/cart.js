"use strict";

const MAX = 10;

document.addEventListener("click", async (e) => {
  const btn = e.target;

  if (btn.classList.contains("inc")) {
    btn.disabled = true;

    const article = btn.closest("article");
    const id = article.dataset.id;
    let quantity = Number(article.dataset.quantity);

    const quantityText = article.querySelector(".quantitytext");

    const meta = document.querySelector('meta[name="customer_csrf_token"]');

    if (!meta) {
      throw new Error("CSRF token not found");
    }
    const token = meta.content;

    if (quantity < MAX) {
      ++quantity;
      quantityText.value = quantity;
      article.dataset.quantity = quantity;
    } else {
      btn.disabled = false;
      return;
    }

    try {
      const res = await fetch("/customer/top/cart/cart_quantity.php", {
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        method: "POST",
        body: new URLSearchParams({
          product_id: id,
          product_quantity: quantity,
          customer_csrf_token: token,
        }),
      });
      const data = await res.json();

      if (data.status === "error") {
        switch (data.code) {
          case "CSRF_TIME_OUT":
            location.href = "/customer/timeout/done.php";
            return;

          case "CSRF_INVALID":
          case "INVALID_ERROR":
          case "SYSTEM_ERROR":
          case "UNKNOWN_ERROR":
            location.href = "/customer/err/done.php";
            return;

          default:
            location.href = "/customer/err/done.php";
            return;
        }
      }
    } catch (error) {
      console.error(error);
    } finally {
      btn.disabled = false;
    }
  }

  if (btn.classList.contains("dec")) {
    btn.disabled = true;

    const article = btn.closest("article");
    const id = article.dataset.id;
    let quantity = Number(article.dataset.quantity);

    const quantityText = article.querySelector(".quantitytext");

    const meta = document.querySelector('meta[name="customer_csrf_token"]');

    if (!meta) {
      throw new Error("CSRF token not found");
    }
    const token = meta.content;

    if (quantity > 1) {
      --quantity;
      quantityText.value = quantity;
      article.dataset.quantity = quantity;
    } else {
      btn.disabled = false;
      return;
    }

    try {
      const res = await fetch("/customer/top/cart/cart_quantity.php", {
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        method: "POST",
        body: new URLSearchParams({
          product_id: id,
          product_quantity: quantity,
          customer_csrf_token: token,
        }),
      });
      const data = await res.json();

      if (data.status === "error") {
        switch (data.code) {
          case "CSRF_TIME_OUT":
            location.href = "/customer/timeout/done.php";
            return;

          case "CSRF_INVALID":
          case "INVALID_ERROR":
          case "SYSTEM_ERROR":
          case "UNKNOWN_ERROR":
            location.href = "/customer/err/done.php";
            return;

          default:
            location.href = "/customer/err/done.php";
            return;
        }
      }
    } catch (error) {
      console.error(error);
    } finally {
      btn.disabled = false;
    }
  }

  if (btn.classList.contains("delete")) {
    btn.disabled = true;

    const article = btn.closest("article");
    const id = article.dataset.id;

    const meta = document.querySelector('meta[name="customer_csrf_token"]');

    if (!meta) {
      throw new Error("CSRF token not found");
    }
    const token = meta.content;

    try {
      const res = await fetch("/customer/top/cart/cart_delete.php", {
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        method: "POST",
        body: new URLSearchParams({
          product_id: id,
          customer_csrf_token: token,
        }),
      });
      const data = await res.json();
      if (data.status === "ok") {
        article.remove();
      }

      if (data.status === "error") {
        switch (data.code) {
          case "CSRF_TIME_OUT":
            location.href = "/customer/timeout/done.php";
            return;

          case "CSRF_INVALID":
          case "INVALID_ERROR":
          case "SYSTEM_ERROR":
          case "UNKNOWN_ERROR":
            location.href = "/customer/err/done.php";
            return;

          default:
            location.href = "/customer/err/done.php";
            return;
        }
      }
    } catch (error) {
      console.error(error);
    } finally {
      btn.disabled = false;
    }
  }
});
