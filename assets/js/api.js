/* =============================================================
   api.js
   Talks to the PHP backend via fetch().

   - menu        GET  api/menu/list.php
   - reservation POST api/reservations/create.php
   - contact     POST api/contact/create.php
   ============================================================= */

/* ---------- small helpers ---------- */

function escapeHtml(value) {
  return String(value)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function showFeedback(elementId, message, type) {
  const el = document.getElementById(elementId);
  if (!el) return;
  el.textContent = message;
  el.className = "form-feedback " + type;
}

async function postJson(url, payload) {
  const response = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  const json = await response.json();
  return { response, json };
}

/* =============================================================
   DYNAMIC MENU  (GET api/menu/list.php)
   ============================================================= */

const menuContent = document.getElementById("menu-content");
const MENU_CURRENCY = "EGP"; // change to match your market

// Decorative sprites per category so each card keeps the original look.
const MENU_SPRITES = {
  starter: {
    images: ["assets/img/menu-flour.png", "assets/img/menu-rosemary.png"],
    classes: ["card-flour1", "card-rosemary"],
  },
  main: {
    images: ["assets/img/menu-flour.png", "assets/img/menu-pepper.png"],
    classes: ["card-flour2", "card-pepper"],
  },
  salad: {
    images: ["assets/img/menu-flour.png", "assets/img/menu-tomato.png"],
    classes: ["card-flour3", "card-tomato"],
  },
  dessert: {
    images: ["assets/img/menu-flour.png"],
    classes: ["card-flour4"],
  },
};

function renderMenu(items) {
  if (!items || items.length === 0) {
    menuContent.innerHTML =
      '<p class="menu-loading">No menu items available right now. Please come back soon.</p>';
    return;
  }

  menuContent.innerHTML = items
    .map((item, index) => {
      const cardType = index % 2 === 0 ? "menu-card" : "menu-card1";
      const sprites = MENU_SPRITES[item.category] || MENU_SPRITES.starter;

      const spriteImages = sprites.images
        .map(
          (src, i) =>
            `<img src="${src}" alt="" class="${sprites.classes[i]}">`
        )
        .join("");

      const price = `${escapeHtml(item.price)} ${MENU_CURRENCY}`;

      return `
        <div class="${cardType}">
          <div class="card-images">
            ${spriteImages}
            <img src="${escapeHtml(item.image)}" alt="Menu Specialty" class="card-dish">
          </div>
          <div class="card-info">
            <h3 class="info-title">${escapeHtml(item.name)}</h3>
            <p class="card-summary">${escapeHtml(item.description)}</p>
            <span class="card-price">${price}</span>
          </div>
        </div>`;
    })
    .join("");
}

async function loadMenu() {
  if (!menuContent) return;

  menuContent.innerHTML = '<p class="menu-loading">Loading our menu...</p>';

  try {
    const response = await fetch("api/menu/list.php");
    const json = await response.json();

    if (!response.ok || !json.success) {
      throw new Error(json.message || "Failed to load the menu.");
    }

    renderMenu(json.data);
  } catch (err) {
    console.error("Menu load failed:", err);
    menuContent.innerHTML =
      '<p class="menu-loading">Our menu is temporarily unavailable. Please try again later.</p>';
  }
}

/* =============================================================
   RESERVATION FORM  (POST api/reservations/create.php)
   ============================================================= */

const reservationForm = document.getElementById("reservation-form");

if (reservationForm) {
  // Prevent booking dates in the past.
  const dateInput = reservationForm.querySelector('input[name="reservation_date"]');
  if (dateInput) {
    dateInput.min = new Date().toISOString().split("T")[0];
  }

  reservationForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    showFeedback("reservation-feedback", "", "");

    const formData = Object.fromEntries(new FormData(reservationForm).entries());

    // Basic client-side check (the server validates everything again).
    const required = ["customer_name", "phone", "email", "reservation_date", "reservation_time", "guests"];
    for (const field of required) {
      if (!String(formData[field] || "").trim()) {
        showFeedback("reservation-feedback", "Please fill in all required fields.", "error");
        return;
      }
    }

    try {
      const { response, json } = await postJson("api/reservations/create.php", formData);

      if (!response.ok || !json.success) {
        const firstError = json.errors ? Object.values(json.errors)[0] : json.message;
        showFeedback(
          "reservation-feedback",
          firstError || "Reservation failed. Please try again.",
          "error"
        );
        return;
      }

      showFeedback("reservation-feedback", json.message || "Reservation submitted successfully.", "success");
      reservationForm.reset();
    } catch (err) {
      console.error("Reservation submit failed:", err);
      showFeedback("reservation-feedback", "Network error. Please try again.", "error");
    }
  });
}

/* =============================================================
   CONTACT FORM  (POST api/contact/create.php)
   ============================================================= */

const contactForm = document.getElementById("contact-form");

if (contactForm) {
  contactForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    showFeedback("contact-feedback", "", "");

    const formData = Object.fromEntries(new FormData(contactForm).entries());

    const required = ["name", "email", "subject", "message"];
    for (const field of required) {
      if (!String(formData[field] || "").trim()) {
        showFeedback("contact-feedback", "Please fill in all required fields.", "error");
        return;
      }
    }

    try {
      const { response, json } = await postJson("api/contact/create.php", formData);

      if (!response.ok || !json.success) {
        const firstError = json.errors ? Object.values(json.errors)[0] : json.message;
        showFeedback(
          "contact-feedback",
          firstError || "Message failed to send. Please try again.",
          "error"
        );
        return;
      }

      showFeedback("contact-feedback", json.message || "Message sent successfully.", "success");
      contactForm.reset();
    } catch (err) {
      console.error("Contact submit failed:", err);
      showFeedback("contact-feedback", "Network error. Please try again.", "error");
    }
  });
}

/* =============================================================
   BOOTSTRAP
   ============================================================= */

document.addEventListener("DOMContentLoaded", loadMenu);