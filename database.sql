-- Database setup for Whiteboard project
CREATE DATABASE IF NOT EXISTS task_manager;
USE task_manager;

-- Users table: stores students and teachers
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'teacher') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses table: courses can be linked to tasks
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Tasks table: assignments created by teachers
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    deadline DATE NOT NULL,
    course_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
);

-- Submissions table: student submissions for tasks
CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    student_id INT NOT NULL,
    answer TEXT,
    file_name VARCHAR(255),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_student_task (task_id, student_id),
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample courses
INSERT INTO courses (name)
SELECT 'CS381'
WHERE NOT EXISTS (SELECT 1 FROM courses WHERE name = 'CS381');

INSERT INTO courses (name)
SELECT 'CS382'
WHERE NOT EXISTS (SELECT 1 FROM courses WHERE name = 'CS382');

INSERT INTO courses (name)
SELECT 'CS302'
WHERE NOT EXISTS (SELECT 1 FROM courses WHERE name = 'CS302');

INSERT INTO courses (name)
SELECT 'CS480'
WHERE NOT EXISTS (SELECT 1 FROM courses WHERE name = 'CS480');
