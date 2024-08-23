<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$CURRENCY = 'UGX';

function is_logged_in()
{
    $loggedIn = false;
    if (isset($_SESSION['user'])) {
        if (isset($_SESSION['user']['id'])) { {
                if ($_SESSION['user']['id'] > 0) {
                    $loggedIn = true;
                }
            }
        }
    }
    return $loggedIn;
}

function get_active_menu_class($path)
{

    $path2 = $_SERVER['REQUEST_URI'];

    $splits = explode('/', $path2);
    //check if splits is empty
    if (empty($splits)) {
        return '';
    }
    $last_seg = end($splits);

    if ($last_seg == $path) {
        return 'active';
    }
    return '';
}
function url($path = null)
{
    $BASE_URL = 'http://localhost:8888/hotel-pro';


    if ($path == null || $path == '') {
        return $BASE_URL;
    }


    //check if path starts with /
    if (substr($path, 0, 1) == '/') {
        return $BASE_URL . $path;
    }
    return $BASE_URL . '/' . $path;
}


function short($text, $limit = 100)
{
    if (strlen($text) > $limit) {
        return substr($text, 0, $limit) . '...';
    }
    return $text;
}

//image uploading function
function upload_image($file)
{
    $resp['status'] = false;
    $resp['message'] = false;

    //if $file is not an array
    if (!is_array($file)) {
        $resp['message'] = 'Invalid file';
        return $resp;
    }

    //check if name is set
    if (!isset($file['name'])) {
        $resp['message'] = 'File name is required';
        return $resp;
    }

    //check if tmp_name is set
    if (!isset($file['tmp_name'])) {
        $resp['message'] = 'File tmp_name is required';
        return $resp;
    }

    //check if error is set
    if (!isset($file['error'])) {
        $resp['message'] = 'File error is required';
        return $resp;
    }

    //check if file has error
    if ($file['error'] !== 0) {
        $resp['message'] = 'File has error';
        return $resp;
    }

    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];
    $file_type = $file['type'];

    $explods = explode('.', $file_name);

    if (count($explods) < 2) {
        $resp['message'] = 'Invalid file name';
        return $resp;
    }
    $file_ext = end($explods);
    $allowed = ['jpg', 'jpeg', 'png'];

    if (!in_array($file_ext, $allowed)) {
        $resp['message'] = 'File type not allowed';
        return $resp;
    }

    if ($file_error !== 0) {
        $resp['message'] = 'File has error';
        return $resp;
    }

    $file_name_new = uniqid('', true) . '.' . $file_ext;
    $file_destination = 'uploads/' . $file_name_new;

    if (move_uploaded_file($file_tmp, $file_destination)) {
        $resp['status'] = true;
        $resp['message'] = $file_name_new;
        return $resp;
    } else {
        $resp['message'] = 'File upload failed';
        return $resp;
    }
    return $resp;
}


//alert message function
function alert_message($type, $data)
{
    $_SESSION['alert_message'] = [
        'type' => $type,
        'message' => $data
    ];
}



function db_find($table, $id)
{
    $data = db_select($table, " id = $id ");
    if (count($data) > 0) {
        return $data[0];
    }
    return null;
}

//db_delete
function db_delete($table, $id)
{
    $conn = db_connect();
    $sql = "DELETE FROM $table WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        return true;
    } else {
        return false;
    }
}


function db_update($table, $id, $data)
{
    $conn = db_connect();
    $fields = '';
    foreach ($data as $key => $value) {
        $value = mysqli_real_escape_string($conn, $value);
        $fields .= "$key = '$value',";
    }
    $fields = rtrim($fields, ',');
    $sql = "UPDATE $table SET $fields WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        return true;
    } else {
        return false;
    }
}

function db_select($table, $where = null, $fields = '*')
{
    $conn = db_connect();

    $sql = "SELECT $fields FROM $table";
    if ($where != null) {
        $sql .= " WHERE $where";
    }
    $result = mysqli_query($conn, $sql);
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}


function db_insert($table, $data)
{
    $conn = db_connect();
    $fields = implode(',', array_keys($data));

    //string escape
    foreach ($data as $key => $value) {
        $data[$key] = mysqli_real_escape_string($conn, $value);
    }
    $values = "'" . implode("','", array_values($data)) . "'";
    $sql = "INSERT INTO $table ($fields) VALUES ($values)";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        return true;
    } else {
        return false;
    }
}

function db_connect()
{
    $conn = mysqli_connect('localhost', 'root', 'root', 'hotel_pro');
    if (!$conn) {
        die('Database connection failed');
    }
    return $conn;
}


