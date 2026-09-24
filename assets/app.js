


/* =========================================================
   PEENA'S PLACE
   Frontend Application
   ========================================================= */

let siteSettings = {};

let products = [];
let cart = [];
let selectedProduct = null;
const API_BASE = "api";
const STATIC_PRODUCTS = [
    {
        id: 1,
        name: "Curated Accent Chair",
        category: "Furniture",
        price: 185000,
        stock_status: "In stock",
        image: "uploads/products/photo-1.avif",
        description: "A sculptural accent chair selected for warm, welcoming interiors.",
        active: 1,
        featured: 1
    },
    {
        id: 2,
        name: "Contemporary Living Edit",
        category: "Furniture",
        price: 240000,
        stock_status: "In stock",
        image: "uploads/products/photo-2.avif",
        description: "A refined piece with an easy silhouette and timeless character.",
        active: 1,
        featured: 1
    },
    {
        id: 3,
        name: "Textured Ceramic Vessel",
        category: "Decor",
        price: 45000,
        stock_status: "In stock",
        image: "uploads/products/photo-3.avif",
        description: "A tactile decorative accent for shelves, consoles and side tables.",
        active: 1,
        featured: 1
    },
    {
        id: 4,
        name: "Natural Form Table Lamp",
        category: "Lighting",
        price: 78000,
        stock_status: "In stock",
        image: "uploads/products/photo-4.avif",
        description: "Soft ambient lighting with a considered, organic form.",
        active: 1,
        featured: 1
    },
    {
        id: 5,
        name: "Handwoven Throw",
        category: "Textiles",
        price: 32000,
        stock_status: "In stock",
        image: "uploads/products/photo-5.avif",
        description: "A versatile layer that adds texture and comfort to your space.",
        active: 1,
        featured: 0
    },
    {
        id: 6,
        name: "Minimalist Side Table",
        category: "Furniture",
        price: 95000,
        stock_status: "In stock",
        image: "uploads/products/photo-6.avif",
        description: "A compact side table designed for everyday living.",
        active: 1,
        featured: 0
    },
    {
        id: 7,
        name: "Decorative Stoneware",
        category: "Decor",
        price: 28000,
        stock_status: "In stock",
        image: "uploads/products/photo-7.avif",
        description: "A quiet finishing touch for a shelf, table or entryway.",
        active: 1,
        featured: 0
    }
];


/* =========================================================
   DOM ELEMENTS
   ========================================================= */

const productsGrid =
    document.getElementById("productsGrid");

const emptyState =
    document.getElementById("emptyState");

const categoryFilters =
    document.getElementById("categoryFilters");

const featuredProductsGrid =
    document.getElementById("featuredProductsGrid");

const cartButton =
    document.getElementById("cartButton");

const cartDrawer =
    document.getElementById("cartDrawer");

const cartOverlay =
    document.getElementById("cartOverlay");

const closeCart =
    document.getElementById("closeCart");

const cartItems =
    document.getElementById("cartItems");

const cartCount =
    document.getElementById("cartCount");

const cartTotal =
    document.getElementById("cartTotal");

const cartFooter =
    document.getElementById("cartFooter");

const continueShopping =
    document.getElementById("continueShopping");

const productModal =
    document.getElementById("productModal");

const closeProductModal =
    document.getElementById("closeProductModal");

const modalProductImage =
    document.getElementById("modalProductImage");

const modalProductCategory =
    document.getElementById("modalProductCategory");

const modalProductName =
    document.getElementById("modalProductName");

const modalProductPrice =
    document.getElementById("modalProductPrice");

const modalProductDescription =
    document.getElementById("modalProductDescription");

const modalAddToCart =
    document.getElementById("modalAddToCart");

const checkoutButton =
    document.getElementById("checkoutButton");

const checkoutModal =
    document.getElementById("checkoutModal");

const closeCheckoutModal =
    document.getElementById("closeCheckoutModal");

const checkoutForm =
    document.getElementById("checkoutForm");

const toast =
    document.getElementById("toast");

const toastMessage =
    document.getElementById("toastMessage");

