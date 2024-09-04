Link Harvester - Laravel Console Command Project
This project is a simple Laravel application that scrapes links from a Pinboard user profile, processes them, and stores them in a database based on matching tags.

Prerequisites
Before getting started, make sure you have the following installed:

XAMPP (for PHP, MySQL, and Apache)
Composer (for managing dependencies)
Google Chrome (if required for scraping)
Git (optional, if you want to clone the project from a repository)
XAMPP Setup
Start Apache and MySQL from the XAMPP Control Panel.
Open phpMyAdmin (can be accessed via http://localhost/phpmyadmin/) and create a new database, e.g., linkharvester.
Project Setup
Clone the project (or download the files):



git clone https://github.com/your-repository/linkharvester.git
cd linkharvester
Install dependencies using Composer:


composer install
Create a copy of the .env file:


cp .env.example .env
Set up environment variables in .env:

Update the database credentials in the .env file:
env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=linkharvester
DB_USERNAME=root
DB_PASSWORD= // leave empty if you didn't set a password
Generate the application key:


php artisan key:generate
Run the migrations to create the necessary database tables:

php artisan migrate
Running the Link Scraper Command
This project includes a custom command to fetch and store links from a Pinboard user's public bookmarks.

To run the scraper command:

Ensure XAMPP's Apache and MySQL services are running.
Run the following command to execute the scraper:

php artisan fetch:links
The scraper will fetch links from a Pinboard user profile, process them, and store the ones with matching tags (laravel, php, vue, api) in the links table of your database.


Viewing the Data
To view the stored links:

Open phpMyAdmin (accessible at http://localhost/phpmyadmin/).
Select the linkharvester database.
Browse the links table to view the scraped and stored links.