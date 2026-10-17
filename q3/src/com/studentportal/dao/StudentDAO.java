package com.studentportal.dao;

import com.studentportal.model.Student;
import com.studentportal.model.StudentFile;
import com.studentportal.util.DBConnection;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

/**
 * Data Access Object for Students and Associated Uploaded Files.
 * Uses JDBC to interact with MySQL student_portal database.
 */
public class StudentDAO {

    /**
     * Fetches all students along with their associated files using a LEFT JOIN query.
     * Groups files under their respective student instances.
     * @return List of Student objects containing their attached files.
     */
    public List<Student> getAllStudentsWithFiles() {
        return searchStudentsWithFiles(null);
    }

    /**
     * Searches students by keyword (name, roll_no, department, or course)
     * and loads all associated file paths via JDBC.
     */
    public List<Student> searchStudentsWithFiles(String searchKeyword) {
        Connection conn = null;
        PreparedStatement stmt = null;
        ResultSet rs = null;
        Map<Integer, Student> studentMap = new LinkedHashMap<>();

        try {
            conn = DBConnection.getConnection();

            StringBuilder sql = new StringBuilder();
            sql.append("SELECT s.id AS s_id, s.name, s.roll_no, s.email, s.course, s.department, ")
               .append("       s.semester, s.cgpa, s.phone, s.bio, ")
               .append("       f.id AS f_id, f.original_name, f.stored_name, f.file_type, ")
               .append("       f.file_category, f.file_size, f.file_path, f.uploaded_at ")
               .append("FROM students s ")
               .append("LEFT JOIN student_files f ON s.id = f.student_id ");

            if (searchKeyword != null && !searchKeyword.trim().isEmpty()) {
                sql.append("WHERE LOWER(s.name) LIKE ? ")
                   .append("   OR LOWER(s.roll_no) LIKE ? ")
                   .append("   OR LOWER(s.department) LIKE ? ")
                   .append("   OR LOWER(s.course) LIKE ? ");
            }

            sql.append("ORDER BY s.id ASC, f.id ASC");

            stmt = conn.prepareStatement(sql.toString());

            if (searchKeyword != null && !searchKeyword.trim().isEmpty()) {
                String pattern = "%" + searchKeyword.trim().toLowerCase() + "%";
                stmt.setString(1, pattern);
                stmt.setString(2, pattern);
                stmt.setString(3, pattern);
                stmt.setString(4, pattern);
            }

            rs = stmt.executeQuery();

            while (rs.next()) {
                int studentId = rs.getInt("s_id");
                Student student = studentMap.get(studentId);

                if (student == null) {
                    student = new Student();
                    student.setId(studentId);
                    student.setName(rs.getString("name"));
                    student.setRollNo(rs.getString("roll_no"));
                    student.setEmail(rs.getString("email"));
                    student.setCourse(rs.getString("course"));
                    student.setDepartment(rs.getString("department"));
                    student.setSemester(rs.getInt("semester"));
                    student.setCgpa(rs.getDouble("cgpa"));
                    student.setPhone(rs.getString("phone"));
                    student.setBio(rs.getString("bio"));
                    studentMap.put(studentId, student);
                }

                // If associated file exists (f_id is not null)
                int fileId = rs.getInt("f_id");
                if (fileId > 0 && !rs.wasNull()) {
                    StudentFile file = new StudentFile();
                    file.setId(fileId);
                    file.setStudentId(studentId);
                    file.setOriginalName(rs.getString("original_name"));
                    file.setStoredName(rs.getString("stored_name"));
                    file.setFileType(rs.getString("file_type"));
                    file.setFileCategory(rs.getString("file_category"));
                    file.setFileSize(rs.getLong("file_size"));
                    file.setFilePath(rs.getString("file_path"));
                    file.setUploadedAt(rs.getTimestamp("uploaded_at"));

                    student.addFile(file);
                }
            }

        } catch (SQLException e) {
            System.err.println("JDBC error in searchStudentsWithFiles: " + e.getMessage());
            e.printStackTrace();
        } finally {
            DBConnection.close(conn, stmt, rs);
        }

        return new ArrayList<>(studentMap.values());
    }

