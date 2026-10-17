package com.studentportal.model;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.List;

/**
 * Model representing a Student along with their uploaded files and documents.
 */
public class Student implements Serializable {
    private static final long serialVersionUID = 1L;

    private int id;
    private String name;
    private String rollNo;
    private String email;
    private String course;
    private String department;
    private int semester;
    private double cgpa;
    private String phone;
    private String bio;
    private List<StudentFile> files = new ArrayList<>();

    public Student() {}

    public Student(int id, String name, String rollNo, String email, String course,
                   String department, int semester, double cgpa, String phone, String bio) {
        this.id = id;
        this.name = name;
        this.rollNo = rollNo;
        this.email = email;
        this.course = course;
        this.department = department;
        this.semester = semester;
        this.cgpa = cgpa;
        this.phone = phone;
        this.bio = bio;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getRollNo() {
        return rollNo;
    }

    public void setRollNo(String rollNo) {
        this.rollNo = rollNo;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getCourse() {
        return course;
    }

    public void setCourse(String course) {
        this.course = course;
    }

    public String getDepartment() {
        return department;
    }

    public void setDepartment(String department) {
        this.department = department;
    }

    public int getSemester() {
        return semester;
    }

    public void setSemester(int semester) {
        this.semester = semester;
    }

    public double getCgpa() {
        return cgpa;
    }

    public void setCgpa(double cgpa) {
        this.cgpa = cgpa;
    }

    public String getPhone() {
        return phone;
    }

    public void setPhone(String phone) {
        this.phone = phone;
    }

    public String getBio() {
        return bio;
    }

    public void setBio(String bio) {
        this.bio = bio;
    }

    public List<StudentFile> getFiles() {
        return files;
    }

    public void setFiles(List<StudentFile> files) {
        this.files = files;
    }

    public void addFile(StudentFile file) {
        if (this.files == null) {
            this.files = new ArrayList<>();
        }
        this.files.add(file);
    }

    /**
     * Finds the profile photo file associated with the student, if present.
     */
    public StudentFile getProfilePhoto() {
        if (files == null) return null;
        for (StudentFile file : files) {
            if ("profile_photo".equalsIgnoreCase(file.getFileCategory())) {
                return file;
            }
        }
        return null;
    }

    /**
     * Checks if the student has an uploaded profile photo.
     */
    public boolean hasProfilePhoto() {
        return getProfilePhoto() != null;
    }

    /**
     * Returns all documents (excluding profile photo) for this student.
     */
    public List<StudentFile> getDocumentFiles() {
        List<StudentFile> docs = new ArrayList<>();
        if (files == null) return docs;
        for (StudentFile file : files) {
            if (!"profile_photo".equalsIgnoreCase(file.getFileCategory())) {
                docs.add(file);
            }
        }
        return docs;
    }

    /**
     * Returns student's initials for avatar placeholders.
     */
    public String getInitials() {
        if (name == null || name.trim().isEmpty()) return "ST";
        String[] parts = name.trim().split("\\s+");
        if (parts.length >= 2) {
            return ("" + parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
        } else {
            return name.substring(0, Math.min(2, name.length())).toUpperCase();
        }
    }
}
