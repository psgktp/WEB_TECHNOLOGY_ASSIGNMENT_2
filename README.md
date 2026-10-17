# Web Technology Assignment II (U21CS501)

A complete, production-grade implementation of **Web Technology Assignment II**, comprising client-side validation forms, PHP/MySQL administrative CRUD with secure binary streaming, and a full-stack Java EE Servlet/JSP enterprise portal with JDBC and session-based access control.

---

## 📁 Repository Structure

```
WEB_TECHNOLOGY_ASSIGNMENT_2/
├── q1/                         # Question 1: Student Admission Registration Form
│   ├── registration.html       # Semantic HTML5 Form with multi-attribute controls
│   ├── style.css               # Modern responsive layout & card styling
│   └── validation.js           # Client-side form & password strength validation
│
├── q2/                         # Question 2: Student Management System (PHP & MySQL)
│   ├── add.php                 # Add new student record with document/image upload
│   ├── database.sql            # MySQL schema DDL & seed data (Roll: 1001-1005)
│   ├── db.php                  # Database connection handler (mysqli)
│   ├── delete.php              # Delete record and cleanup physical files
│   ├── edit.php                # Update student records & change attachments
│   ├── index.php               # Dashboard listing students & file counts
│   ├── stream.php              # Secure binary stream handler (prevents path exposure)
│   ├── style.css               # Clean responsive styling for admin panel
│   ├── upload.php              # Multi-file upload interface with MIME/size checks
│   └── view.php                # Detailed profile view with file metadata table
│
├── q3/                         # Question 3: Dynamic Student Profiles Portal (Java EE)
│   ├── src/                    # Java EE Source Code
│   │   └── com/studentportal/
│   │       ├── dao/            # StudentDAO & UserDAO (JDBC Operations)
│   │       ├── filter/         # AuthenticationFilter (Session security)
│   │       ├── model/          # Student, StudentFile, User entity models
│   │       ├── servlet/        # Login, Logout, StudentPortal, FileStream Servlets
│   │       └── util/           # DBConnection utility (MySQL Connector/J)
│   ├── WebContent/             # Web application resources
│   │   ├── css/style.css       # Enterprise portal styling
│   │   ├── WEB-INF/web.xml     # Deployment descriptor & servlet mappings
│   │   ├── login.jsp           # Secure authentication interface
│   │   ├── students.jsp        # Dynamic profile dashboard with <img> streaming
│   │   ├── student-detail.jsp  # Comprehensive student profile with downloads
│   │   └── error.jsp           # Global error handler
│   ├── sql/                    # Java portal database initialization script
│   ├── uploads/                # Shared uploaded documents directory
│   ├── deploy.bat              # Automated build & deployment script
│   └── README.md               # Q3 detailed technical documentation
│
└── README.md                   # Repository overview & setup guide
```

---

## 🚀 Module Overview

### 1. Question 1 – Student Admission Registration Form
- **Technologies:** HTML5, CSS3, JavaScript (ES6+).
- **Key Features:**
  - Responsive two-column desktop grid with mobile adaptation.
  - Interactive client-side validation preventing empty submissions.
  - Email format verification via RFC-compliant regular expression.
  - Dynamic password strength meter evaluating length, uppercase, numbers, and symbols.
  - Client-side file extension (JPG, PNG, PDF) and size limit (<= 2MB) validation.

### 2. Question 2 – Student Management System (PHP & MySQL)
- **Technologies:** PHP 8+, MySQL (port 3306), Apache (XAMPP).
- **Key Features:**
  - Full CRUD lifecycle (Add, View, Edit, Delete).
  - Prepared statements (`mysqli_stmt`) to defend against SQL Injection.
  - Secure file upload handling: MIME-type verification, 2MB size cap, and timestamp-based unique file naming (`time() . '_' . filename`).
  - `stream.php` binary file streaming handler with dynamic `Content-Type` headers.
  - Normalized database schema linking `students` and `student_files` via foreign keys with `CASCADE DELETE`.
  - Continuous numeric roll numbers (`1001` to `1005`).

### 3. Question 3 – Dynamic Student Profiles (Java Servlet, JDBC, JSP)
- **Technologies:** Java JDK 17+, Apache Tomcat 9/10, MySQL Connector/J, JSP/JSTL.
- **Key Features:**
  - MVC Architecture: Clean separation of models, DAOs, Servlets, and JSP views.
  - Session Management: `AuthenticationFilter` intercepts protected routes (`/students`, `/stream`, `/profile`), redirecting unauthenticated requests to `login.jsp`.
  - Binary Image & Document Streaming: `FileStreamServlet` writes directly to `response.getOutputStream()` without exposing filesystem paths.
  - Dynamic Rendering: JSTL `<c:forEach>` loops and conditionals render student profiles and fallback avatar initials dynamically.

---

## 🛠️ Setup & Installation Instructions

### Prerequisites
1. **XAMPP / WAMP:** Apache HTTP Server & MySQL running on port 3306.
2. **Apache Tomcat:** Tomcat 9/10 running on port 8080.
3. **Java Development Kit:** JDK 11 or higher.

---

### Database Setup
1. Open phpMyAdmin (`http://localhost/phpmyadmin`) or MySQL CLI.
2. Import the schema script located at `q2/database.sql`:
   ```sql
   mysql -u root -p < q2/database.sql
   ```
3. Default administrative credentials:
   - **Username:** `admin`
   - **Password:** `admin123`

---

### Running Question 1 (Registration Form)
Simply open `q1/registration.html` in any modern web browser:
```bash
start q1/registration.html
```

---

### Running Question 2 (PHP Admin Panel)
1. Copy or symlink the `q2/` directory to your web server root (e.g., `C:/xampp/htdocs/student_crud`).
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Access the application in your browser:
   ```
   http://localhost/student_crud/
   ```

---

### Running Question 3 (Java EE Portal)
1. Deploy the `q3/` web application to Apache Tomcat:
   - Copy `q3/WebContent` to `CATALINA_HOME/webapps/student_portal`, or
   - Execute the automated build script:
     ```cmd
     cd q3
     deploy.bat
     ```
2. Start Apache Tomcat:
   ```cmd
   %CATALINA_HOME%\bin\startup.bat
   ```
3. Access the portal in your browser:
   ```
   http://localhost:8080/student_portal/login.jsp
   ```

---

## 🔒 Security Implementations
- **Prepared Statements:** Parameterized queries across both PHP and Java layers prevent SQL Injection.
- **Access Control:** Servlet session validation with `AuthenticationFilter` protecting authenticated endpoints.
- **Secure File Streaming:** Zero direct filesystem path disclosure; all files are streamed through authorized handlers (`stream.php` and `FileStreamServlet`).
- **File Validation:** Multi-layer validation enforcing allowed MIME types (JPEG, PNG, PDF) and strict file size boundaries.

---

## 📜 License
Developed for academic assessment purposes under Course Code **U21CS501 – Web Technology**.
