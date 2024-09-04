Prerequisites

XAMPP (for PHP, MySQL, and Apache)
Composer (for managing dependencies)
Git (optional, if you want to clone the project from a repository)
XAMPP Setup
Start Apache and MySQL from the XAMPP Control Panel.
Open phpMyAdmin (can be accessed via http://localhost/phpmyadmin/) and create a new database, e.g., linkharvester.


project setup
git clone https://github.com/johnheaventerry-9/LinkHarvester
cd linkharvester

composer install

Update the database credentials in the .env file:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=linkharvester
DB_USERNAME=root
DB_PASSWORD= // leave empty if you didn't set a password

Run the migrations to create the necessary database tables:

php artisan migrate

To run the scraper command:

Ensure XAMPP's Apache and MySQL services are running.

Run the following command to execute the scraper:
php artisan fetch:links

Viewing the Data
To view the stored links:

Open phpMyAdmin (accessible at http://localhost/phpmyadmin/).
Select the linkharvester database.
Browse the links table to view the scraped and stored links.


access the frontend via http://127.0.0.1:8000/.
The page will display filter options (tags like php, vue, laravel, api), which allow you to filter and display links based on the tags


Some notes.

I kept a lot of the logs in as it made sense to me to see whats being scraped. Useful for double checking if anything has been missed that shouldn't of done. Again i'm aware this isn't always best practice as its more for debugging. i just think its a nice touch, especially if a link goes wrong down the line can always use it for debugging. 


Other things i'd do

- Improve command in so it takes a url instead of having a static one.
- Possibly add in an option of the command being able to scrape specifed pages, meaning you could pass 2 values from 30-120 and scrape each or something
more flexible.

- Improve styling on vue.js so it doesn't look like my first university project. Very barebones.


Final words

- if theres anything you see that i should be improving on, i'd very much appreciate the feedback given. Thanks for your time. And don't be afraid to email asking questions!

Cheers!
