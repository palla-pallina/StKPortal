# STK Admin - Donation Management System

A complete donation management system integrated into the STK Admin dashboard using PHP, MySQL, and JavaScript.

## Features

- **Unified Interface**: Donations section stays within the same website - no redirects
- **Complete CRUD Operations**: Add, view, update status, and delete donations
- **Search & Sort**: Search by name, email, or donation type. Sort by date, name, or amount
- **File Upload**: Upload proof of donation (images/PDFs)
- **Pagination**: Navigate through large datasets efficiently
- **Status Management**: Approve, reject, or mark donations as pending
- **Responsive Design**: Works on desktop and mobile devices

## Database Setup

1. **Start XAMPP**: Make sure Apache and MySQL services are running
2. **Create Database**: 
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import the `database_setup.sql` file or run the SQL commands manually
   - This will create the `stk_admin` database with the required tables

## File Structure

```
Admin/
├── admin.html          # Main admin interface
├── admin.css           # Styles for the admin dashboard
├── admin.js            # JavaScript functionality
├── config.php          # Database connection configuration
├── donations_handler.php # PHP backend for donation operations
├── database_setup.sql  # SQL file to set up the database
├── uploads/            # Directory for uploaded proof files
├── santa krus logo.png # Logo image
└── README.md           # This file
```

## Installation Steps

1. **Place files in XAMPP htdocs**:
   - Copy all files to `C:\xampp\htdocs\Admin\`

2. **Set up the database**:
   - Open phpMyAdmin
   - Import `database_setup.sql` or run the SQL commands manually

3. **Configure database connection**:
   - Edit `config.php` if you need to change database credentials
   - Default settings work with XAMPP's default configuration

4. **Set permissions**:
   - Ensure the `uploads/` directory is writable by the web server

5. **Access the application**:
   - Open your browser and go to `http://localhost/Admin/admin.html`

## Database Schema

### Users Table
- `user_id` (Primary Key)
- `username`, `email`, `full_name`
- `created_at`

### Donations Table
- `donate_id` (Primary Key)
- `user_id` (Foreign Key to users)
- `donor_fullname`, `donor_email`, `donor_contact_no`
- `donation_categ` (Cash, Clothes, Goods, Specified)
- `donation_specified` (for specified donations)
- `donation_type` (Parish Donation, Tithes, Charity)
- `mode_of_payment` (Onsite, GCash, Bank Transfer)
- `proof_of_donation` (file path)
- `donation_amount` (decimal)
- `donation_status` (pending, verified, unverified)
- `date_posted` (timestamp)

## Usage

### Adding Donations
1. Click the green "+" button in the donations section
2. Fill out the donation form
3. Upload proof of donation (optional)
4. Submit the form

### Managing Donations
- **Search**: Use the search bar to find specific donations
- **Sort**: Use the dropdown to sort by different criteria
- **Status**: Click the checkmark/X buttons to approve/reject pending donations
- **Delete**: Click the trash icon to delete donations
- **View Proof**: Click on proof file links to view uploaded documents

### Navigation
- Use the sidebar to switch between different sections
- The donations section loads automatically when selected
- Pagination controls at the bottom for large datasets

## Technical Details

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **File Upload**: Supports images and PDFs
- **AJAX**: All operations use AJAX for smooth user experience
- **Responsive**: Mobile-friendly design

## Security Features

- SQL injection prevention using prepared statements
- File upload validation
- Input sanitization
- CSRF protection through form tokens

## Troubleshooting

1. **Database Connection Error**:
   - Check if MySQL is running in XAMPP
   - Verify database credentials in `config.php`
   - Ensure the `stk_admin` database exists

2. **File Upload Issues**:
   - Check if the `uploads/` directory exists and is writable
   - Verify file size limits in PHP configuration

3. **Page Not Loading**:
   - Ensure Apache is running in XAMPP
   - Check file permissions
   - Verify all files are in the correct location

## Sample Data

The database setup includes sample users and donations for testing:
- 3 sample users
- 8 sample donations with various statuses and types

You can modify or remove this sample data as needed for production use.
