<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID NO Detection Upload</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
        }
        input, button {
            margin-top: 10px;
            padding: 10px;
        }
        #result {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2>Upload an Image/Document  for ID NUMBER  Detection</h2>
    <div class="error" id="payslipErr"></div>

    <form id="uploadForm" enctype="multipart/form-data" method="post">
        @csrf
        <input type="file" id="image" name="image" accept="image/*" required>
        <input type="text" id="idno" name="idno" placeholder="Enter ID Number" required>

        <button type="submit" id="btn-save-toform">Upload & Detect</button>
    </form>

    <p id="result"></p>
    <img id="preview" style="display:none; max-width:300px; margin-top: 10px;" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.getElementById("image").addEventListener("change", function(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var preview = document.getElementById("preview");
                preview.src = reader.result;
                preview.style.display = "block";
            }
            reader.readAsDataURL(event.target.files[0]);
        });

        $("#uploadForm").submit(function (e) {
            e.preventDefault();

            var submitButton = document.querySelector("#btn-save-toform");
            if (submitButton) {
                submitButton.disabled = true;
            }

            var imageupload = document.getElementById("image").value;
            var image = document.getElementById("image").files[0];
            var idno = document.getElementById("idno").value; // Get the idno value

            // Allow only JPEG, PNG, and JPG
            var allowedExtensions = /(\.jpeg|\.png|\.jpg|\.pdf)$/i;

            if (!allowedExtensions.exec(imageupload)) {
                document.getElementById("payslipErr").innerText = "Please upload a VALID file (JPEG, PNG, JPG,PDF)";
                return;
            }

            var formData = new FormData(this);
            formData.append("idno", idno); // Append idno to FormData

            $.ajax({
                data: formData,
                url: "/uploadanddetectidno",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    console.log("success:", data);
                   // $("#result").html(data.message);
                   $("#result").html(`
                <div>Message: ${data.message}</div>
                <div>Extracted Text: ${data.extracted_text}</div>
                <div>Status: ${data.status ? 'Valid' : 'Invalid'}</div>
            `);
                    submitButton.disabled = false;
                },
                error: function (data) {
                    console.log("Error:", data);
                  //  $("#result").html(data.message,data.extracted_text,data.status);
            //       $("#result").html(`
            //     <div>Message: ${data.message}</div>
            //     <div>Extracted Text: ${data.extracted_text}</div>
            //     <div>Status: ${data.status ? 'Valid' : 'Invalid'}</div>
            // `);
                    //$("#result").html("An error occurred while processing the image.");
                    submitButton.disabled = false;
                }
            });
        });
    </script>
</body>
</html>