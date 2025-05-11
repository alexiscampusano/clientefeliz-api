-- Script to populate the database with initial data
-- Set UTF-8 encoding
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

USE cliente_feliz;

-- Insert test users (password: '$2y$12$OP8xMHqrm6Y7s2NMYDdATOiMEAWKnfC7MdcJ3ImmDtlbbtqkGJVaq' = 'password123')
INSERT INTO User (first_name, last_name, email, password, birth_date, phone, address, role, registration_date, status)
VALUES
('Reclutador', 'Cliente Feliz', 'reclutador@clientefeliz.com', '$2y$12$OP8xMHqrm6Y7s2NMYDdATOiMEAWKnfC7MdcJ3ImmDtlbbtqkGJVaq', '1985-05-15', '56912345678', 'Av. Principal 123, Santiago', 'Recruiter', NOW(), 'Active'),
('Pedro', 'González', 'pedro@ejemplo.com', '$2y$12$OP8xMHqrm6Y7s2NMYDdATOiMEAWKnfC7MdcJ3ImmDtlbbtqkGJVaq', '1990-03-25', '56934567890', 'Pasaje Los Olivos 789, Santiago', 'Candidate', NOW(), 'Active'),
('María', 'López', 'maria@ejemplo.com', '$2y$12$OP8xMHqrm6Y7s2NMYDdATOiMEAWKnfC7MdcJ3ImmDtlbbtqkGJVaq', '1992-07-10', '56945678901', 'Av. Los Leones 321, Santiago', 'Candidate', NOW(), 'Active'),
('Juan', 'Martínez', 'juan@ejemplo.com', '$2y$12$OP8xMHqrm6Y7s2NMYDdATOiMEAWKnfC7MdcJ3ImmDtlbbtqkGJVaq', '1988-12-05', '56956789012', 'Calle Las Flores 654, Santiago', 'Candidate', NOW(), 'Active'); 

-- Insert job offers
INSERT INTO JobOffer (title, description, location, salary, contract_type, publication_date, closing_date, status, recruiter_id)
VALUES
('Desarrollador Full Stack', 'Buscamos desarrollador con experiencia en PHP, JavaScript y React para proyecto de comercio electrónico. Se requiere conocimiento en APIs REST y bases de datos SQL.', 'Santiago', 1500000.00, 'Indefinite', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'Active', 1),
('Analista de Datos', 'Se requiere analista con conocimientos en SQL, Python y herramientas de visualización. Experiencia en manejo de grandes volúmenes de datos y generación de reportes.', 'Remoto', 1200000.00, 'Temporary', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 20 DAY), 'Active', 1);

-- Insert applications
INSERT INTO Application (candidate_id, job_offer_id, application_status, comment, application_date)
VALUES
(2, 1, 'Reviewing', 'Candidato con buen nivel técnico. Pendiente entrevista técnica.', NOW()),
(3, 1, 'Psychological Interview', 'Pasó primera etapa de selección. Programada entrevista psicológica.', NOW()),
(4, 2, 'Applied', 'Postulación recibida, pendiente primera revisión.', NOW());

-- Insert academic backgrounds
INSERT INTO AcademicBackground (candidate_id, institution, degree, start_year, end_year)
VALUES
(2, 'Universidad de Chile', 'Ingeniería en Informática', 2010, 2015),
(3, 'Universidad Católica', 'Psicología', 2012, 2017),
(4, 'INACAP', 'Técnico en Informática', 2015, 2018);

-- Insert work experience
INSERT INTO WorkExperience (candidate_id, company, position, duties, start_date, end_date)
VALUES
(2, 'Empresa Tecnológica', 'Desarrollador Web', 'Desarrollo de aplicaciones web, mantenimiento de sistemas, soporte técnico', '2016-01-01', '2018-06-30'),
(3, 'Consultora RR.HH.', 'Analista de Selección', 'Reclutamiento y selección de personal, entrevistas, evaluación psicológica', '2018-01-01', '2020-12-31'),
(4, 'Startup Tecnológica', 'Analista de Datos', 'Análisis de datos, generación de reportes, visualización de información', '2018-07-01', '2021-03-31');