const menuToggle =
    document.getElementById("menuToggle");

const mainNav =
    document.getElementById("mainNav");

const siteHeader =
    document.getElementById("siteHeader");

const currentYear =
    document.getElementById("currentYear");


/* =========================================================
   SITE SETTINGS
   ========================================================= */

async function loadSiteSettings() {

    try {

        const response =
            await fetch(`${API_BASE}/settings.php`);

        const data =
            await response.json();

        if (!data.success) {

            console.error(
                "Unable to load site settings."
            );

            return;
        }

        siteSettings =
            data.settings || {};

        applySiteSettings();

    } catch (error) {

        console.warn("Using storefront defaults because the settings API is unavailable.", error);

    }
}


function applySiteSettings() {

    const settings =
        siteSettings;

    if (settings.whatsapp) {

        const cleanWhatsApp =
            settings.whatsapp.replace(/\D/g, "");

        document
            .querySelectorAll("[data-whatsapp]")
            .forEach(link => {

                link.href =
                    `https://wa.me/${cleanWhatsApp}`;

            });
    }


    if (settings.instagram_url) {

        document
            .querySelectorAll("[data-instagram]")
            .forEach(link => {

                link.href =
                    settings.instagram_url;

            });
    }


    document
        .querySelectorAll("[data-business-name]")
        .forEach(el => {

            el.textContent =
                settings.business_name ||
                "Peena's Place";

        });


    document
        .querySelectorAll("[data-location]")
        .forEach(el => {

            el.textContent =
                settings.location || "";

        });


    document
        .querySelectorAll("[data-email]")
        .forEach(el => {

            el.textContent =
                settings.email || "";

        });


    document
        .querySelectorAll("[data-email-link]")
        .forEach(el => {

            el.href =
                settings.email
                    ? `mailto:${settings.email}`
                    : "#";

        });


    document
        .querySelectorAll("[data-about]")
        .forEach(el => {

            el.textContent =
                settings.about_text || "";

        });


    document
        .querySelectorAll("[data-founder-name]")
        .forEach(el => {

            el.textContent =
                settings.founder_name || "";

        });


    document
        .querySelectorAll("[data-founder-bio]")
        .forEach(el => {

            el.textContent =
                settings.founder_bio || "";

        });


    document
        .querySelectorAll("[data-opening-hours]")
        .forEach(el => {

            el.textContent =
                settings.opening_hours || "";

        });


    document
        .querySelectorAll("[data-maps]")
        .forEach(link => {

            link.href =
                settings.maps_url || "#";

        });

}


/* =========================================================
   INITIALIZATION
   ========================================================= */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        loadSiteSettings();

        loadProducts();

        loadCart();

        updateCart();

        setupNavigation();

        setupFilters();

        setupCart();

        setupProductModal();

        setupCheckout();

        setupHeader();


        if (currentYear) {

            currentYear.textContent =
                new Date().getFullYear();

        }

    }
);

/* =========================================================
   LOAD PRODUCTS FROM DATABASE
   ========================================================= */

async function loadProducts() {

    try {

        const response =
            await fetch(`${API_BASE}/products.php`);

        if (!response.ok) {

            throw new Error(
                `Server returned ${response.status}`
            );

        }

        const data =
            await response.json();

        console.log("Products API:", data);

        if (!data.success) {

            throw new Error(
                data.message ||
                "Unable to load (products."
            );

        }

        products =
            data.products || [];

        renderProducts(products);

        renderFeaturedProducts(products);

    } catch (error) {

        products = STATIC_PRODUCTS;
        renderProducts(products);
        renderFeaturedProducts(products);
        console.warn("Using the static catalog because the products API is unavailable.", error);

    }
}

/* =========================================================
   RENDER FEATURED PRODUCTS
   ========================================================= */

