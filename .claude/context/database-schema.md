# Database Schema — FATI-PROJET

## Core Tables

### users
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | |
| role | ENUM('admin', 'student') | DEFAULT 'student' | Rôle de l'utilisateur |
| group_id | BIGINT | FOREIGN KEY groups.id, NULL ON DELETE | Groupe de l'étudiant |
| first_name | VARCHAR(255) | NULLABLE | Prénom |
| last_name | VARCHAR(255) | NULLABLE | Nom |
| age | INT | NULLABLE | Âge |
| massar_code | VARCHAR(255) | UNIQUE, NULLABLE | Code MASSAR unique |
| username | VARCHAR(255) | UNIQUE | Identifiant unique |
| email | VARCHAR(255) | UNIQUE | Email |
| password | VARCHAR(255) | | Mot de passe hashé |
| is_first_login | BOOLEAN | DEFAULT true | Première connexion |
| last_login_at | TIMESTAMP | NULLABLE | Dernière connexion |
| remember_token | VARCHAR(100) | NULLABLE | Token de mémorisation |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

### schools
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT | PRIMARY KEY |
| name | VARCHAR(255) | |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### branches
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT | PRIMARY KEY |
| school_id | BIGINT | FOREIGN KEY schools.id, CASCADE ON DELETE |
| name | VARCHAR(255) | |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### academic_years
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT | PRIMARY KEY |
| name | VARCHAR(255) | |
| is_active | BOOLEAN | DEFAULT false |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### groups
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT | PRIMARY KEY |
| branch_id | BIGINT | FOREIGN KEY branches.id, CASCADE ON DELETE |
| academic_year_id | BIGINT | FOREIGN KEY academic_years.id, CASCADE ON DELETE |
| name | VARCHAR(255) | |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

## Course Management Tables

### courses
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PRIMARY KEY | |
| title | VARCHAR(255) | | Titre du cours |
| description | TEXT | NULLABLE | Description |
| category | VARCHAR(255) | NULLABLE | Catégorie |
| level | VARCHAR(255) | NULLABLE | Niveau |
| teacher_id | BIGINT | FOREIGN KEY users.id, NULL ON DELETE | Professeur |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

### course_group (Pivot)
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT | PRIMARY KEY |
| course_id | BIGINT | FOREIGN KEY courses.id, CASCADE ON DELETE |
| group_id | BIGINT | FOREIGN KEY groups.id, CASCADE ON DELETE |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### lessons
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PRIMARY KEY | |
| course_id | BIGINT | FOREIGN KEY courses.id, CASCADE ON DELETE, NULLABLE | Cours parent |
| order | INT | DEFAULT 1 | Ordre dans le cours |
| title | VARCHAR(255) | | Titre de la leçon |
| content_text | LONGTEXT | NULLABLE | Contenu texte |
| pdf_path | VARCHAR(255) | NULLABLE | Chemin PDF |
| video_path | VARCHAR(255) | NULLABLE | Chemin vidéo |
| image_path | VARCHAR(255) | NULLABLE | Chemin image |
| tag | VARCHAR(255) | NULLABLE | Tag/catégorie |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

### lesson_group (Pivot - Legacy)
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PRIMARY KEY | |
| lesson_id | BIGINT | FOREIGN KEY lessons.id, CASCADE ON DELETE | |
| group_id | BIGINT | FOREIGN KEY groups.id, CASCADE ON DELETE | |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

### lesson_user
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PRIMARY KEY | |
| lesson_id | BIGINT | FOREIGN KEY lessons.id, CASCADE ON DELETE | |
| user_id | BIGINT | FOREIGN KEY users.id, CASCADE ON DELETE | |
| completed_at | TIMESTAMP | NULLABLE | Date de complétion |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

## Quiz & Assessement Tables

### quizzes
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PRIMARY KEY | |
| lesson_id | BIGINT | FOREIGN KEY lessons.id, CASCADE ON DELETE | Leçon associée |
| title | VARCHAR(255) | | Titre du quiz |
| scheduled_at | DATETIME | NULLABLE | Date programmée |
| is_active | BOOLEAN | DEFAULT false | Actif ou non |
| passing_score | INT | DEFAULT 80 | Score de passage (%) |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

### questions
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PRIMARY KEY | |
| quiz_id | BIGINT | FOREIGN KEY quizzes.id, CASCADE ON DELETE | Quiz parent |
| content_text | TEXT | | Texte de la question |
| image_path | VARCHAR(255) | NULLABLE | Image de la question |
| video_path | VARCHAR(255) | NULLABLE | Vidéo de la question |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

