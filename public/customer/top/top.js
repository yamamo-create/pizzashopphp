"use strict";

fetchMenuData("/json/meal.json", "gMenu");
fetchMenuData("/json/dessert.json", "dMenu");

// innerHTMLを使わないようにしている。

async function fetchMenuData(url, idName) {
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error(`error status: ${response.status}`);
    }
    const data = await response.json();
    for (let i = 0; i < data.length; ++i) {
      const id = data[i]["id"];
      const name = data[i]["name"];
      const price = data[i]["price"];
      const imagename = data[i]["imagename"];
      const detail = data[i]["detail"];

      const article = document.createElement("article");
      article.dataset.id = id;

      const nameDiv = document.createElement("div");
      nameDiv.textContent = name;

      const contentDiv = document.createElement("div");

      const img = document.createElement("img");
      img.src = "/img/" + imagename;
      img.alt = name + "の画像";

      const ul = document.createElement("ul");
      const liDetail = document.createElement("li");
      liDetail.textContent = detail;
      const liPrice = document.createElement("li");
      liPrice.textContent = "１枚：" + price + "円";
      ul.appendChild(liDetail);
      ul.appendChild(liPrice);

      contentDiv.appendChild(img);
      contentDiv.appendChild(ul);

      const controlDiv = document.createElement("div");

      const btnCart = document.createElement("button");
      btnCart.textContent = "カートに入れる";
      btnCart.className = "cart button";
      btnCart.dataset.inCart = "false";

      controlDiv.append(btnCart);
      article.append(nameDiv, contentDiv, controlDiv);
      document.getElementById(idName).appendChild(article);
    }
  } catch (error) {
    console.error(error);
  }
}

document.addEventListener("click", async (e) => {
  const btn = e.target;

  if (!btn.classList.contains("cart")) {
    return;
  }

  btn.disabled = true;

  const article = btn.closest("article");
  const id = article.dataset.id;

  const inCart = btn.dataset.inCart === "true" ? true : false;

  const meta = document.querySelector('meta[name="customer_csrf_token"]');

  if (!meta) {
    throw new Error("CSRF token not found");
  }
  const token = meta.content;

  try {
    if (!inCart) {
      const res = await fetch("/customer/top/cart_in.php", {
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        method: "POST",
        body: new URLSearchParams({
          product_id: id,
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

      if (data.status === "ok") {
        btn.textContent = "カート追加済み";
        btn.dataset.inCart = "true";
      }
    } else {
      const res = await fetch("/customer/top/cart_out.php", {
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        method: "POST",
        body: new URLSearchParams({
          product_id: id,
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
          case "INVALID_PRODUCT_ID":
          case "SYSTEM_ERROR":
          case "UNKNOWN_ERROR":
            location.href = "/customer/err/done.php";
            return;

          default:
            location.href = "/customer/err/done.php";
            return;
        }
      }

      if (data.status === "ok") {
        btn.textContent = "カートに入れる";
        btn.dataset.inCart = "false";
      }
    }
  } catch (error) {
    console.error(error);
  } finally {
    btn.disabled = false;
  }
});
