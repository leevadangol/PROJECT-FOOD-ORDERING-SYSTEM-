<?php
/*
    FOOD MANAGEMENT (Admin/a-foodmanage.php)
    Admin can: Add, Edit, Delete food items with category and image upload.
*/
include "a-header.php";
require_once "../db.php";

$msg   = '';
$error = '';

// Fixed category list (matches the existing category pages)
$categories = ['Burger', 'Pizza', 'Fried Chicken', 'Pasta', 'Momo', 'Cold Drink'];

// ---- DELETE FOOD ----
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $check  = mysqli_prepare($conn, "SELECT COUNT(*) AS cnt FROM order_items WHERE f_id = ?");
    mysqli_stmt_bind_param($check, "i", $del_id);
    mysqli_stmt_execute($check);
    $cnt = mysqli_fetch_assoc(mysqli_stmt_get_result($check))['cnt'];

    if ($cnt > 0) {
        $error = "Cannot delete this food — it has existing orders linked to it.";
    } else {
        $stmt = mysqli_prepare($conn, "DELETE FROM foods WHERE f_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $del_id);
        mysqli_stmt_execute($stmt);
        $msg = "Food item deleted successfully.";
    }
}

// ---- ADD FOOD ----
// Show success message from a-foodedit.php redirect
if (isset($_GET['msg'])) { $msg = urldecode($_GET['msg']); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $f_name   = trim($_POST['f_name']);
    $price    = floatval($_POST['price']);
    $desc     = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $image_path = '';

    if (!empty($_FILES['image']['name'])) {
        $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        if (!in_array($_FILES['image']['type'], $allowed)) {
            $error = "Only JPG, PNG, GIF, WEBP images are allowed.";
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $error = "Image must be under 2MB.";
        } else {
            $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_name = 'food_' . time() . '_' . rand(100,999) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../IMAGES/' . $new_name);
            $image_path = 'IMAGES/' . $new_name;
        }
    }

    if (!$error) {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO foods (f_name, category, price, image, description) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssdss", $f_name, $category, $price, $image_path, $desc);
        mysqli_stmt_execute($stmt);
        $msg = "Food item added successfully.";
    }
}

// ---- EDIT FOOD ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $f_id       = intval($_POST['f_id']);
    $f_name     = trim($_POST['f_name']);
    $price      = floatval($_POST['price']);
    $desc       = trim($_POST['description'] ?? '');
    $category   = trim($_POST['category'] ?? '');
    $image_path = trim($_POST['existing_image'] ?? '');

    if (!empty($_FILES['image']['name'])) {
        $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        if (in_array($_FILES['image']['type'], $allowed) &&
            $_FILES['image']['size'] <= 2*1024*1024) {
            $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_name = 'food_' . time() . '_' . rand(100,999) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../IMAGES/' . $new_name);
            $image_path = 'IMAGES/' . $new_name;
        }
    }

    $stmt = mysqli_prepare($conn,
        "UPDATE foods SET f_name=?, category=?, price=?, image=?, description=? WHERE f_id=?");
    mysqli_stmt_bind_param($stmt, "ssdssi", $f_name, $category, $price, $image_path, $desc, $f_id);
    mysqli_stmt_execute($stmt);
    $msg = "Food item updated successfully.";
}

// Fetch food being edited
$edit_food = null;
if (isset($_GET['edit_id'])) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM foods WHERE f_id = ?");
    mysqli_stmt_bind_param($stmt, "i", intval($_GET['edit_id']));
    mysqli_stmt_execute($stmt);
    $edit_food = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// Fetch all foods for the table
