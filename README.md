# **Elysium**

Elysium is a plug-and-play web app platform built with HTML and PHP. It allows users to create a personalized web-based client using local servers like XAMPP, WAMP, or Laragon to integrate and interact with their favorite web applications in a single unified dashboard.

**Key Features**

+ **Centralized Hub:** Interact with books, comics, music, podcasts, games, and PDFs in one place
+ **Flexible Storage:** Access content via local web servers, cloud services (Box, Google Drive), or internal/external storage.
+ **Customizable:** Easily add your own categories and web apps.
+ **Plug & Play:** Simple setup with no complex database configurations required.

**Folder Structure**

The platform is organized into the following directory structure:

+ **CSS:** Contains stylesheets for colors, layout, and visual design.
+ **Fonts:** Main typography files for the dashboard.
+ **Images:** Interface wallpapers.
+ **Logos:** Main Elysium branding assets.
+ **Messages:** Text snippets for Elysium and Yggdrasil pages.
+ **Search:** The core directory. This is where all web apps live, organized by category.
+ **Config:** Stores the absolute path to the project's root folder
+ **Security:** Protects against path traversal, XSS, and malicious file access
+ **Index.php:** The main entry point to launch the dashboard.

**Installation & Setup**

Follow these steps to get your local instance running:

1. **Download:** Download the Elysium Project files.
2. **Setup a local server:** Download and install XAMPP, WAMP, or Laragon. (For USB use, grab the portable version.).
3. **Deploy:** Extract the project and place the folder into your server's htdocs (or equivalent) directory
4. **Launch:** Open your web browser and navigate to the index page (e.g., localhost/Elysium/index.php).

**Included Web Apps**

Elysium comes pre-loaded with a variety of apps. You can add more by placing them in the appropriate category within the /Search folder.

+ **Apps:** AppBarn, Webology, Yggdrasil
+ **Audio:** Aurora, Soundify, Storyboard, Themata, Wavora.
+ **Gaming:** Flashbot, Flashboy, Web U.
+ **Books & Comics:** Fumetti, Graphique, Novella, Scribe.
+ **Gallary:** Boardify, Figura, Gallerium, Imagen.
+ **Music:** Andromeda, Moodify, Musica, Pandora, Sahara, Serenade.
+ **Office:** Legere, Nota.
+ **Online:** Hematite, Obsidian.
+ **Videos:** Hydrae, Lorevania, Vetra, Videre, WonderBox.
+ **Xtras:** Guides.

### Nexus: Portable Applications
🛠️ **Status:** In development. Coming soon!

### Homunculus: Automated Scripts
🛠️ **Status:** In development. Coming soon!

> 🚀 **Continuous improvement :** An ongoing effort to enhance the Elysium web platform through updates.
> 
> 📝 **Usage:** Elysium is for personal use only.
>
> 🆘 **Support:** If you have any issues with Elysium please reach out politely in GitHub Discussions