function renderFeaturedProducts(productList) {

    const featuredGrid =
        document.getElementById("featuredProductsGrid");

    if (!featuredGrid) {
        console.error("Featured products grid not found.");
        return;
    }

    const featuredProducts =
        (productList || [])
            .filter(product => {
                return String(product.featured).trim() === "1";
            })
            .slice(0, 4);

    console.log(
        "Products received:",
        productList
    );

    console.log(
        "Featured products found:",
        featuredProducts
    );

    if (featuredProducts.length === 0) {
            featuredGrid.innerHTML = `
            <div class="featured-empty">
                <p>
                    No featured products found.
                </p>
            </div>
        `;

        return;
    }

    featuredGrid.innerHTML =
        featuredProducts.map((product, index) => {

            const image =
                product.image ||
                "https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=1000&q=85";

            const regularPrice =
                Number(product.price || 0);

            const salePrice =
                Number(product.sale_price || 0);

            const hasSale =
                salePrice > 0 &&
                salePrice < regularPrice;

            const displayPrice =
                hasSale
                    ? salePrice
                    : regularPrice;

            return `
                <article
                    class="featured-product-card"
                    style="animation-delay:${index * 80}ms"
                >

                    <div class="featured-product-image">

                        <img
                            src="${escapeAttribute(image)}"
                            alt="${escapeAttribute(product.name)}"
                            loading="lazy"
                            onerror="
                                this.src='https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=1000&q=85'
                            "
                        >

                        ${
                            hasSale
                                ? `
                                    <span class="featured-sale">
                                        Sale
                                    </span>
                                `
                                : ""
                        }

                    </div>

                    <div class="featured-product-info">

                        <span>
                            ${escapeHTML(product.category)}
                        </span>

                        <h3>
                            ${escapeHTML(product.name)}
                        </h3>

                        <div class="featured-price">

                            ${
                                hasSale
                                    ? `
                                        <strong>
                                            ${formatCurrency(displayPrice)}
                                        </strong>

                                        <del>
                                            ${formatCurrency(regularPrice)}
                                        </del>
                                    `
                                    : `
                                        <strong>
                                            ${formatCurrency(displayPrice)}
                                        </strong>
                                    `
                            }

                        </div>

                        <button
                            type="button"
                            onclick="openProduct(${product.id})"
                        >
                            View Piece
                        </button>

                    </div>

                </article>
            `;

        }).join("");
}

/* =========================================================
   RENDER PRODUCTS
   ========================================================= */

function renderProducts(items) {

    if (!items.length) {

        productsGrid.innerHTML = "";

        emptyState.classList.remove("hidden");

        return;
    }

    emptyState.classList.add("hidden");

    productsGrid.innerHTML = items.map((product, index) => {

        const image =
            product.image ||
            "https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=1000&q=85";

        const regularPrice = Number(product.price || 0);

        const salePrice = Number(product.sale_price || 0);

        const hasSale =
            salePrice > 0 &&
            salePrice < regularPrice;

        const stockStatus =
            product.stock_status || "In Stock";

        const isFeatured =
            Number(product.featured) === 1;

        const isOutOfStock =
            stockStatus === "Out of Stock";

        return `
            <article
                class="product-card"
                style="animation-delay:${index * 70}ms">

                <div class="product-image">

                    <img
                        src="${escapeAttribute(image)}"
                        alt="${escapeAttribute(product.name)}"
                        loading="lazy"
                        onerror="this.src='https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=1000&q=85'">

                    ${
                        isFeatured
                        ? `
                            <span class="product-badge featured-badge">
                                Featured
                            </span>
                        `
                        : ""
                    }

                    ${
                        hasSale
                        ? `
                            <span class="product-badge sale-badge">
                                Sale
                            </span>
                        `
                        : ""
                    }

                    <div class="product-overlay">

                        <button
                            type="button"
                            onclick="openProduct(${product.id})">
                            Quick View
                        </button>

                        <button
                            type="button"
                            onclick="addToCart(${product.id})"
                            ${isOutOfStock ? "disabled" : ""}>
                            ${
                                isOutOfStock
                                ? "Out of Stock"
                                : "Add to Cart"
                            }
                        </button>

                    </div>

                </div>

                <div class="product-info">

                    <span class="product-category">
                        ${escapeHTML(product.category)}
                    </span>

                    <h3 class="product-name">
                        ${escapeHTML(product.name)}
                    </h3>

                    <div class="product-price">

                        ${
                            hasSale
                            ? `
                                <span class="sale-price">
                                    ${formatCurrency(salePrice)}
                                </span>

                                <span class="regular-price">
                                    ${formatCurrency(regularPrice)}
                                </span>
                            `
                            : `
                                <span class="current-price">
                                    ${formatCurrency(regularPrice)}
                                </span>
                            `
                        }

                    </div>

                    <span class="product-stock">
                        ${escapeHTML(stockStatus)}
                    </span>

                </div>

            </article>
        `;

    }).join("");

}

