-- Drop existing database if it exists
DROP DATABASE IF EXISTS college_repo;

-- Create the database
CREATE DATABASE college_repo;

-- Use the created database
USE college_repo;

--  users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    username VARCHAR(50) NOT NULL,
    user_unique_id VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

--  repositories table
CREATE TABLE IF NOT EXISTS repositories (
    repo_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

--  projects table
CREATE TABLE IF NOT EXISTS projects (
    project_id INT AUTO_INCREMENT PRIMARY KEY,
    repo_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (repo_id) REFERENCES repositories(repo_id)
);

--  evaluations table
CREATE TABLE IF NOT EXISTS evaluations (
    evaluation_id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    teacher_id INT NOT NULL,
    comments TEXT,
    grade VARCHAR(10),
    score INT,
    FOREIGN KEY (project_id) REFERENCES projects(project_id),
    FOREIGN KEY (teacher_id) REFERENCES users(user_id)
);
