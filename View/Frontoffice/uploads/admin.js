let products = [];

const form = document.getElementById("productForm");
const productList = document.getElementById("productList");
const nameInput = document.getElementById("name");
const priceInput = document.getElementById("price");
const categoryInput = document.getElementById("category");
const editIndexInput = document.getElementById("editIndex");

form.addEventListener("submit", function (e) {
  e.preventDefault();

  const name = nameInput.value.trim();
  const price = parseFloat(priceInput.value);
  const category = categoryInput.value.trim();

  if (name.length < 3 || category.length < 2 || isNaN(price) || price <= 0) {
    alert("Remplissez correctement tous les champs.");
    return;
  }

  const newProduct = { name, price, category };
  const index = editIndexInput.value;

  if (index === "") {
    products.push(newProduct);
  } else {
    products[index] = newProduct;
    editIndexInput.value = "";
  }

  renderProducts();
  form.reset();
});

function renderProducts() {
  productList.innerHTML = "";
  products.forEach((product, index) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${product.name}</td>
      <td>${product.price.toFixed(2)} DT</td>
      <td>${product.category}</td>
      <td class="actions">
        <button onclick="editProduct(${index})">\u270F\ufe0f</button>
        <button onclick="deleteProduct(${index})">🗑</button>
      </td>
    `;
    productList.appendChild(row);
  });
}

function editProduct(index) {
  const product = products[index];
  nameInput.value = product.name;
  priceInput.value = product.price;
  categoryInput.value = product.category;
  editIndexInput.value = index;
}

function deleteProduct(index) {
  if (confirm("Supprimer ce produit ?")) {
    products.splice(index, 1);
    renderProducts();
  }
}
