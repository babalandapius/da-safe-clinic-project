<?php
if (!defined('ACCESS_DASHBOARD')) { header('Location: ../dashboard.php'); exit; }
include('config/db.php');
// 1. Fetch Inventory Data
$query = "SELECT * FROM inventory ORDER BY status DESC, item_name ASC";
$result = mysqli_query($conn, $query);

// 2. Logic for Quick Stats
$lowStockQuery = "SELECT COUNT(*) as total FROM inventory WHERE status = 'Low Stock'";
$lowStockCount = mysqli_fetch_assoc(mysqli_query($conn, $lowStockQuery))['total'];

$expiringQuery = "SELECT COUNT(*) as total FROM inventory WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH)";
$expiringCount = mysqli_fetch_assoc(mysqli_query($conn, $expiringQuery))['total'];
?>

<div class="workspace-container">
    <div class="workspace-header">
        <div>
            <h2><i class="fas fa-boxes"></i> Lab Inventory</h2>
            <p>Real-time tracking of reagents and consumables for DA SAFE Lab.</p>
        </div>
    </div>

    <div class="card-container" style="padding: 0 0 30px 0;">
        <div class="card">
            <div class="card-icon" style="color: #e74c3c; background: #fff5f5;"><i class="fas fa-exclamation-triangle"></i></div>
            <div>
                <div class="stat-number"><?php echo $lowStockCount; ?></div>
                <div class="stat-label">Low Stock Items</div>
            </div>
        </div>
        <div class="card">
            <div class="card-icon" style="color: #f39c12; background: #fffaf0;"><i class="fas fa-calendar-times"></i></div>
            <div>
                <div class="stat-number"><?php echo $expiringCount; ?></div>
                <div class="stat-label">Expiring Soon</div>
            </div>
        </div>
        <div class="card">
            <div class="card-icon" style="color: #2d9cdb; background: #e7f4fd;"><i class="fas fa-robot"></i></div>
            <div>
                <div class="stat-number">AI Assist</div>
                <div class="stat-label">Forecasts stock pressure</div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>AI Forecast</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): 
                    $isExpiring = (strtotime($row['expiry_date']) < strtotime('+3 months'));
                    $statusClass = strtolower(str_replace(' ', '-', $row['status']));
                    $forecast = LabAI::predictStockDepletion($row['quantity'], $row['status'] === 'Low Stock' ? 6 : 2);
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['item_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo $row['quantity'] . " " . $row['unit']; ?></td>
                    <td>
                        <span style="<?php echo $isExpiring ? 'color: #e74c3c; font-weight: bold;' : ''; ?>">
                            <?php echo date('M d, Y', strtotime($row['expiry_date'])); ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-pill <?php echo $statusClass; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($forecast); ?></td>
                    <td>
                        <a href="?view=edit_inventory&id=<?php echo $row['id']; ?>" class="action-link">
                            <i class="fas fa-edit"></i> Update
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>