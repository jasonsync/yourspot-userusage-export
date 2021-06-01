
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- <script src="TableFilter/tablefilter.js"></script> -->
  <title>YourSpot CSV file manipulator</title>
</head>

<body>
  <h1>YourSpot CSV file manipulator</h1>
  <style>
    body {
      margin: 0;
      width: auto;
      font-family: sans-serif;
      background-color: #464646;
      color: #d6d6d6;
    }

    h1,
    h3 {
      text-align: center;
      padding: 20px;
    }

    .flex-container {
      display: flex;
      justify-content: center;
    }

  </style>
  <div class="flex-container">
    <form accept-charset="utf-8" enctype='multipart/form-data' action="upload.php" method="post" name="form">
      <h4>Step 1 - Select a .csv source file containing exported session data from yourspot portal.
        <br />Step 2 - Click "Process CSV File"<br />Step 3 - Your file will be downloaded once processed."<br />
      </h4>
      <input type="file" name="fileupload" value="" />
      <br />
      <br />
<label for="delimiter">Source File Row Separator</label>
      <input type="text" name="upload_delimiter" value=";" style="width:25px; padding:2px 5px; font-weight:bold;"/> - Leave alone if you are not sure
      <br /><br />
<label for="delimiter">Output File Row Separator</label>
      <input type="text" name="download_delimiter" value=";" style="width:25px; padding:2px 5px; font-weight:bold;"/> - Leave alone if you are not sure
      <br /><br />
      <input type="submit" name="btnupload" value="Process CSV File" />
    </form>
  </div>
</body>

</html>
