# Tots and Blocks Inventory Managment System
The Tots and Blocks Inventory Management System will allow the owner
to manage the inventory of the academy and preschool locations.

## Description
The application will address the following problems that the owner currently experiences:
* Difficulty keeping track of the inventory of items/supplies at their academy and preschool.
* Unnecessary spending on supplies they have plenty of stock.
* Eliminate last minute orders for supplies.
* Delay in activities due to supply shortage.

## Features
The application allows the owner to manage items and their stock information, and view and print
reports of the inventory based on the owner's given criteria.

### Manage Items
To accurately maintain records of the items available at the Tots and Blocks location; the application
will allow the owner to add new item information, update the existing information of items, and delete items that
will no longer be stored at either location. 

### Manage Stock
After adding new item information, the owner will then be able to add new stock information for that item,
update the stock information of existing items, and delete stock information of items that will no longer
be stored at either location. To ensure the integrity of the database information, we prevent the owner
from deleting item information before first deleting the stock information of an item.

### View and Print Reports
The application allows the owner to view the items that match certain criteria (specified by the owner) and if they'd like,
print a report of the search results. This feature allows the owner to quickly finds that are in low-stock, out-of-stock, and sort the results by quantity or name.

## Technologies
The application is being built using:
* HTML
* CSS
* PHP
* MySQL

## How to Run Locally
### Prerequisites 
1. Download ZIP file of main branch (totsandblocks)
2. Download MAMP (free or pro version)
3. totsandblocks(4).sql file (inside totsandblocks folder)

### Importing the database
1. Run MAMP
2. Navigate to phpMyAdmin by typing the following: http://localhost:configuredPortNumber/phpMyAdmin5/
    1. Configured port number by default is 8888. Ex: http://localhost:8888/phpMyAdmin5/
3. Import the databse file: totsandblocks(4).sql to phpMyAdmin
    1. Click 'New' on the left-hand sidebar
    2. Name the database 'totsandblocks'
    3. Click on totsandblocks database
    4. Select the Import Tab
    5. Under 'File to import' click the 'Choose File' button
    6. Navigate to where the file totsandblocks(4).sql file is located and select it
    7. Click 'Go' on the bottom-right
4. Database has now been imported

### Running the application
1. Navigate to http://localhost:configurePortNumber/totsandblocks/
    1. totsandblocks is the name of folder, downloaded from the repo, and should be in the MAMP/htdocs folder
    2. Configured port number by default is 8888. Ex: http://localhost:8888/totsandblocks/
2. Log in to the program:
    * Username: kbarvaliya
    * Password: test12345
3. You should now be logged in.

## User Manual
The User Manual will help the user learn how to use the application. 
User Manual is located at the following link and will be receiving updates until the end of the semester:
https://docs.google.com/document/d/1aRXTmHYL4OCiGaI48OBdd4HlkO6NuMlemQbIzMV0-pM/edit 

## Credits
This application was developed by Keval Barvaliya, Damian Lewocha, Piero Coronado.
For the owner of Tots and Blocks, Mr. Bharat.
We'd like to thank the team, the client, and Dr. Morreale for their assistance in completing this project.
