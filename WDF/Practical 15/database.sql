-- StudentHub database
CREATE DATABASE IF NOT EXISTS studenthub CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE studenthub;

DROP TABLE IF EXISTS contacts, results, attendance, assignments, courses, users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','student') NOT NULL DEFAULT 'student',
  remember_token VARCHAR(64) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL UNIQUE,
  name VARCHAR(150) NOT NULL,
  credits INT NOT NULL DEFAULT 3,
  instructor VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE assignments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  due_date DATE NOT NULL,
  file_name VARCHAR(255) DEFAULT NULL,
  stored_name VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE attendance (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  course_id INT NOT NULL,
  att_date DATE NOT NULL,
  status ENUM('Present','Absent') NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  course_id INT NOT NULL,
  marks DECIMAL(5,2) NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Demo users: password for ALL of them is  password
INSERT INTO users (name,email,password,role) VALUES
('Admin','admin@studenthub.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin'),
('Feni Kaila','student@studenthub.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','student'),
('Demo Student','demo@studenthub.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','student');

INSERT INTO courses (code,name,credits,instructor) VALUES
('CE101','Web Technology',4,'Prof. Shah'),
('CE102','Database Management Systems',4,'Prof. Patel'),
('CE103','Research Methodology',2,'Prof. Mehta'),
('CE104','Software Engineering',3,'Prof. Joshi');

INSERT INTO assignments (course_id,title,description,due_date) VALUES
(1,'Build a Login System','Create login/logout using PHP sessions.','2026-11-01'),
(2,'ER Diagram & Queries','Design ER diagram and write 10 SQL queries.','2026-11-05');

INSERT INTO attendance (user_id,course_id,att_date,status) VALUES
(2,1,'2026-10-01','Present'),(2,1,'2026-10-02','Present'),(2,1,'2026-10-03','Absent'),
(2,2,'2026-10-01','Present'),(2,2,'2026-10-02','Present'),
(3,1,'2026-10-01','Present');

INSERT INTO results (user_id,course_id,marks) VALUES
(2,1,85.50),(2,2,78.00),(2,3,91.00),(3,1,66.00);
