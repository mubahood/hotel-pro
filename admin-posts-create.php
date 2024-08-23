<?php

require_once('functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    //check if name is submited
    if (!isset($_POST['title']) || empty($_POST['title'])) {
        $_SESSION['form_errors']['title'] = 'Title is required';
        header('Location: admin-posts-create.php');
        exit;
    }

    //check if template is submited
    if (!isset($_POST['type']) || empty($_POST['type'])) {
        $_SESSION['form_errors']['type'] = 'type is required';
        header('Location: admin-posts-create.php');
        exit;
    }

    $photo = null;
    if (isset($_FILES) && isset($_FILES['photo'])) {
        $resp = upload_image($_FILES['photo']);
        if (is_array($resp) && isset($resp['status']) && $resp['status'] === true) {
            $photo = $resp['message'];
        }
    }

    $photos = [];
    if (isset($_FILES) && isset($_FILES['photos'])) {
        //check if $_FILES['gallery_photos'] is array and not empty
        if (is_array($_FILES['photos']) && !empty($_FILES['photos'])) {
            $max_count = count($_FILES['photos']['name']);
            for ($i = 0; $i < $max_count; $i++) {
                $pic['name'] = $_FILES['photos']['name'][$i];
                $pic['type'] = $_FILES['photos']['type'][$i];
                $pic['tmp_name'] = $_FILES['photos']['tmp_name'][$i];
                $pic['error'] = $_FILES['photos']['error'][$i];
                $pic['size'] = $_FILES['photos']['size'][$i];
                $resp = upload_image($pic);
                if (is_array($resp) && isset($resp['status']) && $resp['status'] === true) {
                    $photos[] = $resp['message'];
                }
            }
        }
    }


    //check if is editing
    if (isset($_POST['edit_id'])) {
        die("time to update");
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
        $pics = '[]';
        if ($photos != null && !empty($photos)) {
            $pics = json_encode($photos);
        }
        $u = $_SESSION['user'];
        db_insert('posts', [
            'created_by' => $u['id'],
            'created_at' => date('Y-m-d H:i:s'),
            'title' => $_POST['title'],
            'type' => $_POST['type'],
            'category' => $_POST['category'],
            'description' => $_POST['description'],
            'details' => $_POST['details'],
            'photo' => $photo,
            'photos' => $pics,
            'meta_1' => $_POST['meta_1'],
            'meta_2' => $_POST['meta_2'],
            'meta_3' => $_POST['meta_3'],
            'meta_4' => $_POST['meta_4'],
        ]);
        unset($_SESSION['form_errors']);

        alert_message('success', 'Post created successfully.');
        header('Location: admin-posts.php');
    }

    exit;
}

$PAGE_SECTION = 'Posts';
$PAGE_TITLE = 'Create new post';
$SECTION_LINK = 'admin-posts-create.php';

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
                                'label' => 'Title',
                                'name' => 'title',
                                'attributes' => ' autofocus required ',
                                'value' => $isEditing ? $post['title'] : ''
                            ]) ?>
                        </div>
                        <div class="col-md-3 mt-1 mt-md-2">
                            <?php echo select_input([
                                'label' => 'Post Type',
                                'name' => 'type',
                                'attributes' => ' required ',
                                'value' => $isEditing ? $post['type'] : '',
                                'options' => [
                                    'NEWS' => 'News Post',
                                    'SLIDER' => 'Slider Post',
                                    'SERVICE' => 'Service Post',
                                    'FACILITY' => 'Facility Post',
                                    'REVIEW' => 'Review Post',
                                ]
                            ]) ?>
                        </div>
                        <div class="col-md-3 mt-1 mt-md-2">
                            <?php echo text_input([
                                'label' => 'Category',
                                'name' => 'category',
                                'type' => 'text',
                                'attributes' => '  ',
                                'value' => $isEditing ? $post['category'] : ''
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6 mt-1 mt-md-2">
                            <?php echo text_input([
                                'label' => 'Main photo',
                                'name' => 'photo',
                                'type' => 'file',
                                'attributes' => ' accept="image/*" ',
                                'value' => $isEditing ? $post['photo'] : ''
                            ]) ?>
                            <!-- display main photo if is editing and not empty -->
                            <?php if ($isEditing && $post['photo'] != null && strlen($post['photo']) > 2) { ?>
                                <img src="uploads/<?= $post['photo'] ?>" alt="<?= $post['name'] ?>" class="img-thumbnail rounded" style="width: 70px;">
                            <?php } ?>
                        </div>

                        <!-- gallery_photos -->
                        <div class="col-md-6 mt-1 mt-md-2">
                            <?php echo text_input([
                                'label' => 'Gallery Photos',
                                'name' => 'photos[]',
                                'type' => 'file',
                                'attributes' => ' multiple accept="image/*" ',
                                'value' => $isEditing ? $post['photos'] : ''
                            ]) ?>
                        </div>
                        <!-- if is editing and $existing_gallery_photos is not empty -->
                        <?php if ($isEditing && $existing_gallery_photos != null && !empty($existing_gallery_photos)) { ?>
                            <div class="row">
                                <?php foreach ($existing_gallery_photos as $photo) { ?>
                                    <div class="col-1">
                                        <img src="uploads/<?= $photo ?>" class="img-thumbnail rounded" style="width: 70px;">
                                        <!-- delete  button -->
                                        <a target="_blank" href="admin-delete-photo.php?id=<?= $post['id'] ?>&photo=<?= $photo ?>&type=post" class="btn btn-sm btn-danger">X</a>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="row">
                        <!-- details -->
                        <div class="col-md-6 mt-1 mt-md-2">
                            <?php echo textarea_input([
                                'label' => 'Post Description',
                                'name' => 'description',
                                'attributes' => ' required rows="3"',
                                'value' => $isEditing ? $post['description'] : ''
                            ]) ?>
                        </div>
                        <!-- details -->
                        <div class="col-md-6 mt-1 mt-md-2">
                            <?php echo textarea_input([
                                'label' => 'Post Details',
                                'name' => 'details',
                                'attributes' => ' required rows="3"',
                                'value' => $isEditing ? $post['details'] : ''
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <!-- details -->
                        <div class="col-md-6 mt-1 mt-md-2">
                            <?php echo textarea_input([
                                'label' => 'Meta Description #1',
                                'name' => 'meta_1',
                                'attributes' => '  rows="2" ',
                                'value' => $isEditing ? $post['meta_1'] : ''
                            ]) ?>
                        </div>

                        <div class="col-md-6 mt-1 mt-md-2">
                            <?php echo textarea_input([
                                'label' => 'Meta Description #2',
                                'name' => 'meta_2',
                                'attributes' => '  rows="2" ',
                                'value' => $isEditing ? $post['meta_2'] : ''
                            ]) ?>
                        </div>

                    </div>


                    <div class="row">
                        <!-- details -->
                        <div class="col-md-6 mt-1 mt-md-2">
                            <?php echo textarea_input([
                                'label' => 'Meta Description #3',
                                'name' => 'meta_3',
                                'attributes' => '  rows="2" ',
                                'value' => $isEditing ? $post['meta_3'] : ''
                            ]) ?>
                        </div>

                        <div class="col-md-6 mt-1 mt-md-2">
                            <?php echo textarea_input([
                                'label' => 'Meta Description #4',
                                'name' => 'meta_4',
                                'attributes' => '  rows="4" ',
                                'value' => $isEditing ? $post['meta_4'] : ''
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">

                        <div class="row">
                            <div class="col-12 ">
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