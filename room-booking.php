<?php
if (!isset($_GET['id'])) {
    die("Room not set.");
}
//require_once('functions.php');
require_once('functions.php');
if (!$_SESSION['user']) {
    alert_message('success', 'Please login to continue with your booking.');
    header('Location: register.php');
    exit;
}

$room_id = $_GET['id'];
$room = db_find('rooms', $room_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['form_data'] = $_POST;

    if (isset($_SESSION['form_errors'])) {
        unset($_SESSION['form_errors']);
    }


    //check check_in
    if (!isset($_POST['check_in']) || empty($_POST['check_in'])) {
        $_SESSION['form_errors']['check_in'] = 'Check in date is required.';
    }

    //check if customer_email is sset and is valid 
    if (!isset($_POST['customer_email']) || empty($_POST['customer_email'])) {
        $_SESSION['form_errors']['customer_email'] = 'Email address is required.';
    } else {
        if (!filter_var($_POST['customer_email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['form_errors']['customer_email'] = 'Invalid email address.';
        }
    }

    //check days
    if (!isset($_POST['days']) || empty($_POST['days'])) {
        $_SESSION['form_errors']['days'] = 'Number of days is required.';
    } else {
        if ($_POST['days'] < 1) {
            $_SESSION['form_errors']['days'] = 'Number of days must be greater than 0.';
        }
    }


    $data['customer_id'] = $_SESSION['user']['id'];
    $data['customer_name'] = $_SESSION['user']['name'];
    $data['customer_phone'] = $_POST['customer_phone'];
    $data['customer_email'] = $_POST['customer_email'];
    $data['room_id'] = $room_id;
    $data['number_of_days'] = $_POST['days'];
    $data['check_in'] = $_POST['check_in'];
    $data['check_out'] = date('m/d/Y', strtotime($_POST['check_in'] . ' + ' . $_POST['days'] . ' days'));
    $data['adults'] = $_POST['adults'];
    $data['children'] = 0;
    $data['total_price'] = $room['price'] * $_POST['days'];
    $data['order_status'] = 'Pending';
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['amount_paid'] = 0;
    $data['payment_method'] = '';
    $data['payment_reference'] = '';
    $data['payment_status'] = 'Pending';

    try {
        $order_id = db_insert('bookings', $data);
        if ($order_id) {
            alert_message('success', 'Your booking has been received. Please wait for confirmation.');
            header('Location: customer-bookings.php');
            //clear form session
            unset($_SESSION['form_data']);
            exit;
        } else {
            alert_message('danger', 'An error occurred while processing your booking. Please try again.');
        }
    } catch (\Throwable $th) {
        alert_message('danger', 'An error occurred while processing your booking. Please try again. ' . $th->getMessage());
    }

    //redirect back to the form
    header('Location: room-booking.php?id=' . $room_id);
};

$room_category = db_find('room_categories', $room['room_category_id']);
if ($room_category == null) {
    die("room category not found.");
}

if ($room == null) {
    die("Room not found.");
}
require_once('public-header.php');
$HOMEPAGE_ROOMS = db_select('rooms', " show_at_home = 'Yes' ");

$room = db_find('rooms', $room_id);
$room_category = db_find('room_categories', $room['room_category_id']);
$related_rooms = db_select('rooms', " room_category_id = " . $room['room_category_id'] . " AND id != " . $room['id'] . " LIMIT 6 ");

$gallery_photos = [];
if ($room['gallery_photos'] != null) {
    if (strlen($room['gallery_photos']) > 4) {
        try {
            $gallery_photos = json_decode($room['gallery_photos']);
        } catch (\Throwable $th) {
            //throw $th;
        }
        if (!is_array($gallery_photos)) {
            $gallery_photos = [];
        }
    }
}
?>

<!-- Reservation & Booking Form -->
<section class="testimonials">
    <div class="background bg-img bg-fixed section-padding pb-0" data-background="img/slider/2.jpg" data-overlay-dark="2">
        <div class="container">
            <div class="row">
                <!-- Reservation -->
                <div class="col-md-4 mb-30 mt-30">
                    <p><i class="star-rating"></i><i class="star-rating"></i><i class="star-rating"></i><i class="star-rating"></i><i class="star-rating"></i></p>
                    <h5>Each of our guest rooms feature a private bath, wi-fi, cable television and include full breakfast.</h5>
                    <div class="reservations mb-30">
                        <div class="icon color-1"><span class="flaticon-call"></span></div>
                        <div class="text">
                            <p class="color-1">Reservation</p> <a class="color-1" href="tel:855-100-4444">855 100 4444</a>
                        </div>
                    </div>
                    <p><i class="ti-check"></i><small>Call us, it's toll-free.</small></p>
                </div>
                <!-- Booking From -->
                <div class="col-md-6 offset-md-2">
                    <div class="booking-box">
                        <div class="head-box">
                            <h6>Rooms & Suites</h6>
                            <h4>Hotel Booking Form</h4>

                            <p style="color: black;">
                                You are booing for the room: <b><?= $room['name'] ?></b>, price per-night <b><?= $CURRENCY . " " . $room['price'] ?></b>, Check in: <b><?= $room['check_in'] ?></b>, Checkout: <b><?= $room['check_out'] ?></b>.
                            </p>
                        </div>

                        <div class="booking-inner clearfix">
                            <form action="" class="form1 clearfix" method="post">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input1_wrapper">
                                            <label>Check in</label>
                                            <div class="input1_inner">
                                                <input type="text" class="form-control input datepicker" placeholder="Check in" name="check_in">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <div class="bg-white p-2">
                                            <label>Email Address</label>
                                            <div class="customer_email">
                                                <input type="email" class="form-control input " placeholder="Email address" name="customer_email">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-12">

                                        <div class="bg-white p-2">
                                            <label>Phone Number</label>
                                            <div class="customer_phone">
                                                <input type="text" class="form-control input" placeholder="Phone number" name="customer_phone">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="select1_wrapper">
                                            <label>Number of days</label>
                                            <div class="select1_inner">
                                                <select class="select2 select" style="width: 100%" name="days">
                                                    <option value="0">Number of days</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="select1_wrapper">
                                            <label>Adults</label>
                                            <div class="select1_inner">
                                                <select class="select2 select" style="width: 100%" name="adults">
                                                    <option value="0">Number of people</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn-form1-submit mt-15">Book Now</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Clients -->
<section class="clients">



    <?php
    require_once('public-footer.php');
    ?>