document.addEventListener('DOMContentLoaded', () => {
    const tabsContainer = document.querySelector('.category-tabs');
    const tabs = document.querySelectorAll('.tab');
    const categories = document.querySelectorAll('.category');
    const interests = document.querySelectorAll('.interest');

    // Function to toggle the 'selected' class on interest clicks
    interests.forEach(interest => {
        interest.addEventListener('click', () => {
            interest.classList.toggle('selected');
        });
    });
    
    // Function to rotate tabs
    function rotateTabs(activeTab) {
      tabsContainer.appendChild(tabsContainer.firstElementChild); // Move the first tab to the end
      // Find the newly placed tab and click it if it's the active one we want
      if (tabsContainer.lastElementChild.getAttribute('data-category') === activeTab) {
        tabsContainer.lastElementChild.click();
      }
    }
  
    function showCategory(categoryId) {
      // Hide all categories
      categories.forEach(category => {
        category.style.display = 'none';
      });
  
      // Remove 'active' class from all tabs
      tabs.forEach(tab => {
        tab.classList.remove('active');
      });
  
      // Show the selected category
      const selectedCategory = document.getElementById(categoryId);
      selectedCategory.style.display = 'flex';
  
      // Find and highlight the active tab
      const activeTab = Array.from(tabs).find(tab => tab.getAttribute('data-category') === categoryId);
      activeTab.classList.add('active');
  
      // Rotate tabs to put the active tab in the center
      rotateTabs(categoryId);
    }
  
    // Initialize event listeners for tabs
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const category = tab.getAttribute('data-category');
        showCategory(category);
      });
    });
  
    // Initialize the tabs to show the first three
    rotateTabs(tabs[0].getAttribute('data-category'));
  });
  

  document.getElementById('saveInterests').addEventListener('click', function() {
    // Gather selected interests
    var selectedInterests = [];
    document.querySelectorAll('.interest.selected').forEach(function(interest) {
        selectedInterests.push(interest.textContent.trim()); // Use .textContent or a data attribute, depending on your HTML
    });

    // Prepare data to be sent
    var data = JSON.stringify({ interests: selectedInterests });

    // Send data to save_interests.php using fetch API
    fetch('save_interests.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'profile.php'; // Redirect on success
        } else {
            alert('Failed to save interests: ' + data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
});



