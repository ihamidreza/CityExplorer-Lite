// Initialize map
const map = L.map("map").setView([53.56, 13.27], 13); // Center on Neubrandenburg

// Marker icons
const defaultIcon = L.icon({
  iconUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png",
  iconRetinaUrl:
    "https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png",
  shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41],
});

const highlightIcon = L.icon({
  iconUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png",
  iconRetinaUrl:
    "https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png",
  shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
  iconSize: [30, 49],
  iconAnchor: [15, 49],
  popupAnchor: [1, -40],
  shadowSize: [50, 50],
});

L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  maxZoom: 19,
  attribution: "&copy; OpenStreetMap contributors",
}).addTo(map);

// Data storage
let allPois = [];
const poiMarkers = [];
let userMarker = null;
let userAccuracyCircle = null;

function addPoiMarkers(pois) {
  pois.forEach((poi) => {
    if (!poi.latitude || !poi.longitude) return;

    const marker = L.marker([poi.latitude, poi.longitude], {
      icon: defaultIcon,
    }).addTo(map);

    const popupHtml = `
            <strong>${poi.name}</strong><br>
            <span>${poi.category}</span><br>
            <small>${poi.description ?? ""}</small>
        `;

    marker.bindPopup(popupHtml);

    poiMarkers.push({
      marker: marker,
      data: poi,
    });
  });
}

function renderPoiList(pois) {
  const container = document.getElementById("poiList");
  const countEl = document.getElementById("poiCount");

  container.innerHTML = "";

  if (!pois || pois.length === 0) {
    if (countEl) {
      countEl.textContent = "0 items";
    }
    const empty = document.createElement("div");
    empty.className = "text-xs text-slate-400 italic";
    empty.textContent = "No results.";
    container.appendChild(empty);
    return;
  }

  if (countEl) {
    countEl.textContent = `${pois.length} item${pois.length > 1 ? "s" : ""}`;
  }

  pois.forEach((poi) => {
    const item = document.createElement("div");
    item.className =
      "p-2 bg-slate-700 rounded cursor-pointer hover:bg-slate-600";

    item.innerHTML = `
            <strong>${poi.name}</strong><br>
            <span class="text-xs text-slate-300">${poi.category}</span>
        `;

    // Click: focus and open popup
    item.addEventListener("click", () => {
      map.setView([poi.latitude, poi.longitude], 15);
      const found = poiMarkers.find((m) => m.data.id == poi.id);
      if (found) {
        found.marker.openPopup();
      }
    });

    // Hover: highlight marker
    item.addEventListener("mouseenter", () => {
      const found = poiMarkers.find((m) => m.data.id == poi.id);
      if (found) {
        found.marker.setIcon(highlightIcon);
        found.marker.setZIndexOffset(1000);
      }
    });

    item.addEventListener("mouseleave", () => {
      const found = poiMarkers.find((m) => m.data.id == poi.id);
      if (found) {
        found.marker.setIcon(defaultIcon);
        found.marker.setZIndexOffset(0);
      }
    });

    container.appendChild(item);
  });
}

// Load POIs from API
fetch("get_pois.php")
  .then((response) => response.json())
  .then((data) => {
    if (Array.isArray(data)) {
      allPois = data;
      addPoiMarkers(allPois);
      renderPoiList(allPois);
    } else if (data.error) {
      console.error("API error:", data.error);
    }
  })
  .catch((error) => {
    console.error("Fetch error:", error);
  });

// Filtering
function applyFilter() {
  const searchName = document
    .getElementById("searchName")
    .value.trim()
    .toLowerCase();
  const category = document.getElementById("categoryFilter").value;

  const filtered = allPois.filter((poi) => {
    const name = poi.name ? poi.name.toLowerCase() : "";
    const itemCategory = poi.category || "";

    let nameMatch = true;
    if (searchName !== "") {
      nameMatch = name.includes(searchName);
    }

    let categoryMatch = true;
    if (category !== "") {
      categoryMatch = itemCategory === category;
    }

    return nameMatch && categoryMatch;
  });

  // Update markers
  poiMarkers.forEach((item) => {
    const match = filtered.some((p) => p.id == item.data.id);

    if (match) {
      if (!map.hasLayer(item.marker)) {
        item.marker.addTo(map);
      }
      item.marker.setIcon(defaultIcon);
      item.marker.setZIndexOffset(0);
    } else {
      if (map.hasLayer(item.marker)) {
        map.removeLayer(item.marker);
      }
    }
  });

  renderPoiList(filtered);
}

function resetFilter() {
  const searchInput = document.getElementById("searchName");
  const categorySelect = document.getElementById("categoryFilter");

  if (searchInput) searchInput.value = "";
  if (categorySelect) categorySelect.value = "";

  poiMarkers.forEach((item) => {
    if (!map.hasLayer(item.marker)) {
      item.marker.addTo(map);
    }
    item.marker.setIcon(defaultIcon);
    item.marker.setZIndexOffset(0);
  });

  renderPoiList(allPois);
}

function locateUser() {
  if (!navigator.geolocation) {
    alert("Geolocation is not supported by your browser.");
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (position) => {
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;
      const accuracy = position.coords.accuracy;

      if (userMarker) {
        map.removeLayer(userMarker);
      }
      if (userAccuracyCircle) {
        map.removeLayer(userAccuracyCircle);
      }

      userMarker = L.marker([lat, lng], {
        title: "You are here",
      }).addTo(map);

      userAccuracyCircle = L.circle([lat, lng], {
        radius: accuracy,
      }).addTo(map);

      map.setView([lat, lng], 15);
    },
    (error) => {
      console.error(error);
      alert("Could not get your location.");
    }
  );
}

// Zoom to all visible POIs
function zoomToAll() {
  const visibleMarkers = poiMarkers.filter((item) => map.hasLayer(item.marker));

  if (visibleMarkers.length === 0) {
    return;
  }

  const bounds = L.latLngBounds(
    visibleMarkers.map((item) => [item.data.latitude, item.data.longitude])
  );

  map.fitBounds(bounds);
}

// Event listeners
document.getElementById("applyFilter").addEventListener("click", function (e) {
  e.preventDefault();
  applyFilter();
});

document.getElementById("resetFilter").addEventListener("click", function (e) {
  e.preventDefault();
  resetFilter();
});

document.getElementById("zoomAll").addEventListener("click", function (e) {
  e.preventDefault();
  zoomToAll();
});

document.getElementById("locateMe").addEventListener("click", function (e) {
  e.preventDefault();
  locateUser();
});

// Apply filter with Enter in search
document.getElementById("searchName").addEventListener("keydown", function (e) {
  if (e.key === "Enter") {
    e.preventDefault();
    applyFilter();
  }
});
