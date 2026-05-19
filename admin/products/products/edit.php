<?php
/**
 * Edit Product
 */
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/db.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$pageTitle = 'Edit Product';
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    setFlash('error', 'Product not found');
    redirect(BASE_URL . '/admin/products/products/');
}

$errors = [];
$categories = $pdo->query('SELECT * FROM product_categories WHERE status = "active" ORDER BY sort_order')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $slug = generateSlug($_POST['slug'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0) ?: null;
    $description = sanitize($_POST['description'] ?? '');
    $shortDescription = sanitize($_POST['short_description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $isFree = sanitize($_POST['is_free'] ?? 'no');
    $filePath = sanitize($_POST['file_path'] ?? '');
    $previewImage = sanitize($_POST['preview_image'] ?? '');
    $featured = sanitize($_POST['featured'] ?? 'no');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $status = sanitize($_POST['status'] ?? 'active');
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($slug)) {
        $slug = generateSlug($name);
    }
    
    $stmt = $pdo->prepare('SELECT id FROM products WHERE slug = ? AND id != ?');
    $stmt->execute([$slug, $id]);
    if ($stmt->fetch()) {
        $errors[] = 'Slug already exists';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE products SET name = ?, slug = ?, category_id = ?, description = ?, short_description = ?, price = ?, is_free = ?, file_path = ?, preview_image = ?, featured = ?, sort_order = ?, status = ? WHERE id = ?');
        $stmt->execute([$name, $slug, $categoryId, $description, $shortDescription, $price, $isFree, $filePath, $previewImage, $featured, $sortOrder, $status, $id]);
        setFlash('success', 'Product updated successfully');
        redirect(BASE_URL . '/admin/products/products/');
    }
}

include INCLUDES_PATH . '/admin-header.php';
include INCLUDES_PATH . '/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="page-header">
        <div>
            <h1>Edit Product</h1>
            <p class="page-subtitle">Update product details</p>
        </div>
        <a href="index.php" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Back
        </a>
    </div>

    <?php if ($flash = getFlash()): ?>
        <div class="alert alert-<?php echo $flash['type']; ?>">
            <i class="fa-solid fa-<?php echo $flash['type'] === 'success' ? 'check' : 'xmark'; ?>"></i>
            <p><?php echo $flash['message']; ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-xmark"></i>
            <div>
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" class="admin-form">
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fa-solid fa-box"></i>
                    <h3>Basic Information</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label for="name">Product Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <div class="input-with-prefix">
                            <span class="input-prefix">/products/</span>
                            <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($product['slug']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group form-group-full">
                        <label for="short_description">Short Description</label>
                        <input type="text" id="short_description" name="short_description" value="<?php echo htmlspecialchars($product['short_description'] ?? ''); ?>" placeholder="Brief one-line description">
                    </div>
                    
                    <div class="form-group form-group-full">
                        <label for="description">Full Description</label>
                        <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-header">
                    <i class="fa-solid fa-tag"></i>
                    <h3>Pricing</h3>
                </div>
                <div class="form-grid form-grid-3">
                    <div class="form-group">
                        <label for="is_free">Pricing Type</label>
                        <select id="is_free" name="is_free" onchange="togglePrice(this.value)">
                            <option value="no" <?php echo $product['is_free'] === 'no' ? 'selected' : ''; ?>>Paid</option>
                            <option value="yes" <?php echo $product['is_free'] === 'yes' ? 'selected' : ''; ?>>Free</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="price-group" style="<?php echo $product['is_free'] === 'yes' ? 'opacity:0.5' : ''; ?>">
                        <label for="price">Price (USD)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo number_format($product['price'], 2); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="featured">Featured</label>
                        <select id="featured" name="featured">
                            <option value="no" <?php echo $product['featured'] === 'no' ? 'selected' : ''; ?>>No</option>
                            <option value="yes" <?php echo $product['featured'] === 'yes' ? 'selected' : ''; ?>>Yes</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-header">
                    <i class="fa-solid fa-image"></i>
                    <h3>Media & Files</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label for="preview_image">Preview Image URL</label>
                        <input type="url" id="preview_image" name="preview_image" value="<?php echo htmlspecialchars($product['preview_image'] ?? ''); ?>" placeholder="https://example.com/image.jpg">
                    </div>
                    
                    <div class="form-group form-group-full">
                        <label for="file_path">Download File URL</label>
                        <input type="url" id="file_path" name="file_path" value="<?php echo htmlspecialchars($product['file_path'] ?? ''); ?>" placeholder="https://example.com/download/file.zip">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-header">
                    <i class="fa-solid fa-chart-simple"></i>
                    <h3>Statistics</h3>
                </div>
                <div class="form-grid form-grid-3">
                    <div class="form-group">
                        <label>Downloads</label>
                        <div class="stat-display">
                            <span class="stat-number"><?php echo $product['download_count']; ?></span>
                            <span class="stat-label">total</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-header">
                    <i class="fa-solid fa-sliders"></i>
                    <h3>Settings</h3>
                </div>
                <div class="form-grid form-grid-3">
                    <div class="form-group">
                        <label for="sort_order">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" value="<?php echo $product['sort_order']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i>
                    Update Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePrice(value) {
    const priceGroup = document.getElementById('price-group');
    if (value === 'yes') {
        priceGroup.style.opacity = '0.5';
        document.getElementById('price').value = '0.00';
    } else {
        priceGroup.style.opacity = '1';
    }
}
</script>

<?php include INCLUDES_PATH . '/admin-footer.php'; ?>