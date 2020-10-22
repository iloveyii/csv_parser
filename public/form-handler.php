<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2020-10-22
 * Time: 10:26
 */


ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

echo $rrr;
$FILE_SIZE_THRESHOLD = 1024 * 500;
$ALLOWED_FILE_TYPES = ['text/plain'];
$ALLOWED_FILE_EXTENSIONS = ['csv'];


$errors = [];

$targetDirectory = '../csv_files/';
$fileName = sprintf("%s%s", $targetDirectory, basename($_FILES['csv_file']['name']));
$bodyHtml = '';

function parseCsv($fileName, $header = false)
{
    $fileContents = file_get_contents($fileName);
    $fileRows = explode("\n", $fileContents);
    $fileContentsArray = [];

    foreach ($fileRows as $row) {
        $rowArray = explode(',', $row);
        array_push($fileContentsArray, $rowArray);
    }

    return $fileContentsArray;
}

function makeRow($row, $th = false)
{
    $rowHtml = '<tr>';
    $tag = $th ? 'th' : 'td';
    foreach ($row as $td) {
        $rowHtml .= "<{$tag}>{$td}</{$tag}>";
    }

    $rowHtml .= '</tr>';

    return $rowHtml;
}

function arrayToTable($data)
{
    $header = array_shift($data);
    $headerHtml = makeRow($header, true);
    $bodyHtml = '';
    foreach ($data as $row) {
        // print_r($row);
        $bodyHtml .= makeRow($row);
    }

    $tableHtml = "
    <table class='table table-hover'>
      <thead>
        {$headerHtml}
      </thead>
      <tbody> 
        {$bodyHtml}
      </tbody>
    </table>
";

    return $tableHtml;
}

// Validate file characters, dir non public

function validateForm() {

    global $errors;
    $name = $_POST["name"];
    if(empty($name)) {
        $errors['name'] = "Name cannot be empty";
    }

    if(ctype_upper($name[0]) == false) {
        $errors['name'] = "First character of name must be uppercase";
    }

    if (!preg_match("/^[a-zA-Z-' ]*$/",$name)) {
        $errors['name'] = "Only letters and white space allowed";
    }

    $email = $_POST["email"];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }
}

// Validate form submit
if (isset($_POST['name'])) {

    // Validate form inputs
    validateForm();

    // Check that file size does not exceed the limit - $FILE_SIZE_THRESHOLD
    $checkFileSize = $_FILES['csv_file']['size'];
    if ($checkFileSize > $FILE_SIZE_THRESHOLD) {
        $errors['file_size'] = "File size exceeds the limit {$FILE_SIZE_THRESHOLD}";
    }
    if ($checkFileSize == 0) {
        $errors['file_size'] = "File size is zero, empty file!";
    }


    // Check file extension
    $extensionType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($extensionType, $ALLOWED_FILE_EXTENSIONS)) {
        $errors['file_extension'] = "File extension is not in the allowed extensions : " . implode(', ', $ALLOWED_FILE_EXTENSIONS);
    }

    // Check the actual file format - not extension
    $tmp_file_name = $_FILES["csv_file"]["tmp_name"];
    $fInfo = finfo_open(FILEINFO_MIME_TYPE); // return mime-type extension
    $fileType = finfo_file($fInfo, $tmp_file_name);

    finfo_close($fInfo);
    if (!in_array($fileType, $ALLOWED_FILE_TYPES)) {
        $errors['file_type'] = "File type {$fileType} is not in the allowed formats : " . implode(', ', $ALLOWED_FILE_TYPES);
    }

    // Move uploaded file to target directory - $targetDirectory
    if (count($errors) == 0) {
        $moved = move_uploaded_file($_FILES["csv_file"]["tmp_name"], $fileName);

        if ($moved) {
            // echo "The file " . htmlspecialchars(basename($_FILES["csv_file"]["name"])) . " has been uploaded.";
            $data = parseCsv($fileName);
            $bodyHtml = arrayToTable($data);
        } else {
            $errors['move_file'] = "There was an error in moving the file to target directory {$moved}";
        }
    } else {
        // print_r($errors);
    }

} ?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/css/bootstrap.min.css"
          integrity="sha512-oc9+XSs1H243/FRN9Rw62Fn8EtxjEYWHXRvjS43YtueEewbS6ObfXcJNyohjHqVKFPoXXUxwc+q1K7Dee6vv9g=="
          crossorigin="anonymous"/>
    <link rel="stylesheet" href="/css/form.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"
            integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg=="
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/js/bootstrap.min.js"
            integrity="sha512-8qmis31OQi6hIRgvkht0s6mCOittjMa9GMqtK9hes5iEQBQE/Ca6yGE5FsW36vyipGoWQswBj/QBm2JR086Rkw=="
            crossorigin="anonymous"></script>
    <title>CSV Parser</title>
</head>
<body class="text-center">

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a class="btn btn-success" href="/">Back</a>
                <?php if(count($errors) == 0) : ?>
                    <h3>CSV File Data</h3>
                    <p>Name : <?php echo $_POST['name']; ?></p>
                    <p>Email : <?php echo $_POST['email']; ?></p>
                    <br>
                    <?php echo $bodyHtml; ?>
                <?php else : ?>
                    <h3 class="text-danger">Errors</h3>
                    <br>
                    <ul style="text-align: left">
                    <?php foreach ($errors as $type => $message) : ?>
                        <li><strong><?php echo $type ?></strong> <?php echo $message ?></li>

                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>