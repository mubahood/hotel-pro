<?php

require_once('functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //check if name is submited
    if (!isset($_POST['name']) || empty($_POST['name'])) {
        $_SESSION['form_errors']['name'] = 'Name is required';
        die('Name is required');
        header('Location: admin-room-categories-create.php');
        exit;
    }

    //check if template is submited
    if (!isset($_POST['template']) || empty($_POST['template'])) {
        $_SESSION['form_errors']['template'] = 'Template is required';
        header('Location: admin-room-categories-create.php');
        exit;
    }


    $main_photo = null;
    if (isset($_FILES) && isset($_FILES['main_photo'])) {
        $resp = upload_image($_FILES['main_photo']);
        if (is_array($resp) && isset($resp['status']) && $resp['status'] === true) {
            $main_photo = $resp['message'];
        }
    }

    $uploaded_gallery_photos = [];
    if (isset($_FILES) && isset($_FILES['gallery_photos'])) {
        //check if $_FILES['gallery_photos'] is array and not empty
        if (is_array($_FILES['gallery_photos']) && !empty($_FILES['gallery_photos'])) {
            $max_count = count($_FILES['gallery_photos']['name']);

            for ($i = 0; $i < $max_count; $i++) {
                $photo['name'] = $_FILES['gallery_photos']['name'][$i];
                $photo['type'] = $_FILES['gallery_photos']['type'][$i];
                $photo['tmp_name'] = $_FILES['gallery_photos']['tmp_name'][$i];
                $photo['error'] = $_FILES['gallery_photos']['error'][$i];
                $photo['size'] = $_FILES['gallery_photos']['size'][$i];
                $resp = upload_image($photo);
                if (is_array($resp) && isset($resp['status']) && $resp['status'] === true) {
                    $uploaded_gallery_photos[] = $resp['message'];
                }
            }
        }
    }



    //check if is editing
    if (isset($_POST['edit_id'])) {
        $id = $_POST['edit_id'];
        $room = db_find('rooms', $id);
        if ($room == null) {
            alert_message('danger', 'Room not found.');
            header('Location: admin-rooms.php');
            exit;
        }
        $data = [
            'amenities_parking' => $_POST['amenities_parking'],
            'pets_allowed' => $_POST['pets_allowed'],
            'check_in' => $_POST['check_in'],
            'children' => $_POST['children'],
            'gallery_photos' => $gallery_photos,
            'check_out' => $_POST['check_out'],
            'special_instructions' => $_POST['special_instructions'],
            'template' => $_POST['template'],
            'rating' => $_POST['rating'],
            'amenities_max_people' => $_POST['amenities_max_people'],
            'amenities_wifi' => $_POST['amenities_wifi'],
            'amenities_breakfast' => $_POST['amenities_breakfast'],
            'amenities_gym' => $_POST['amenities_gym'],
            'amenities_towels' => $_POST['amenities_towels'],
            'amenities_swimming_pool' => $_POST['amenities_swimming_pool'],
            'amenities_ac' => $_POST['amenities_ac'],
            'details' => $_POST['details'],
            'name' => $_POST['name'],
            'room_category_id' => $_POST['room_category_id'],
            'price' => $_POST['price'],
            'status' => $_POST['status'],
            'show_at_home' => $_POST['show_at_home'],
        ];

        $database_gallery_photos = [];
        if ($room['gallery_photos'] != null && strlen($room['gallery_photos']) > 4) {
            $database_gallery_photos = json_decode($room['gallery_photos']);
        }

        if (!is_array($database_gallery_photos)) {
            $database_gallery_photos = [];
        }
        if ($uploaded_gallery_photos != null && !empty($uploaded_gallery_photos)) {
            foreach ($uploaded_gallery_photos as $photo) {
                $database_gallery_photos[] = $photo;
            }
            $data['gallery_photos'] = json_encode($database_gallery_photos);
        }

        //check if image is uploaded
        if ($main_photo != null && !empty($main_photo)) {
            $data['main_photo'] = $main_photo;
        }

        db_update('rooms', $id, $data);

        //clear $_SESSION['form_errors']
        unset($_SESSION['form_errors']);

        alert_message('success', 'Room updated successfully.');
        header('Location: admin-rooms.php');
        exit;
    } else {

        //$uploaded_gallery_photos
        $gallery_photos = '[]';
        if ($uploaded_gallery_photos != null && !empty($uploaded_gallery_photos)) {
            $gallery_photos = json_encode($uploaded_gallery_photos);
        }

        db_insert('rooms', [
            'amenities_parking' => $_POST['amenities_parking'],
            'pets_allowed' => $_POST['pets_allowed'],
            'check_in' => $_POST['check_in'],
            'children' => $_POST['children'],
            'gallery_photos' => $gallery_photos,
            'check_out' => $_POST['check_out'],
            'special_instructions' => $_POST['special_instructions'],
            'template' => $_POST['template'],
            'rating' => $_POST['rating'],
            'amenities_max_people' => $_POST['amenities_max_people'],
            'amenities_wifi' => $_POST['amenities_wifi'],
            'amenities_breakfast' => $_POST['amenities_breakfast'],
            'amenities_gym' => $_POST['amenities_gym'],
            'amenities_towels' => $_POST['amenities_towels'],
            'amenities_swimming_pool' => $_POST['amenities_swimming_pool'],
            'amenities_ac' => $_POST['amenities_ac'],
            'details' => $_POST['details'],
            'name' => $_POST['name'],
            'room_category_id' => $_POST['room_category_id'],
            'main_photo' => $main_photo,
            'price' => $_POST['price'],
            'status' => $_POST['status'],
            'show_at_home' => $_POST['show_at_home'],
        ]);
        unset($_SESSION['form_errors']);

        alert_message('success', 'Room created successfully.');
        header('Location: admin-rooms.php');
    }

    exit;
}

