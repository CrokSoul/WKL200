<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$user_id = $_SESSION['user_id'];

$services_query = "SELECT id, service_name FROM services ORDER BY service_name ASC";
$services_result = mysqli_query($conn, $services_query);

$time_slots = [
    "09:00:00",
    "10:00:00",
    "11:00:00",
    "12:00:00",
    "13:00:00",
    "14:00:00",
    "15:00:00",
    "16:00:00",
    "17:00:00"
];

/* CANCEL BOOKING */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_booking_id'])) {
    $cancel_booking_id = (int)$_POST['cancel_booking_id'];

    $cancel_sql = "DELETE FROM appointments WHERE id = ? AND user_id = ? AND status IN ('pending', 'accepted')";
    $cancel_stmt = mysqli_prepare($conn, $cancel_sql);
    mysqli_stmt_bind_param($cancel_stmt, "ii", $cancel_booking_id, $user_id);

    if (mysqli_stmt_execute($cancel_stmt)) {
        if (mysqli_stmt_affected_rows($cancel_stmt) > 0) {
            $message = "Booking cancelled successfully.";
        } else {
            $message = "This booking cannot be cancelled.";
        }
    } else {
        $message = "Something went wrong while cancelling the booking.";
    }
}

/* CREATE BOOKING */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['service_id'])) {
    $service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';

    if ($service_id <= 0 || empty($appointment_date) || empty($appointment_time)) {
        $message = "Please fill in all booking fields.";
    } elseif (!in_array($appointment_time, $time_slots)) {
        $message = "Please select a valid time slot.";
    } elseif ($appointment_date < date("Y-m-d")) {
        $message = "Please choose a current or future date.";
    } else {
        $check_sql = "SELECT id FROM appointments WHERE appointment_date = ? AND appointment_time = ? AND status IN ('pending', 'accepted')";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "ss", $appointment_date, $appointment_time);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $message = "This time slot is already booked. Please choose another one.";
        } else {
            $insert_sql = "INSERT INTO appointments (user_id, service_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, 'pending')";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "iiss", $user_id, $service_id, $appointment_date, $appointment_time);

            if (mysqli_stmt_execute($insert_stmt)) {
                $message = "Booking created successfully.";
            } else {
                $message = "Something went wrong. Please try again.";
            }
        }
    }
}

$bookings_sql = "SELECT appointments.id, services.service_name, appointments.appointment_date, appointments.appointment_time, appointments.status
                 FROM appointments
                 INNER JOIN services ON appointments.service_id = services.id
                 WHERE appointments.user_id = ?
                 ORDER BY appointments.appointment_date ASC, appointments.appointment_time ASC";

$bookings_stmt = mysqli_prepare($conn, $bookings_sql);
mysqli_stmt_bind_param($bookings_stmt, "i", $user_id);
mysqli_stmt_execute($bookings_stmt);
$bookings_result = mysqli_stmt_get_result($bookings_stmt);

include 'includes/header.php';
?>

<main class="booking-page">
    <section class="booking-section">
        <h1>Book a service</h1>

        <?php if (!empty($message)) : ?>
            <p class="form-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form action="" method="POST" class="booking-form">
            <select name="service_id" required>
                <option value="">Select a service</option>
                <?php while ($service = mysqli_fetch_assoc($services_result)) : ?>
                    <option value="<?php echo $service['id']; ?>">
                        <?php echo htmlspecialchars($service['service_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <div class="booking-row">
                <input type="date" name="appointment_date" min="<?php echo date('Y-m-d'); ?>" required>

                <select name="appointment_time" required>
                    <option value="">Select time</option>
                    <?php foreach ($time_slots as $slot) : ?>
                        <option value="<?php echo $slot; ?>">
                            <?php echo date("H:i", strtotime($slot)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="booking-btn">Book now</button>
        </form>

        <h2>Your bookings</h2>

        <div class="bookings-table-wrapper">
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($bookings_result) > 0) : ?>
                        <?php while ($booking = mysqli_fetch_assoc($bookings_result)) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                <td><?php echo date("d.m.Y", strtotime($booking['appointment_date'])); ?></td>
                                <td><?php echo date("H:i", strtotime($booking['appointment_time'])); ?></td>
                                <td class="status-<?php echo strtolower($booking['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                </td>
                                <td>
                                    <?php if ($booking['status'] === 'pending' || $booking['status'] === 'accepted') : ?>
                                        <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            <input type="hidden" name="cancel_booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" class="cancel-booking-btn">Cancel</button>
                                        </form>
                                    <?php else : ?>
                                        <span class="no-action-text">Unavailable</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5">You do not have any bookings yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>