### options
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PRIMARY KEY | |
| question_id | BIGINT | FOREIGN KEY questions.id, CASCADE ON DELETE | Question parent |
| content_text | TEXT | | Texte de l'option |
| is_correct | BOOLEAN | DEFAULT false | Est correcte |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

### answers
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PRIMARY KEY | |
| user_id | BIGINT | FOREIGN KEY users.id, CASCADE ON DELETE | Étudiant |
| quiz_id | BIGINT | FOREIGN KEY quizzes.id, CASCADE ON DELETE | Quiz |
| question_id | BIGINT | FOREIGN KEY questions.id, CASCADE ON DELETE | Question |
| option_id | BIGINT | FOREIGN KEY options.id, CASCADE ON DELETE | Option choisie |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

### results
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PRIMARY KEY | |
| user_id | BIGINT | FOREIGN KEY users.id, CASCADE ON DELETE | Étudiant |
| quiz_id | BIGINT | FOREIGN KEY quizzes.id, CASCADE ON DELETE | Quiz |
| score | INT | DEFAULT 0 | Nombre de bonnes réponses |
| total_questions | INT | DEFAULT 0 | Total de questions |
| is_passed | BOOLEAN | | Réussi (score >= passing_score) |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

### quiz_retakes
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PRIMARY KEY | |
| quiz_id | BIGINT | FOREIGN KEY quizzes.id, CASCADE ON DELETE | Quiz |
| user_id | BIGINT | FOREIGN KEY users.id, CASCADE ON DELETE | Étudiant |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

## Tracking Tables

### student_progress
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PRIMARY KEY | |
| user_id | BIGINT | FOREIGN KEY users.id, CASCADE ON DELETE | Étudiant |
| lesson_id | BIGINT | FOREIGN KEY lessons.id, CASCADE ON DELETE | Leçon |
| is_completed | BOOLEAN | DEFAULT false | Complétée |
| last_accessed_at | TIMESTAMP | NULLABLE | Dernière visite |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

## Communication Tables

### messages
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PRIMARY KEY | |
| sender_id | BIGINT | FOREIGN KEY users.id, CASCADE ON DELETE | Expéditeur |
| receiver_id | BIGINT | FOREIGN KEY users.id, CASCADE ON DELETE | Destinataire |
| content | TEXT | | Contenu du message |
| read_at | TIMESTAMP | NULLABLE | Date de lecture |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

## Framework Tables

### cache
| Column | Type | Constraints |
|--------|------|-------------|
| key | VARCHAR(255) | PRIMARY KEY |
| value | LONGTEXT | |
| expiration | INT | |

### jobs
| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY |
| queue | VARCHAR(255) | |
| payload | LONGTEXT | |
| attempts | TINYINT UNSIGNED | |
| reserved_at | INT UNSIGNED | NULLABLE |
| available_at | INT UNSIGNED | |
| created_at | INT UNSIGNED | |

### password_reset_tokens
| Column | Type | Constraints |
|--------|------|-------------|
| email | VARCHAR(255) | PRIMARY KEY |
| token | VARCHAR(255) | |
| created_at | TIMESTAMP | NULLABLE |

### sessions
| Column | Type | Constraints |
|--------|------|-------------|
| id | VARCHAR(255) | PRIMARY KEY |
| user_id | BIGINT | NULLABLE |
| ip_address | VARCHAR(45) | NULLABLE |
| user_agent | TEXT | NULLABLE |
| payload | LONGTEXT | |
| last_activity | INT | |

## Relationships Summary

### Many-to-Many
- `Course` ↔ `Group` (via `course_group`)
- `Lesson` ↔ `Group` (via `lesson_group`, legacy)
- `Lesson` ↔ `User` (via `lesson_user`, tracking completion)

### One-to-Many
- `School` → `Branch`
- `Branch` → `Group`
- `AcademicYear` → `Group`
- `Group` → `User` (students)
- `Course` → `Lesson`
- `Lesson` → `Quiz`
- `Quiz` → `Question`
- `Question` → `Option`
- `Quiz` → `Result`
- `User` → `Result`
- `User` → `Answer`
- `Quiz` → `Answer`
- `Quiz` → `QuizRetake`
- `User` → `Message` (sent)
- `User` → `Message` (received)
- `User` → `StudentProgress`
- `Lesson` → `StudentProgress`
