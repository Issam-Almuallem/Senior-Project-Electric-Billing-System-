const yearSpan = document.getElementById('year');
yearSpan.textContent = new Date().getFullYear();

let currentIndex = 0;
const slides = document.getElementById('slides');
const totalSlides = slides.children.length;

function showSlide(index) {
  if (index >= totalSlides) {
    currentIndex = 0;
  } else if (index < 0) {
    currentIndex = totalSlides - 1;
  } else {
    currentIndex = index;
  }
  slides.style.transform = `translateX(${-currentIndex * 100}%)`;
}

setInterval(() => {
  showSlide(currentIndex + 1);
}, 5000); // Change slide every 5 seconds
function myFunction() {
  var element = document.body;
  element.dataset.bsTheme =
    element.dataset.bsTheme == "light" ? "dark" : "light";
    
}

function stepFunction(event) {
  debugger;
  var element = document.getElementsByClassName("collapse");
  for (var i = 0; i < element.length; i++) {
    if (element[i] !== event.target.ariaControls) {
      element[i].classList.remove("show");
    }
  }
}
function toggleDiscount() {
  const isChecked = document.getElementById('yearlyToggle').checked;

  // Prices for yearly and monthly
  const prices = {
    Basic: { yearly: 500, monthly: 600 },
    Standard: { yearly: 1000, monthly: 1200 },
    Economy: { yearly: 600, monthly: 720 },
    Premium: { yearly: 2000, monthly: 2400 },
    Business: { yearly: 3500, monthly: 4200 },
    Enterprise: { yearly: 6000, monthly: 7200 }
  };

  // Update the displayed prices
  document.getElementById('BasicPrice').innerText = `$${isChecked ? prices.Basic.yearly : prices.Basic.monthly}/Year`;
  document.getElementById('StandardPrice').innerText = `$${isChecked ? prices.Standard.yearly : prices.Standard.monthly}/Year`;
  document.getElementById('EconomyPrice').innerText = `$${isChecked ? prices.Economy.yearly : prices.Economy.monthly}/Year`;
  document.getElementById('PremiumPrice').innerText = `$${isChecked ? prices.Premium.yearly : prices.Premium.monthly}/Year`;
  document.getElementById('BusinessPrice').innerText = `$${isChecked ? prices.Business.yearly : prices.Business.monthly}/Year`;
  document.getElementById('EnterprisePrice').innerText = `$${isChecked ? prices.Enterprise.yearly : prices.Enterprise.monthly}/Year`;
}
function getUserLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (position) {
      const latitude = position.coords.latitude;
      const longitude = position.coords.longitude;
      const userLocation = `Latitude: ${latitude}, Longitude: ${longitude}`;
      document.getElementById('user-location').value = userLocation;
    }, function (error) {
      alert("Unable to retrieve your location. Please enter it manually.");
    });
  } else {
    alert("Geolocation is not supported by this browser.");
  }
}
function togglePaymentMethod() {
  // Get the selected payment method
  var paymentMethod = document.querySelector('input[name="payment"]:checked').value;

  // Show or hide credit card information based on selected payment method
  var creditCardInfo = document.getElementById('credit-card-info');
  var cardNumber = document.querySelector('input[name="cardNumber"]');
  var cardType = document.querySelector('select[name="cardType"]');

  if (paymentMethod === 'paypal') {
    // Hide credit card section and remove required attributes
    creditCardInfo.style.display = 'none';
    cardNumber.removeAttribute('required');
    cardType.removeAttribute('required');
  } else {
    // Show credit card section and add required attributes
    creditCardInfo.style.display = 'block';
    cardNumber.setAttribute('required', 'required');
    cardType.setAttribute('required', 'required');
  }
}

// Call togglePaymentMethod on page load to set the initial state
document.addEventListener('DOMContentLoaded', togglePaymentMethod);


// Initialize the page with the correct view based on the selected payment method
window.onload = function () {
  togglePaymentMethod();
};

function selectBundle(price, bundleName) {
  // Update the "Choose Your Bundle" button text
  document.querySelector('.btn-info').textContent = `${bundleName} - $${price}/Year`;

  // Update the Subtotal
  document.getElementById("subtotal").textContent = price;

  // Calculate tax (e.g., 5% tax)
  var tax = price * 0.05;
  document.getElementById("tax").textContent = tax.toFixed(2);

  // Calculate Total (Subtotal + Tax)
  var total = price + tax;
  document.getElementById("total").textContent = total.toFixed(2);
}
// Function to handle the bundle selection and price updates
function updateBundlePrice() {
  // Get selected bundle price from the dropdown
  var bundlePrice = parseFloat(document.getElementById("bundleSelect").value);

  if (bundlePrice === 0) {
    // If no bundle is selected, exit the function (or reset values)
    document.getElementById("subtotal").textContent = "0";
    document.getElementById("tax").textContent = "0";
    document.getElementById("total").textContent = "0";
    document.getElementById("amountPaid").value = "0"; // Reset the hidden input
    return;
  }

  // Calculate the tax (example: 5% tax)
  var tax = bundlePrice * 0.05;

  // Calculate the total (Subtotal + Tax)
  var total = bundlePrice + tax;

  // Update the order summary with calculated values
  document.getElementById("subtotal").textContent = bundlePrice.toFixed(2);
  document.getElementById("tax").textContent = tax.toFixed(2);
  document.getElementById("total").textContent = total.toFixed(2);

  // Update the hidden input with the total value
  document.getElementById("amountPaid").value = total.toFixed(2);
}

