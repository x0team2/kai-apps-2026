document.addEventListener("keydown", (e) => {
  switch(e.key) {

    /* Volume Down */
    case "V-":
      if (navigator.volumeManager)
        navigator.volumeManager.requestDown();
      break;

    /* Volume Up */
    case "V+":
      if (navigator.volumeManager)
        navigator.volumeManager.requestUp();
      break;

    /* Exit App */
    case "SoftRight":
      window.close();
      break;

    /* Show Kai Ad */
    case "Ads":
      showKaiAd();
      break;

    /* Toggle Emulated Cursor */
    case "SoftLeft": // Or any key you prefer
      if (navigator.spatialNavigationEnabled !== undefined) {
        navigator.spatialNavigationEnabled = !navigator.spatialNavigationEnabled;
        alert(`Emulated Cursor ${navigator.spatialNavigationEnabled ? 'Enabled' : 'Disabled'}`);
      }
      break;


/* Scroll Up 100px with '2' key */
    case "2":
      window.scrollBy({ top: -100, behavior: 'smooth' });
      break;

    /* Scroll Down 100px with '8' key */
    case "8":
      window.scrollBy({ top: 100, behavior: 'smooth' });
      break;


  }
});