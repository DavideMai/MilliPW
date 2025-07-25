import os
from django.core.management.base import BaseCommand
from django.db import connection, DatabaseError
from django.conf import settings

class Command(BaseCommand):
    help = 'Clears the database and seeds it with data from the specified SQL file.'

    def add_arguments(self, parser):
        # Add a --no-input argument to bypass the confirmation prompt for automated scripts
        parser.add_argument(
            '--no-input',
            action='store_true',
            help='Do not prompt for user input.',
        )

    def handle(self, *args, **options):
        """
        The main logic for the command.
        """
        confirmation_needed = not options['no_input']
        
        if confirmation_needed:
            self.stdout.write(self.style.WARNING(
                "\nThis command will completely wipe your current database tables and re-populate them."
            ))
            self.stdout.write(self.style.WARNING("This is a DESTRUCTIVE operation."))
            
            confirmation = input("Are you sure you want to continue? (yes/no): ")
            if confirmation.lower() != 'yes':
                self.stdout.write(self.style.ERROR("Database seeding cancelled."))
                return

        sql_file_path = os.path.join(settings.BASE_DIR, 'db_scripts', 'load_postgres_data.sql')
        self.stdout.write(f"Reading SQL script from: {sql_file_path}")

        try:
            with open(sql_file_path, 'r', encoding='utf-8') as f:
                sql_script = f.read()
        except FileNotFoundError:
            self.stdout.write(self.style.ERROR(f"Error: The file {sql_file_path} was not found."))
            return

        self.stdout.write("Executing SQL script...")

        try:
            with connection.cursor() as cursor:
                cursor.execute(sql_script)
            self.stdout.write(self.style.SUCCESS(
                "\nDatabase has been successfully seeded!"
            ))
        except DatabaseError as e:
            self.stdout.write(self.style.ERROR(f"\nAn error occurred during database seeding: {e}"))
            self.stdout.write(self.style.WARNING(
                "Please check your .env file and ensure your PostgreSQL server is running and the database exists."
            ))