<?php


$PAGE_SECTION = 'Room Categories';
$PAGE_TITLE = 'Categories List';
require_once('dashboard-header.php');

$room_categories = db_select('room_categories');
/* 
    [1] => Array
    (
        [id] => 2
        [name] => Maggy Hatfield
        [details] => Ratione voluptates p
        [photo] => 6685234c8bcce8.09769798.png
        [template] => TEMPLATE_3
    )
*/

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <h5 class="card-header">
                <a type="button" href="admin-room-categories-create.php" class="btn rounded-pill btn-primary">Create new category</a>
            </h5>
            <div class="card-body">
                <table class="table table-striped table-bordered table-hover table-checkable" id="category_tables">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Room Template</th>
                            <th>Category Photo</th>
                            <th>Category Details</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($room_categories as $category) : ?>
                            <tr>
                                <td><?= $category['name'] ?></td>
                                <td><?= $category['template'] ?></td>
                                <td>
                                    <img src="uploads/<?= $category['photo'] ?>" alt="<?= $category['name'] ?>" class="img-thumbnail" style="width: 100px;">
                                </td>
                                <td><?= $category['details'] ?></td>
                                <td>
                                    <a href="admin-room-categories-edit.php?id=<?= $category['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="admin-room-categories-delete.php?id=<?= $category['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
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

<!-- css assets/vendor/libs/datatables-bs5/datatables-bootstrap5.css -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
<script>
    $(document).ready(function() {
        $('#category_tables').DataTable();
    });
</script>