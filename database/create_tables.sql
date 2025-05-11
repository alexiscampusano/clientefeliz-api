-- Script para crear las tablas de la base de datos Cliente Feliz
-- Primero, creamos la base de datos si no existe
CREATE DATABASE IF NOT EXISTS cliente_feliz
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Usamos la base de datos
USE cliente_feliz;

-- Eliminamos las tablas existentes para evitar errores
DROP TABLE IF EXISTS WorkExperience;
DROP TABLE IF EXISTS AcademicBackground;
DROP TABLE IF EXISTS Application;
DROP TABLE IF EXISTS JobOffer;
DROP TABLE IF EXISTS User;

-- TABLE: User
CREATE TABLE User (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    birth_date DATE,
    phone VARCHAR(20),
    address VARCHAR(255),
    role ENUM('Recruiter', 'Candidate') NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Active', 'Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: JobOffer
CREATE TABLE JobOffer (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    location VARCHAR(150),
    salary DECIMAL(10,2),
    contract_type ENUM('Indefinite', 'Temporary', 'Fee', 'Internship') DEFAULT 'Indefinite',
    publication_date DATE DEFAULT (CURRENT_DATE),
    closing_date DATE,
    status ENUM('Active', 'Closed', 'Inactive') DEFAULT 'Active',
    recruiter_id INT NOT NULL,
    FOREIGN KEY (recruiter_id) REFERENCES User(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: Application
CREATE TABLE Application (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidate_id INT NOT NULL,
    job_offer_id INT NOT NULL,
    application_status ENUM('Applied', 'Reviewing', 'Psychological Interview', 'Personal Interview', 'Selected', 'Rejected') DEFAULT 'Applied',
    comment TEXT,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (candidate_id) REFERENCES User(id),
    FOREIGN KEY (job_offer_id) REFERENCES JobOffer(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: AcademicBackground
CREATE TABLE AcademicBackground (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidate_id INT NOT NULL,
    institution VARCHAR(150) NOT NULL,
    degree VARCHAR(150) NOT NULL,
    start_year YEAR,
    end_year YEAR,
    FOREIGN KEY (candidate_id) REFERENCES User(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: WorkExperience
CREATE TABLE WorkExperience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidate_id INT NOT NULL,
    company VARCHAR(150) NOT NULL,
    position VARCHAR(150) NOT NULL,
    duties TEXT,
    start_date DATE,
    end_date DATE,
    FOREIGN KEY (candidate_id) REFERENCES User(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
