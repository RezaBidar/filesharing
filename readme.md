File Sharing
===
Information
---
* Problem number : 2 part 2
* Email : reza.smart306@gmail.com
* Programming Language : PHP
* Framework : Codeigniter

Requirements
---
1. PHP 5.4 or greater
2. MySql 5.1+
3. PHP extensions : GD2 , ZIP

_file folder inside project folder should has write premission_

Installation
---
1. write your database connection information in `path/to/project/application/config/database.php`
	```php

	//path/to/project/application/config/database.php
	$db['default'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => '',
	'password' => '',
	'database' => '',

	```
2. Load migrate controller `http://localhost/path/to/project/public_html/migrate`

How to use it
---
1. load `http://localhost/path/to/project/public_html/` .
2. Login page is default controller .
3. Click on `create new account` at below of login form and create new account .
4. After login you will push in your repository with 200 mb space .
	* each user has one repository but repositories can be share between users
5. by click on `upgrade panel` in top menu you will be able to add to your repository space .
6. In left side of your repository you see a list of user that can access to your repository .
	* by click on plus button you can add exists user to your repository
7. In the top menu by click on `friends repository` you can see a list of user that share their repository with you and by click on each user you can see their repository .
8. In the main repository page you see upload form and new folder from for uploading and creating new folder 
9. After uploading or creating folder you will be see file tree in repository 
	* by click on folder you will see folder content
	* by click on file you will see file preview
		* file preview for text and zip and pics is special (you will able to see their content and download file inside zip files)
10. after click on each file you see file preview
	1. by click on `get link for this file` , system generate a link for the file ( this link is public and you can share it with all people that doesnt has account in app) .
	2. after that you can see 2 button (`clear link`,`make link private`) , by click on clear link you can remove the link .
	3. by click on `make link private` this link will be accessable only for your repository members and users that you add in `Add user to access this file` form .

**PART 2:**
1. If you file extension be .txt , in file preview you will see `CILICK HERE TO EDIT THIS FILE` link at top of text preview . by click on that you can edit text file content .
2. In your repository in left side by click on plus(+) button you can add user to access to your repository . in add from in drop-down `select permision level` field , if you select `READ/WRITE/EDIT` option , the added user can edit text file ;

> Notice : upload allows type is defined in `path/to/project/application/config/my_app_config.php`




