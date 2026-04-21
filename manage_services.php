<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";
$edit_mode = false;
$edit_service = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_id'])) {
        $delete_id = (int)$_POST['delete_id'];

        $delete_sql = "DELETE FROM services WHERE id = ?";
        $stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Service deleted successfully.";
        } else {
            $message = "This service could not be deleted. It may already be used in appointments.";
        }
    }

    if (isset($_POST['save_service'])) {
        $service_name = trim($_POST['service_name']);
        $description = trim($_POST['description']);
        $price = trim($_POST['price']);
        $duration = trim($_POST['duration']);

        if (empty($service_name) || empty($description) || empty($price) || empty($duration)) {
            $message = "Please fill in all service fields.";
        } elseif (!is_numeric($price) || $price < 0) {
            $message = "Please enter a valid price.";
        } else {
            if (!empty($_POST['service_id'])) {
                $service_id = (int)$_POST['service_id'];

                $update_sql = "UPDATE services SET service_name = ?, description = ?, price = ?, duration = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($stmt, "ssdsi", $service_name, $description, $price, $duration, $service_id);

                if (mysqli_stmt_execute($stmt)) {
                    $message = "Service updated successfully.";
                } else {
                    $message = "Something went wrong while updating the service.";
                }
            } else {
                $insert_sql = "INSERT INTO services (service_name, description, price, duration) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $insert_sql);
                mysqli_stmt_bind_param($stmt, "ssds", $service_name, $description, $price, $duration);

                if (mysqli_stmt_execute($stmt)) {
                    $message = "Service added successfully.";
                } else {
                    $message = "Something went wrong while adding the service.";
                }
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];

    $edit_sql = "SELECT * FROM services WHERE id = ?";
    $stmt = mysqli_prepare($conn, $edit_sql);
    mysqli_stmt_bind_param($stmt, "i", $edit_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $edit_service = mysqli_fetch_assoc($result);
        $edit_mode = true;
    }
}

$services_sql = "SELECT * FROM services ORDER BY id ASC";
$services_result = mysqli_query($conn, $services_sql);

include 'includes/header.php';
?>

<main class="manage-page">
    <section class="manage-section">
        <div class="manage-header">
            <h1>Manage Services</h1>
            <a href="admin.php" class="back-admin-btn">Back to Dashboard</a>
        </div>

        <div class="manage-line"></div>

        <?php if (!empty($message)) : ?>
            <p class="form-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST" class="service-form">
            <input type="hidden" name="service_id" value="<?php echo $edit_mode ? $edit_service['id'] : ''; ?>">

            <div class="service-form-grid">
                <input 
                    type="text" 
                    name="service_name" 
                    placeholder="Service name" 
                    value="<?php echo $edit_mode ? htmlspecialchars($edit_service['service_name']) : ''; ?>" 
                    required
                >

                <input 
                    type="text" 
                    name="duration" 
                    placeholder="Duration (e.g. 45 minutes)" 
                    value="<?php echo $edit_mode ? htmlspecialchars($edit_service['duration']) : ''; ?>" 
                    required
                >

                <input 
                    type="number" 
                    step="0.01" 
                    name="price" 
                    placeholder="Price" 
                    value="<?php echo $edit_mode ? htmlspecialchars($edit_service['price']) : ''; ?>" 
                    required
                >
            </div>

            <textarea 
                name="description" 
                placeholder="Service description" 
                required
            ><?php echo $edit_mode ? htmlspecialchars($edit_service['description']) : ''; ?></textarea>

            <div class="service-form-actions">
                <button type="submit" name="save_service" class="small-action-btn">
                    <?php echo $edit_mode ? 'Update Service' : 'Add Service'; ?>
                </button>

                <?php if ($edit_mode) : ?>
                    <a href="manage_services.php" class="cancel-edit-btn">Cancel Edit</a>
                <?php endif; ?>
            </div>
        </form>

        <div class="manage-table-wrapper">
            <table class="manage-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($services_result && mysqli_num_rows($services_result) > 0) : ?>
                        <?php while ($service = mysqli_fetch_assoc($services_result)) : ?>
                            <tr>
                                <td><?php echo $service['id']; ?></td>
                                <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                                <td class="review-text-cell"><?php echo htmlspecialchars($service['description']); ?></td>
                                <td>£<?php echo number_format($service['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($service['duration']); ?></td>
                                <td>
                                    <div class="review-actions">
                                        <a href="manage_services.php?edit=<?php echo $service['id']; ?>" class="small-link-btn">Edit</a>

                                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                            <input type="hidden" name="delete_id" value="<?php echo $service['id']; ?>">
                                            <button type="submit" class="delete-btn">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6">No services found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>