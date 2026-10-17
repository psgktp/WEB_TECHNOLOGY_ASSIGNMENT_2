<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.util.List" %>
<%@ page import="com.studentportal.model.Student" %>
<%@ page import="com.studentportal.model.StudentFile" %>
<%@ page import="com.studentportal.model.User" %>
<%
    // ========================================================
    // Session Verification & Access Security
    // Ensures only authenticated users can view student records
    // ========================================================
    User currentUser = (User) session.getAttribute("user");
    if (currentUser == null) {
        response.sendRedirect(request.getContextPath() + "/login.jsp?error=unauthorized");
        return;
    }

    @SuppressWarnings("unchecked")
    List<Student> students = (List<Student>) request.getAttribute("students");
    
    // In case user accesses students.jsp directly without hitting servlet
    if (students == null) {
        response.sendRedirect(request.getContextPath() + "/students");
        return;
    }

    Integer totalStudents = (Integer) request.getAttribute("totalStudents");
    Integer totalFiles = (Integer) request.getAttribute("totalFiles");
    Integer totalPhotos = (Integer) request.getAttribute("totalPhotos");
    Integer totalDocuments = (Integer) request.getAttribute("totalDocuments");
    String searchKeyword = (String) request.getAttribute("searchKeyword");
    if (searchKeyword == null) searchKeyword = "";
%>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Profiles & Uploaded Documents</title>
    <link rel="stylesheet" href="<%= request.getContextPath() %>/css/style.css">
