<?php

require_once('functions.php');

//check if delete is set
if (isset($_GET['duplicate'])) {
    $id = $_GET['duplicate'];

    $original = db_find('rooms', $id);
    if ($original == null) {
        alert_message('danger', 'Room not found.');
        header('Location: admin-rooms.php');
        exit;
    }
    $duplicate = $original;
    unset($duplicate['id']);
    $duplicate['name'] = $original['name'] . ' (Copy)';

    //insert
    db_insert('rooms', $duplicate);

    alert_message('success', 'Room duplicated successfully.');
    header('Location: admin-rooms.php');
    exit;
}
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $category = db_find('rooms', $id);
    if ($category == null) {
        alert_message('danger', 'Room not found.');
        header('Location: admin-rooms.php');
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

    db_delete('rooms', $id);
    alert_message('success', 'Room deleted successfully.');
    header('Location: admin-rooms.php');
    exit;
}

$PAGE_SECTION = 'Posts';
$PAGE_TITLE = 'Posts List';
require_once('dashboard-header.php');

$rooms = db_select('rooms');

?>
<!-- css assets/vendor/libs/datatables-bs5/datatables-bootstrap5.css -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a type="button" href="admin-posts-create.php" class="btn rounded-pill btn-primary">Create new post</a>
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
                                    <a href="admin-rooms.php?duplicate=<?= $room['id'] ?>" class="btn btn-sm btn-info">Duplicate</a>
                                    <a href="admin-posts-create.php?id=<?= $room['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="admin-rooms.php?delete=<?= $room['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
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