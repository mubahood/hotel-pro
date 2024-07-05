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

$PAGE_SECTION = 'Room Categories';
$PAGE_TITLE = 'Create Room Category';
$SECTION_LINK = 'admin-room-categories.php';

$isEditing = false;
$id = null;
$category = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $category = db_find('room_categories', $id);
    if ($category != null) {
        $isEditing = true;
    }
}

if ($isEditing) {
    $PAGE_TITLE = 'Edit Room Category - #' . $id;
}

require_once('dashboard-header.php');




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
                        <input type="hidden" name="edit_id" value="<?= $category['id'] ?>">
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-8 mt-2 mt-md-1">
                            <?php echo text_input([
                                'label' => 'Category Name',
                                'name' => 'name',
                                'attributes' => 'autofocus required ',
                                'value' => $isEditing ? $category['name'] : ''
                            ]) ?>
                        </div>
                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo select_input([
                                'label' => 'Room Template',
                                'name' => 'template',
                                'attributes' => ' required ',
                                'value' => $isEditing ? $category['template'] : '',
                                'options' => [
                                    'TEMPLATE_1' => 'Template 1',
                                    'TEMPLATE_2' => 'Template 2',
                                    'TEMPLATE_3' => 'Template 3',
                                    'TEMPLATE_4' => 'Template 4',
                                ]
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mt-2 mt-md-1">
                            <?php echo text_input([
                                'label' => 'Category Photo',
                                'name' => 'photo',
                                'type' => 'file',
                                'attributes' => ''
                            ]) ?>
                        </div>
                        <!-- if is edit, display photo -->
                        <?php if ($isEditing) : ?>
                            <div class="col-md-4 mt-2 mt-md-1">
                                <img src="uploads/<?= $category['photo'] ?>" alt="<?= $category['name'] ?>" class="img-thumbnail" style="width: 100px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-2 mt-md-1">
                            <?php echo textarea_input([
                                'label' => 'Category Details',
                                'value' => $isEditing ? $category['details'] : '',
                                'name' => 'details',
                                'attributes' => ' rows="5" required '
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