$PAGE_SECTION = 'Rooms';
$PAGE_TITLE = 'Create Room';
$SECTION_LINK = 'admin-rooms.php';

$isEditing = false;
$id = null;
$room = null;
$existing_gallery_photos = [];
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $room = db_find('rooms', $id);
    if ($room != null) {
        $isEditing = true;
        if ($room['gallery_photos'] != null && strlen($room['gallery_photos']) > 4) {
            $existing_gallery_photos = json_decode($room['gallery_photos']);
        }
    }
}

if ($isEditing) {
    $PAGE_TITLE = 'Edit Room - #' . $id;
}


require_once('dashboard-header.php');



$room_cats = db_select('room_categories');
$room_categories = [];
foreach ($room_cats as $cat) {
    $room_categories[$cat['id']] = $cat['name'];
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
                        <input type="hidden" name="edit_id" value="<?= $room['id'] ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 mt-1 mt-md-2">
                            <?php echo text_input([
                                'label' => 'Room Name',
                                'name' => 'name',
                                'attributes' => ' autofocus required ',
                                'value' => $isEditing ? $room['name'] : ''
                            ]) ?>
                        </div>
                        <div class="col-md-4 mt-1 mt-md-2">
                            <?php echo select_input([
                                'label' => 'Room Category',
                                'name' => 'room_category_id',
                                'attributes' => ' required ',
                                'value' => $isEditing ? $room['room_category_id'] : '',
                                'options' => $room_categories
                            ]) ?>
                        </div>
                        <div class="col-md-2 mt-1 mt-md-2">
                            <?php echo text_input([
                                'label' => 'Room Price',
                                'name' => 'price',
                                'type' => 'number',
                                'attributes' => ' required ',
                                'value' => $isEditing ? $room['price'] : ''
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-3 mt-1 mt-md-2">
                            <?php echo select_input([
                                'label' => 'Room Status',
                                'name' => 'status',
                                'attributes' => ' required ',
                                'value' => $isEditing ? $room['status'] : '',
                                'options' => [
                                    'VACANT' => 'VACANT',
                                    'OCCUPIED' => 'OCCUPIED',
                                    'RESERVED' => 'RESERVED',
                                    'MAINTENANCE' => 'MAINTENANCE',
                                ]
                            ]) ?>
                        </div>
                        <div class="col-md-3 mt-1 mt-md-2">
                            <?php echo select_input([
                                'label' => 'Show at Home Page',
                                'name' => 'show_at_home',
                                'attributes' => ' required ',
                                'value' => $isEditing ? $room['show_at_home'] : '',
                                'options' => [
                                    'Yes' => 'Yes',
                                    'No' => 'No',
                                ]
                            ]) ?>
                        </div>

                        <div class="col-md-3 mt-1 mt-md-2">
                            <?php echo text_input([
                                'label' => 'Main photo',
                                'name' => 'main_photo',
                                'type' => 'file',
                                'attributes' => ' accept="image/*" ',
                                'value' => $isEditing ? $room['main_photo'] : ''
                            ]) ?>
                            <!-- display main photo if is editing and not empty -->
                            <?php if ($isEditing && $room['main_photo'] != null && strlen($room['main_photo']) > 2) { ?>
                                <img src="uploads/<?= $room['main_photo'] ?>" alt="<?= $room['name'] ?>" class="img-thumbnail rounded" style="width: 70px;">
                            <?php } ?>
                        </div>

                        <!-- gallery_photos -->
                        <div class="col-md-3 mt-1 mt-md-2">
                            <?php echo text_input([
                                'label' => 'Gallery Photos',
                                'name' => 'gallery_photos[]',
                                'type' => 'file',
                                'attributes' => ' multiple accept="image/*" ',
                                'value' => $isEditing ? $room['gallery_photos'] : ''
                            ]) ?>
                        </div>
                        <!-- if is editing and $existing_gallery_photos is not empty -->
                        <?php if ($isEditing && $existing_gallery_photos != null && !empty($existing_gallery_photos)) { ?>
                            <div class="row">
                                <?php foreach ($existing_gallery_photos as $photo) { ?>
                                    <div class="col-1">
                                        <img src="uploads/<?= $photo ?>" alt="<?= $room['name'] ?>" class="img-thumbnail rounded" style="width: 70px;">
                                        <!-- delete  button -->
                                        <a target="_blank" href="admin-delete-photo.php?id=<?= $room['id'] ?>&photo=<?= $photo ?>&type=room" class="btn btn-sm btn-danger">X</a>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="row">
                        <!-- details -->
                        <div class="col-12 mt-1 mt-md-2">
                            <?php echo textarea_input([
                                'label' => 'Room Details',
                                'name' => 'details',
                                'attributes' => ' required rows="3"',
                                'value' => $isEditing ? $room['details'] : ''
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mt-1 mt-md-2">
                            <?php echo text_input([
                                'label' => 'Rating',
                                'name' => 'rating',
                                'attributes' => ' required ',
                                'type' => 'number',
                                'value' => $isEditing ? $room['rating'] : ''
                            ]) ?>
                        </div>
                        <div class="col-md-4 mt-1 mt-md-2">
                            <?php echo select_input([
                                'label' => 'Room Template',
                                'name' => 'template',
                                'attributes' => ' required ',
                                'value' => $isEditing ? $room['template'] : '',
                                'options' => [
                                    'TEMPLATE_1' => 'TEMPLATE 1',
                                    'TEMPLATE_2' => 'TEMPLATE 2',
                                    'TEMPLATE_3' => 'TEMPLATE 3',
                                ]
                            ]) ?>
                        </div>

                        <div class="col-md-4 mt-1 mt-md-2">
                            <?php echo text_input([
                                'label' => 'Pets Allowed',
                                'name' => 'pets_allowed',
                                'attributes' => ' required ',
                                'type' => 'text',
                                'value' => $isEditing ? $room['pets_allowed'] : ''
                            ]) ?>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6 mt-1 mt-md-2">
                            <?php echo textarea_input([
                                'label' => 'Check In',
                                'name' => 'check_in',
                                'attributes' => ' required ',
                                'type' => 'text',
                                'value' => $isEditing ? $room['check_in'] : ''
                            ]) ?>
                        </div>
                        <div class="col-md-6 mt-1 mt-md-2">
                            <?php echo textarea_input([
                                'label' => 'Check Out',
                                'name' => 'check_out',
                                'attributes' => ' required ',
                                'type' => 'text',
                                'value' => $isEditing ? $room['check_out'] : ''
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mt-1 mt-md-2">
                            <?php echo textarea_input([
                                'label' => 'Special Instructions',
                                'name' => 'special_instructions',
                                'attributes' => ' required ',
                                'type' => 'text',
                                'value' => $isEditing ? $room['special_instructions'] : ''
                            ]) ?>
                        </div>
                        <div class="col-md-6 mt-1 mt-md-2">
                            <?php echo textarea_input([
                                'label' => 'Children',
                                'name' => 'children',
                                'attributes' => ' required ',
                                'type' => 'text',
                                'value' => $isEditing ? $room['children'] : ''
                            ]) ?>
                        </div>
                    </div>





                    <div class="row">
                        <div class="col-md-3 mt-1 mt-md-2">
                            <?php echo text_input([
                                'label' => 'Amenities max people',
                                'name' => 'amenities_max_people',
                                'attributes' => ' required ',
                                'type' => 'text',
                                'value' => $isEditing ? $room['rating'] : ''
                            ]) ?>
                        </div>
                        <div class="col-md-3 mt-1 mt-md-2">
                            <?php echo select_input([
                                'label' => 'Wifi',
                                'name' => 'amenities_wifi',
                                'attributes' => ' required ',
                                'value' => $isEditing ? $room['amenities_wifi'] : '',
                                'options' => [
                                    'YES' => 'YES',
                                    'NO' => 'NO',
                                ]
                            ]) ?>
                        </div>
                        <!-- amenities_parking -->
                        <div class="col-md-3 mt-1 mt-md-2">
                            <?php echo select_input([
                                'label' => 'Parking',
                                'name' => 'amenities_parking',
                                'attributes' => ' required ',
                                'value' => $isEditing ? $room['amenities_parking'] : '',
                                'options' => [
                                    'YES' => 'YES',
                                    'NO' => 'NO',
                                ]
                            ]) ?>
                        </div>
                        <!-- amenities_breakfast -->
                        <div class="col-md-3 mt-1 mt-md-2">
                            <?php echo select_input([
                                'label' => 'Breakfast',
                                'name' => 'amenities_breakfast',
                                'attributes' => ' required ',
                                'value' => $isEditing ? $room['amenities_breakfast'] : '',
                                'options' => [
                                    'YES' => 'YES',
                                    'NO' => 'NO',
                                ]
                            ]) ?>
                        </div>

                    </div>

                    <!-- amenities_towels
                    amenities_swimming_pool
                    amenities_gym
                    amenities_ac -->
                    <div class="row">
                        <div class="col-md-3 mt-1 mt-md-2">
                            <?php echo select_input([
                                'label' => 'Towels',
                                'name' => 'amenities_towels',
                                'attributes' => ' required ',
                                'value' => $isEditing ? $room['amenities_towels'] : '',
                                'options' => [
                                    'YES' => 'YES',
                                    'NO' => 'NO',
                                ]
                            ]) ?>
                        </div>
                        <div class="col-md-3 mt-1 mt-md-2">
                            <?php echo select_input([
                                'label' => 'Swimming Pool',
                                'name' => 'amenities_swimming_pool',
                                'attributes' => ' required ',
                                'value' => $isEditing ? $room['amenities_swimming_pool'] : '',
                                'options' => [
                                    'YES' => 'YES',
                                    'NO' => 'NO',
                                ]
                            ]) ?>
                        </div>
                        <div class="col-md-3 mt-1 mt-md-2">
                            <?php echo select_input([
                                'label' => 'Gym',
                                'name' => 'amenities_gym',
                                'attributes' => ' required ',
                                'value' => $isEditing ? $room['amenities_gym'] : '',
                                'options' => [
                                    'YES' => 'YES',
                                    'NO' => 'NO',
                                ]
                            ]) ?>
                        </div>
                        <div class="col-md-3 mt-1 mt-md-2">
                            <?php echo select_input([
                                'label' => 'AC',
                                'name' => 'amenities_ac',
                                'attributes' => ' required ',
                                'value' => $isEditing ? $room['amenities_ac'] : '',
                                'options' => [
                                    'YES' => 'YES',
                                    'NO' => 'NO',
                                ]
                            ]) ?>
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