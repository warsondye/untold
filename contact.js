// Function to send WhatsApp message
function sendWhatsAppMessage(message) {
    const formattedMessage = encodeURIComponent(message);
    const whatsappLink = `https://wa.me/233208320063/?text=${formattedMessage}`; // Replace 1234567890 with your WhatsApp number
    window.open(whatsappLink, '_blank');
  }
  
  // Function to handle form submission
  document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
  
    // Get form values
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const message = document.getElementById('message').value;
  
    // Construct WhatsApp message
    const whatsappMessage = `Name: ${name}%0AEmail: ${email}%0AMessage: ${message}`;
  
    // Send WhatsApp message
    sendWhatsAppMessage(whatsappMessage);
  
    // Alert user and optionally clear the form fields
    alert('Message sent successfully!');
    document.getElementById('name').value = '';
    document.getElementById('email').value = '';
    document.getElementById('message').value = '';
  });
  