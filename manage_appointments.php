<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id'], $_POST['new_status'])) {
    $appointment_id = (int)$_POST['appointment_id'];
    $new_status = $_POST['new_status'];

    $allowed_statuses = ['accepted', 'declined', 'completed'];

    if (in_array($new_status, $allowed_statuses)) {
        $update_sql = "UPDATE appointments SET status = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "si", $new_status, $appointment_id);

        if (mysqli_stmt_execute($update_stmt)) {
            $message = "Appointment status updated successfully.";
        } else {
            $message = "Something went wrong while updating the appointment.";
        }
    } else {
        $message = "Invalid status selected.";
    }
}

$appointments_sql = "SELECT 
                        appointments.id,
                        appointments.appointment_date,
                        appointments.appointment_time,
                        appointments.status,
                        users.first_name,
                        users.surname,
                        services.service_name
                     FROM appointments
                     INNER JOIN users ON appointments.user_id = users.id
                     INNER JOIN services ON appointments.service_id = services.id
                     ORDER BY appointments.appointment_date ASC, appointments.appointment_time ASC";

$appointments_result = mysqli_query($conn, $appointments_sql);

include 'includes/header.php';
?>

<main class="manage-page">
    <section class="manage-section">
        <div class="manage-header">
            <h1>Manage Appointments</h1>
            <a href="admin.php" class="back-admin-btn">Back to Dashboard</a>
        </div>

        <div class="manage-line"></div>

        <?php if (!empty($message)) : ?>
            <p class="form-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <div class="manage-table-wrapper">
            <table class="manage-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($appointments_result && mysqli_num_rows($appointments_result) > 0) : ?>
                        <?php while ($appointment = mysqli_fetch_assoc($appointments_result)) : ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['surname']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($appointment['service_name']); ?>
                                </td>
                                <td>
                                    <?php echo date("d.m.Y", strtotime($appointment['appointment_date'])); ?>
                                </td>
                                <td>
                                    <?php echo date("H:i", strtotime($appointment['appointment_time'])); ?>
                                </td>
                                <td class="status-<?php echo strtolower($appointment['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($appointment['status'])); ?>
                                </td>
                                <td>
                                    <form method="POST" class="status-form">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">

                                        <select name="new_status" required>
                                            <option value="">Change status</option>
                                            <option value="accepted">Accepted</option>
                                            <option value="declined">Declined</option>
                                            <option value="completed">Completed</option>
                                        </select>

                                        <button type="submit" class="small-action-btn">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6">No appointments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>