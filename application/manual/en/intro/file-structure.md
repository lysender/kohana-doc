# File Structure

Kohana uses a simple file and directory structure to organize your project. It is recommended to follow this structure because most of the modules available assumes this structure. However, you are free to create your own structure when needed.

Below is the basic structure for a typical Kohana application. Names ending with &quot;/&quot; (forward slash) are directories and others are regular files. Indentions indicates subdirectory or subdirectories (folder / subfolders). 

	application/
		cache/
		classes/
			controller/
			model/
		config/
		i18n/
		logs/
		messages/
		views/
		bootstrap.php
	modules/
	system/
	index.php
	.htaccess

## Other Variations

For static files such as css, JavaScripts and images, I recommend you to put them on media directory (folder). Below is the sample file structure after we add the media directory (folder).

	application/
		cache/
		classes/
			controller/
			model/
		config/
		i18n/
		logs/
		messages/
		views/
		bootstrap.php
	modules/
	system/
	media/
		css/
		js/
		img/
	index.php
	.htaccess
