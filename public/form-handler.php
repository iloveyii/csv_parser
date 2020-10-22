<?php
/**
 * Created by PhpStorm.
 * User: H Ali
 * Date: 2020-10-22
 * Time: 10:26
 */


// For production use disable errors
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

//
$FILE_SIZE_THRESHOLD = 1024 * 500;      // 500 MB
$ALLOWED_FILE_TYPES = ['text/plain'];   // Actual file format
$ALLOWED_FILE_EXTENSIONS = ['csv'];     // extensions allowed
$TARGET_DIRECTORY = '../csv_files/';     // Target directory where to save csf uploaded files
$errors = [];                           // Array that holds all errors


/**
 * This function takes the file path as input and parses csv file to array
 *
 * @param $fileName string - file path to csv file
 * @param $header bool - if the csv file has a header row
 *
 * @return array
 */
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

/**
 * This function takes an associative array and makes an html row
 *
 * @param $row array - a key value array
 * @param $th bool - if it is a td or th
 *
 * @return string
 */

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

/**
 * This function converts a 2D array to html table
 * @param $data array of arrays - An array of associative arrays
 *
 * @return string
 */
function arrayToTable($data)
{
    $header = array_shift($data);
    $headerHtml = makeRow($header, true);
    $tbodyHtml = '';
    foreach ($data as $row) {
        // print_r($row);
        $tbodyHtml .= makeRow($row);
    }

    $tableHtml = "
    <table class='table table-hover'>
      <thead>
        {$headerHtml}
      </thead>
      <tbody> 
        {$tbodyHtml}
      </tbody>
    </table>
";

    return $tableHtml;
}


/**
 * This function validates input name and email, places errors in global $errors array
 *
 * @return void
 */
function validateForm()
{

    global $errors;
    $name = $_POST["name"];
    if (empty($name)) {
        $errors['name'] = "Name cannot be empty";
    }

    if (ctype_upper($name[0]) == false) {
        $errors['name'] = "First character of name must be uppercase";
    }

    if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
        $errors['name'] = "Only letters and white space allowed";
    }

    $email = $_POST["email"];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }
}


/**
 * This function handles the form submit
 *  - Validates form input
 *  - Validates uploading file and moves it to $TARGET_DIRECTORY
 *  - Returns the html table in string format
 *
 * @return  string
 */
function handleFormSubmit()
{
    global $errors, $FILE_SIZE_THRESHOLD, $TARGET_DIRECTORY, $ALLOWED_FILE_EXTENSIONS, $ALLOWED_FILE_TYPES;

    $tableHtml = '';

    if (isset($_POST['name'])) {

        // Validate form inputs
        validateForm();

        // File name for the uploading file
        $fileName = sprintf("%s%s", $TARGET_DIRECTORY, basename($_FILES['csv_file']['name']));


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
        $tmpFileName = $_FILES["csv_file"]["tmp_name"];
        $fInfo = finfo_open(FILEINFO_MIME_TYPE); // return mime-type extension
        $fileType = finfo_file($fInfo, $tmpFileName);
        finfo_close($fInfo);

        if (!in_array($fileType, $ALLOWED_FILE_TYPES)) {
            $errors['file_type'] = "File type {$fileType} is not in the allowed formats : " . implode(', ', $ALLOWED_FILE_TYPES);
        }

        // Move uploaded file to target directory - $targetDirectory
        if (count($errors) == 0) {
            $moved = move_uploaded_file($_FILES["csv_file"]["tmp_name"], $fileName);

            if ($moved) {
                $data = parseCsv($fileName);
                $tableHtml = arrayToTable($data);
            } else {
                $errors['move_file'] = "There was an error in moving the file to target directory {$moved}";
            }
        }

    }

    return $tableHtml;
}

?>

<?php require_once('./inc/header.php') ?>

<body class="text-center">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php $bodyHtml = handleFormSubmit(); ?>
                <a class="btn btn-success" href="/">Back</a>
                <?php if (count($errors) == 0) : ?>
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

<?php require_once('./inc/footer.php') ?>