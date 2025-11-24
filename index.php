<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CityExplorer Lite</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-slate-900">

    <!-- Header -->
    <header class="w-full bg-slate-800 text-white p-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">CityExplorer Lite</h1>

        <div class="flex items-center gap-4">
            <span class="text-sm text-slate-300">Educational Web GIS</span>

            <a
                href="admin.php"
                class="text-sm bg-sky-600 px-3 py-1 rounded hover:bg-sky-500"
            >
                Admin Panel
            </a>
        </div>
    </header>

    <!-- Main layout: sidebar + map -->
    <main class="h-[calc(100vh-64px)] flex flex-col md:flex-row">
        <!-- Sidebar -->
        <aside class="bg-slate-800 text-white md:w-80 w-full p-4 space-y-4">
            <h2 class="text-lg font-semibold mb-2">Filter & Search</h2>

            <!-- Search by name -->
            <div>
                <label class="block text-sm text-slate-300 mb-1" for="searchName">Search by name</label>
                <input
                    type="text"
                    id="searchName"
                    placeholder="e.g. Hochschule"
                    class="w-full px-3 py-2 rounded bg-slate-900 text-sm border border-slate-700 focus:outline-none focus:ring focus:ring-sky-500"
                />
            </div>

            <!-- Category filter -->
            <div>
                <label class="block text-sm text-slate-300 mb-1" for="categoryFilter">Category</label>
                <select
                    id="categoryFilter"
                    class="w-full px-3 py-2 rounded bg-slate-900 text-sm border border-slate-700 focus:outline-none focus:ring focus:ring-sky-500"
                >
                    <option value="">All categories</option>
                    <option value="Attraction">Attraction</option>
                    <option value="Restaurant">Restaurant</option>
                    <option value="School">School</option>
                    <option value="Hospital">Hospital</option>
                </select>
            </div>

            <!-- Filter & Reset buttons -->
            <div class="flex gap-2 mt-2">
                <button
                    id="applyFilter"
                    class="flex-1 py-2 rounded bg-sky-600 hover:bg-sky-500 text-sm font-medium"
                >
                    Apply filter
                </button>
                <button
                    id="resetFilter"
                    class="px-3 py-2 rounded bg-slate-600 hover:bg-slate-500 text-xs font-medium"
                >
                    Reset
                </button>
            </div>

            <!-- Zoom to all button -->
            <button
                id="zoomAll"
                class="w-full mt-2 py-2 rounded bg-green-600 hover:bg-green-500 text-sm font-medium"
            >
                Zoom to All
            </button>

            <!-- Locate me button -->
            <button
                id="locateMe"
                class="w-full mt-2 py-2 rounded bg-emerald-600 hover:bg-emerald-500 text-sm font-medium"
            >
                Locate Me
            </button>

            <!-- POI list -->
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-slate-300">POIs</h3>
                    <span id="poiCount" class="text-xs text-slate-400">0 items</span>
                </div>
                <div id="poiList" class="space-y-2"></div>
            </div>
        </aside>

        <!-- Map section -->
        <section class="flex-1">
            <div id="map" class="w-full h-full"></div>
        </section>
    </main>

    <!-- Leaflet JS -->
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin="">
    </script>

    <!-- Custom JS -->
    <script src="assets/js/app.js"></script>
</body>
</html>
