// Initialize spatial navigation state
        let spatialNavigationEnabled = navigator.spatialNavigationEnabled || false;

        // Function to toggle spatial navigation
        function toggleSpatialNavigation() {
            spatialNavigationEnabled = !spatialNavigationEnabled;
            navigator.spatialNavigationEnabled = spatialNavigationEnabled;
            alert(`Spatial Navigation ${spatialNavigationEnabled ? 'Enabled' : 'Disabled'}`);
        }

        // Function to prompt exit
        function promptExit() {
            if (confirm("Do you want to exit the app?")) {
                navigator.app.exit();
            }
        }

        // Add event listener for key presses
        document.addEventListener('keydown', function(event) {
            if (event.key === 'SoftLeft') {
                toggleSpatialNavigation();
                event.preventDefault();
            } else if (event.key === 'SoftRight') {
                promptExit();
                event.preventDefault();
            }
        });