package com.studentportal.servlet;

import com.studentportal.dao.StudentDAO;
import com.studentportal.model.StudentFile;

import javax.servlet.ServletContext;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;

/**
 * FileStreamServlet retrieves and streams images and documents from the storage directory.
 * Used by <img> tags for images (e.g. src="stream-file?studentId=1&category=profile_photo")
 * and by document download/preview links.
 */
@WebServlet(name = "FileStreamServlet", urlPatterns = {"/stream-file"})
public class FileStreamServlet extends HttpServlet {
    private static final long serialVersionUID = 1L;

    private static final int BUFFER_SIZE = 8192; // 8KB buffer
    private StudentDAO studentDAO;

    @Override
    public void init() throws ServletException {
        studentDAO = new StudentDAO();
    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        // 1. Session verification: ensure only authenticated users can access stored files
        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("user") == null) {
            response.sendError(HttpServletResponse.SC_UNAUTHORIZED, "Unauthorized access. Please log in first.");
            return;
        }

        // 2. Determine target file to stream
        String fileIdParam = request.getParameter("fileId");
        String studentIdParam = request.getParameter("studentId");
        String categoryParam = request.getParameter("category");
        String defaultParam = request.getParameter("default");
        boolean isDownload = "true".equalsIgnoreCase(request.getParameter("download"));

        // If explicit default avatar requested
        if ("true".equalsIgnoreCase(defaultParam)) {
            streamDefaultAvatar(request, response);
            return;
        }

        StudentFile targetFile = null;

        if (fileIdParam != null && !fileIdParam.trim().isEmpty()) {
            try {
                int fileId = Integer.parseInt(fileIdParam.trim());
                targetFile = studentDAO.getFileById(fileId);
            } catch (NumberFormatException ignored) {}
        } else if (studentIdParam != null && !studentIdParam.trim().isEmpty()) {
            try {
                int studentId = Integer.parseInt(studentIdParam.trim());
                if ("profile_photo".equalsIgnoreCase(categoryParam) || categoryParam == null) {
                    targetFile = studentDAO.getProfilePhotoByStudentId(studentId);
                }
            } catch (NumberFormatException ignored) {}
        }

        // If file not found in database or not found for student
        if (targetFile == null) {
            if ("profile_photo".equalsIgnoreCase(categoryParam)) {
                // Graceful fallback to default avatar for missing student profile pictures
                streamDefaultAvatar(request, response);
                return;
            } else {
                response.sendError(HttpServletResponse.SC_NOT_FOUND, "Requested file was not found in the database.");
                return;
            }
        }

        // 3. Resolve physical file path on disk
        File physicalFile = resolvePhysicalFile(request.getServletContext(), targetFile.getFilePath(), targetFile.getStoredName());

        if (physicalFile == null || !physicalFile.exists() || !physicalFile.isFile()) {
            if ("profile_photo".equalsIgnoreCase(targetFile.getFileCategory())) {
                streamDefaultAvatar(request, response);
                return;
            }
            response.sendError(HttpServletResponse.SC_NOT_FOUND, "Physical file not found on disk: " + targetFile.getOriginalName());
            return;
        }

        // 4. Determine MIME type and headers
        String mimeType = targetFile.getFileType();
        if (mimeType == null || mimeType.trim().isEmpty()) {
            mimeType = request.getServletContext().getMimeType(physicalFile.getName());
            if (mimeType == null) {
                mimeType = "application/octet-stream";
            }
        }

        response.setContentType(mimeType);
        response.setContentLengthLong(physicalFile.length());

        // Cache control
        response.setHeader("Cache-Control", "private, max-age=3600");

        // Content-Disposition (inline for img tags / viewing, attachment for forced downloads)
        String disposition = isDownload ? "attachment" : "inline";
        String encodedFilename = targetFile.getOriginalName().replace("\"", "\\\"");
        response.setHeader("Content-Disposition", disposition + "; filename=\"" + encodedFilename + "\"");

        // 5. Stream the file bytes using buffered streams
        streamBytes(physicalFile, response);
    }

    /**
     * Resolves the physical file location checking both servlet context and external storage.
     */
    private File resolvePhysicalFile(ServletContext context, String filePath, String storedName) {
        // Priority 1: Check in WebContent/uploads/
        if (storedName != null && !storedName.isEmpty()) {
            String webPath = context.getRealPath("/uploads/" + storedName);
            if (webPath != null) {
                File f = new File(webPath);
                if (f.exists() && f.isFile()) return f;
            }
        }

        // Priority 2: Check relative path in servlet context
        if (filePath != null && !filePath.isEmpty()) {
            String webPath = context.getRealPath("/" + filePath);
            if (webPath != null) {
                File f = new File(webPath);
                if (f.exists() && f.isFile()) return f;
            }
        }

        // Priority 3: Check external fixed project upload folders
        String[] fallbackDirs = {
            "d:/wt2/q3/uploads/",
            "d:/wt2/q3/WebContent/uploads/",
            System.getProperty("user.dir") + "/uploads/"
        };

        for (String dir : fallbackDirs) {
            if (storedName != null) {
                File f = new File(dir, storedName);
                if (f.exists() && f.isFile()) return f;
            }
            if (filePath != null) {
                File f = new File(filePath);
                if (f.exists() && f.isFile()) return f;
                File f2 = new File(dir, new File(filePath).getName());
                if (f2.exists() && f2.isFile()) return f2;
            }
        }

        return null;
    }

    /**
     * Streams fallback default avatar when profile image is unavailable.
     */
    private void streamDefaultAvatar(HttpServletRequest request, HttpServletResponse response) throws IOException {
        File defaultAvatar = resolvePhysicalFile(request.getServletContext(), "uploads/default_avatar.png", "default_avatar.png");
        if (defaultAvatar != null && defaultAvatar.exists()) {
            response.setContentType("image/png");
            response.setContentLengthLong(defaultAvatar.length());
            response.setHeader("Cache-Control", "public, max-age=86400");
            response.setHeader("Content-Disposition", "inline; filename=\"default_avatar.png\"");
            streamBytes(defaultAvatar, response);
        } else {
            // If even fallback image is missing, send 404
            response.sendError(HttpServletResponse.SC_NOT_FOUND, "Avatar image not found.");
        }
    }

    /**
     * Reads bytes from file and writes to HTTP response stream.
     */
    private void streamBytes(File file, HttpServletResponse response) throws IOException {
        try (BufferedInputStream bis = new BufferedInputStream(new FileInputStream(file), BUFFER_SIZE);
             BufferedOutputStream bos = new BufferedOutputStream(response.getOutputStream(), BUFFER_SIZE)) {

            byte[] buffer = new byte[BUFFER_SIZE];
            int bytesRead;
            while ((bytesRead = bis.read(buffer)) != -1) {
                bos.write(buffer, 0, bytesRead);
            }
            bos.flush();
        }
    }

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        doGet(request, response);
    }
}
