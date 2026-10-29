# StudentHub – Student Management Portal

A web-based student portal built with **HTML, CSS, JavaScript, PHP and MySQL**.
Students can register, log in, view courses, assignments, attendance and results; admins can manage students and courses.

## Features
- User registration and secure login/logout (PHP sessions, password hashing)
- Role-based access (Admin / Student)
- Session timeout and remember-me
- Profile management
- Courses (course list + course API)
- Assignments (upload / download)
- Attendance tracking
- Results / marks view
- Student management (admin)
- Contact page

## Tech Stack
| Layer     | Technology                  |
|-----------|-----------------------------|
| Frontend  | HTML5, CSS3, JavaScript     |
| Backend   | PHP 8.x                     |
| Database  | MySQL / MariaDB             |
| Server    | XAMPP (Apache + MySQL)      |

## Project Structure
```
studenthub/
├── config.php               # DB connection, sessions, CSRF, auth helpers (not committed)
├── config.sample.php        # Template for config.php
├── database.sql             # Schema + sample data
├── login.php / logout.php / register.php
├── index.php                # Dashboard
├── sidebar.php / footer.php # Shared layout
├── profile.php
├── courses.php / course-api.php
├── assignment.php / assignment_download.php
├── attendance.php / result.php
├── student_management.php / admin.php   # Admin only
├── contact.php
├── style.css / script.js
├── assignment_uploads/      # Uploaded files
├── README.md / TESTING.md / DEPLOYMENT.md / github_commands.txt
```

## Setup (Local)
1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL**.
2. Copy the project folder to `C:\xampp\htdocs\studenthub`.
3. Open `http://localhost/phpmyadmin`, create a database `studenthub`, and **Import** `database.sql`.
4. Copy `config.sample.php` to `config.php` and set your DB credentials.
5. Open `http://localhost/studenthub/login.php`.

See `DEPLOYMENT.md` for full steps and `TESTING.md` for test cases.

## Default Login (demo)
| Role    | Email                  | Password |
|---------|------------------------|----------|
| Admin   | admin@studenthub.com   | password |
| Student | student@studenthub.com | password |

Change these passwords after the first login.

## Author
**Feni Kaila** – B.Tech Computer Engineering, CHARUSAT
