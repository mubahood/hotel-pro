<?php

require_once('functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = [
        'room_id' => $_POST['room_id'],
        'customer_id' => $_POST['customer_id'],
        'customer_phone' => $_POST['customer_phone'],
        'customer_email' => $_POST['customer_email'],
        'adults' => $_POST['adults'],
        'children' => $_POST['children'],
        'check_in' => $_POST['check_in'],
        'number_of_days' => $_POST['number_of_days'],
        'order_status' => $_POST['order_status'],
        'amount_paid' => $_POST['amount_paid'],
        'payment_method' => $_POST['payment_method'],
        'payment_reference' => $_POST['payment_reference'],
    ];
    $check_out = date('Y-m-d', strtotime($_POST['check_in'] . ' + ' . $_POST['number_of_days'] . ' days'));
    $data['check_out'] = $check_out;
    $room = db_find('rooms', $_POST['room_id']);

    $room_price = $room['price'];
    $total_price = $room_price * $_POST['number_of_days'] * $_POST['adults'];
    $data['total_price'] = $total_price;

    //payment_status set paid if amount_paid is equal to total_price
    $payment_status = 'Pending';
    if ($_POST['amount_paid'] >= $total_price) {
        $payment_status = 'PAID';
    } else {
        $payment_status = 'NOT PAID';
    }
    $data['payment_status'] = $payment_status;



    //toady in Y-m-d
    $today = date('Y-m-d');

    $room_availability = '';
    if ($today <= $check_out) {
        $room_availability = 'OCCUPIED';
    } else {
        $room_availability = 'VACANT';
    }

    db_update('rooms', $_POST['room_id'], [
        'status' => $room_availability
    ]);


    if (isset($_POST['edit_id'])) {
        $id = $_POST['edit_id'];

        db_update('bookings', $id, $data);


        alert_message('success', 'Order updated successfully.');
        header('Location: admin-bookings.php');
        exit;
    } else {
        $created_at = date('Y-m-d H:i:s');
        $data['created_at'] = $created_at;
        $id = db_insert('bookings', $data);
        alert_message('success', 'Order created successfully.');
        header('Location: admin-bookings.php');
        exit;
    }

    exit;
}

$PAGE_SECTION = 'My Bookings';
$PAGE_TITLE = 'Update Booking';
$SECTION_LINK = 'admin-bookings.php';

$isEditing = false;
$id = null;
$booking = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $booking = db_find('bookings', $id);
    if ($booking != null) {
        $isEditing = true;
    }
}

if ($isEditing) {
    $PAGE_TITLE = 'Update booking - #' . $id;
}

require_once('dashboard-header.php');



$rooms = [];
foreach (db_select('rooms') as $r) {
    $rooms[$r['id']] = $r['name'] . ' - ' . $r['price'] . ' (' . $r['status'] . ')';
}

$customeers = [];
foreach (db_select('users', " user_type = 'Customer' ") as $c) {
    $customers[$c['id']] = $c['name'] . ' - ' . $c['email'];
}

?>

<div class="row">
    <!-- FormValidation -->
    <div class="col-12">
        <div class="card">
            <h5 class="card-header"><?= $PAGE_TITLE ?></h5>
            <div class="card-body">
                <!-- make the form multipart -->
                <form action="" method="post" enctype="multipart/form-data">
                    <!-- check if is editing and hide id in the form -->
                    <?php if ($isEditing) : ?>
                        <input type="hidden" name="edit_id" value="<?= $booking['id'] ?>">
                    <?php endif; ?>
                    <div class="row">

                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo select_input([
                                'label' => 'Select Room',
                                'name' => 'room_id',
                                'value' => $isEditing ? $booking['room_id'] : '',
                                'attributes' => ' required ',
                                'options' => $rooms
                            ]) ?>
                        </div>

                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo select_input([
                                'label' => 'Select Customer',
                                'name' => 'customer_id',
                                'value' => $isEditing ? $booking['customer_id'] : '',
                                'attributes' => ' required ',
                                'options' => $customers
                            ]) ?>
                        </div>

                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo text_input([
                                'label' => 'Customer Phone',
                                'name' => 'customer_phone',
                                'value' => $isEditing ? $booking['customer_phone'] : '',
                                'attributes' => ''
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo text_input([
                                'label' => 'Customer Email Address',
                                'name' => 'customer_email',
                                'value' => $isEditing ? $booking['customer_email'] : '',
                                'attributes' => ''
                            ]) ?>
                        </div>
                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo text_input([
                                'label' => 'Number of Adults',
                                'name' => 'adults',
                                'value' => $isEditing ? $booking['adults'] : '',
                                'type' => 'number',
                                'attributes' => ' required min="1" ',
                            ]) ?>
                        </div>
                        <!-- children -->
                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo text_input([
                                'label' => 'Number of Children',
                                'name' => 'children',
                                'type' => 'number',
                                'value' => $isEditing ? $booking['children'] : '',
                                'attributes' => ' required min="0" ',
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo text_input([
                                'label' => 'Check In Date',
                                'name' => 'check_in',
                                'value' => $isEditing ? $booking['check_in'] : '',
                                'type' => 'date',
                                'attributes' => ' required ',
                            ]) ?>
                        </div>
                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo text_input([
                                'label' => 'Number of Days',
                                'name' => 'number_of_days',
                                'value' => $isEditing ? $booking['number_of_days'] : '',
                                'type' => 'number',
                                'attributes' => ' required min="1" ',
                            ]) ?>
                        </div>
                        <!-- order_status dropdown-->
                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo select_input([
                                'label' => 'Order Status',
                                'name' => 'order_status',
                                'value' => $isEditing ? $booking['order_status'] : '',
                                'attributes' => ' required ',
                                'options' => [
                                    'Pending' => 'Pending',
                                    'Confirmed' => 'Confirmed',
                                    'Cancelled' => 'Cancelled',
                                ]
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo text_input([
                                'label' => 'Amount Paid',
                                'value' => $isEditing ? $booking['amount_paid'] : '',
                                'name' => 'amount_paid',
                                'type' => 'number',
                                'attributes' => ' required min="0" ',
                            ]) ?>
                        </div>
                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo text_input([
                                'label' => 'Payment Method',
                                'value' => $isEditing ? $booking['payment_method'] : '',
                                'name' => 'payment_method',
                                'attributes' => ''
                            ]) ?>
                        </div>
                        <!-- payment_reference -->
                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo text_input([
                                'label' => 'Payment Reference',
                                'name' => 'payment_reference',
                                'value' => $isEditing ? $booking['payment_reference'] : '',
                                'attributes' => ''
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary">SUBMIT</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /FormValidation -->
</div>


<?php
require_once('dashboard-footer.php');
?>