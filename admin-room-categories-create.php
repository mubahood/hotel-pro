<?php

require_once('functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    echo '<pre>';

    if (isset($_FILES) && isset($_FILES['photo'])) {
        $resp = upload_image($_FILES['photo']);
        echo '<hr>';
        print_r($resp);
    }

    echo '</pre>';
    exit;

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

    db_insert('room_categories', [
        'name' => $_POST['name'],
        'template' => $_POST['template'],
        'details' => $_POST['details'],
    ]);
    alert_message('success', 'Room Category created successfully.');
    header('Location: admin-room-categories.php');
    exit;
}

$PAGE_SECTION = 'Room Categories';
$PAGE_TITLE = 'Create Room Category';

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
                    <div class="row">
                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo text_input([
                                'label' => 'Category Name',
                                'name' => 'name',
                                'attributes' => 'autofocus required '
                            ]) ?>
                        </div>
                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo select_input([
                                'label' => 'Room Template',
                                'name' => 'template',
                                'attributes' => ' required ',
                                'options' => [
                                    'TEMPLATE_1' => 'Template 1',
                                    'TEMPLATE_2' => 'Template 2',
                                    'TEMPLATE_3' => 'Template 3',
                                    'TEMPLATE_4' => 'Template 4',
                                ]
                            ]) ?>
                        </div>
                        <div class="col-md-4 mt-2 mt-md-1">
                            <?php echo text_input([
                                'label' => 'Category Photo',
                                'name' => 'photo',
                                'type' => 'file',
                                'attributes' => ''
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-2 mt-md-1">
                            <?php echo textarea_input([
                                'label' => 'Category Details',
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