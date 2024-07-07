<?php

require_once('functions.php');

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

$PAGE_SECTION = 'Rooms';
$PAGE_TITLE = 'Rooms List';
require_once('dashboard-header.php');

$rooms = db_select('rooms');

?>
<!-- css assets/vendor/libs/datatables-bs5/datatables-bootstrap5.css -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a type="button" href="admin-room-create.php" class="btn rounded-pill btn-primary">Create new room</a>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered table-hover" id="category_tables">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rooms as $room) : ?>
                            <tr>
                                <td><?= $room['name'] ?></td>
                                <td><?= $room['price'] ?></td>
                                <td><?= $room['status'] ?></td>
                                <td>
                                    <img src="uploads/<?= $room['main_photo'] ?>" alt="<?= $room['name'] ?>" class="img-thumbnail" style="width: 100px;">
                                </td>
                                <td>
                                    <a href="admin-room-categories-create.php?id=<?= $room['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="admin-room-categories.php?delete=<?= $room['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
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