<?php
require_once 'vendor/autoload.php';
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
$connectionString = "DefaultEndpointsProtocol=https;AccountName=blobstoragemacd;AccountKey=9Dec1o4RtNHLwnYY3zNYIkv3R2q8RGCT2Z4bzC2wN3pfsWF0JcbkkRlcIOHtmSfiZRGYsIJFjhtgYWN/KRS2zw==";
$containerName = "blob";

//create blob client
$blobClient = BlobRestProxy::createBlobService($connectionString);
  
if (isset($_POST['submit'])) {
  $fileToUpload = $_FILES["fileToUpload"]["name"];
  $content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
  echo fread($content, filesize($fileToUpload));
    
  $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
  header("Location: index.php");
} 
  
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Image Analyzer</title>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Image Analyzer</h1>
        <p class="text-center">We can find out how computer tells about image using azure cognitive service</p>

        <div class="form-group mt-5">
            <label for="exampleFormControlFile1">Let's start with uploading your images or photos, and then click "Start!" to analyze</label>
            <form action="index.php" method="post" enctype="multipart/form-data">
            <input type="file" class="form-control-file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
            <button type="submit" class="btn btn-primary mt-2" name="submit"><i class="fas fa-cloud-upload-alt"></i> Upload</button>
            </form>
        </div>

        <table class="table table-hover text-left">
			<thead>
				<tr>
					<th class="text-left">File Name</th>
					<th class="text-left">File URL</th>
					<th class="text-left">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
				do {
					foreach ($result->getBlobs() as $blob)
					{
						?>
						<tr>
							<td><?php echo $blob->getName() ?></td>
							<td><?php echo $blob->getUrl() ?></td>
							<td>
								<form action="computer-vision.php" method="post">
									<input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
									<input type="submit" name="submit" value="Start!" class="btn btn-success">
								</form>
							</td>
						</tr>
						<?php
					}
					$listBlobsOptions->setContinuationToken($result->getContinuationToken());
				} while($result->getContinuationToken());
				?>
			</tbody>
		</table>
    </div>
    

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="https://getbootstrap.com/docs/4.0/assets/js/vendor/popper.min.js"></script>
    <script src="https://getbootstrap.com/docs/4.0/dist/js/bootstrap.min.js"></script>
</body>
</html>