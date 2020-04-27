<?php
session_start();
include('db/config.php');
// Validating Session
if (strlen($_SESSION['userlogin']) == 0) {
    header('location:index.php');
} else {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <script src="js/jquery-3.5.0.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </head>

    <body>

        <div class="container">
            <br>
            <div class="float-right">
                <a href="logout.php" class="btn btn-large btn-info"><i class="icon-home icon-white"></i> Log me out</a>
            </div>

            <div class="container">

                <br><br>

                <div class="row justify-content-md-center">
                    <div class="hero-unit center">
                        <?php
                        // Code for fecthing user full name on the bassis of username or email.
                        $username = $_SESSION['userlogin'];
                        $query = $dbh->prepare("SELECT  fullname FROM userdata WHERE (username=:username || email=:username)");
                        $query->execute(array(':username' => $username));
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            $username = $row['fullname'];
                        }
                        ?>
                        <h1>Welcome Back <font face="Tahoma" color="red"><?php echo $username; ?> ! </font>
                        </h1>
                        <br />


                    </div>
                    <div class="alert alert-info" role="alert">
                        This software accept illumina default fastq extension _R1_001.fastq.gz _R2_001.fastq.gz
                    </div>
                    <div class="col col-lg-8">
                        <div class="file_drag_area text-center">
                            Drop files here or click on choose files
                        </div>
                        <br>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <br>
                        <form id="myform" method="post">

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="file">File Extension</label>
                                        <input type="file" id="file" class="form-control-file" name="file[]" accept=".fastq.gz" multiple>
                                    </div>
                                </div>

                            </div>


                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="project_name">Project Name</label>
                                        <input type="text" name="project_name" id="project_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="adapter">Adapter</label>
                                        <select id="adapter" class="form-control" name="adapter" required>
                                            <!-- <option hidden>--Adapter--</option> -->
                                            <option>NexteraPE</option>
                                            <option>TruSeq2</option>
                                            <option>TruSeq3</option>
                                            <option>TruSeq4</option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <button id="b_submit" type="submit" class="btn btn-success">Send</button>
                        </form>

                        <div id="selected_files"></div>

                        <!-- Display upload status -->
                        <div id="uploadStatus"></div>
                        <div id="uploaded_file">

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <script>
            var formData = '';
            $('.file_drag_area').on('dragover', function() {
                $(this).addClass('file_drag_over');
                return false;
            });
            $('.file_drag_area').on('dragleave', function() {
                $(this).removeClass('file_drag_over');

            });
            $('.file_drag_area').on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('file_drag_over');

                formData = new FormData();
                var files_list = e.originalEvent.dataTransfer.files;
                $('#selected_files').html('');
                if ((files_list.length % 2) == 0) {
                    $('#selected_files').append('<ul></ul>');
                    $('#uploaded_file').removeClass('alert alert-danger').html('');

                    for (var i = 0; i < files_list.length; i++) {
                        formData.append('file[]', files_list[i]);

                        $('#selected_files ul').append('<li>' + files_list[i].name + '</li>');
                    }
                } else {
                    $('#uploaded_file').html("sorry we accept only paired end data").addClass('alert alert-danger');
                    return;
                }
                //console.log(formData.length);


            });

            $('#file').change(function() {
                formData = new FormData();
                var allowedTypes = ['fastq.gz'];

                if (($(this).get(0).files.length % 2) == 0) {
                    $('#uploaded_file').removeClass('alert alert-danger').html('');
                    $('#selected_files').html('');
                    $('#selected_files').append('<ul></ul>');
                    $.map($('#file').get(0).files, function(file) {
                        formData.append('file[]', file);
                        //console.log(file);
                        $('#selected_files ul').append('<li>' + file.name + '</li>');
                    });
                } else {
                    $('#selected_files').html('');
                    $('#uploaded_file').html("sorry we accept only paired end data").addClass('alert alert-danger');
                    return;
                }


            });
            $('form').submit(function(e) {
                e.preventDefault();
                var regex = new RegExp('^[A-Z0-9._%+-]+_R[1-2]_001.fastq.gz$');

                formData.append('adapter', $('#adapter').val());
                formData.append('project', $('#project_name').val());
                // return;
                // formData.append('file[]' ,$('#file').serialize()) ;
                $.ajax({
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                //var percentComplete = ((evt.loaded / evt.total) * 100);
                                var percentComplete = Math.round((evt.loaded * 100) / evt.total);
                                $(".progress-bar").width(percentComplete + '%');
                                $(".progress-bar").html(percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    url: "upload.php",
                    method: "post",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $(".progress-bar").width('0%');
                        $('#uploadStatus').html('<img src="images/loading.gif"/>');
                    },
                    error: function() {
                        $('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');

                    },
                    success: function(resp) {
                        //console.log(resp);
                        if (resp == 'ok') {
                            //$('#uploadForm')[0].reset();
                            $('#uploadStatus').html('<p style="color:#28A74B;">File has uploaded successfully!</p>');
                        } else if (resp == 'err') {
                            $(".progress-bar").width('0%');
                            $(".progress-bar").html('');
                            $('#uploadStatus').html('<p style="color:#EA4335;">Please select a valid file to upload.</p>');
                        }
                    }
                });
            });
        </script>
    </body>

    </html>

<?php } ?>