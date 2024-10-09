# Loyalty Card Management System

## Project Overview

This project is a **Loyalty Card Management System** developed in PHP. It enables to offer loyalty programs to their customers. Users can register, manage their loyalty cards, view available rewards, and track their points history. Admins have full control over users, loyalty cards, partners, and transactions. The system also includes QR code scanning functionality to process loyalty transactions.

The **QR code** is a key feature in this system. Once a user is approved for a loyalty card, the system dynamically generates a QR code, which can be used in future transactions. This QR code is the main point of interaction for loyalty card transactions.

## Technologies Used

- **PHP**: Handles server-side scripting.
- **MySQL**: Relational database management for user, loyalty card, and transaction records.
- **HTML/CSS/JavaScript**: For building the front-end user interface.
- **QR Code Integration**: For generating and scanning loyalty cards via mobile or desktop.
- **jQuery**: Handles AJAX and dynamic page updates.
- **Session Management**: PHP sessions for user authentication and security.

## Key Features

### User Features:

1. **User Registration & Login**: Users can create accounts and securely log in.
2. **View Loyalty Cards**: Users can view their loyalty card details and current points balance.
3. **Track Points History**: Users have access to a detailed history of purchases, and redeemed points.
4. **Rewards / Promos**: Users can view available rewards and promos.
5. **QR Code Generation**: Once a user's loyalty card is approved, a dynamic QR code is generated. This QR code is used for loyalty transactions.
6. **Account Management**: Users can update their account details.

### Admin Features:

1. **Admin Dashboard**: Provides an overview of the system’s usage.
2. **User Management**: Admins can view, edit, or delete users.
3. **Loyalty Card Management**: Admins can manage loyalty cards, including approving or rejecting them.
4. **Transaction Processing**: Admins can process loyalty card transactions and assign points.
5. **Partner Management**: Admins can manage partners involved in the loyalty program.
6. **QR Code Scanning**: Only admins are allowed to scan the generated QR codes to manage loyalty card-related transactions.
7. **Admin Account Creation**: Admins cannot register using the normal registration process (`register.php`). Instead, they are created manually via the `admin-maker.php` file.

## File Structure and Descriptions

### Core Files:

- `index.php`: Main entry point to the site, where users can log in or register.
- `login.php`: Handles user authentication and login.
- `logout.php`: Ends the user's session and redirects them to the main page.
- `register.php`: Handles new user registrations and account creation.
- `admin.php`: Main dashboard for admin users.
- `config.php`: Database connection configuration, where MySQL credentials are stored.
- `create-database.php`: Script for creating the necessary database tables for the project.

### User Features:

- `view-loyalty-card.php`: Displays details of a user’s loyalty cards.
- `points-history.php`: Shows the user's points history.
- `rewards.php`: Displays the rewards a user can redeem using their loyalty points.
- `loyalty-card-register.php`: Allows users to register a new loyalty card.
- `edit-account.php`: Page for users to update their account information.

### Admin Features:

- `manage-users.php`: Admin functionality to manage user accounts.
- `manage-loyalty-cards.php`: Admin functionality to manage loyalty cards.
- `process-transaction.php`: Admins can process transactions related to loyalty cards.
- `partners.php`: Admins can manage business partners involved in the loyalty program.
- `admin-maker.php`: Admin-specific page used to manually create admin accounts.

### Shared Components:

- `navbar.php`, `footer.php`: Common navigation bar and footer used across pages.
- `admin-navbar.php`, `admin-footer.php`: Navigation bar and footer specifically for admin pages.
- `user-navbar.php`, `user-footer.php`: Navigation bar and footer for logged-in user pages.
- `default-navbar.php`, `default-footer.php`: Default navigation and footer for the main pages.

## Database Structure

The system uses a MySQL database to manage users, loyalty cards, transactions, and rewards. Below is a simplified schema:

### Tables:

1. **Users**: Stores user information (ID, username, password, email, role, etc.)
2. **Loyalty Cards**: Contains information about loyalty cards (Card ID, User ID, Points, etc.)
3. **Transactions**: Tracks transactions associated with loyalty cards (Transaction ID, Card ID, Points Earned/Redeemed, Timestamp).
4. **Partners**: Holds details about partners involved in the loyalty program.

## Installation Instructions

1. **Clone the repository** to your local machine.

   ```bash
   git clone <repository-url>
   cd project_folder
   ```

2. **Set up the database**:

   - Open your MySQL client (phpMyAdmin, MySQL Workbench, or command line).
   - Import the SQL schema by running `create-database.php` or executing the SQL script in your MySQL client.
   - Ensure the tables are correctly created for users, loyalty cards, transactions, etc.

3. **Configure the `config.php` file**:

   - Open `config.php` and update the following values to match your database setup:
     ```php
     $servername = "localhost";
     $username = "your_username";
     $password = "your_password";
     $dbname = "your_database_name";
     ```

4. **Running the project**:
   - Place the project folder in your server’s root directory (`htdocs` for XAMPP or `www` for WAMP).
   - Start your server and navigate to `http://localhost/project_folder/index.php` in your web browser.

## Usage

### Admin Login:

Admins can log in by navigating to `/login.php` and entering their credentials.

### User Registration & Login:

Users can register for a new account using the `register.php` page and log in using `login.php`. Upon login, users can view and manage their loyalty cards, track their points, and redeem rewards.

### Managing Loyalty Cards:

Users can register a loyalty card via the `loyalty-card-register.php` page.

### QR Code Functionality:

- **Admin Scanning**: Only admins are permitted to scan the QR code for processing loyalty transactions.
- **QR Code Generation**: A QR code is automatically generated for each approved loyalty card.

## User Roles and Permissions

- **Admin**: Has full control over user accounts, loyalty cards, and transactions. Admins can also process loyalty card transactions and manage system-wide settings.
- **Regular User**: Can view rewards/promos and track their points history.

## Security and Session Management

The system uses PHP sessions to manage logged-in users. Upon login, a session is started and the user’s information is stored securely. Session timeouts and logout mechanisms are implemented to ensure user security.

- **Session Management**: Users remain logged in via a PHP session.
- **Authentication**: Passwords are securely hashed using [bcrypt] to ensure user data remains safe.
- **Authorization**: Pages like `/admin.php` are restricted to admin users. Unauthorized users are redirected to the `unauthorized.php` page.

## Troubleshooting

1. **Database Connection Issues**:

   - Ensure the `config.php` file contains correct database credentials.
   - Verify that the MySQL server is running.

2. **Page Not Loading**:
   - Check if your local server (XAMPP, WAMP) is running.
   - Confirm the project folder is in the correct directory (e.g., `htdocs`).
