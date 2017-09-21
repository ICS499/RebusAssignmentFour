<!DOCTYPE html>
<html>
<head>
    <?PHP
    session_start();
    require('session_validation.php');
    ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles/main_style.css" type="text/css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="javascript/typeahead.min.js"></script>
    <link rel="stylesheet" href="styles/custom_nav.css" type="text/css">
    <title>Rebus Add Word</title>


</head>
<body>
<?php
require('db_configuration.php');
require('InsertUtil.php');
?>
<?PHP echo getTopNav(); ?>
<div id="pop_up_fail" class="container pop_up" style="display:none">
    <div class="pop_up_background">
        <img class="pop_up_img_fail" src="pic/info_circle.png">
        <div class="pop_up_text">Incorrect! <br>Try Again!</div>
        <button class="pop_up_button" onclick="toggle_display('pop_up_fail')">OK</button>
    </div>
</div>
<div>
    <br><br>
    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
        <div class="col-xs-5">
        <div class="btn-group mr-2" role="group" aria-label="Second group">
            <button class="main-buttons" onClick="addTableRows('word_table')">Add Rows</button>
        </div>
        </div>
        <div class="btn-group mr-2" role="group" aria-label="Third group">
            <button type="submit" form="theForm" class="main-buttons" name="submit">Add Words</button>
        </div>
    </div>
    <br><br>
    <form action="" id="theForm"  method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
        <table class="table table-condensed main-tables" id="word_table" style="margin-left: 5%">
            <thead>
                <tr>
                    <th>Word</th>
                    <th>English Word</th>
                    <th>Image Thumbnail</th>
                </tr>
            </thead>
            <tbody id="formRows">
                <script>
                    var entry = 0;
                    function validateForm() {
                        var eng = document.forms["importFrom"]["fileToUpload"].value;
                        if (eng == "") {
                            document.getElementById("error").style = "display: block; background-color: #ce4646; padding:5px;color: #fff;";
                            return false;
                        }
                    }

                    function addTableRows() {
                        // No more than 10 words
                        if (entry > 9) { return };
                        var rows = document.getElementById("formRows");
                        // Create the entry row
                        var newEntryRow = document.createElement("tr");

                        // Word
                        var newEntryCell0 = document.createElement("td");
                        var newEntryField0 = document.createElement("input");
                        newEntryField0.setAttribute("type", "textbox");
                        newEntryField0.setAttribute("name", "word" + entry);
                        newEntryField0.setAttribute("id", "word" + entry);
                        newEntryCell0.appendChild(newEntryField0);

                        // English word
                        var newEntryCell1 = document.createElement("td");
                        var newEntryField1 = document.createElement("input");
                        newEntryField1.setAttribute("type", "textbox");
                        newEntryField1.setAttribute("name", "eng_word" + entry);
                        newEntryField1.setAttribute("id", "eng_word" + entry);
                        newEntryCell1.appendChild(newEntryField1);

                        // File to upload
                        var newEntryCell2 = document.createElement("td");
                        var newEntryField2 = document.createElement("input");
                        newEntryField2.setAttribute("class", "upload");
                        newEntryField2.setAttribute("type", "file");
                        newEntryField2.setAttribute("name", "fileToUpload" + entry);
                        newEntryField2.setAttribute("id", "fileToUpload" + entry);
                        newEntryCell2.appendChild(newEntryField2);

                        // Build the entry/row
                        newEntryRow.appendChild(newEntryCell0);
                        newEntryRow.appendChild(newEntryCell1);
                        newEntryRow.appendChild(newEntryCell2);
                        rows.appendChild(newEntryRow);

                        entry++;
                    }
                    // Add the first row upon reading of the javascript / page load
                    addTableRows();
                </script>
                <?php
                if (isset($_POST['submit'])) {
                    // Add all the submitted elements to three arrays, representing fields of each entry
                    $arrayWords = array();
                    $arrayEngWords = array();
                    $arrayImageNames = array();

                    $counter = 0;
                    while ($counter < 9) { // Not adding more than 10 words
                        if ( isset($_POST['word'.$counter]) && isset($_POST['eng_word'.$counter]) ) {
                            array_push($arrayWords, $_POST['word'.$counter]);
                            array_push($arrayEngWords, $_POST['eng_word'.$counter]);

                            // Check for image
                            if ( isset($_FILES["fileToUpload".$counter]['name'])){
                                $inputFileName = $_FILES["fileToUpload".$counter]["tmp_name"];
                                $target_File = "./Images/".basename($_FILES["fileToUpload".$counter]['name']);
                                $imageFileType = pathinfo($target_File,PATHINFO_EXTENSION);
                                $imageName = basename($_FILES["fileToUpload".$counter]["name"]);
                                if (!empty($imageName)) {
                                    copy($inputFileName, $target_File);
                                }
                                // Push empty string for no image, or push the name of the image to use
                                array_push($arrayImageNames, $imageName);
                            }
                        } else {
                            break; // The goal being to break the loop when there are no more entries
                        }

                        $counter++;
                    }

                    // Add each row
                    for ($i = 0; $i < count($arrayWords); $i++) {
                        insertIntoWordsTable($arrayWords[$i], $arrayEngWords[$i], $arrayImageNames[$i]);
                    }
                }
                ?>
            </tbody>
        </table>
    </form>
</div>
</body>
</html>
