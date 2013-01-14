CKFinder Amazon S3 Connector
=============

This repository is a connector for CKFinder to Amazon's S3 storage service. It is a work in progress, but seems to be making steady progress.

Currently have implemented the following features/API calls for CKFinder:

1. GetFiles
2. GetFolders
3. CreateFolder
4. FileUpload
5. DeleteFile
6. DeleteFolder
7. RenameFile
8. RenameFolder
9. MoveFiles (untested because disabled in demo verison)
10. CopyFiles

I could use help on this project if anyone is familiar with this stuff. If you check my commits you can see I've organized 
them to hopefully allow you to see how I got to this point. All of the work is in /core/connector/s3. Work that needs to be done:

1. Implement thumbnails
2. Figure out the "ResourceType" system and how that fits into Amazon S3
3. Better checks and tests on each of the operations above ("check file exists" type stuff)
4. QuickUpload needs to be implemented (whatever that is?)
5. Testing

Installation
-------

1. Clone, or zip download the repo
2. Edit config.php to enter your $baseURL, $baseDir, and $config['AmazonS3'] parameters (AccessKey, Secret, and Bucket). I haven't tested root directory, so try it first as a subfolder under the root.
3. Config.js also has some settings changed in it. Shouldn't have to touch, but if you want to edit your existing installation you will have to change this file
3. Using a web server visit /_samples/standalone.html and it should work. Try uploading, and playing around with files.
