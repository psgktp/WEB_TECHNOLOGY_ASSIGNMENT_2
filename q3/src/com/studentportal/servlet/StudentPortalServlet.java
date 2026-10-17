package com.studentportal.servlet;

import com.studentportal.dao.StudentDAO;
import com.studentportal.model.Student;
import com.studentportal.model.StudentFile;
import com.studentportal.model.User;

import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import java.io.IOException;
import java.util.List;

/**
 * StudentPortalServlet handles fetching student details and associated files
 * from MySQL using JDBC and forwarding to students.jsp for dynamic rendering.
 */
@WebServlet(name = "StudentPortalServlet", urlPatterns = {"/students"})
public class StudentPortalServlet extends HttpServlet {
    private static final long serialVersionUID = 1L;

    private StudentDAO studentDAO;

    @Override
    public void init() throws ServletException {
        studentDAO = new StudentDAO();
    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        // 1. Session verification check (defense-in-depth alongside Filter)
        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("user") == null) {
            response.sendRedirect(request.getContextPath() + "/login.jsp?error=unauthorized");
            return;
        }

        User currentUser = (User) session.getAttribute("user");
        request.setAttribute("currentUser", currentUser);

        // 2. Read optional search filter
        String search = request.getParameter("search");
        List<Student> students;

        if (search != null && !search.trim().isEmpty()) {
            students = studentDAO.searchStudentsWithFiles(search.trim());
            request.setAttribute("searchKeyword", search.trim());
        } else {
            students = studentDAO.getAllStudentsWithFiles();
        }

        // 3. Compute portal metrics for dashboard summary cards
        int totalStudents = students.size();
        int totalFiles = 0;
        int totalPhotos = 0;
        int totalDocuments = 0;

        for (Student s : students) {
            if (s.getFiles() != null) {
                totalFiles += s.getFiles().size();
                for (StudentFile f : s.getFiles()) {
                    if ("profile_photo".equalsIgnoreCase(f.getFileCategory())) {
                        totalPhotos++;
                    } else {
                        totalDocuments++;
                    }
                }
            }
        }

        request.setAttribute("students", students);
        request.setAttribute("totalStudents", totalStudents);
        request.setAttribute("totalFiles", totalFiles);
        request.setAttribute("totalPhotos", totalPhotos);
        request.setAttribute("totalDocuments", totalDocuments);

        // 4. Forward to dynamic JSP view
        request.getRequestDispatcher("/students.jsp").forward(request, response);
    }

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        doGet(request, response);
    }
}
