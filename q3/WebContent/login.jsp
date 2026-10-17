<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%
    // If already logged in, redirect straight to students portal
    if (session != null && session.getAttribute("user") != null) {
        response.sendRedirect(request.getContextPath() + "/students");
        return;
    }

    String errorParam = request.getParameter("error");
    String logoutParam = request.getParameter("logout");
    String errorMessage = (String) request.getAttribute("errorMessage");
    String enteredUsername = (String) request.getAttribute("enteredUsername");
    if (enteredUsername == null) enteredUsername = "";
%>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Secure Sign In</title>
    <link rel="stylesheet" href="<%= request.getContextPath() %>/css/style.css">
</head>
<body class="login-body">

    <div class="login-card">
        <div class="login-brand">
            <div class="brand-icon">&#127891;</div>
            <h1>Student Portal</h1>
            <p>Front-End Academic Management System</p>
        </div>

        <%-- Feedback Alerts based on session & query params --%>
        <% if ("unauthorized".equals(errorParam)) { %>
            <div class="alert alert-danger">
                <span>&#9888;</span>
                <div><strong>Access Denied:</strong> Please sign in with valid credentials to access student profiles.</div>
            </div>
        <% } %>

        <% if ("true".equals(logoutParam)) { %>
            <div class="alert alert-success">
                <span>&#10003;</span>
                <div>You have been safely logged out. Session terminated.</div>
            </div>
        <% } %>

        <% if (errorMessage != null) { %>
            <div class="alert alert-danger">
                <span>&#9888;</span>
                <div><%= errorMessage %></div>
            </div>
        <% } %>

        <form action="<%= request.getContextPath() %>/login" method="POST" id="loginForm">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" 
                       placeholder="Enter your username" value="<%= enteredUsername %>" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Sign In to Portal &rarr;
            </button>
        </form>

        <div class="demo-creds">
            <div class="demo-creds-title">Demo Credentials (Click to Autofill)</div>
            <div class="cred-pills">
                <span class="cred-pill" onclick="fillCreds('admin', 'admin123')">admin / admin123</span>
                <span class="cred-pill" onclick="fillCreds('faculty', 'faculty123')">faculty / faculty123</span>
                <span class="cred-pill" onclick="fillCreds('student', 'student123')">student / student123</span>
            </div>
        </div>
    </div>

    <script>
        function fillCreds(u, p) {
            document.getElementById('username').value = u;
            document.getElementById('password').value = p;
        }

        // Auto-login trigger for automated testing and documentation capture
        const params = new URLSearchParams(window.location.search);
        if (params.get('autologin') === 'admin') {
            fillCreds('admin', 'admin123');
            document.getElementById('loginForm').submit();
        }
    </script>
</body>
</html>
