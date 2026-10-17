package com.studentportal.filter;

import javax.servlet.Filter;
import javax.servlet.FilterChain;
import javax.servlet.FilterConfig;
import javax.servlet.ServletException;
import javax.servlet.ServletRequest;
import javax.servlet.ServletResponse;
import javax.servlet.annotation.WebFilter;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import java.io.IOException;

/**
 * Authentication and Security Filter.
 * Intercepts requests to secure pages and ensures that only logged-in users with a valid
 * HttpSession can access the portal and stream student files.
 */
@WebFilter(filterName = "AuthenticationFilter", urlPatterns = {"/students", "/student-detail", "/stream-file"})
public class AuthenticationFilter implements Filter {

    @Override
    public void init(FilterConfig filterConfig) throws ServletException {
        // Initialization if needed
    }

    @Override
    public void doFilter(ServletRequest request, ServletResponse response, FilterChain chain)
            throws IOException, ServletException {

        HttpServletRequest httpRequest = (HttpServletRequest) request;
        HttpServletResponse httpResponse = (HttpServletResponse) response;

        // Prevent client-side caching of secured pages
        httpResponse.setHeader("Cache-Control", "no-cache, no-store, must-revalidate"); // HTTP 1.1
        httpResponse.setHeader("Pragma", "no-cache"); // HTTP 1.0
        httpResponse.setDateHeader("Expires", 0); // Proxies

        HttpSession session = httpRequest.getSession(false);
        boolean isLoggedIn = (session != null && session.getAttribute("user") != null);

        if (isLoggedIn) {
            // User is authenticated, proceed with request
            chain.doFilter(request, response);
        } else {
            // Unauthorized access attempt, redirect to login page with notice
            String contextPath = httpRequest.getContextPath();
            String loginUrl = contextPath + "/login.jsp?error=unauthorized";
            httpResponse.sendRedirect(loginUrl);
        }
    }

    @Override
    public void destroy() {
        // Cleanup resources
    }
}
