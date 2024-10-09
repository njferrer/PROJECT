// Function to handle successful scan
function onScanSuccess(decodedText, decodedResult) {
  // Optional: Validate the scanned card number before redirecting
  if (validateCardNumber(decodedText)) {
    // Redirect to process-transaction.php with the scanned content
    window.location.href =
      "process-transaction.php?card_number=" + encodeURIComponent(decodedText);
  } else {
    alert("Invalid QR Code detected.");
  }
}

// Function to handle scan errors
function onScanError(errorMessage) {
  // Handle scan error (optional: display to user)
  console.error(errorMessage);
}

// Function to validate card number format
function validateCardNumber(cardNumber) {
  // Example validation: starts with 'CARD' followed by 10 hexadecimal characters
  const regex = /^CARD[A-F0-9]{10}$/;
  return regex.test(cardNumber);
}

// Initialize the scanner
var html5QrcodeScanner = new Html5QrcodeScanner(
  "reader",
  { fps: 10, qrbox: 250 },
  /* verbose= */ false
);

// Start scanning
html5QrcodeScanner.render(onScanSuccess, onScanError);
