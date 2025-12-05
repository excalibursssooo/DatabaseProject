# Car Auction Website
This project is based on xampp, primarily using PHP as the programming language.

Place the project folder in the htdocs folder, start the server, import ```init.sql``` into the database, and then access the following link in your browser to preview:

```localhost/DatabaseProject/index.php```

## Features
1. User login and logout
2. Admin account settings to view current auction status and all user information
3. Users can bid before the specified deadline
4. Users can view the current highest bid
5. Blockchain module to prevent tampering during the bidding process

## Database Overview
Contains Customer, User, Bids, autos, and blockchain tables

The User table ID references the Customer table ID, the Bid table auto ID connects to its own ID, CustomerID connects to the Customer table ID, and the blockchain table's cus_id and auto_id reference the Customer and auto tables.

# File Descriptions

## config.php
Implements initialization settings including database name and password, and functions to check user login status

## index.php
The main user interface page

Upon entry, it checks the user status. If not logged in, it redirects to ```login.php```. If logged in, it displays the user page or ```admin_dashboard.php``` for administrators.

This page presents all available features with links to corresponding pages.

### 1. View Vehicles ```view_vehicles.php```
Displays all available vehicles for auction in the marketplace

### 2. Bid Management ```max_bid.php```
Shows the current highest bid for each vehicle by calling the max_bid procedure defined in ```init.sql```

### 3. Results Modal ```winners_losers.php```
Displays bid results for a specific vehicle ID entered by the user after the deadline

### 4. Bidding Modal
Implements the bidding functionality. After entering a price, it redirects to ```process_bid.php``` to validate and record the bid

### 5. Blockchain Explorer
An experimental feature recently added. Main implementation in ```blockchain_explorer.php``` and functionality in ```Blockchain.php```

A blockchain consists of multiple blocks connected together. Each block contains user information, bid details, and timestamp, plus a hash value. Each block has two hashes: the previous block's hash and its own hash, and a variable called ```$nonce```.

A new block is generated with each bid:

1. Set difficulty level ```$difficult```
2. Hash the current block information (previousHash, data (bid info), nonce (initially 0))
3. Increment nonce until the hash's first ```$difficult``` digits are 0
4. Store the complete block information in the ```blockchain``` table

Verification method:
Each time the blockchain explorer is accessed, the system validates all blocks by recalculating their hashes. If the data is tampered with, the recalculated hash will differ significantly from the stored hash, ensuring data integrity.
