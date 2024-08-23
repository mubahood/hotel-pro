<?php
require_once('functions.php');

if (isset($_GET['id']) && isset($_GET['photo']) && isset($_GET['type'])) {
    $id = $_GET['id'];
    $photo = $_GET['photo'];
    $type = $_GET['type'];

    if ($type == 'room') {
        $room = db_find('rooms', $id);
        if ($room == null) {
            die('Room not found.');
        }
        $existing_gallery_photos = [];
        if ($room['gallery_photos'] != null && strlen($room['gallery_photos']) > 4) {
            $existing_gallery_photos = json_decode($room['gallery_photos']);
        }

        if (!is_array($existing_gallery_photos)) {
            $existing_gallery_photos = [];
        }

        $new_gallery_photos = [];

        foreach ($existing_gallery_photos as $existing_gallery_photo) {
            if ($existing_gallery_photo != $photo) {
                $new_gallery_photos[] = $existing_gallery_photo;
            }
        }


        //check if file exists
        $file = 'uploads/' . $photo;
        if (file_exists($file)) {
            unlink($file);
        }
        $data['gallery_photos'] = json_encode($new_gallery_photos);
        db_update('rooms', $id, $data);
        die('Room photo deleted successfully.');
        exit;
    }
}