//text input
function text_input($param)
{
    $label = 'Field';
    $name = null;
    $type = 'text';
    $value = null;
    $classes = null;
    $error = null;
    $attributes = null;



    if (is_array($param)) {
        $name = isset($param['name']) ? $param['name'] : $name;
        $label = isset($param['label']) ? $param['label'] : $label;
        $type = isset($param['type']) ? $param['type'] : $type;
        $value = isset($param['value']) ? $param['value'] : $value;
        $classes = isset($param['classes']) ? $param['classes'] : $classes;
        $error = isset($param['error']) ? $param['error'] : $error;
        $attributes = isset($param['attributes']) ? $param['attributes'] : $attributes;
    } else {
        throw new Exception('Invalid parameter');
    }

    if ($name == null) {
        throw new Exception('Name is required');
    }

    if (isset($_SESSION['form_data'])) {
        $value = isset($_SESSION['form_data'][$name]) ? $_SESSION['form_data'][$name] : $value;
    }

    if (isset($_SESSION['form_errors']) && isset($_SESSION['form_errors'][$name])) {
        $error = isset($_SESSION['form_errors'][$name]) ? $_SESSION['form_errors'][$name] : $value;
    }


    if ($error != null) {
        $classes .= ' is-invalid';
    }

    $required_text = '';
    if (strpos($attributes, 'required') !== false) {
        $required_text = ' <sup class="text-danger">*</sup>';
    }

    $view = <<<EOT
    <div class="mb-1">
        <label for="$name" class="form-label ">
        $label $required_text     
        </label>
        <input type="$type" class="form-control $classes" value="$value" id="$name" name="$name" placeholder="Enter $label" $attributes>
        <div class="invalid-feedback">
            $error 
        </div>
    </div>
    EOT;
    return $view;
}



//text input
function select_input($param)
{
    $label = 'Field';
    $name = null;
    $type = 'text';
    $value = null;
    $classes = null;
    $error = null;
    $attributes = null;
    $options = [];



    if (is_array($param)) {
        $name = isset($param['name']) ? $param['name'] : $name;
        $label = isset($param['label']) ? $param['label'] : $label;
        $type = isset($param['type']) ? $param['type'] : $type;
        $value = isset($param['value']) ? $param['value'] : $value;
        $classes = isset($param['classes']) ? $param['classes'] : $classes;
        $error = isset($param['error']) ? $param['error'] : $error;
        $attributes = isset($param['attributes']) ? $param['attributes'] : $attributes;

        //check if options is and is array
        if (isset($param['options']) && is_array($param['options'])) {
            $options = $param['options'];
        }
    } else {
        throw new Exception('Invalid parameter');
    }

    if ($name == null) {
        throw new Exception('Name is required');
    }

    if (isset($_SESSION['form_data'])) {
        $value = isset($_SESSION['form_data'][$name]) ? $_SESSION['form_data'][$name] : $value;
    }

    if (isset($_SESSION['form_errors']) && isset($_SESSION['form_errors'][$name])) {
        $error = isset($_SESSION['form_errors'][$name]) ? $_SESSION['form_errors'][$name] : $value;
    }


    if ($error != null) {
        $classes .= ' is-invalid';
    }

    $required_text = '';
    if (strpos($attributes, 'required') !== false) {
        $required_text = ' <sup class="text-danger">*</sup>';
    }
    $drop_down = '<option >Select ' . $label . '</option>';

    foreach ($options as $key1 => $option) {
        $selected = '';
        if ($value == $key1) {
            $selected = 'selected';
        }
        $drop_down .= '<option value="' . $key1 . '" ' . $selected . '>' . $option . '</option>';
    }

    $view = <<<EOT
    <div class="mb-1">
        <label for="$name" class="form-label ">
        $label $required_text     
        </label>
        <select class="form-control $classes" id="$name" name="$name" $attributes>
            $drop_down
        </select>
        <div class="invalid-feedback">
            $error 
        </div>
    </div>
    EOT;
    return $view;
}



//text area
function textarea_input($param)
{
    $label = 'Field';
    $name = null;
    $type = 'text';
    $value = null;
    $classes = null;
    $error = null;
    $attributes = null;



    if (is_array($param)) {
        $name = isset($param['name']) ? $param['name'] : $name;
        $label = isset($param['label']) ? $param['label'] : $label;
        $type = isset($param['type']) ? $param['type'] : $type;
        $value = isset($param['value']) ? $param['value'] : $value;
        $classes = isset($param['classes']) ? $param['classes'] : $classes;
        $error = isset($param['error']) ? $param['error'] : $error;
        $attributes = isset($param['attributes']) ? $param['attributes'] : $attributes;
    } else {
        throw new Exception('Invalid parameter');
    }

    if ($name == null) {
        throw new Exception('Name is required');
    }

    if (isset($_SESSION['form_data'])) {
        $value = isset($_SESSION['form_data'][$name]) ? $_SESSION['form_data'][$name] : $value;
    }

    if (isset($_SESSION['form_errors']) && isset($_SESSION['form_errors'][$name])) {
        $error = isset($_SESSION['form_errors'][$name]) ? $_SESSION['form_errors'][$name] : $value;
    }


    if ($error != null) {
        $classes .= ' is-invalid';
    }

    $required_text = '';
    if (strpos($attributes, 'required') !== false) {
        $required_text = ' <sup class="text-danger">*</sup>';
    }

    $view = <<<EOT
    <div class="mb-1">
        <label for="$name" class="form-label ">
        $label $required_text     
        </label>
        <textarea class="form-control $classes"  id="$name" name="$name" placeholder="Enter $label" $attributes>$value</textarea>
        <div class="invalid-feedback">
            $error 
        </div>
    </div>
    EOT;
    return $view;
}
