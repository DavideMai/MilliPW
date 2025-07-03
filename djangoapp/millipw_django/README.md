# MilliPW - Django Version

This is a Django conversion of the original MilliPW PHP application. It provides the same functionality but is built on a modern, secure, and scalable framework.

## Features

*   **Homepage:** Welcome page for the Hospital Administration.
*   **Cittadini (Citizens):** Search and view citizens from the database.
*   **Ospedali (Hospitals):** Full CRUD (Create, Read, Update, Delete) functionality for hospitals, including a search feature.
*   **Patologie (Pathologies):** Search and view pathologies, with a count of associated admissions.
*   **Ricoveri (Admissions):** Search and view hospital admissions, including details of associated pathologies.
*   **Modern Tech Stack:**
    *   Python 3.10+
    *   Django 4.2+
    *   PostgreSQL

---

## Automated Setup (Recommended Method)

This project includes scripts that automate the entire setup process for a local PostgreSQL database.

### Prerequisites

*   **Python 3.10+** must be installed and available in your system's PATH.
*   **PostgreSQL** must be installed and running on your local machine. You must also have created an empty database for this project.

### Instructions

1.  **Download and Unzip:** Download or clone the project files to a folder on your computer.

2.  **Run the Setup Script:**
    *   **For Windows:**
        1.  Simply double-click the `setup_and_run.bat` file.

3.  **Provide Database Details:** The script will pause and interactively ask for your local PostgreSQL connection details:
    *   Database Name
    *   Database User
    *   Database Password
    *   Host (default is `localhost`)
    *   Port (default is `5432`)

4.  **Done!** The script will handle everything else:
    *   It will create a `.env` file with your database credentials.
    *   It will set up a Python virtual environment.
    *   It will install all required packages.
    *   It will create the database tables (`migrate`).
    *   It will populate the tables with the initial data (`seed_db`).
    *   Finally, it will start the Django development server.

You can then access the application at **http://127.0.0.1:8000/**.

---

## Manual Setup Instructions

If you prefer to set up the project manually or are connecting to a remote database like NeonDB, follow these steps.

### 1. Prerequisites (Manual)

*   Python 3.10 or newer installed.
*   A PostgreSQL database (local or remote, like [NeonDB](https://neon.tech)).
*   `git` (optional, for version control).

### 2. Initial Setup (Manual)

1.  **Clone the repository or download the files:**
    Place the files in a directory on your computer.

2.  **Create and activate a virtual environment:**
    This isolates the project's dependencies.
    ```bash
    # On macOS/Linux
    python3 -m venv venv
    source venv/bin/activate

    # On Windows
    python -m venv venv
    .\venv\Scripts\activate
    ```

3.  **Install dependencies:**
    ```bash
    pip install -r requirements.txt
    ```

### 3. Database Configuration (Manual)

1.  **Create `.env` file:**
    Copy the `.env.example` file to a new file named `.env`.

2.  **Edit `.env` file:**
    Open the `.env` file and set the `DATABASE_URL` to your full PostgreSQL connection string.
    *   **Example for NeonDB or other remote DB:**
        `DATABASE_URL="postgres://user:password@host:port/dbname?sslmode=require"`
    *   **Example for a local DB:**
        `DATABASE_URL="postgres://your_user:your_password@localhost:5432/your_db_name"`

3.  **Update `SECRET_KEY`:** For security, it's recommended to open `millipw_django/settings.py` and change the `SECRET_KEY` to a new, unique, and random string.

### 4. Database Migration and Data Loading (Manual)

1.  **Run Migrations:**
    This command creates the database tables based on the Django models.
    ```bash
    python manage.py makemigrations hospital
    python manage.py migrate
    ```

2.  **Load Initial Data:**
    You can use the built-in management command or a GUI tool.
    *   **Using the Management Command (Recommended):**
        ```bash
        python manage.py seed_db
        ```
    *   **Using a GUI Tool:** Connect to your database using a tool like DBeaver, DataGrip, or pgAdmin. Open the `db_scripts/load_postgres_data.sql` file and execute its content.

### 5. Run the Application

1.  **Start the development server:**
    ```bash
    python manage.py runserver
    ```

2.  **View the app:**
    Open your web browser and go to `http://127.0.0.1:8000/`. You should see the homepage of your new Django application!

## Non-Text Files

The original project contained `Descrizione.docx` and `db/data/Milli.ods`. These are non-text files and have not been converted, but you can place them in a `docs/` directory for reference.