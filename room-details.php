<?php
if (!isset($_GET['id'])) {
    die("Room not set.");
}
//require_once('functions.php');
require_once('functions.php');
$room_id = $_GET['id'];
$room = db_find('rooms', $room_id);
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




<?php if (count($gallery_photos) > 0) { ?>
    <!-- Room Page Slider -->
    <header class="header slider">
        <div class="owl-carousel owl-theme">
            <!-- The opacity on the image is made with "data-overlay-dark="number". You can change it using the numbers 0-9. -->
            <?php foreach ($gallery_photos as $photo) { ?>
                <div class="text-center item bg-img" data-overlay-dark="3" data-background="<?php echo url('uploads/' . $photo); ?>"></div>
            <?php } ?>
        </div>
        <!-- arrow down -->
        <div class="arrow bounce text-center">
            <a href="#" data-scroll-nav="1" class=""> <i class="ti-arrow-down"></i> </a>
        </div>
    </header>
<?php } ?>

<!-- Room Content -->
<section class="rooms-page section-padding" data-scroll-index="1">
    <div class="container">
        <!-- project content -->
        <div class="row">
            <div class="col-md-12">
                <span>
                    <i class="star-rating"></i>
                    <i class="star-rating"></i>
                    <i class="star-rating"></i>
                    <i class="star-rating"></i>
                    <i class="star-rating"></i>
                </span>
                <div class="section-subtitle"><?= $room_category['name'] ?></div>
                <div class="section-title"><?= $room['name'] ?></div>
            </div>
            <div class="col-md-8">
                <p class="mb-30"><?= $room['details'] ?></p>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Check-in</h6>
                        <ul class="list-unstyled page-list mb-30">
                            <li>
                                <div class="page-list-icon"> <span class="ti-check"></span> </div>
                                <div class="page-list-text">
                                    <p><?= $room['check_in'] ?></p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Check-out</h6>
                        <ul class="list-unstyled page-list mb-30">
                            <li>
                                <div class="page-list-icon"> <span class="ti-check"></span> </div>
                                <div class="page-list-text">
                                    <p><?= $room['check_out'] ?></p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <h6>Special check-in instructions</h6>
                        <p><?= $room['special_instructions'] ?></p>
                    </div>
                    <div class="col-md-12">
                        <h6>Pets</h6>
                        <p><?= $room['pets_allowed'] ?></p>
                    </div>
                    <div class="col-md-12">
                        <h6>Children and extra beds</h6>
                        <p><?= $room['children'] ?></p>
                    </div>
                    <div class="col-md-12">
                        <div class="butn-dark mt-15 mb-30"> <a href="room-booking.php?id=<?= $room['id'] ?>"><span>Book Now</span></a> </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 offset-md-1">
                <h6>Amenities</h6>
                <ul class="list-unstyled page-list mb-30">
                    <li>
                        <div class="page-list-icon"> <span class="flaticon-group"></span> </div>
                        <div class="page-list-text">
                            <p><?= $room['amenities_max_people'] ?></p>
                        </div>
                    </li>
                    <li>
                        <div class="page-list-icon"> <span class="flaticon-wifi"></span> </div>
                        <div class="page-list-text">
                            <?php if ($room['amenities_wifi'] == 'Yes') { ?>
                                <p>Free Wifi</p>
                            <?php } else { ?>
                                <s>
                                    <p>No Wifi</p>
                                </s>
                            <?php } ?>
                        </div>
                    </li>
                    <li>
                        <div class="page-list-icon"> <span class="flaticon-clock-1"></span> </div>
                        <div class="page-list-text">
                            <!-- amenities_parking -->
                            <?php if ($room['amenities_parking'] == 'Yes') { ?>
                                <p>Free Parking</p>
                            <?php } else { ?>
                                <s>
                                    <p>No Parking</p>
                                </s>
                            <?php } ?>
                        </div>
                    </li>
                    <li>
                        <div class="page-list-icon"> <span class="flaticon-breakfast"></span> </div>
                        <div class="page-list-text">
                            <!-- amenities_breakfast -->
                            <?php if ($room['amenities_breakfast'] == 'Yes') { ?>
                                <p>Breakfast</p>
                            <?php } else { ?>
                                <s>
                                    <p>No Breakfast</p>
                                </s>
                            <?php } ?>
                        </div>
                    </li>
                    <li>
                        <div class="page-list-icon"> <span class="flaticon-towel"></span> </div>
                        <div class="page-list-text">
                            <p>Towels</p>
                        </div>
                    </li>
                    <li>
                        <div class="page-list-icon"> <span class="flaticon-swimming"></span> </div>
                        <div class="page-list-text">
                            <!-- amenities_swimming_pool -->
                            <?php if ($room['amenities_swimming_pool'] == 'Yes') { ?>
                                <p>Swimming Pool</p>
                            <?php } else { ?>
                                <s>
                                    <p>No Swimming Pool</p>
                                </s>
                            <?php } ?>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!-- Similiar Room -->
