- The website loads the folders and calculates total amount of files (with subfolders) and getting the total size of all these files.
- It's quick and dirty! Just put the `.htaccess` and `index.php` files in root of the folder you want to get information about and you're good to go!

**Important!**
If you want to put the files in a folder that are not in root of the file server (example: `/var/www/`), you must change the lines 19, 35, and 40 in `.htaccess` to the name of the folder. If you don't do this, Explorer thinks you want to explore for a example `/var/www/`.
