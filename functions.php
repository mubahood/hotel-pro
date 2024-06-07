<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
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

    $view = <<<EOT
    <div class="mb-1">
        <label for="$name" class="form-label ">$label</label>
        <input type="$type" class="form-control $classes" value="$value" id="$name" name="$name" placeholder="Enter $label" $attributes>
        <div class="invalid-feedback">
            $error 
        </div>
    </div>
    EOT;
    return $view;
}