/* =========================================================
   FILTERS
   ========================================================= */

function setupFilters() {

    if (!categoryFilters) return;

    categoryFilters.addEventListener("click", event => {

        const button =
            event.target.closest(".filter-btn");

        if (!button) return;

        document
            .querySelectorAll(".filter-btn")
            .forEach(btn =>
                btn.classList.remove("active")
            );

        button.classList.add("active");

        const category =
            button.dataset.category;

        if (category === "all") {

            renderProducts(products);

        } else {

            const filtered =
                products.filter(product =>
                    product.category.toLowerCase() ===
                    category.toLowerCase()
                );

            renderProducts(filtered);

        }

    });

}



/* =========================================================
   PRODUCT MODAL
   ========================================================= */

function setupProductModal() {

    if (closeProductModal) {

        closeProductModal.addEventListener(
            "click",
            closeProduct
        );

    }

    if (productModal) {

        productModal.addEventListener(
            "click",
            event => {

                if (event.target === productModal) {
                    closeProduct();
                }

            }
        );

    }

    if (modalAddToCart) {

        modalAddToCart.addEventListener(
            "click",
            () => {

                if (!selectedProduct) return;

                addToCart(selectedProduct.id);

                closeProduct();

                openCart();

            }
        );

    }

}


function openProduct(id) {

    const product =
        products.find(item =>
            Number(item.id) === Number(id)
        );

    if (!product) return;

    selectedProduct = product;

    const image =
        product.image ||
        "https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=1000&q=85";

    modalProductImage.src = image;

    modalProductImage.alt = product.name;

    modalProductCategory.textContent =
        product.category;

    modalProductName.textContent =
        product.name;

    modalProductPrice.textContent =
        formatCurrency(product.price);

    modalProductDescription.textContent =
        product.description ||
        "A thoughtfully selected piece from the Peena's Place collection.";

    productModal.classList.add("active");

    document.body.classList.add("no-scroll");

}


function closeProduct() {

    productModal.classList.remove("active");

    document.body.classList.remove("no-scroll");

    selectedProduct = null;

}


/* =========================================================
   CART
   ========================================================= */

function setupCart() {

    cartButton?.addEventListener(
        "click",
        openCart
    );

    closeCart?.addEventListener(
        "click",
        closeCartDrawer
    );

    cartOverlay?.addEventListener(
        "click",
        closeCartDrawer
    );

    continueShopping?.addEventListener(
        "click",
        closeCartDrawer
    );

}


function loadCart() {

    try {

        const savedCart =
            localStorage.getItem("peenas_place_cart");

        cart =
            savedCart
                ? JSON.parse(savedCart)
                : [];

    } catch (error) {

        console.error(
            "Unable to load cart:",
            error
        );

        cart = [];

    }

}


function saveCart() {

    localStorage.setItem(
        "peenas_place_cart",
        JSON.stringify(cart)
    );

}


function addToCart(id) {

    const product =
        products.find(item =>
            Number(item.id) === Number(id)
        );

    if (!product) return;

    const existing =
        cart.find(item =>
            Number(item.id) === Number(id)
        );

    if (existing) {

        existing.quantity += 1;

    } else {

        cart.push({
            id: Number(product.id),
            name: product.name,
            price: Number(product.price),
            image: product.image,
            quantity: 1
        });

    }

    saveCart();

    updateCart();

    showToast(
        `${product.name} added to your cart`
    );

}


function removeFromCart(id) {

    cart =
        cart.filter(item =>
            Number(item.id) !== Number(id)
        );

    saveCart();

    updateCart();

}


