package com.studentportal.model;

import java.io.Serializable;
import java.sql.Timestamp;

/**
 * Model representing an uploaded file/document associated with a student.
 */
public class StudentFile implements Serializable {
    private static final long serialVersionUID = 1L;

    private int id;
    private int studentId;
    private String originalName;
    private String storedName;
    private String fileType;
    private String fileCategory; // 'profile_photo', 'id_card', 'document', 'resume'
    private long fileSize;
    private String filePath;
    private Timestamp uploadedAt;

    public StudentFile() {}

    public StudentFile(int id, int studentId, String originalName, String storedName,
                       String fileType, String fileCategory, long fileSize,
                       String filePath, Timestamp uploadedAt) {
        this.id = id;
        this.studentId = studentId;
        this.originalName = originalName;
        this.storedName = storedName;
        this.fileType = fileType;
        this.fileCategory = fileCategory;
        this.fileSize = fileSize;
        this.filePath = filePath;
        this.uploadedAt = uploadedAt;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getStudentId() {
        return studentId;
    }

    public void setStudentId(int studentId) {
        this.studentId = studentId;
    }

    public String getOriginalName() {
        return originalName;
    }

    public void setOriginalName(String originalName) {
        this.originalName = originalName;
    }

    public String getStoredName() {
        return storedName;
    }

    public void setStoredName(String storedName) {
        this.storedName = storedName;
    }

    public String getFileType() {
        return fileType;
    }

    public void setFileType(String fileType) {
        this.fileType = fileType;
    }

    public String getFileCategory() {
        return fileCategory;
    }

    public void setFileCategory(String fileCategory) {
        this.fileCategory = fileCategory;
    }

    public long getFileSize() {
        return fileSize;
    }

    public void setFileSize(long fileSize) {
        this.fileSize = fileSize;
    }

    public String getFilePath() {
        return filePath;
    }

    public void setFilePath(String filePath) {
        this.filePath = filePath;
    }

    public Timestamp getUploadedAt() {
        return uploadedAt;
    }

    public void setUploadedAt(Timestamp uploadedAt) {
        this.uploadedAt = uploadedAt;
    }

    /**
     * Checks if this file is an image based on MIME type or category.
     */
    public boolean isImage() {
        if (fileType != null && fileType.toLowerCase().startsWith("image/")) {
            return true;
        }
        return "profile_photo".equalsIgnoreCase(fileCategory) || "id_card".equalsIgnoreCase(fileCategory);
    }

    /**
     * Checks if this file is a PDF document.
     */
    public boolean isPdf() {
        return (fileType != null && fileType.toLowerCase().contains("pdf"))
                || (originalName != null && originalName.toLowerCase().endsWith(".pdf"));
    }

    /**
     * Returns human-readable formatted file size (e.g. 45.2 KB).
     */
    public String getFormattedSize() {
        if (fileSize < 1024) {
            return fileSize + " B";
        } else if (fileSize < 1024 * 1024) {
            return String.format("%.1f KB", fileSize / 1024.0);
        } else {
            return String.format("%.1f MB", fileSize / (1024.0 * 1024.0));
        }
    }
}
