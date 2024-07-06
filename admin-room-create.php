<?php

require_once('functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //check if name is submited
    if (!isset($_POST['name']) || empty($_POST['name'])) {
        $_SESSION['form_errors']['name'] = 'Name is required';
        header('Location: admin-room-categories-create.php');
        exit;
    }

    //check if template is submited
    if (!isset($_POST['template']) || empty($_POST['template'])) {
        $_SESSION['form_errors']['template'] = 'Template is required';
        header('Location: admin-room-categories-create.php');
        exit;
    }


    $image = null;
    if (isset($_FILES) && isset($_FILES['photo'])) {
        $resp = upload_image($_FILES['photo']);
        if (is_array($resp) && isset($resp['status']) && $resp['status'] === true) {
            $image = $resp['message'];
        }
    }


    //check if is editing
    if (isset($_POST['edit_id'])) {
        $id = $_POST['edit_id'];
        $data = [
            'name' => $_POST['name'],
            'template' => $_POST['template'],
            'details' => $_POST['details'],
        ];

        //check if image is uploaded
        if ($image != null && !empty($image)) {
            $data['photo'] = $image;
        }

        db_update('room_categories', $id, $data);

        alert_message('success', 'Room Category updated successfully.');
        header('Location: admin-room-categories.php');
        exit;
    } else {
        db_insert('room_categories', [
            'name' => $_POST['name'],
            'template' => $_POST['template'],
            'photo' => $image,
            'details' => $_POST['details'],
        ]);
        alert_message('success', 'Room Category created successfully.');
        header('Location: admin-room-categories.php');
    }

    exit;
}

$PAGE_SECTION = 'Rooms';
$PAGE_TITLE = 'Create Room';
$SECTION_LINK = 'admin-rooms.php';

$isEditing = false;
$id = null;
$room = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $room = db_find('rooms', $id);
    if ($room != null) {
        $room = true;
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
                        <div class="col-md-4 mt-1 mt-md-2">
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