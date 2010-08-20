# Installation

1. Download the latest **stable** release from the [Kohana website](http://kohanaframework.org/download).
2. Unzip the downloaded package to create a `kohana` directory. Contents of the `kohana` directory would look like the regular [Kohana file structure](intro.file-structure). You can use a different directory when needed.
3. Upload the contents of this folder to your webserver. If your webserver is your local development machine, copy it to the webserver's document root:
	- `/var/www/htdocs` - for most Linux system
	- `C:\Program Files\Apache Software Foundation\Apache2.2\htdocs` - for most Apache in Windows
	- `C:\xammp\htdocs` - for most XAMMP installations
	- If you have a different setup or different operating system (ex: Mac), copy the files to your web server's document root. 
	- You may copy it as-is: copying the whole directory `kohana` and no configuration is needed, or copy the contents to directly to the document root but you need to change some configurations that will be discussed later on.
	- You can also create a virtual host and copy the files to it. However, creating virtual host is not covered in this documentation.
4. Open `application/bootstrap.php` and make the following changes:
	- Set the default [timezone](http://php.net/timezones) for your application. For example, I changed it to `Asia/Manila` because I' living in the Philippines.
	- Set the `base_url` in the [Kohana::init] call to reflect the location of the kohana folder on your server.
		- If the directory name is `kohana` and it is directly under the webserver's document root, change `base_url` to `/kohana/`.
		- If the directory name is `stealthproject` and is also directly under the document root directory, change `base_url` to `/stealthproject`.
		- If you copied the contents directly to the document root, change `base_url` to `/`.
6. Make sure the `application/cache` and `application/logs` directories are writable by the web server.
	- For Linux systems, set the following permissions / ownership:
		- For a standard setup, run the `chown` command to set Apache user able to write into the cache and logs directory. If your username is `lonedev` and your Apache's username is `apache`, simply execute this command as root:
			- `cd /var/www/htdocs/yourprojectdir`
			- `chown -R lonedev:apache cache logs
		- For a quick and dirty setup (which means insecure), you can just change permission to grant read/write/execute for everone:
			- `chmod 777 -R cache logs`
	- Most Windows installation doesn't need such permission modification not unless your Apache is installed in a different way.
7. Test your installation by opening the URL you set as the `base_url` in your favorite browser.
	- example: `http://localhost/kohana`

[!!] Depending on your platform, the installation's subdirs may have lost their permissions thanks to zip extraction. Chmod them all to 755 by running `find . -type d -exec chmod 0755 {} \;` from the root of your Kohana installation.

You should see the installation page. If it reports any errors, you will need to correct them before continuing.

![Install Page](/media/images/install.png "Example of install page")

Once your install page reports that your environment is set up correctly you need to either rename or delete `install.php` in the project root directory. You should then see the Kohana welcome page:

![Welcome Page](/media/images/welcome.jpg "Example of welcome page")

Don't worry if that is all you got. Kohana did't create a fancy default welcome page.
