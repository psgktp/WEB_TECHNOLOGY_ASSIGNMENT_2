# Student Front-End Portal: Servlets, JSP, JDBC & Streaming Architecture

A robust enterprise Java Web Application demonstrating secure session-managed student profile exploration, dynamic document and image streaming from local disk storage, and dynamic JSP rendering using JDBC and MySQL.

---

## Architecture Overview

```
                                      +---------------------------------------------+
                                      |                 Browser                     |
                                      +---------------------------------------------+
                                        |                   ^                     |
             1. POST /login (Credentials)                   | 3. HTML with        | 4. GET /stream-file
                                        v                   |    <img src=...>    |    (Protected)
       +----------------------------------+                 |                     v
       |    AuthenticationFilter.java     |                 |          +----------------------+
       |  (Validates active HttpSession)  |                 |          | FileStreamServlet    |
       +----------------------------------+                 |          +----------------------+
                        |                                   |                     |
           +------------+------------+                      |                     | 5. Stream binary
           |                         |                      |                     |    bytes with MIME
           v                         v                      |                     v
+--------------------+    +-----------------------+         |          +----------------------+
|  LoginServlet      |    | StudentPortalServlet  |---------+          | Uploads Storage /    |
+--------------------+    +-----------------------+                    | Physical Disk Files  |
           |                         |                                 +----------------------+
           v                         v
+--------------------+    +-----------------------+
|   UserDAO.java     |    |   StudentDAO.java     |
+--------------------+    +-----------------------+
           |                         |
           +------------+------------+
                        | (JDBC / Connection Pool)
                        v
         +-----------------------------+
         | MySQL: `student_portal` DB  |
         +-----------------------------+
```

---

## Key Features & Requirements Matrix

| Requirement | Implementation Component | Technical Highlights |
|---|---|---|
| **1. MySQL JDBC Connection** | [`DBConnection.java`](file:///d:/wt2/q3/src/com/studentportal/util/DBConnection.java), [`StudentDAO.java`](file:///d:/wt2/q3/src/com/studentportal/dao/StudentDAO.java) | Loads MySQL Connector/J driver, manages prepared statements, and executes optimized queries. |
| **2. Image/Document Streaming** | [`FileStreamServlet.java`](file:///d:/wt2/q3/src/com/studentportal/servlet/FileStreamServlet.java) | Streams binary chunks with `BufferedInputStream` / `BufferedOutputStream`, sets `Content-Type` dynamically, prevents directory traversal, and supports inline viewing & attachment download. |
| **3. Dynamic HTML/JSP & `<img>` Tags** | [`students.jsp`](file:///d:/wt2/q3/WebContent/students.jsp) | `<img src="stream-file?studentId=...&category=profile_photo">` tags dynamically invoking the servlet for profile and proof display. |
| **4. Looping & Conditional Logic** | [`students.jsp`](file:///d:/wt2/q3/WebContent/students.jsp), [`student-detail.jsp`](file:///d:/wt2/q3/WebContent/student-detail.jsp) | Iterates over student lists; checks for presence of profile photos, falls back to default avatar, conditional badges for CGPA, and renders empty-state cards when no files exist. |
| **5. Session Handling & Security** | [`AuthenticationFilter.java`](file:///d:/wt2/q3/src/com/studentportal/filter/AuthenticationFilter.java), [`LoginServlet.java`](file:///d:/wt2/q3/src/com/studentportal/servlet/LoginServlet.java), [`LogoutServlet.java`](file:///d:/wt2/q3/src/com/studentportal/servlet/LogoutServlet.java) | Rejects unauthenticated requests with `302 Redirect` to `login.jsp?error=unauthorized`, invalidates session upon logout, and avoids session fixation. |

---

## Database Configuration

- **Database Name**: `student_portal`
- **Port**: `3306`
- **User**: `root`
- **Password**: *(empty)*
- **Script**: Located at [`sql/database_setup.sql`](file:///d:/wt2/q3/sql/database_setup.sql)

### Tables
1. `users`: Stores user credentials (`admin`, `faculty`, `student`).
2. `students`: Stores student demographic and academic records (`id`, `name`, `roll_no`, `course`, `department`, `cgpa`, etc.).
3. `student_files`: Stores metadata and physical disk paths for uploaded images, proofs, and documents.

---

## Demo Credentials

| Role | Username | Password |
|---|---|---|
| **Administrator** | `admin` | `admin123` |
| **Faculty Member** | `faculty` | `faculty123` |
| **Student** | `student` | `student123` |

---

## How to Build and Run

1. **Verify MySQL and Apache Tomcat**:
   Ensure MySQL is running on port `3306` (e.g., via XAMPP Control Panel) and Tomcat is available on port `8080`.

2. **Execute Database Setup**:
   ```powershell
   Get-Content "d:\wt2\q3\sql\database_setup.sql" | & "D:\xampp\mysql\bin\mysql.exe" -u root
   ```

3. **Compile & Deploy**:
   Run the automated build script:
   ```cmd
   d:\wt2\q3\deploy.bat
   ```

4. **Access in Web Browser**:
   Navigate to:
   ```
   http://localhost:8080/q3/
   ```
