@echo off
REM ========================================================
REM Build and Deployment Script for Student Front-End Portal
REM ========================================================

echo [1/4] Preparing directories...
if not exist "WebContent\WEB-INF\classes" mkdir "WebContent\WEB-INF\classes"
if not exist "WebContent\WEB-INF\lib" mkdir "WebContent\WEB-INF\lib"
if not exist "WebContent\uploads" mkdir "WebContent\uploads"

echo [2/4] Copying MySQL JDBC connector to WEB-INF\lib...
if exist "D:\xampp\tomcat\lib\mysql-connector-j-8.3.0.jar" (
    copy /Y "D:\xampp\tomcat\lib\mysql-connector-j-8.3.0.jar" "WebContent\WEB-INF\lib\"
)

echo [3/4] Compiling Java Servlet and DAO classes...
set CP=D:\xampp\tomcat\lib\servlet-api.jar;D:\xampp\tomcat\lib\jsp-api.jar;WebContent\WEB-INF\lib\mysql-connector-j-8.3.0.jar
"C:\Program Files\Java\jdk-27\bin\javac.exe" -cp "%CP%" -d "WebContent\WEB-INF\classes" src\com\studentportal\model\*.java src\com\studentportal\util\*.java src\com\studentportal\dao\*.java src\com\studentportal\filter\*.java src\com\studentportal\servlet\*.java

if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Compilation failed!
    exit /b %ERRORLEVEL%
)

echo [4/4] Deploying to Apache Tomcat webapps...
if exist "D:\xampp\tomcat\webapps\q3" rmdir /S /Q "D:\xampp\tomcat\webapps\q3"
xcopy /E /I /Y "WebContent" "D:\xampp\tomcat\webapps\q3"

echo.
echo ========================================================
echo BUILD AND DEPLOYMENT SUCCESSFUL!
echo Portal URL: http://localhost:8080/q3/
echo ========================================================