</head>
<body>

    <!-- Main Navigation Header -->
    <header class="navbar">
        <a href="<%= request.getContextPath() %>/students" class="nav-brand">
            <span style="font-size: 22px;">&#127891;</span>
            <span>Student Portal</span>
            <span class="tag">Active Session</span>
        </a>

        <div class="nav-user">
            <div class="user-badge">
                <span>&#128100;</span>
                <strong><%= currentUser.getFullName() %></strong>
                <span class="user-role-tag"><%= currentUser.getRole() %></span>
            </div>
            <a href="<%= request.getContextPath() %>/logout" class="btn btn-outline btn-sm btn-danger">
                <span>&#128274;</span> Sign Out
            </a>
        </div>
    </header>

    <!-- Main Container -->
    <main class="container">

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <h2>Student Records &amp; Documents</h2>
                <p>Viewing verified student profiles, uploaded ID proofs, and academic credentials.</p>
            </div>
        </div>

        <!-- Metric Summary Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">&#128101;</div>
                <div class="stat-info">
                    <div class="stat-value"><%= (totalStudents != null) ? totalStudents : 0 %></div>
                    <div class="stat-label">Enrolled Students</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">&#128193;</div>
                <div class="stat-info">
                    <div class="stat-value"><%= (totalFiles != null) ? totalFiles : 0 %></div>
                    <div class="stat-label">Total Files Stored</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">&#128247;</div>
                <div class="stat-info">
                    <div class="stat-value"><%= (totalPhotos != null) ? totalPhotos : 0 %></div>
                    <div class="stat-label">Profile Images Streamed</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber">&#128196;</div>
                <div class="stat-info">
                    <div class="stat-value"><%= (totalDocuments != null) ? totalDocuments : 0 %></div>
                    <div class="stat-label">Academic Documents</div>
                </div>
            </div>
        </div>

        <!-- Search & Filter Form -->
        <div class="search-bar-wrap">
            <form action="<%= request.getContextPath() %>/students" method="GET" class="search-form">
                <div class="search-input-wrap">
                    <span class="search-icon">&#128269;</span>
                    <input type="text" name="search" class="search-input" 
                           placeholder="Search by student name, roll number, department, or course..."
                           value="<%= searchKeyword %>">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
                <% if (!searchKeyword.isEmpty()) { %>
                    <a href="<%= request.getContextPath() %>/students" class="btn btn-secondary">Clear</a>
                <% } %>
            </form>
        </div>

        <!-- Conditional Logic: Check if Student List is Empty -->
        <% if (students.isEmpty()) { %>
            <div class="no-results">
                <div class="no-results-icon">&#128269;</div>
                <h3>No Student Records Found</h3>
                <p>No student profiles matched your search query "<%= searchKeyword %>".</p>
                <div style="margin-top: 16px;">
                    <a href="<%= request.getContextPath() %>/students" class="btn btn-primary">View All Students</a>
                </div>
            </div>
        <% } else { %>

            <!-- Dynamic Looping over Multiple Students -->
            <div class="students-grid">
                <% 
                    for (Student student : students) { 
                        double cgpa = student.getCgpa();
                        String cgpaClass = "med";
                        if (cgpa >= 9.0) cgpaClass = "high";
                        else if (cgpa < 8.0) cgpaClass = "low";
                %>
                    <div class="student-card">
                        
                        <!-- Top Header Section with Dynamic <img> tag -->
                        <div class="card-top">
                            <div class="avatar-container">
                                <%-- Conditional Logic for Profile Image: pointing to FileStreamServlet --%>
                                <% if (student.hasProfilePhoto()) { %>
                                    <img src="<%= request.getContextPath() %>/stream-file?studentId=<%= student.getId() %>&category=profile_photo" 
                                         alt="<%= student.getName() %>" 
                                         class="avatar-img"
                                         loading="lazy">
                                <% } else { %>
                                    <%-- Fallback to default avatar image via servlet or initials badge --%>
                                    <img src="<%= request.getContextPath() %>/stream-file?default=true" 
                                         alt="<%= student.getName() %>" 
                                         class="avatar-img"
                                         title="Default Avatar">
                                <% } %>
                            </div>

                            <div class="student-header-info">
                                <h3><%= student.getName() %></h3>
                                <div class="roll-badge"><%= student.getRollNo() %></div>
                                <div class="student-dept"><%= student.getDepartment() %> &bull; Sem <%= student.getSemester() %></div>
                            </div>
                        </div>

                        <!-- Card Body with Profile Details -->
                        <div class="card-body">
                            <div class="meta-row">
                                <div class="meta-item">
                                    <div class="meta-label">Course</div>
                                    <div class="meta-value" style="font-size: 11px;"><%= student.getCourse() %></div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">CGPA</div>
                                    <div class="meta-value">
                                        <span class="badge-cgpa <%= cgpaClass %>"><%= String.format("%.2f", cgpa) %></span>
                                    </div>
                                </div>
                            </div>

                            <div class="contact-info">
                                <div class="contact-line">
                                    <span>&#9993;</span>
                                    <span><%= student.getEmail() %></span>
                                </div>
                                <div class="contact-line">
                                    <span>&#9742;</span>
                                    <span><%= student.getPhone() %></span>
                                </div>
                            </div>

                            <!-- Uploaded Files Section: Looping & Conditional Logic -->
                            <div class="files-section">
                                <div class="files-section-title">
                                    <span>Uploaded Files &amp; Proofs</span>
                                    <span class="files-count-tag">
                                        <%= (student.getFiles() != null) ? student.getFiles().size() : 0 %> attached
                                    </span>
                                </div>

                                <%-- Conditional Logic: Files List empty check --%>
                                <% if (student.getFiles() == null || student.getFiles().isEmpty()) { %>
                                    <div class="empty-files">
                                        <span>&#8505;</span> No uploaded documents or files found for this student.
                                    </div>
                                <% } else { %>
                                    <div class="files-list">
                                        <%-- Loop through associated files --%>
                                        <% for (StudentFile file : student.getFiles()) { %>
                                            <div class="file-item">
                                                
                                                <%-- Conditional File Icon / Thumbnail --%>
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
                                                    <div class="file-name" title="<%= file.getOriginalName() %>">
                                                        <%= file.getOriginalName() %>
                                                    </div>
                                                    <div class="file-sub">
                                                        <span><%= file.getFileCategory().replace("_", " ").toUpperCase() %></span>
                                                        &bull;
                                                        <span><%= file.getFormattedSize() %></span>
                                                    </div>
                                                </div>

                                                <!-- Action Buttons pointing to Streaming Servlet -->
                                                <div class="file-actions">
                                                    <a href="<%= request.getContextPath() %>/stream-file?fileId=<%= file.getId() %>" 
                                                       target="_blank" 
                                                       class="btn btn-outline btn-sm" 
                                                       title="View File">
                                                        Preview
                                                    </a>
                                                    <a href="<%= request.getContextPath() %>/stream-file?fileId=<%= file.getId() %>&download=true" 
                                                       class="btn btn-secondary btn-sm" 
                                                       title="Download to PC">
                                                        &#11015;
                                                    </a>
                                                </div>
                                            </div>
                                        <% } %>
                                    </div>
                                <% } %>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer">
                            <span style="font-size: 11px; color: var(--text-muted);">
                                ID #<%= student.getId() %>
                            </span>
                            <a href="<%= request.getContextPath() %>/student-detail?id=<%= student.getId() %>" 
                               class="btn btn-primary btn-sm">
                                Full Profile &rarr;
                            </a>
                        </div>
                    </div>
                <% } %>
            </div>
        <% } %>

    </main>

    <footer class="footer">
        <p>&copy; 2026 Student Front-End Portal. Powered by Java Servlets, JSP &amp; JDBC on Apache Tomcat.</p>
    </footer>

</body>
</html>
