<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="com.studentportal.model.Student" %>
<%@ page import="com.studentportal.model.StudentFile" %>
<%@ page import="com.studentportal.model.User" %>
<%
    User currentUser = (User) session.getAttribute("user");
    if (currentUser == null) {
        response.sendRedirect(request.getContextPath() + "/login.jsp?error=unauthorized");
        return;
    }

    Student student = (Student) request.getAttribute("student");
    if (student == null) {
        response.sendRedirect(request.getContextPath() + "/students");
        return;
    }
%>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><%= student.getName() %> - Student Detail View</title>
    <link rel="stylesheet" href="<%= request.getContextPath() %>/css/style.css">
    <style>
        .profile-hero {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
        }
        .hero-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary-light);
            box-shadow: var(--shadow-md);
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
        @media (max-width: 768px) {
            .detail-grid { grid-template-columns: 1fr; }
            .profile-hero { flex-direction: column; text-align: center; }
        }
        .detail-panel {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
        }
        .detail-panel h3 {
            font-size: 16px;
            margin-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 8px;
            color: var(--secondary);
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 8px 4px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        .info-table td.label {
            font-weight: 600;
            color: var(--text-muted);
            width: 35%;
        }
    </style>
</head>
<body>

    <!-- Main Navigation Header -->
    <header class="navbar">
        <a href="<%= request.getContextPath() %>/students" class="nav-brand">
            <span style="font-size: 22px;">&#127891;</span>
            <span>Student Portal</span>
            <span class="tag">Detail View</span>
        </a>

        <div class="nav-user">
            <div class="user-badge">
                <span>&#128100;</span>
                <strong><%= currentUser.getFullName() %></strong>
            </div>
            <a href="<%= request.getContextPath() %>/students" class="btn btn-secondary btn-sm">
                &larr; Back to Directory
            </a>
            <a href="<%= request.getContextPath() %>/logout" class="btn btn-outline btn-sm btn-danger">
                Sign Out
            </a>
        </div>
    </header>

    <main class="container">
        
        <!-- Profile Hero Section -->
        <div class="profile-hero">
            <% if (student.hasProfilePhoto()) { %>
                <img src="<%= request.getContextPath() %>/stream-file?studentId=<%= student.getId() %>&category=profile_photo" 
                     alt="<%= student.getName() %>" class="hero-avatar">
            <% } else { %>
                <img src="<%= request.getContextPath() %>/stream-file?default=true" 
                     alt="<%= student.getName() %>" class="hero-avatar">
            <% } %>

            <div style="flex: 1;">
                <h2><%= student.getName() %></h2>
                <div style="display: flex; gap: 8px; align-items: center; margin-top: 6px;">
                    <span class="roll-badge"><%= student.getRollNo() %></span>
                    <span style="color: var(--text-muted);">&bull;</span>
                    <span style="color: var(--text-muted); font-size: 14px;"><%= student.getCourse() %></span>
                </div>
                <p style="margin-top: 10px; color: var(--text-muted); font-size: 14px; max-width: 600px;">
                    <%= (student.getBio() != null) ? student.getBio() : "No biographical overview provided." %>
                </p>
            </div>
        </div>

        <div class="detail-grid">
            <!-- Academic & Contact Details -->
            <div class="detail-panel">
                <h3>Academic &amp; Contact Information</h3>
                <table class="info-table">
                    <tr>
                        <td class="label">Roll Number</td>
                        <td><%= student.getRollNo() %></td>
                    </tr>
                    <tr>
                        <td class="label">Email Address</td>
                        <td><%= student.getEmail() %></td>
                    </tr>
                    <tr>
                        <td class="label">Phone</td>
                        <td><%= student.getPhone() %></td>
                    </tr>
                    <tr>
                        <td class="label">Department</td>
                        <td><%= student.getDepartment() %></td>
                    </tr>
                    <tr>
                        <td class="label">Semester</td>
                        <td>Semester <%= student.getSemester() %></td>
                    </tr>
                    <tr>
                        <td class="label">Current CGPA</td>
                        <td><strong><%= String.format("%.2f", student.getCgpa()) %></strong> / 10.00</td>
                    </tr>
                </table>
            </div>

            <!-- Uploaded Documents & Media -->
            <div class="detail-panel">
                <h3>Uploaded Images &amp; Documents</h3>
                <% if (student.getFiles() == null || student.getFiles().isEmpty()) { %>
                    <div class="empty-files">
                        No files or documents uploaded for this student.
                    </div>
                <% } else { %>
                    <div class="files-list">
                        <% for (StudentFile file : student.getFiles()) { %>
                            <div class="file-item">
                                <% if (file.isImage()) { %>
                                    <img src="<%= request.getContextPath() %>/stream-file?fileId=<%= file.getId() %>" 
                                         alt="<%= file.getOriginalName() %>" 
                                         class="file-thumb-preview">
                                <% } else if (file.isPdf()) { %>
                                    <div class="file-icon-wrap pdf">&#128196;</div>
                                <% } else { %>
                                    <div class="file-icon-wrap doc">&#128193;</div>
                                <% } %>

                                <div class="file-details">
                                    <div class="file-name"><%= file.getOriginalName() %></div>
                                    <div class="file-sub">
                                        <span><%= file.getFileCategory().toUpperCase() %></span> &bull; 
                                        <span><%= file.getFormattedSize() %></span> &bull;
                                        <span>Uploaded: <%= file.getUploadedAt() %></span>
                                    </div>
                                </div>

                                <div class="file-actions">
                                    <a href="<%= request.getContextPath() %>/stream-file?fileId=<%= file.getId() %>" 
                                       target="_blank" class="btn btn-outline btn-sm">Stream / View</a>
                                    <a href="<%= request.getContextPath() %>/stream-file?fileId=<%= file.getId() %>&download=true" 
                                       class="btn btn-secondary btn-sm">Download</a>
                                </div>
                            </div>
                        <% } %>
                    </div>
                <% } %>
            </div>
        </div>

    </main>

    <footer class="footer">
        <p>&copy; 2026 Student Front-End Portal</p>
    </footer>

</body>
</html>
