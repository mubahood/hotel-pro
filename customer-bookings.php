<?php

require_once('functions.php');

//check if delete is set
if (isset($_GET['cancel'])) {
    $id = $_GET['cancel'];

    $booking = db_find('bookings', $id);
    if ($booking == null) {
        alert_message('danger', 'Booking not found.');
        header('Location: customer-bookings.php');
        exit;
    }

    if ($booking['order_status'] != 'Pending') {
        alert_message('danger', 'Booking cannot be cancelled.');
        header('Location: customer-bookings.php');
        exit;
    }
    $b['order_status'] = 'Cancelled';
    db_update('bookings', $id, $b);
    alert_message('success', 'Booking cancelled successfully.');
    header('Location: customer-bookings.php');
    exit;
}

//check if delete is set
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $category = db_find('room_categories', $id);
    if ($category == null) {
        alert_message('danger', 'Room Category not found.');
        header('Location: admin-room-categories.php');
        exit;
    }

    //check if it has photos and delete them
    if ($category['photo'] != null && !empty($category['photo'])) {
        //check if file exists
        $file = 'uploads/' . $category['photo'];
        if (file_exists($file)) {
            unlink($file);
        }
    }

    db_delete('room_categories', $id);
    alert_message('success', 'Room Category deleted successfully.');
    header('Location: admin-room-categories.php');
    exit;
}

$PAGE_SECTION = 'My Bookings';
$PAGE_TITLE = 'List';

require_once('dashboard-header.php');

$bookings = db_select('bookings', ' customer_id = ' . $_SESSION['user']['id'] . ' ORDER BY id DESC');
/* 
Array
(
    [0] => Array
        (
            [id] => 1
            [customer_id] => 6
            [customer_name] => Axel Cunningham
            [customer_phone] => +1 (643) 745-8201
            [customer_email] => mubahood360@gmail.com
            [room_id] => 1
            [check_in] => 07/15/2024
            [number_of_days] => 4
            [check_out] => 07/19/2024
            [adults] => 3
            [children] => 0
            [total_price] => 400
            [order_status] => Pending
            [created_at] => 2024-07-15 09:33:49
            [amount_paid] => 0
            [payment_method] => 
            [payment_reference] => 
            [payment_status] => Pending
        )

)
*/
?>
<!-- css assets/vendor/libs/datatables-bs5/datatables-bootstrap5.css -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">

<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-body">
                <table class="table table-striped table-bordered table-hover" id="category_tables">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Room</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Adults</th>
                            <th>Children</th>
                            <th>Total Price</th>
                            <th>Order Status</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking) :
                            $room = db_find('rooms', $booking['room_id']);
                            if ($room == null) {
                                continue;
                            }
                            $context_class = '';
                            if ($booking['order_status'] == 'Cancelled') {
                                $context_class = 'bg-danger';
                            } elseif ($booking['order_status'] == 'Pending') {
                                $context_class = 'bg-warning';
                            } elseif ($booking['order_status'] == 'Confirmed') {
                                $context_class = 'bg-success';
                            } elseif ($booking['order_status'] == 'Completed') {
                                $context_class = 'bg-success';
                            } else {
                                $context_class = 'bg-info';
                            }
                        ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($booking['created_at'])) ?></td>
                                <td><?= $room['name'] ?></td>
                                <td><?= date('d/m/Y', strtotime($booking['check_in'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($booking['check_out'])) ?></td>
                                <td><?= $booking['adults'] ?></td>
                                <td><?= $booking['children'] ?></td>
                                <td><?= $booking['total_price'] ?></td>
                                <td><span class="badge <?= $context_class ?>"><?= $booking['order_status'] ?></span></td>
                                <td><?= $booking['payment_status'] ?></td>
                                <td>
                                    <!-- if status is pending, show cancel button -->
                                    <?php if ($booking['order_status'] == 'Pending') : ?>
                                        <a href="customer-bookings.php?cancel=<?= $booking['id'] ?>" class="btn btn-sm btn-danger">Cancel Booking</a>
                                        <!-- else -->
                                    <?php else : ?>
                                        No actions
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
require_once('dashboard-footer.php');
?>
<script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>

<script>
    $(document).ready(function() {
        $('#category_tables').DataTable();
    });
</script>