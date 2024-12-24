const products = [
  { id: 1, category: 'Perfume Paradise', name: 'Body splash', price: 45, image: 'pica.jpg' },
  { id: 2, category: 'Perfume Paradise', name: 'Cherry creme', price: 25, image: 'picb.jpg' },
  { id: 3, category: 'Skin care', name: 'Soothing gel', price: 35, image: 'picc.jpg' },
  { id: 4, category: 'Skin care', name: 'Milk Shower gel', price: 70, image: 'picd.jpg' },
  { id: 5, category: 'Gleaming Glasses', name: 'Glass cup', price: 45, image: 'pice.jpg' },
  { id: 6, category: 'Skin care', name: 'Kleanz body wash', price: 35, image: 'picf.jpg' }
];

// Function to display products based on category or search query
function displayProducts(query = '') {
  const productsContainer = document.getElementById('products');
  productsContainer.innerHTML = ''; // Clear existing products
  products.forEach(product => {
    if (query === '' || product.category === query || product.name.toLowerCase().includes(query.toLowerCase())) {
      const productCard = document.createElement('div');
      productCard.classList.add('product-card');
      productCard.innerHTML = `
        <h2>${product.name}</h2>
        <img src="${product.image}" alt="${product.name}">
        <p>Category: ${product.category}</p>
        <p>Price: GH$${product.price}</p>
        <button onclick="openOrderModal('${product.name}', ${product.price})" style="background-color: red;">Order Now</button>
      `;
      productsContainer.appendChild(productCard);
    }
  });
}

// Call the function to display products when the page loads initially
window.onload = function() {
  // Simulate delay to show animation
  setTimeout(() => {
    const animationContainer = document.querySelector('.animation-container');
    animationContainer.style.display = 'none'; // Hide animation container
    document.body.style.overflow = 'auto'; // Restore overflow
    displayProducts(); // Display products
  }, 4000); // 4-second delay
};

// Function to open the order modal
function openOrderModal(productName, productPrice) {
  const modal = document.getElementById('orderModal');
  modal.style.display = 'block';
  // Reset modal content every time it's opened
  modal.innerHTML = `
    <div class="modal-content">
      <h2>How would you like to receive your order?</h2>
      <button onclick="orderPickup('${productName}', ${productPrice})">Pick-Up</button>
      <button onclick="orderDelivery('${productName}', ${productPrice})">Delivery</button>
      <button onclick="closeOrderModal()">Close</button>
    </div>
  `;
}

// Function to handle order pick-up
function orderPickup(productName, productPrice) {
  const modal = document.getElementById('orderModal');
  modal.innerHTML = `
    <div class="modal-content">
      <h2>Confirm Order for Pick-Up</h2>
      <input type="text" id="pickupNameInput" placeholder="Your Name">
      <input type="text" id="pickupPhoneInput" placeholder="Your Phone Number">
      <button onclick="confirmOrderPickup('${productName}', ${productPrice})">Confirm Order</button>
      <button onclick="closeOrderModal()">Cancel</button>
    </div>
  `;
}

// Function to handle order delivery
function orderDelivery(productName, productPrice) {
  const modal = document.getElementById('orderModal');
  modal.innerHTML = `
    <div class="modal-content">
      <h2>Confirm Order for Delivery</h2>
      <input type="text" id="deliveryNameInput" placeholder="Your Name">
      <input type="text" id="deliveryPhoneInput" placeholder="Your Phone Number">
      <input type="text" id="deliveryLocationInput" placeholder="Delivery Location">
      <button onclick="confirmOrderDelivery('${productName}', ${productPrice})">Confirm Order</button>
      <button onclick="closeOrderModal()">Cancel</button>
    </div>
  `;
}

// Function to send WhatsApp message
function sendWhatsAppMessage(message) {
  const whatsappNumber = '+233208320063'; // Replace with your WhatsApp number
  const encodedMessage = encodeURIComponent(message);
  const whatsappURL = `https://wa.me/${whatsappNumber}/?text=${encodedMessage}`;
  window.open(whatsappURL);
}

// Function to confirm order for pick-up
function confirmOrderPickup(productName, productPrice) {
  const name = document.getElementById('pickupNameInput').value;
  const phone = document.getElementById('pickupPhoneInput').value;
  if (!name || !phone) {
    alert('Please fill out all fields.');
    return;
  }
  const message = `Hello! My name is ${name} and my phone number is ${phone}. I would like to order ${productName} for pick-up at GH$${productPrice}.`;
  console.log(message); // Debugging: check message in console
  sendWhatsAppMessage(message);
  closeOrderModal();
}

// Function to confirm order for delivery
function confirmOrderDelivery(productName, productPrice) {
  const name = document.getElementById('deliveryNameInput').value;
  const phone = document.getElementById('deliveryPhoneInput').value;
  const location = document.getElementById('deliveryLocationInput').value;
  if (!name || !phone || !location) {
    alert('Please fill out all fields.');
    return;
  }
  const message = `Hello! My name is ${name} and my phone number is ${phone}. I would like to order ${productName} for delivery at GH$${productPrice} to ${location}.`;
  console.log(message); // Debugging: check message in console
  sendWhatsAppMessage(message);
  closeOrderModal();
}



// Function to close the order modal
function closeOrderModal() {
  const modal = document.getElementById('orderModal');
  modal.style.display = 'none';
}

// Function to filter products based on category and update search input
function filterProducts(category) {
  document.getElementById('searchInput').value = category; // Set search input to category
  displayProducts(category);
}

// Function to search for products based on search query
function searchProducts() {
  const searchInput = document.getElementById('searchInput').value;
  displayProducts(searchInput);
}

// Function to clear the search and show all products
function clearSearch() {
  document.getElementById('searchInput').value = '';
  displayProducts();
}
document.addEventListener("DOMContentLoaded", function() {
  // Simulate loading time before hiding animation container
  setTimeout(function() {
    document.querySelector('.animation-container').classList.add('hide');
  }, 3000); // Adjust time as needed, 3000ms = 3 seconds
});
