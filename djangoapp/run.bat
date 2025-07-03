@echo off
setlocal

ECHO.
ECHO  ======================================================
ECHO       MilliPW Django Project - Run Development Server
ECHO  ======================================================
ECHO.

:: --- Pre-flight Checks ---

:: Check 1: Ensure the virtual environment exists.
:: We check for 'activate' as a reliable sign of a created venv.
IF NOT EXIST venv\Scripts\activate (
    ECHO [ERROR] Virtual environment 'venv' not found.
    ECHO Please run the main 'setup.bat' script first to create it.
    ECHO.
    PAUSE
    EXIT /B 1
)

:: Check 2: Ensure manage.py is in the current directory.
IF NOT EXIST manage.py (
    ECHO [ERROR] 'manage.py' not found in this directory.
    ECHO Please make sure you are running this script from the root of your Django project.
    ECHO.
    PAUSE
    EXIT /B 1
)

ECHO [INFO] Checks passed. Virtual environment and manage.py found.
ECHO.

:: --- Start the Server ---

ECHO [ACTION] Starting the Django development server...
ECHO You can access the application at http://127.0.0.1:8000
ECHO Press CTRL+C in this window to stop the server.
ECHO.

:: Define the path to the venv python executable for clarity
set "VENV_PYTHON=venv\Scripts\python.exe"

:: Run the Django development server
CALL %VENV_PYTHON% manage.py runserver

:: This part of the script will execute after the server is stopped (e.g., with CTRL+C)
endlocal
ECHO.
ECHO Server stopped. Press any key to exit.
PAUSE