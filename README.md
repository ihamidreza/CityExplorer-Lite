# CityExplorer Lite – Web GIS Demo

CityExplorer Lite is a small Web GIS application built with **PHP**, **MySQL**, **Leaflet.js**, and **Tailwind CSS**.

It displays a map of a city with Points of Interest (POIs) loaded from a MySQL database, allows filtering and searching, and includes a simple admin panel (CRUD) for managing POIs.

---

## Features

- **Interactive map** with Leaflet and OpenStreetMap tiles
- **POI markers** loaded from MySQL (via a PHP JSON API)
- **Filter & search**:
- Filter by category (Attraction, Restaurant, School, Hospital)
- Search by name
- **Sidebar POI list** synchronized with the map:
- Clicking a list item zooms to the marker and opens its popup
- Hovering a list item highlights the corresponding marker on the map
- **Extra map tools**:
- Reset filters
- Zoom to all visible POIs
- Locate Me (HTML Geolocation API)
- **Admin panel** (`admin.php`):
- Create / Edit / Delete POIs
- Simple validation
- Uses the same database as the map

---

## Tech Stack

- **Frontend**
  - HTML, JavaScript
  - Leaflet.js
  - Tailwind CSS via CDN
- **Backend**
  - PHP (no framework)
- **Database**
  - MySQL (tested with XAMPP)

---

## Project Structure

- **index.php** Main map UI (frontend)

- **admin.php** Admin CRUD panel for POIs

- **get_pois.php** PHP API that returns POIs as JSON

- **db_config.php** Database connection (PDO)

- **README.md**

- **assets/**

  **css/styles.css** Custom styles (map container, etc.)

  **js/app.js** All frontend map logic (Leaflet, filters, list, tools)