$foods_result = mysqli_query($conn,
    "SELECT f.*, COALESCE(SUM(oi.quantity),0) AS times_ordered
     FROM foods f
     LEFT JOIN order_items oi ON f.f_id = oi.f_id
     GROUP BY f.f_id
     ORDER BY f.category ASC, f.f_id ASC");
?>

<div class="admin-page">

<?php if ($msg):   ?><div class="alert-success">&#10004; <?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert-error">&#10006; <?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<!-- ===== ADD / EDIT FORM ===== -->
<div class="admin-card">
    <h2><?php echo $edit_food ? '&#9998; Edit Food Item' : '&#43; Add New Food Item'; ?></h2>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?php echo $edit_food ? 'edit' : 'add'; ?>">
        <?php if ($edit_food): ?>
            <input type="hidden" name="f_id"           value="<?php echo $edit_food['f_id']; ?>">
            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($edit_food['image']); ?>">
        <?php endif; ?>

        <!-- Row 1: Food Name, Category, Price -->
        <div style="display:flex; gap:15px; flex-wrap:wrap;">

            <div class="form-group" style="flex:2; min-width:180px;">
                <label>Food Name *</label>
                <input type="text" name="f_name" required
                       placeholder="e.g. CHEESE BURGER"
                       value="<?php echo $edit_food ? htmlspecialchars($edit_food['f_name']) : ''; ?>">
            </div>

            <!-- CATEGORY DROPDOWN -->
            <div class="form-group" style="flex:1; min-width:160px;">
                <label>Category *</label>
                <select name="category" required>
                    <option value="" disabled <?php echo !$edit_food ? 'selected' : ''; ?>>-- Select Category --</option>
                    <?php foreach ($categories as $cat):
                        $selected = ($edit_food && ($edit_food['category'] ?? '') === $cat) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $cat; ?>" <?php echo $selected; ?>>
                            <?php echo $cat; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="flex:1; min-width:130px;">
                <label>Price (Rs.) *</label>
                <input type="number" name="price" step="0.01" min="0" required
                       placeholder="e.g. 450.00"
                       value="<?php echo $edit_food ? $edit_food['price'] : ''; ?>">
            </div>

        </div>

        <!-- Row 2: Description -->
        <div class="form-group">
            <label>Description</label>
            <input type="text" name="description"
                   placeholder="Short description of the food"
                   value="<?php echo $edit_food ? htmlspecialchars($edit_food['description'] ?? '') : ''; ?>">
        </div>

        <!-- Row 3: Image -->
        <div class="form-group">
            <label>Food Image <?php echo $edit_food ? '(leave blank to keep current image)' : ''; ?></label>
            <input type="file" name="image" accept="image/*">
            <?php if ($edit_food && !empty($edit_food['image'])): ?>
                <div style="margin-top:8px; display:flex; align-items:center; gap:10px;">
                    <img src="../<?php echo htmlspecialchars($edit_food['image']); ?>"
                         style="height:55px; border-radius:6px; object-fit:cover;">
                    <small style="color:#888;">Current: <?php echo htmlspecialchars($edit_food['image']); ?></small>
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-primary" style="padding:10px 28px; font-size:14px;">
            <?php echo $edit_food ? '&#10004; Update Food' : '&#43; Add Food'; ?>
        </button>
        <?php if ($edit_food): ?>
            <a href="a-foodmanage.php" class="btn-info" style="margin-left:10px; padding:10px 18px;">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<!-- ===== FOOD LIST TABLE ===== -->
<div class="admin-card">
    <h2>&#127829; All Food Items (<?php echo mysqli_num_rows($foods_result); ?>)</h2>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Category</th>
                    <th>Price (Rs.)</th>
                    <th>Times Ordered</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $current_cat = '';
            while ($food = mysqli_fetch_assoc($foods_result)):
                // Show a category separator row when the category changes
                if ($food['category'] !== $current_cat):
                    $current_cat = $food['category'];
            ?>
                <tr style="background:#fff3ec;">
                    <td colspan="8" style="font-weight:bold; color:#f25d07; padding:8px 13px; font-size:13px;">
                        &#128204; <?php echo htmlspecialchars($current_cat); ?>
                    </td>
                </tr>
            <?php endif; ?>
                <tr>
                    <td><?php echo $food['f_id']; ?></td>
                    <td>
                        <?php if (!empty($food['image'])): ?>
                            <img src="../<?php echo htmlspecialchars($food['image']); ?>"
                                 style="width:48px;height:48px;border-radius:8px;object-fit:cover;">
                        <?php else: ?>
                            <span style="color:#ccc;font-size:12px;">No image</span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($food['f_name']); ?></strong></td>
                    <td>
                        <span style="background:#fff3ec;color:#f25d07;padding:3px 9px;
                                     border-radius:12px;font-size:12px;font-weight:bold;">
                            <?php echo htmlspecialchars($food['category'] ?? '—'); ?>
                        </span>
                    </td>
                    <td><?php echo number_format($food['price'],2); ?></td>
                    <td><?php echo $food['times_ordered']; ?></td>
                    <td style="max-width:180px;font-size:12px;color:#666;">
                        <?php echo htmlspecialchars(substr($food['description'] ?? '—', 0, 60)); ?>
                        <?php if (strlen($food['description'] ?? '') > 60) echo '...'; ?>
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="a-foodedit.php?f_id=<?php echo $food['f_id']; ?>"
                           class="btn-info" style="font-size:12px;">Edit</a>
                        &nbsp;
                        <a href="a-foodmanage.php?delete_id=<?php echo $food['f_id']; ?>"
                           class="btn-danger" style="font-size:12px;"
                           onclick="return confirm('Delete <?php echo htmlspecialchars($food['f_name']); ?>?')">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</div><!-- end admin-page -->
</body>
</html>
