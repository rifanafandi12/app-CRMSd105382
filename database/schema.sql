-- ==============================================
-- CRM SD Negeri 105382 Galang
-- Database Schema
-- ==============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `db_crm_sd_negeri_105382`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `db_crm_sd_negeri_105382`;

-- ==============================================
-- HAPUS TABEL LAMA (kalau ada)
-- ==============================================
DROP TABLE IF EXISTS `user_status`;
DROP TABLE IF EXISTS `log_activities`;
DROP TABLE IF EXISTS `chat_messages`;
DROP TABLE IF EXISTS `scores`;
DROP TABLE IF EXISTS `reports`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `classes`;
DROP TABLE IF EXISTS `aspirations`;
DROP TABLE IF EXISTS `announcements`;
DROP TABLE IF EXISTS `gallery`;
DROP TABLE IF EXISTS `academic_subjects`;
DROP TABLE IF EXISTS `communications`;
DROP TABLE IF EXISTS `users`;

-- ==============================================
-- TABLE: users
-- ==============================================
CREATE TABLE `users` (
    `id_user`         INT NOT NULL AUTO_INCREMENT,
    `username`        VARCHAR(50)  NOT NULL,
    `password`        VARCHAR(255) NOT NULL,
    `name`            VARCHAR(100) NOT NULL,
    `email`           VARCHAR(100) DEFAULT NULL,
    `phone`           VARCHAR(20)  DEFAULT NULL,
    `role`            ENUM('admin','teacher','parent') NOT NULL DEFAULT 'parent',
    `profile_picture` VARCHAR(255) DEFAULT NULL,
    `remember_token`  VARCHAR(100) DEFAULT NULL,
    `is_active`       TINYINT(1)   NOT NULL DEFAULT 1,
    `last_login`      DATETIME     DEFAULT NULL,
    `created_at`      TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_user`),
    UNIQUE KEY `uk_username` (`username`),
    UNIQUE KEY `uk_email` (`email`),
    KEY `idx_role` (`role`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLE: user_status (untuk tracking online/offline)
-- ==============================================
CREATE TABLE `user_status` (
    `id_user`       INT NOT NULL,
    `is_online`     TINYINT(1) NOT NULL DEFAULT 0,
    `socket_id`     VARCHAR(100) DEFAULT NULL,
    `last_activity` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_user`),
    CONSTRAINT `fk_user_status_user` FOREIGN KEY (`id_user`)
        REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLE: classes (kelas)
