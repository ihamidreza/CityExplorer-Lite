<?php
require_once 'db_config.php';

$errors = [];
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle delete
if ($action === 'delete' && $id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM poi WHERE id = :id");
        $stmt->execute(['id' => $id]);
        header('Location: admin.php?msg=deleted');
        exit;
    } catch (PDOException $e) {
        $errors[] = "Delete failed.";
    }
}

// Handle create/update submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $latitude = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $errors[] = "Name is required.";
    }
    if ($category === '') {
        $errors[] = "Category is required.";
    }
    if ($latitude === '' || !is_numeric($latitude)) {
        $errors[] = "Latitude must be a numeric value.";
    }
    if ($longitude === '' || !is_numeric($longitude)) {
        $errors[] = "Longitude must be a numeric value.";
    }

    if (empty($errors)) {
        try {
            if ($formId > 0) {
                // Update
                $sql = "UPDATE poi 
                        SET name = :name, category = :category, latitude = :lat, longitude = :lng, description = :description
                        WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'name' => $name,
                    'category' => $category,
                    'lat' => $latitude,
                    'lng' => $longitude,
                    'description' => $description,
                    'id' => $formId
                ]);
                header('Location: admin.php?msg=updated');
                exit;
            } else {
                // Insert
                $sql = "INSERT INTO poi (name, category, latitude, longitude, description)
                        VALUES (:name, :category, :lat, :lng, :description)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'name' => $name,
                    'category' => $category,
                    'lat' => $latitude,
                    'lng' => $longitude,
                    'description' => $description
                ]);
                header('Location: admin.php?msg=created');
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = "Database save failed.";
        }
    }
}

// Load record for editing
$editPoi = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM poi WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $editPoi = $stmt->fetch();
}

// Load all POIs for list
$stmt = $pdo->query("SELECT * FROM poi ORDER BY id DESC");
$allPois = $stmt->fetchAll();

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CityExplorer Admin</title>
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Shared custom CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-slate-900 text-slate-100">

<header class="w-full bg-slate-800 p-4 flex items-center justify-between">
    <h1 class="text-xl font-semibold">CityExplorer Admin Panel</h1>
    <div class="space-x-3 flex items-center">
        <a href="index.php" class="text-sm bg-sky-600 px-3 py-1 rounded hover:bg-sky-500">
            Back to Map
        </a>
    </div>
</header>

<main class="max-w-6xl mx-auto p-4 space-y-6">

    <!-- Messages -->
    <?php if (!empty($msg)): ?>
        <div class="p-3 rounded bg-emerald-700 text-sm">
            <?php
            if ($msg === 'created') echo "POI created successfully.";
            elseif ($msg === 'updated') echo "POI updated successfully.";
            elseif ($msg === 'deleted') echo "POI deleted successfully.";
            ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="p-3 rounded bg-red-700 text-sm space-y-1">
            <?php foreach ($errors as $e): ?>
                <div><?php echo htmlspecialchars($e); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Create / Edit form -->
    <section class="bg-slate-800 rounded-lg p-4 space-y-3">
        <h2 class="text-lg font-semibold">
            <?php echo $editPoi ? "Edit POI #".$editPoi['id'] : "Create new POI"; ?>
        </h2>

        <form method="post" class="space-y-3">
            <input type="hidden" name="id" value="<?php echo $editPoi['id'] ?? ''; ?>">

            <div>
                <label class="block text-sm mb-1" for="name">Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php echo htmlspecialchars($editPoi['name'] ?? ''); ?>"
                    class="w-full px-3 py-2 rounded bg-slate-900 border border-slate-600 text-sm"
                    required
                />
            </div>

            <div>
                <label class="block text-sm mb-1" for="category">Category</label>
                <select
                    id="category"
                    name="category"
                    class="w-full px-3 py-2 rounded bg-slate-900 border border-slate-600 text-sm"
                    required
                >
                    <?php
                    $categories = ['Attraction', 'Restaurant', 'School', 'Hospital'];
                    $currentCategory = $editPoi['category'] ?? '';
                    ?>
                    <option value="">Select category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php echo $currentCategory === $cat ? 'selected' : ''; ?>>
                            <?php echo $cat; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm mb-1" for="latitude">Latitude</label>
                    <input
                        type="text"
                        id="latitude"
                        name="latitude"
                        value="<?php echo htmlspecialchars($editPoi['latitude'] ?? ''); ?>"
                        class="w-full px-3 py-2 rounded bg-slate-900 border border-slate-600 text-sm"
                        required
                    />
                </div>
                <div>
                    <label class="block text-sm mb-1" for="longitude">Longitude</label>
                    <input
                        type="text"
                        id="longitude"
                        name="longitude"
                        value="<?php echo htmlspecialchars($editPoi['longitude'] ?? ''); ?>"
                        class="w-full px-3 py-2 rounded bg-slate-900 border border-slate-600 text-sm"
                        required
                    />
                </div>
            </div>

            <div>
                <label class="block text-sm mb-1" for="description">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    class="w-full px-3 py-2 rounded bg-slate-900 border border-slate-600 text-sm"
                ><?php echo htmlspecialchars($editPoi['description'] ?? ''); ?></textarea>
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    class="px-4 py-2 rounded bg-sky-600 hover:bg-sky-500 text-sm font-medium"
                >
                    <?php echo $editPoi ? "Save changes" : "Create POI"; ?>
                </button>

                <?php if ($editPoi): ?>
                    <a
                        href="admin.php"
                        class="text-xs text-slate-300 hover:underline"
                    >
                        Cancel edit
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <!-- List of POIs -->
    <section class="bg-slate-800 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold">All POIs</h2>
            <span class="text-xs text-slate-400">
                <?php echo count($allPois); ?> record(s)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-700">
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">Name</th>
                        <th class="px-3 py-2 text-left">Category</th>
                        <th class="px-3 py-2 text-left">Latitude</th>
                        <th class="px-3 py-2 text-left">Longitude</th>
                        <th class="px-3 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($allPois)): ?>
                    <tr>
                        <td colspan="6" class="px-3 py-3 text-center text-slate-400">
                            No records yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($allPois as $poi): ?>
                        <tr class="border-b border-slate-700 hover:bg-slate-700">
                            <td class="px-3 py-2"><?php echo $poi['id']; ?></td>
                            <td class="px-3 py-2"><?php echo htmlspecialchars($poi['name']); ?></td>
                            <td class="px-3 py-2"><?php echo htmlspecialchars($poi['category']); ?></td>
                            <td class="px-3 py-2"><?php echo $poi['latitude']; ?></td>
                            <td class="px-3 py-2"><?php echo $poi['longitude']; ?></td>
                            <td class="px-3 py-2 space-x-2">
                                <a
                                    href="admin.php?action=edit&id=<?php echo $poi['id']; ?>"
                                    class="text-xs text-sky-300 hover:underline"
                                >
                                    Edit
                                </a>
                                <a
                                    href="admin.php?action=delete&id=<?php echo $poi['id']; ?>"
                                    class="text-xs text-red-400 hover:underline"
                                    onclick="return confirm('Delete this POI?');"
                                >
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

</body>
</html>
