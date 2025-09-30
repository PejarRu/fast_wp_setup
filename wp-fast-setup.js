// Test script loading
alert('WP Fast Setup: JavaScript loaded successfully!');

console.log('WP Fast Setup: Script loaded - Test version');

document.addEventListener('DOMContentLoaded', function() {
    console.log('WP Fast Setup: DOMContentLoaded fired');

    // Tab switching - Test version
    const tabs = document.querySelectorAll('.wpf-tab');
    console.log('WP Fast Setup: Found', tabs.length, 'tabs');

    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Tab clicked: ' + this.getAttribute('data-tab'));

            // Remove active class from all tabs
            document.querySelectorAll('.wpf-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.wpf-tab-content').forEach(tc => tc.classList.remove('active'));

            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            const targetTab = this.getAttribute('data-tab');
            const targetContent = document.getElementById(targetTab);
            if (targetContent) {
                targetContent.classList.add('active');
                alert('Switched to tab: ' + targetTab);
            }
        });
    });

    console.log('WP Fast Setup: Tab switching initialized');
});