<section class="rooms1 section-padding bg-blck">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-subtitle"><span>Luxury Hotel</span></div>
                <div class="section-title"><span>Similar Rooms</span></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="owl-carousel owl-theme">

                    <?php foreach ($related_rooms as $room) { ?>
                        <div class="item">
                            <div class="position-re o-hidden"> <img src="<?= 'uploads/' . $room['main_photo'] ?>" alt=""> </div> <span class="category"><a href="room-booking.php?id=<?= $room['id'] ?>">Book</a></span>
                            <div class="con">
                                <h6><a href="room-details.php?id=<?= $room['id'] ?>"><?= $CURRENCY . " " . $room['price'] ?> / Night</a></h6>
                                <h5><a href="room-details.php?id=<?= $room['id'] ?>"><?= $room['name'] ?></a> </h5>
                                <div class="line"></div>
                                <div class="row facilities">
                                    <div class="col col-md-7">
                                        <ul>
                                            <li><i class="flaticon-bed"></i></li>
                                            <li><i class="flaticon-bath"></i></li>
                                            <li><i class="flaticon-breakfast"></i></li>
                                            <li><i class="flaticon-towel"></i></li>
                                        </ul>
                                    </div>
                                    <div class="col col-md-5 text-end">
                                        <div class="permalink"><a href="room-details.php?id=<?= $room['id'] ?>">Details <i class="ti-arrow-right"></i></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Pricing -->
<section class="pricing section-padding">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="section-subtitle"><span>Best Prices</span></div>
                <div class="section-title">Extra Services</div>
                <p>The best prices for your relaxing vacation. The utanislen quam nestibulum ac quame odion elementum sceisue the aucan.</p>
                <p>Orci varius natoque penatibus et magnis disney parturient monte nascete ridiculus mus nellen etesque habitant morbine.</p>
                <div class="reservations mb-30">
                    <div class="icon"><span class="flaticon-call"></span></div>
                    <div class="text">
                        <p>For information</p> <a href="tel:855-100-4444">855 100 4444</a>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="owl-carousel owl-theme">
                    <div class="pricing-card">
                        <img src="img/pricing/1.jpg" alt="">
                        <div class="desc">
                            <div class="name">Room cleaning</div>
                            <div class="amount">$50<span>/ month</span></div>
                            <ul class="list-unstyled list">
                                <li><i class="ti-check"></i> Hotel ut nisan the duru</li>
                                <li><i class="ti-check"></i> Orci miss natoque vasa ince</li>
                                <li><i class="ti-close unavailable"></i>Clean sorem ipsum morbin</li>
                            </ul>
                        </div>
                    </div>
                    <div class="pricing-card">
                        <img src="img/pricing/2.jpg" alt="">
                        <div class="desc">
                            <div class="name">Drinks included</div>
                            <div class="amount">$30<span>/ daily</span></div>
                            <ul class="list-unstyled list">
                                <li><i class="ti-check"></i> Hotel ut nisan the duru</li>
                                <li><i class="ti-check"></i> Orci miss natoque vasa ince</li>
                                <li><i class="ti-close unavailable"></i>Clean sorem ipsum morbin</li>
                            </ul>
                        </div>
                    </div>
                    <div class="pricing-card">
                        <img src="img/pricing/3.jpg" alt="">
                        <div class="desc">
                            <div class="name">Room Breakfast</div>
                            <div class="amount">$30<span>/ daily</span></div>
                            <ul class="list-unstyled list">
                                <li><i class="ti-check"></i> Hotel ut nisan the duru</li>
                                <li><i class="ti-check"></i> Orci miss natoque vasa ince</li>
                                <li><i class="ti-close unavailable"></i>Clean sorem ipsum morbin</li>
                            </ul>
                        </div>
                    </div>
                    <div class="pricing-card">
                        <img src="img/pricing/4.jpg" alt="">
                        <div class="desc">
                            <div class="name">Safe & Secure</div>
                            <div class="amount">$15<span>/ daily</span></div>
                            <ul class="list-unstyled list">
                                <li><i class="ti-check"></i> Hotel ut nisan the duru</li>
                                <li><i class="ti-check"></i> Orci miss natoque vasa ince</li>
                                <li><i class="ti-close unavailable"></i>Clean sorem ipsum morbin</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Reservation & Booking Form -->
<section class="testimonials">
    <div class="background bg-img bg-fixed section-padding pb-0" data-background="img/slider/2.jpg" data-overlay-dark="2">
        <div class="container">
            <div class="row">
                <!-- Reservation -->
                <div class="col-md-5 mb-30 mt-30">
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
                <div class="col-md-5 offset-md-2">
                    <div class="booking-box">
                        <div class="head-box">
                            <h6>Rooms & Suites</h6>
                            <h4>Hotel Booking Form</h4>
                        </div>
                        <div class="booking-inner clearfix">
                            <form action="../../../../../external.html?link=https://duruthemes.com/demo/html/cappa/demo1-light/rooms2.html" class="form1 clearfix">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input1_wrapper">
                                            <label>Check in</label>
                                            <div class="input1_inner">
                                                <input type="text" class="form-control input datepicker" placeholder="Check in">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input1_wrapper">
                                            <label>Check out</label>
                                            <div class="input1_inner">
                                                <input type="text" class="form-control input datepicker" placeholder="Check out">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="select1_wrapper">
                                            <label>Adults</label>
                                            <div class="select1_inner">
                                                <select class="select2 select" style="width: 100%">
                                                    <option value="0">Adults</option>
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
                                            <label>Children</label>
                                            <div class="select1_inner">
                                                <select class="select2 select" style="width: 100%">
                                                    <option value="0">Children</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn-form1-submit mt-15">Check Availability</button>
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