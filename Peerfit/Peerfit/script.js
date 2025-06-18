function initMap() {
  const myLatLng = { lat: 51.507232666015625, lng: -0.12758620083332062 };
  const map = new google.maps.Map(document.getElementById("gmp-map"), {
    zoom: 14,
    center: myLatLng,
    fullscreenControl: false,
    zoomControl: true,
    streetViewControl: false
  });
  new google.maps.Marker({
    position: myLatLng,
    map,
    title: "My location"
  });
}

// Place a marker at the specified location
function placeMarker(location) {
  var marker = new google.maps.Marker({
    position: location,
    map: map
  });

  // Save the location data to your database
  // ...

  // You can also display an info window or prompt the user for further actions
}

$(document).ready(function() {
  // Handle message form submission
  $('#messageForm').submit(function(e) {
      e.preventDefault();
      var message = $('#messageInput').val().trim(); // Trim the message to remove leading/trailing whitespaces

      if (message !== '') {
          $.post('sendMessage.php', { message: message }, function(response) {
              var res = JSON.parse(response);
              if (res.status === "success") {
                  // Message was successfully sent, now clear input and append the message
                  $('#messageInput').val(''); // Clear input field only after successful send
                  // Optionally append the message or call loadMessages() to refresh the chat
                  loadMessages();
              } else {
                  // Handle error: message not sent
                  console.error("Failed to send message:", res.message || "An error occurred");
              }
          });
      }
  });

  // Search functionality for user list
  $('#searchInput').keyup(function() {
      var searchTerm = $(this).val().toLowerCase();

      $('#user-list .user').each(function() {
          var userName = $(this).text().toLowerCase();

          // Check if the user name contains the search term
          if (userName.includes(searchTerm)) {
              $(this).show();
          } else {
              $(this).hide();
          }
      });
  });

  // Load initial set of messages
  loadMessages();
  // Refresh messages every 5 seconds
  setInterval(loadMessages, 5000);
});

// Function to load messages
function loadMessages() {
  $.get('loadMessages.php', function(messages) {
      $('#messageContainer').html(messages);
      $('#messageContainer').scrollTop($('#messageContainer')[0].scrollHeight);
  });
}

  loadMessages(); // Initial load
  setInterval(loadMessages, 5000); // Refresh messages every 5 seconds


  // User selection handling
  $(document).on('click', '.user', function() {
      var userId = $(this).data('user-id');
      var userName = $(this).text(); // Capture the user's name

      // Update the chat container's header with the selected user's name
      if ($('.chat-header').length > 0) {
          $('.chat-header').text(userName); // Update existing header
      } else {
          // Prepend a new header if it doesn't exist
          $('.chat-container').prepend(`<h3 class="chat-header">${userName}</h3>`);
      }

      // AJAX call to set the chat partner ID in the session on the server
      $.post('setChatPartner.php', { chat_partner_id: userId }, function(response) {
          var res = JSON.parse(response);
          if (res.status === "success") {
              // Successfully set the chat partner, now load messages for the selected chat partner
              loadMessages();
          } else {
              console.error("Failed to set chat partner:", res.message || "Unknown error");
          }
      });
  });


  // Script to manage category tabs and content display

  