// Set theme on page load based on stored preference
window.addEventListener('load', () => {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme) {
    document.documentElement.setAttribute('data-theme', savedTheme);
  } else {
    // Default to light mode if no theme is set
    document.documentElement.setAttribute('data-theme', 'light');
  }
});
function viewPaymentHistory() {
  // Show the modal when the button is clicked
  var myModal = new bootstrap.Modal(document.getElementById('paymentHistoryModal'));
  myModal.show();
}
document.getElementById('contact-form').addEventListener('submit', function (event) {
  event.preventDefault();  // Prevent form submission from reloading the page

  // Simulate form submission logic (you can integrate with your backend here)
  let form = document.getElementById('contact-form');
  let formData = new FormData(form);

  // Show the confirmation popup
  document.getElementById('popup-modal').style.display = 'flex';

  // Optionally reset the form
  form.reset();
});

// Close the popup when the user clicks the "OK" button
document.getElementById('close-modal').addEventListener('click', function () {
  document.getElementById('popup-modal').style.display = 'none';
});
function toggleMobileNav() {
  var mobileNav = document.querySelector('.mobile-nav');
  mobileNav.classList.toggle('active');
}
document.addEventListener('DOMContentLoaded', function () {
  const prevBtn = document.querySelector('.prev-btn');
  const nextBtn = document.querySelector('.next-btn');
  const sliderContainer = document.querySelector('.slider-container');
  const testimonialItems = document.querySelectorAll('.testimonial-item');

  // Clone the first and last items for infinite loop
  const firstItem = testimonialItems[0].cloneNode(true);
  const lastItem = testimonialItems[testimonialItems.length - 1].cloneNode(true);

  // Append the cloned items
  sliderContainer.appendChild(firstItem);
  sliderContainer.insertBefore(lastItem, testimonialItems[0]);

  const allItems = document.querySelectorAll('.testimonial-item'); // Now includes the cloned first and last items

  let currentIndex = 1; // Start at the first testimonial (not the cloned first one)

  // Show the next testimonial
  function showNext() {
      if (currentIndex === allItems.length - 1) {
          // If we reach the last testimonial (the cloned one), reset to the real last item
          currentIndex = allItems.length - 2; // Skip the cloned last item
          updateSliderPosition();
          setTimeout(() => {
              sliderContainer.style.transition = 'none'; // Disable transition
              sliderContainer.style.transform = `translateX(-${currentIndex * 100}%)`; // Reset position instantly
          }, 500); // Allow time for the last transition to complete
      } else {
          currentIndex++;
          updateSliderPosition();
      }
  }

  // Show the previous testimonial
  function showPrev() {
      if (currentIndex === 0) {
          // If we reach the first testimonial (the cloned one), reset to the real first item
          currentIndex = 1; // Skip the cloned first item
          updateSliderPosition();
          setTimeout(() => {
              sliderContainer.style.transition = 'none'; // Disable transition
              sliderContainer.style.transform = `translateX(-${currentIndex * 100}%)`; // Reset position instantly
          }, 500); // Allow time for the last transition to complete
      } else {
          currentIndex--;
          updateSliderPosition();
      }
  }

  // Update the slider position
  function updateSliderPosition() {
      sliderContainer.style.transition = 'transform 0.5s ease'; // Add transition for smoothness
      sliderContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
  }

  // Event Listeners for buttons
  nextBtn.addEventListener('click', showNext);
  prevBtn.addEventListener('click', showPrev);

  // Optional: Autoplay feature
  setInterval(showNext, 7000); // Change slide every 8 seconds
});
window.addEventListener('beforeunload', function () {
  // Send a request to the server to destroy the session
  navigator.sendBeacon('logout.php');
});
document.getElementById('bundleSelect').addEventListener('change', function () {
  const total = this.value; // Directly get the selected value from the dropdown
  document.getElementById('total').textContent = total; // Update the total display
});
document.getElementById('viewHistoryButton').addEventListener('click', function() {
  var user_id = 1; // Replace this with the actual user ID

  // Make an AJAX call to get the payment and consumption records
  fetch('get_history.php?user_id=' + user_id)
      .then(response => response.json())
      .then(data => {
          // Show the history form
          let historyForm = document.getElementById('historyForm');
          historyForm.style.display = 'block';

          // Populate the history form with payment and consumption records
          let historyContent = `
              <h3>Payment History</h3>
              <table border="1">
                  <tr><th>Card Number</th><th>Balance</th><th>Card Type</th></tr>
                  ${data.payments.map(payment => `
                      <tr>
                          <td>${payment.Credit_num}</td>
                          <td>${payment.Balance}</td>
                          <td>${payment.CreditCardType}</td>
                      </tr>
                  `).join('')}
              </table>

              <h3>Consumption Records</h3>
              <table border="1">
                  <tr><th>Record ID</th><th>Consumption</th></tr>
                  ${data.consumption.map(consumption => `
                      <tr>
                          <td>${consumption.Record_ID}</td>
                          <td>${consumption.Consumption}</td>
                      </tr>
                  `).join('')}
              </table>
          `;
          historyForm.innerHTML = historyContent;
      });
});
const toggleFormBtn = document.getElementById('toggleFormBtn');
const userFormContainer = document.getElementById('userFormContainer');

toggleFormBtn.addEventListener('click', () => {
    if (userFormContainer.style.display === 'none' || userFormContainer.style.display === '') {
        userFormContainer.style.display = 'block';
    } else {
        userFormContainer.style.display = 'none';
    }
});