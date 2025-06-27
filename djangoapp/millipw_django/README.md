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
    *   PostgreSQL (compatible with NeonDB)

## Setup Instructions

### 1. Prerequisites

*   Python 3.10 or newer installed.
*   A PostgreSQL database. You can create a free one at [NeonDB](https://neon.tech).
*   `git` (optional, for version control).

### 2. Initial Setup

1.  **Clone the repository or download the files:**
    If you have the files, place them in a directory named `millipw_django`.

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

### 3. Database Configuration

1.  **Get your Database Connection URL:**
    From your NeonDB dashboard (or other PostgreSQL provider), find your connection string. It will look like this:
    `postgres://user:password@host:port/dbname?sslmode=require`

2.  **Configure `settings.py`:**
    Open the file `millipw_django/millipw_django/settings.py`. Find the `DATABASES` section and replace the placeholder `DATABASE_URL` with your actual connection string.

    ```python
    # millipw_django/settings.py

    # ... other settings

    # PASTE YOUR NEONDB (or other Postgres) CONNECTION URL HERE
    DATABASE_URL = "postgres://your_neon_db_user:your_password@ep-....aws.neon.tech/dbname?sslmode=require"

    DATABASES = {
        'default': dj_database_url.config(default=DATABASE_URL, conn_max_age=600)
    }

    # ... other settings
    ```
    **Important:** Also, change the `SECRET_KEY` to a new, unique, and random string.

### 4. Database Migration and Data Loading

1.  **Run Migrations:**
    This command creates the database tables based on the Django models.
    ```bash
    python manage.py migrate
    ```

2.  **Load Initial Data:**
    The provided SQL script contains all the data from your original application, converted for PostgreSQL.
    
    *   **Using `psql` (Recommended):** If you have the `psql` command-line tool, you can load the data directly.
        ```bash
        psql "YOUR_DATABASE_URL" < db_scripts/load_postgres_data.sql
        ```
        Replace `YOUR_DATABASE_URL` with your full connection string.
    
    *   **Using a GUI Tool:** Alternatively, connect to your NeonDB database using a tool like DBeaver, DataGrip, or pgAdmin. Open the `db_scripts/load_postgres_data.sql` file and execute its content.

### 5. Run the Application

1.  **Start the development server:**
    ```bash
    python manage.py runserver
    ```

2.  **View the app:**
    Open your web browser and go to `http://127.0.0.1:8000/`. You should see the homepage of your new Django application!

## Non-Text Files

The original project contained `Descrizione.docx` and `db/data/Milli.ods`. These are non-text files and have not been converted, but you can place them in a `docs/` directory for reference.