<?php

// Get the item ID from the URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the current data for this item
$query = "SELECT * FROM inventory WHERE id = $id";
$result = mysqli_query($conn, $query);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    echo "<div class='alert alert-danger'>Item not found.</div>";
    exit;
}
?>

<div class="centered-form-wrapper">
    <div class="card request-card">
        <h2><i class="fas fa-edit"></i> Update Inventory</h2>
        <p class="form-subtitle">Modify stock levels or details for: <strong><?php echo htmlspecialchars($item['item_name']); ?></strong></p>

        <form action="dashboard.php?view=lab_inventory" method="POST" class="modern-form">
            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
            <input type="hidden" name="action" value="update_inventory">

            <div class="form-group">
                <label><i class="fas fa-box"></i> Item Name</label>
                <input type="text" name="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-layer-group"></i> Category</label>
                    <select name="category">
                        <option value="Reagents" <?php if($item['category'] == 'Reagents') echo 'selected'; ?>>Reagents</option>
                        <option value="Consumables" <?php if($item['category'] == 'Consumables') echo 'selected'; ?>>Consumables</option>
                        <option value="Chemicals" <?php if($item['category'] == 'Chemicals') echo 'selected'; ?>>Chemicals</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-sort-numeric-up"></i> Quantity</label>
                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-calendar-alt"></i> Expiry Date</label>
                <input type="date" name="expiry_date" value="<?php echo $item['expiry_date']; ?>">
            </div>

            <div class="form-actions">
                <a href="?view=lab_inventory" class="btn-form secondary">Cancel</a>
                <button type="submit" class="btn-form primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>