function changeQuantity(id, amount) {

    const item =
        cart.find(product =>
            Number(product.id) === Number(id)
        );

    if (!item) return;

    item.quantity += amount;

    if (item.quantity <= 0) {

        removeFromCart(id);

        return;
    }

    saveCart();

    updateCart();

}


function updateCart() {

    const totalItems =
        cart.reduce(
            (sum, item) =>
                sum + item.quantity,
            0
        );

    const totalPrice =
        cart.reduce(
            (sum, item) =>
                sum + item.price * item.quantity,
            0
        );

    cartCount.textContent =
        totalItems;

    cartTotal.textContent =
        formatCurrency(totalPrice);


    if (!cart.length) {

        cartItems.innerHTML = `
            <div class="cart-empty">

                <div class="cart-empty-icon">
                    🛋️
                </div>

                <h3>
                    Your cart is empty
                </h3>

                <p>
                    Discover something beautiful
                    for your space.
                </p>

                <button
                    class="btn btn-primary"
                    id="continueShopping"
                    type="button">
                    Continue Shopping
                </button>

            </div>
        `;

        cartFooter.style.display = "none";

        document
            .getElementById("continueShopping")
            ?.addEventListener(
                "click",
                closeCartDrawer
            );

        return;
    }


    cartFooter.style.display = "block";


    cartItems.innerHTML =
        cart.map(item => {

            return `
                <div class="cart-item">

                    <img
                        class="cart-item-image"
                        src="${escapeAttribute(
                            item.image || ""
                        )}"
                        alt="${escapeAttribute(item.name)}">

                    <div>

                        <h4 class="cart-item-name">
                            ${escapeHTML(item.name)}
                        </h4>

                        <p class="cart-item-price">
                            ${formatCurrency(item.price)}
                        </p>

                        <div class="cart-item-quantity">

                            <button
                                class="quantity-btn"
                                type="button"
                                onclick="changeQuantity(${item.id}, -1)">
                                −
                            </button>

                            <span>
                                ${item.quantity}
                            </span>

                            <button
                                class="quantity-btn"
                                type="button"
                                onclick="changeQuantity(${item.id}, 1)">
                                +
                            </button>

                        </div>

                    </div>

                    <button
                        class="cart-item-remove"
                        type="button"
                        onclick="removeFromCart(${item.id})">
                        Remove
                    </button>

                </div>
            `;

        }).join("");

}


function openCart() {

    cartDrawer.classList.add("active");

    cartOverlay.classList.add("active");

    document.body.classList.add("no-scroll");

}


function closeCartDrawer() {

    cartDrawer.classList.remove("active");

    cartOverlay.classList.remove("active");

    document.body.classList.remove("no-scroll");

}


/* =========================================================
   CHECKOUT
   ========================================================= */

function setupCheckout() {

    checkoutButton?.addEventListener(
        "click",
        () => {

            if (!cart.length) {

                showToast(
                    "Your cart is empty"
                );

                return;
            }

            checkoutModal.classList.add("active");

        }
    );


    closeCheckoutModal?.addEventListener(
        "click",
        closeCheckout
    );


    checkoutModal?.addEventListener(
        "click",
        event => {

            if (
                event.target === checkoutModal
            ) {
                closeCheckout();
            }

        }
    );


    checkoutForm?.addEventListener(
        "submit",
        handleCheckout
    );

}


function closeCheckout() {

    checkoutModal.classList.remove("active");

}