-- ==============================================
CREATE TABLE `classes` (
    `id_class`            INT NOT NULL AUTO_INCREMENT,
    `class_name`          VARCHAR(20) NOT NULL,
    `grade_level`         INT NOT NULL,
    `homeroom_teacher_id` INT DEFAULT NULL,
    `academic_year`       VARCHAR(20) DEFAULT NULL,
    `description`         TEXT,
    `created_at`          TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_class`),
    UNIQUE KEY `uk_class_name_year` (`class_name`, `academic_year`),
    KEY `idx_homeroom_teacher` (`homeroom_teacher_id`),
    KEY `idx_grade_level` (`grade_level`),
    CONSTRAINT `fk_class_teacher` FOREIGN KEY (`homeroom_teacher_id`)
        REFERENCES `users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLE: students (siswa)
-- ==============================================
CREATE TABLE `students` (
    `id_student`      INT NOT NULL AUTO_INCREMENT,
    `nisn`            VARCHAR(20) NOT NULL,
    `student_name`    VARCHAR(100) NOT NULL,
    `gender`          ENUM('Laki-laki','Perempuan') NOT NULL,
    `birth_place`     VARCHAR(50) DEFAULT NULL,
    `birth_date`      DATE DEFAULT NULL,
    `address`         TEXT,
    `parent_name`     VARCHAR(100) NOT NULL,
    `parent_phone`    VARCHAR(20) DEFAULT NULL,
    `parent_email`    VARCHAR(100) DEFAULT NULL,
    `id_class`        INT DEFAULT NULL,
    `id_user_parent`  INT DEFAULT NULL,
    `enrollment_year` YEAR DEFAULT NULL,
    `photo`           VARCHAR(255) DEFAULT NULL,
    `is_active`       TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_student`),
    UNIQUE KEY `uk_nisn` (`nisn`),
    KEY `idx_class` (`id_class`),
    KEY `idx_parent` (`id_user_parent`),
    KEY `idx_is_active` (`is_active`),
    KEY `idx_student_name` (`student_name`),
    CONSTRAINT `fk_student_class` FOREIGN KEY (`id_class`)
        REFERENCES `classes` (`id_class`) ON DELETE SET NULL,
    CONSTRAINT `fk_student_parent` FOREIGN KEY (`id_user_parent`)
        REFERENCES `users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLE: academic_subjects (mata pelajaran)
-- ==============================================
CREATE TABLE `academic_subjects` (
    `id_subject`    INT NOT NULL AUTO_INCREMENT,
    `subject_name`  VARCHAR(100) NOT NULL,
    `subject_code`  VARCHAR(20) DEFAULT NULL,
    `grade_level`   INT DEFAULT NULL,
    `description`   TEXT,
    `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_subject`),
    UNIQUE KEY `uk_subject_code` (`subject_code`),
    KEY `idx_subject_name` (`subject_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLE: scores (nilai siswa)
-- ==============================================
CREATE TABLE `scores` (
    `id_score`         INT NOT NULL AUTO_INCREMENT,
    `id_student`       INT NOT NULL,
    `id_subject`       INT NOT NULL,
    `semester`         TINYINT NOT NULL,
    `academic_year`    VARCHAR(20) NOT NULL,
    `daily_score_1`    DECIMAL(5,2) DEFAULT NULL,
    `daily_score_2`    DECIMAL(5,2) DEFAULT NULL,
    `mid_exam_score`   DECIMAL(5,2) DEFAULT NULL,
    `final_exam_score` DECIMAL(5,2) DEFAULT NULL,
    `final_score`      DECIMAL(5,2) DEFAULT NULL,
    `grade`            CHAR(2) DEFAULT NULL,
    `notes`            TEXT,
    `created_by`       INT DEFAULT NULL,
    `created_at`       TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_score`),
    UNIQUE KEY `uk_score_unique` (`id_student`, `id_subject`, `semester`, `academic_year`),
    KEY `idx_student` (`id_student`),
    KEY `idx_subject` (`id_subject`),
    KEY `idx_period` (`semester`, `academic_year`),
    CONSTRAINT `fk_score_student` FOREIGN KEY (`id_student`)
        REFERENCES `students` (`id_student`) ON DELETE CASCADE,
    CONSTRAINT `fk_score_subject` FOREIGN KEY (`id_subject`)
        REFERENCES `academic_subjects` (`id_subject`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLE: chat_messages (pesan realtime)
-- ==============================================
CREATE TABLE `chat_messages` (
    `id_message`  INT NOT NULL AUTO_INCREMENT,
    `id_sender`   INT NOT NULL,
    `id_receiver` INT NOT NULL,
    `message`     TEXT NOT NULL,
    `is_read`     TINYINT(1) NOT NULL DEFAULT 0,
    `read_at`     DATETIME DEFAULT NULL,
    `created_at`  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_message`),
    KEY `idx_sender` (`id_sender`),
    KEY `idx_receiver` (`id_receiver`),
    KEY `idx_is_read` (`is_read`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_chat_sender` FOREIGN KEY (`id_sender`)
        REFERENCES `users` (`id_user`),
    CONSTRAINT `fk_chat_receiver` FOREIGN KEY (`id_receiver`)
        REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLE: announcements (pengumuman)
-- ==============================================
CREATE TABLE `announcements` (
    `id_announcement` INT NOT NULL AUTO_INCREMENT,
    `title`           VARCHAR(200) NOT NULL,
    `content`         TEXT NOT NULL,
    `category`        ENUM('akademik','kesiswaan','umum','event','libur','darurat')
                      NOT NULL DEFAULT 'umum',
    `target_class_id` INT DEFAULT NULL,
    `posted_by`       INT NOT NULL,
    `posted_date`     DATETIME DEFAULT CURRENT_TIMESTAMP,
    `expiry_date`     DATE DEFAULT NULL,
    `is_published`    TINYINT(1) NOT NULL DEFAULT 1,
    `attachment`      VARCHAR(255) DEFAULT NULL,
    `view_count`      INT NOT NULL DEFAULT 0,
    `created_at`      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_announcement`),
    KEY `idx_category` (`category`),
    KEY `idx_is_published` (`is_published`),
    KEY `idx_posted_date` (`posted_date`),
    KEY `idx_target_class` (`target_class_id`),
    CONSTRAINT `fk_announcement_user` FOREIGN KEY (`posted_by`)
        REFERENCES `users` (`id_user`),
    CONSTRAINT `fk_announcement_class` FOREIGN KEY (`target_class_id`)
        REFERENCES `classes` (`id_class`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLE: aspirations (aspirasi orang tua)
-- ==============================================
CREATE TABLE `aspirations` (
    `id_aspiration` INT NOT NULL AUTO_INCREMENT,
    `id_parent`     INT NOT NULL,
    `subject`       VARCHAR(200) NOT NULL,
    `content`       TEXT NOT NULL,
    `category`      ENUM('saran','kritik','keluhan','pertanyaan','lainnya')
                    NOT NULL DEFAULT 'lainnya',
    `attachment`    VARCHAR(255) DEFAULT NULL,
    `status`        ENUM('pending','dibaca','diproses','selesai')
                    NOT NULL DEFAULT 'pending',
    `response`      TEXT,
    `responded_by`  INT DEFAULT NULL,
    `responded_at`  DATETIME DEFAULT NULL,
    `created_at`    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_aspiration`),
    KEY `idx_parent` (`id_parent`),
    KEY `idx_status` (`status`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_aspiration_parent` FOREIGN KEY (`id_parent`)
        REFERENCES `users` (`id_user`),
    CONSTRAINT `fk_aspiration_responder` FOREIGN KEY (`responded_by`)
        REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLE: gallery (galeri foto)
-- ==============================================
CREATE TABLE `gallery` (
    `id_gallery`   INT NOT NULL AUTO_INCREMENT,
    `title`        VARCHAR(200) NOT NULL,
    `description`  TEXT,
    `image_path`   VARCHAR(255) NOT NULL,
    `category`     ENUM('kegiatan','prestasi','fasilitas','acara','lainnya')
                   NOT NULL DEFAULT 'kegiatan',
    `uploaded_by`  INT DEFAULT NULL,
    `view_count`   INT NOT NULL DEFAULT 0,
    `is_published` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_gallery`),
    KEY `idx_category` (`category`),
    KEY `idx_is_published` (`is_published`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_gallery_user` FOREIGN KEY (`uploaded_by`)
        REFERENCES `users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- TABLE: log_activities (log aktivitas)
-- ==============================================
CREATE TABLE `log_activities` (
    `id_log`        INT NOT NULL AUTO_INCREMENT,
    `id_user`       INT DEFAULT NULL,
    `activity_type` VARCHAR(50) DEFAULT NULL,
    `description`   TEXT,
    `ip_address`    VARCHAR(45) DEFAULT NULL,
    `user_agent`    VARCHAR(500) DEFAULT NULL,
    `created_at`    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_log`),
    KEY `idx_user` (`id_user`),
    KEY `idx_activity_type` (`activity_type`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_log_user` FOREIGN KEY (`id_user`)
        REFERENCES `users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- SEED DATA
-- ==============================================

-- Users
-- Password default: admin123, guru123, ortu123
INSERT INTO `users` (`id_user`, `username`, `password`, `name`, `email`, `phone`, `role`, `is_active`) VALUES
(1, 'admin',     '$2y$10$YourHashForAdmin123',  'Administrator',           'admin@sdn105382.sch.id',     '081234567890', 'admin',   1),
(2, 'guru1',     '$2y$10$YourHashForGuru123',   'Rika Astuti, S.Pd.',      'rika@sdn105382.sch.id',      '081234567891', 'teacher', 1),
(3, 'ortu1',     '$2y$10$YourHashForOrtu123',   'M Rifan Afandi',          'rifan@example.com',          '081234567892', 'parent',  1);

-- Classes
INSERT INTO `classes` (`id_class`, `class_name`, `grade_level`, `homeroom_teacher_id`, `academic_year`) VALUES
(1, 'I-A',   1, NULL, '2025/2026'),
(2, 'I-B',   1, NULL, '2025/2026'),
(3, 'II-A',  2, NULL, '2025/2026'),
(4, 'II-B',  2, NULL, '2025/2026'),
(5, 'III-A', 3, NULL, '2025/2026'),
(6, 'III-B', 3, NULL, '2025/2026'),
(7, 'IV-A',  4, NULL, '2025/2026'),
(8, 'IV-B',  4, NULL, '2025/2026'),
(9, 'V-A',   5, NULL, '2025/2026'),
(10, 'V-B',  5, NULL, '2025/2026'),
(11, 'VI-A', 6, 2,    '2025/2026'),
(12, 'VI-B', 6, NULL, '2025/2026');

-- Academic Subjects
INSERT INTO `academic_subjects` (`id_subject`, `subject_name`, `subject_code`, `grade_level`, `is_active`) VALUES
(1, 'Pendidikan Agama dan Budi Pekerti', 'PAI',  NULL, 1),
(2, 'Pendidikan Pancasila dan Kewarganegaraan', 'PPKn', NULL, 1),
(3, 'Bahasa Indonesia', 'BIN', NULL, 1),
(4, 'Matematika', 'MTK', NULL, 1),
(5, 'Ilmu Pengetahuan Alam', 'IPA', NULL, 1),
(6, 'Ilmu Pengetahuan Sosial', 'IPS', NULL, 1),
(7, 'Bahasa Inggris', 'BIG', NULL, 1),
(8, 'Seni Budaya dan Prakarya', 'SBdP', NULL, 1),
(9, 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'PJOK', NULL, 1);

-- Sample Student
INSERT INTO `students` (`id_student`, `nisn`, `student_name`, `gender`, `birth_place`, `birth_date`, `address`, `parent_name`, `parent_phone`, `parent_email`, `id_class`, `id_user_parent`, `enrollment_year`, `is_active`) VALUES
(1, '0059333442', 'Muhammad Afandi', 'Laki-laki', 'Lubuk Pakam', '2014-10-13', 'Jl. Sadar Timur, Lubuk Pakam', 'Nasib', '083165354885', 'rifan@example.com', 11, 3, 2025, 1);

-- Sample Announcement
INSERT INTO `announcements` (`id_announcement`, `title`, `content`, `category`, `posted_by`, `expiry_date`, `is_published`) VALUES
(1, 'Rapat Wali Murid', 'Akan diadakan rapat wali murid pada hari Sabtu pekan depan untuk membahas persiapan ujian akhir semester.', 'kegiatan', 1, DATE_ADD(CURDATE(), INTERVAL 7 DAY), 1);

-- Update user_status untuk user yang ada
INSERT INTO `user_status` (`id_user`, `is_online`) VALUES
(1, 0), (2, 0), (3, 0);
