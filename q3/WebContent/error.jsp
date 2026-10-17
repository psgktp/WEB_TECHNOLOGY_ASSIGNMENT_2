<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8" isErrorPage="true" %>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error - Student Portal</title>
    <link rel="stylesheet" href="<%= request.getContextPath() %>/css/style.css">
</head>
<body class="login-body">
    <div class="login-card" style="text-align: center;">
        <div class="brand-icon" style="background: #fee2e2; color: #dc2626;">&#9888;</div>
        <h2>Notice / Error</h2>
        <p style="margin: 16px 0; color: var(--text-muted);">
            <%= (request.getAttribute("errorMessage") != null) ? request.getAttribute("errorMessage") : "The requested resource could not be found or an error occurred." %>
        </p>
        <a href="<%= request.getContextPath() %>/students" class="btn btn-primary btn-block">
            Return to Student Directory
        </a>
    </div>
</body>
</html>
