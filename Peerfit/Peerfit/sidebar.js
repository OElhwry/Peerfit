document.addEventListener('DOMContentLoaded', () => {
  const profileLink = document.querySelector('a[href="#profile"]');
  const secondarySidebar = document.querySelector('.secondary-sidebar');
  const chatContainer = document.querySelector('.chat-container');
  const userSelectionSidebar = document.querySelector('.user-selection-sidebar');

  profileLink.addEventListener('click', (event) => {
      event.preventDefault();
      toggleSecondarySidebar();
  });

  function toggleSecondarySidebar() {
      // Check if the secondary sidebar is currently displayed
      const isDisplayed = secondarySidebar.style.display === 'flex';
      
      if (!isDisplayed) {
          // Show the secondary sidebar
          secondarySidebar.style.display = 'flex';
          // Adjust the chat container and user selection sidebar to the right to make space
          chatContainer.style.marginLeft = '550px'; // Adjust this value based on your sidebars' widths
          if (userSelectionSidebar) { // Check if userSelectionSidebar exists to avoid errors
              userSelectionSidebar.style.left = '550px'; // Align with the chat container's new margin
          }
      } else {
          // Hide the secondary sidebar
          secondarySidebar.style.display = 'none';
          // Reset the chat container and user selection sidebar back to their original positions
          chatContainer.style.marginLeft = '200px'; // Original margin considering only the primary sidebar
          if (userSelectionSidebar) { // Check if userSelectionSidebar exists to avoid errors
              userSelectionSidebar.style.left = '200px'; // Align with the primary sidebar's width
          }
      }
  }
});
