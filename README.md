# Luxury Hotel Management System

A comprehensive web-based hotel management system that allows users to browse rooms, make bookings, and administrators to manage the entire hotel operation.

## Technologies Used

### 1. Frontend Technologies
- **HTML5**
  - Used for structuring the web pages
  - Semantic elements for better accessibility
  - Forms for user input and data collection

- **CSS3**
  - Custom styling for a luxurious look and feel
  - Responsive design for all device sizes
  - Bootstrap integration for modern UI components

- **JavaScript**
  - Dynamic content updates
  - Form validation
  - Date calculations for bookings
  - Interactive user interface elements

- **Bootstrap 5.1.3**
  - Responsive grid system
  - Pre-built components
  - Modal dialogs
  - Navigation elements
  - Form styling

### 2. Backend Technologies
- **PHP 8.0+**
  - Server-side scripting
  - Database interactions
  - Session management
  - User authentication
  - File handling

- **MySQL Database**
  - Storing user information
  - Room details and inventory
  - Booking records
  - Secure data management

### 3. Additional Tools
- **XAMPP**
  - Apache web server
  - MySQL database server
  - PHP interpreter
  - Development environment

## Project Setup

### 1. Prerequisites
- XAMPP (or similar local server environment)
- Web browser (Chrome/Firefox recommended)
- Text editor (VS Code/Sublime Text recommended)

### 2. Installation Steps
1. **Install XAMPP**
   - Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Install with default settings
   - Start Apache and MySQL services

2. **Setup Project Files**
   - Clone/Download project files
   - Place in `C:/xampp/htdocs/Hotel_managment/`
   - Ensure proper file permissions

3. **Database Setup**
   ```sql
   -- Create database
   CREATE DATABASE hotel_management;
   USE hotel_management;

   -- Create tables
   -- Users table
   CREATE TABLE users (
       id INT PRIMARY KEY AUTO_INCREMENT,
       name VARCHAR(100) NOT NULL,
       email VARCHAR(100) UNIQUE NOT NULL,
       password VARCHAR(255) NOT NULL,
       role ENUM('admin', 'user') DEFAULT 'user',
       status ENUM('active', 'blocked') DEFAULT 'active',
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   -- Rooms table
   CREATE TABLE rooms (
       id INT PRIMARY KEY AUTO_INCREMENT,
       name VARCHAR(100) NOT NULL,
       description TEXT,
       price DECIMAL(10,2) NOT NULL,
       image VARCHAR(255),
       status ENUM('available', 'booked', 'maintenance') DEFAULT 'available',
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   -- Bookings table
   CREATE TABLE bookings (
       id INT PRIMARY KEY AUTO_INCREMENT,
       user_id INT,
       room_id INT,
       check_in DATE NOT NULL,
       check_out DATE NOT NULL,
       status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (user_id) REFERENCES users(id),
       FOREIGN KEY (room_id) REFERENCES rooms(id)
   );
   ```

4. **Configuration**
   - Update database credentials in `db.php`
   - Configure email settings if required
   - Set proper permissions for image uploads

## Features

### 1. User Features
- User registration and authentication
- Browse available rooms
- Make room bookings
- View booking history
- Cancel bookings
- Update profile

### 2. Admin Features
- Dashboard with statistics
- Manage rooms (Add/Edit/Delete)
- Manage bookings (Confirm/Cancel)
- Manage users (Block/Unblock)
- View reports

## Common Viva Questions & Answers

### 1. What is the purpose of this project?
This Hotel Management System is designed to automate and streamline hotel operations, making it easier for both customers and administrators to manage bookings, rooms, and user accounts. It replaces traditional paper-based systems with a modern digital solution.

### 2. What are the security measures implemented?
- Password hashing using PHP's password_hash()
- Prepared statements to prevent SQL injection
- Input validation and sanitization
- Session management for authentication
- Role-based access control
- CSRF protection in forms

### 3. How does the booking system work?
1. User selects a room
2. Chooses check-in and check-out dates
3. System checks room availability
4. Creates pending booking
5. Admin confirms booking
6. User can track booking status

### 4. What are the main challenges faced during development?
- Implementing real-time availability checking
- Managing concurrent bookings
- Ensuring proper date validation
- Creating responsive design
- Handling image uploads securely

### 5. How is the database designed?
The database follows a relational model with three main tables:
- Users: Stores user information
- Rooms: Contains room details
- Bookings: Links users and rooms with booking details

### 6. What improvements could be made?
- Payment gateway integration
- Email notifications
- Room reviews and ratings
- Advanced search filters
- Mobile application
- Room service requests
- Housekeeping management

### 7. How does the system handle concurrent bookings?
- Real-time availability checking
- Database transaction management
- Status tracking for each booking
- Automatic updates when status changes

## Troubleshooting

### Common Issues
1. **Database Connection Failed**
   - Check XAMPP services
   - Verify database credentials
   - Ensure proper permissions

2. **Images Not Uploading**
   - Check folder permissions
   - Verify file size limits
   - Enable proper PHP extensions

3. **Session Issues**
   - Clear browser cache
   - Check PHP session configuration
   - Verify session storage permissions

## Best Practices Used

1. **Code Organization**
   - MVC-like structure
   - Separate configuration files
   - Modular components
   - Clean code principles

2. **Security**
   - Input validation
   - Output escaping
   - Secure password handling
   - Access control

3. **User Experience**
   - Responsive design
   - Intuitive navigation
   - Clear error messages
   - Loading indicators

## Support and Contact

For any queries or support:
- Email: support@luxuryhotel.com
- Phone: +1-234-567-8900
- Website: www.luxuryhotel.com

## License

This project is licensed under the MIT License - see the LICENSE file for details.

---
Created with ❤️ by [RUSHIKESH RAKHE ]
Last Updated: [@20/03/2025] 