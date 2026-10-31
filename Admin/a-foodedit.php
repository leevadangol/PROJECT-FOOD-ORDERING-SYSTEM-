<?php
/*
    FOOD EDIT PAGE (Admin/a-foodedit.php)
    A dedicated full-page editor for a single food item.
    Handles: Food Name, Category, Price, Description, Image.

    Accessed from: Admin/a-foodmanage.php?edit_id=X
    On save: redirects back to a-foodmanage.php with success message.
*/

// All logic runs BEFORE a-header.php to allow header() redirects
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once "../db.php";

$categories = ['Burger', 'Pizza', 'Fried Chicken', 'Pasta', 'Momo', 'Cold Drink'];
$error      = '';
$msg        = '';

// ---- HANDLE EDIT FORM SUBMIT ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $f_id       = intval($_POST['f_id']);
    $f_name     = trim($_POST['f_name']);
    $category   = trim($_POST['category']);
    $price      = floatval($_POST['price']);
    $desc       = trim($_POST['description'] ?? '');
    $image_path = trim($_POST['existing_image'] ?? '');

    // Validate required fields
    if (empty($f_name) || empty($category) || $price <= 0) {
        $error = "Please fill in all required fields.";
    } else {
        // Handle new image upload (optional — keeps old image if nothing uploaded)
        if (!empty($_FILES['image']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                $error = "Only JPG, PNG, GIF, WEBP images are allowed.";
            } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                $error = "Image must be under 2MB.";
            } else {
                $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_name = 'food_' . time() . '_' . rand(100, 999) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../IMAGES/' . $new_name);
                $image_path = 'IMAGES/' . $new_name;
            }
        }

        if (!$error) {
            $stmt = mysqli_prepare($conn,
                "UPDATE foods SET f_name=?, category=?, price=?, image=?, description=?
                 WHERE f_id=?");
            mysqli_stmt_bind_param($stmt, "ssdssi",
                $f_name, $category, $price, $image_path, $desc, $f_id);
            mysqli_stmt_execute($stmt);

            // Redirect back to food list with success message
            header("Location: a-foodmanage.php?msg=" . urlencode("Food item updated successfully."));
            exit();
        }
    }
}

// ---- LOAD FOOD DATA ----
if (!isset($_GET['f_id']) && !isset($_POST['f_id'])) {
    header("Location: a-foodmanage.php");
    exit();
}

$food_id = intval($_GET['f_id'] ?? $_POST['f_id']);
$stmt    = mysqli_prepare($conn, "SELECT * FROM foods WHERE f_id = ?");
mysqli_stmt_bind_param($stmt, "i", $food_id);
mysqli_stmt_execute($stmt);
$food = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$food) {
    header("Location: a-foodmanage.php?msg=" . urlencode("Food item not found."));
    exit();
}

// NOW include header (outputs HTML)
include "a-header.php";
?>

<div class="admin-page">
<div class="admin-card" style="max-width:750px; margin:0 auto;">

    <!-- Back link -->
    <a href="a-foodmanage.php"
       style="display:inline-flex; align-items:center; gap:6px; color:#f25d07;
              text-decoration:none; font-size:14px; font-weight:bold; margin-bottom:18px;">
        &#8592; Back to All Food Items
    </a>

    <h2>&#9998; Edit Food Item</h2>

    <!-- Error message -->
    <?php if ($error): ?>
        <div class="alert-error">&#10006; <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" action="a-foodedit.php">
        <input type="hidden" name="action"         value="edit">
        <input type="hidden" name="f_id"           value="<?php echo $food['f_id']; ?>">
        <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($food['image']); ?>">

        <!-- Current food image preview (large) -->
        <div style="text-align:center; margin-bottom:25px;">
            <?php if (!empty($food['image'])): ?>
                <img src="../<?php echo htmlspecialchars($food['image']); ?>"
                     id="imagePreview"
                     style="width:160px; height:160px; object-fit:cover; border-radius:12px;
                            box-shadow:0 3px 12px rgba(0,0,0,0.15);"
                     alt="<?php echo htmlspecialchars($food['f_name']); ?>">
            <?php else: ?>
                <div id="imagePreview"
                     style="width:160px; height:160px; background:#f5f5f5; border-radius:12px;
                            display:inline-flex; align-items:center; justify-content:center;
                            font-size:40px; color:#ccc; box-shadow:0 3px 12px rgba(0,0,0,0.08);">
                    &#127829;
                </div>
            <?php endif; ?>
            <p style="font-size:12px; color:#aaa; margin-top:8px;">Current image</p>
        </div>

        <!-- Food Name -->
        <div class="form-group">
            <label>Food Name *</label>
            <input type="text" name="f_name" required
                   placeholder="e.g. CHEESE BURGER"
                   value="<?php echo htmlspecialchars($food['f_name']); ?>">
        </div>

        <!-- Category + Price side by side -->
        <div style="display:flex; gap:15px; flex-wrap:wrap;">
            <div class="form-group" style="flex:1; min-width:160px;">
                <label>Category *</label>
                <select name="category" required>
                    <option value="" disabled>-- Select Category --</option>
                    <?php foreach ($categories as $cat):
                        $sel = ($cat === ($food['category'] ?? '')) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $cat; ?>" <?php echo $sel; ?>>
                            <?php echo $cat; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="flex:1; min-width:130px;">
                <label>Price (Rs.) *</label>
                <input type="number" name="price" step="0.01" min="0" required
                       placeholder="e.g. 450.00"
                       value="<?php echo $food['price']; ?>">
            </div>
        </div>

        <!-- Description -->
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"
                      placeholder="Short description of the food item..."
                      style="width:100%; padding:9px 12px; border:1px solid #ccc;
                             border-radius:5px; font-size:14px; box-sizing:border-box;
                             resize:vertical;"><?php echo htmlspecialchars($food['description'] ?? ''); ?></textarea>
        </div>

        <!-- New Image Upload -->
        <div class="form-group">
            <label>Update Image <span style="color:#aaa; font-weight:normal;">(leave blank to keep current)</span></label>
            <input type="file" name="image" accept="image/*" id="imageInput"
                   style="padding:5px 0;">
            <small style="color:#888; display:block; margin-top:4px;">
                Max 2MB. JPG, PNG, GIF, WEBP accepted.
            </small>
        </div>

        <!-- Action buttons -->
        <div style="display:flex; gap:12px; margin-top:10px;">
            <button type="submit" class="btn-primary"
                    style="flex:1; padding:13px; font-size:15px; font-weight:bold;">
                &#10004; Save Changes
            </button>
            <a href="a-foodmanage.php"
               style="flex:0.4; padding:13px; text-align:center; background:#ccc; color:#333;
                      border-radius:5px; text-decoration:none; font-size:15px; display:block;">
                Cancel
            </a>
        </div>
    </form>

</div>
</div>

<script>
    // Live image preview — when admin picks a new image, show it immediately
    // instead of making them guess what it'll look like after saving
    document.getElementById('imageInput').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            const preview = document.getElementById('imagePreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Replace the placeholder div with an actual img tag
                const img = document.createElement('img');
                img.id  = 'imagePreview';
                img.src = e.target.result;
                img.style.cssText = 'width:160px;height:160px;object-fit:cover;border-radius:12px;box-shadow:0 3px 12px rgba(0,0,0,0.15);';
                preview.parentNode.replaceChild(img, preview);
            }
        };
        reader.readAsDataURL(file);
    });
</script>

</body>
</html>