    /**
     * Retrieves a single student with all their associated files by student ID.
     */
    public Student getStudentById(int studentId) {
        Connection conn = null;
        PreparedStatement stmt = null;
        ResultSet rs = null;
        Student student = null;

        try {
            conn = DBConnection.getConnection();
            String sql = "SELECT s.id AS s_id, s.name, s.roll_no, s.email, s.course, s.department, "
                    + "       s.semester, s.cgpa, s.phone, s.bio, "
                    + "       f.id AS f_id, f.original_name, f.stored_name, f.file_type, "
                    + "       f.file_category, f.file_size, f.file_path, f.uploaded_at "
                    + "FROM students s "
                    + "LEFT JOIN student_files f ON s.id = f.student_id "
                    + "WHERE s.id = ? "
                    + "ORDER BY f.id ASC";

            stmt = conn.prepareStatement(sql);
            stmt.setInt(1, studentId);
            rs = stmt.executeQuery();

            while (rs.next()) {
                if (student == null) {
                    student = new Student();
                    student.setId(rs.getInt("s_id"));
                    student.setName(rs.getString("name"));
                    student.setRollNo(rs.getString("roll_no"));
                    student.setEmail(rs.getString("email"));
                    student.setCourse(rs.getString("course"));
                    student.setDepartment(rs.getString("department"));
                    student.setSemester(rs.getInt("semester"));
                    student.setCgpa(rs.getDouble("cgpa"));
                    student.setPhone(rs.getString("phone"));
                    student.setBio(rs.getString("bio"));
                }

                int fileId = rs.getInt("f_id");
                if (fileId > 0 && !rs.wasNull()) {
                    StudentFile file = new StudentFile();
                    file.setId(fileId);
                    file.setStudentId(studentId);
                    file.setOriginalName(rs.getString("original_name"));
                    file.setStoredName(rs.getString("stored_name"));
                    file.setFileType(rs.getString("file_type"));
                    file.setFileCategory(rs.getString("file_category"));
                    file.setFileSize(rs.getLong("file_size"));
                    file.setFilePath(rs.getString("file_path"));
                    file.setUploadedAt(rs.getTimestamp("uploaded_at"));

                    student.addFile(file);
                }
            }
        } catch (SQLException e) {
            System.err.println("JDBC error in getStudentById: " + e.getMessage());
            e.printStackTrace();
        } finally {
            DBConnection.close(conn, stmt, rs);
        }

        return student;
    }

    /**
     * Fetches file record metadata by file ID.
     */
    public StudentFile getFileById(int fileId) {
        Connection conn = null;
        PreparedStatement stmt = null;
        ResultSet rs = null;
        StudentFile file = null;

        try {
            conn = DBConnection.getConnection();
            String sql = "SELECT id, student_id, original_name, stored_name, file_type, file_category, file_size, file_path, uploaded_at "
                    + "FROM student_files WHERE id = ?";
            stmt = conn.prepareStatement(sql);
            stmt.setInt(1, fileId);
            rs = stmt.executeQuery();

            if (rs.next()) {
                file = new StudentFile();
                file.setId(rs.getInt("id"));
                file.setStudentId(rs.getInt("student_id"));
                file.setOriginalName(rs.getString("original_name"));
                file.setStoredName(rs.getString("stored_name"));
                file.setFileType(rs.getString("file_type"));
                file.setFileCategory(rs.getString("file_category"));
                file.setFileSize(rs.getLong("file_size"));
                file.setFilePath(rs.getString("file_path"));
                file.setUploadedAt(rs.getTimestamp("uploaded_at"));
            }
        } catch (SQLException e) {
            System.err.println("JDBC error in getFileById: " + e.getMessage());
            e.printStackTrace();
        } finally {
            DBConnection.close(conn, stmt, rs);
        }

        return file;
    }

    /**
     * Fetches a student's profile photo file record by student ID.
     */
    public StudentFile getProfilePhotoByStudentId(int studentId) {
        Connection conn = null;
        PreparedStatement stmt = null;
        ResultSet rs = null;
        StudentFile file = null;

        try {
            conn = DBConnection.getConnection();
            String sql = "SELECT id, student_id, original_name, stored_name, file_type, file_category, file_size, file_path, uploaded_at "
                    + "FROM student_files WHERE student_id = ? AND file_category = 'profile_photo' LIMIT 1";
            stmt = conn.prepareStatement(sql);
            stmt.setInt(1, studentId);
            rs = stmt.executeQuery();

            if (rs.next()) {
                file = new StudentFile();
                file.setId(rs.getInt("id"));
                file.setStudentId(rs.getInt("student_id"));
                file.setOriginalName(rs.getString("original_name"));
                file.setStoredName(rs.getString("stored_name"));
                file.setFileType(rs.getString("file_type"));
                file.setFileCategory(rs.getString("file_category"));
                file.setFileSize(rs.getLong("file_size"));
                file.setFilePath(rs.getString("file_path"));
                file.setUploadedAt(rs.getTimestamp("uploaded_at"));
            }
        } catch (SQLException e) {
            System.err.println("JDBC error in getProfilePhotoByStudentId: " + e.getMessage());
            e.printStackTrace();
        } finally {
            DBConnection.close(conn, stmt, rs);
        }

        return file;
    }
}