async function handleCheckout(event) {

    event.preventDefault();

    if (!cart.length) {

        showToast(
            "Your cart is empty"
        );

        return;
    }


    const customerName =
        document
            .getElementById("customerName")
            .value
            .trim();


    const customerPhone =
        document
            .getElementById("customerPhone")
            .value
            .trim();


    if (!customerName || !customerPhone) {

        showToast(
            "Please complete your details"
        );

        return;
    }


    const total =
        cart.reduce(
            (sum, item) =>
                sum + item.price * item.quantity,
            0
        );


    const orderData = {

        customer_name:
            customerName,

        phone:
            customerPhone,

        items:
            cart.map(item => ({
                id: item.id,
                name: item.name,
                price: item.price,
                quantity: item.quantity
            })),

        total:
            total

    };


    let orderSaved = false;

    try {

        const response =
            await fetch(
                `${API_BASE}/orders.php`,
                {
                    method: "POST",

                    headers: {
                        "Content-Type":
                            "application/json"
                    },

                    body:
                        JSON.stringify(orderData)
                }
            );


        const data =
            await response.json();


        orderSaved =
            response.ok &&
            data.success === true;

    } catch (error) {

        console.warn(
            "Order API unavailable; continuing with WhatsApp checkout.",
            error
        );
    }

    try {


        const whatsappMessage =
            createWhatsAppMessage(
                customerName,
                customerPhone,
                total
            );


        const whatsappURL =
            `https://wa.me/2347062569181?text=${encodeURIComponent(
                whatsappMessage
            )}`;


        cart = [];

        saveCart();

        updateCart();

        checkoutForm.reset();

        closeCheckout();

        closeCartDrawer();

        showToast(
            orderSaved
                ? "Order saved. Opening WhatsApp..."
                : "Opening WhatsApp to complete your order..."
        );


        setTimeout(() => {

            window.open(
                whatsappURL,
                "_blank"
            );

        }, 700);


    } catch (error) {

        console.error(
            "Checkout error:",
            error
        );

        showToast(
            "We couldn't process the order. Please try again."
        );

    }

}


/* =========================================================
   WHATSAPP MESSAGE
   ========================================================= */

function createWhatsAppMessage(
    customerName,
    customerPhone,
    total
) {

    let message =
        `Hello Peena's Place 👋\n\n`;

    message +=
        `I'd like to make an inquiry/order.\n\n`;

    message +=
        `*Customer Details*\n`;

    message +=
        `Name: ${customerName}\n`;

    message +=
        `WhatsApp: ${customerPhone}\n\n`;

    message +=
        `*Selected Items*\n`;


    cart.forEach(item => {

        message +=
            `• ${item.name} x${item.quantity} - ` +
            `${formatCurrency(
                item.price * item.quantity
            )}\n`;

    });


    message +=
        `\n*Estimated Total:* ${formatCurrency(total)}\n\n`;

    message +=
        `Thank you. I'd like to confirm availability and next steps.`;

    return message;

}


/* =========================================================
   NAVIGATION
   ========================================================= */

function setupNavigation() {

    menuToggle?.addEventListener(
        "click",
        () => {

            mainNav.classList.toggle(
                "active"
            );

        }
    );


    mainNav?.querySelectorAll("a")
        .forEach(link => {

            link.addEventListener(
                "click",
                () => {

                    mainNav.classList.remove(
                        "active"
                    );

                }
            );

        });

}


/* =========================================================
   HEADER
   ========================================================= */

function setupHeader() {

    function updateHeader() {

        if (window.scrollY > 50) {

            siteHeader.classList.add(
                "scrolled"
            );

        } else {

            siteHeader.classList.remove(
                "scrolled"
            );

        }

    }


    updateHeader();

    window.addEventListener(
        "scroll",
        updateHeader,
        {
            passive: true
        }
    );

}


/* =========================================================
   TOAST
   ========================================================= */

let toastTimer;


function showToast(message) {

    clearTimeout(toastTimer);

    toastMessage.textContent =
        message;

    toast.classList.add("show");


    toastTimer =
        setTimeout(() => {

            toast.classList.remove(
                "show"
            );

        }, 3000);

}


/* =========================================================
   FORMATTING
   ========================================================= */

function formatCurrency(value) {

    const amount =
        Number(value) || 0;

    return new Intl.NumberFormat(
        "en-NG",
        {
            style: "currency",
            currency: "NGN",
            maximumFractionDigits: 0
        }
    ).format(amount);

}


/* =========================================================
   SECURITY / HTML HELPERS
   ========================================================= */

function escapeHTML(value) {

    const div =
        document.createElement("div");

    div.textContent =
        value ?? "";

    return div.innerHTML;

}


function escapeAttribute(value) {

    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/"/g, "&quot;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
}