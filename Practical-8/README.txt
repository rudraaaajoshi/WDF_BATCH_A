PRACTICAL 8 — StudentHub MySQL + PDO

FILES
- studenthub.sql: creates database/tables and inserts sample data
- db.php: reusable PDO connection
- test.php: connection test and displays sample students

SETUP
1. Start Apache and MySQL in XAMPP.
2. Open http://localhost/phpmyadmin
3. Select the SQL tab (or create/import database), and run the complete contents of studenthub.sql.
   If importing: click Import and choose studenthub.sql.
4. Copy this Practical-8 folder to C:\xampp\htdocs\Practical-8\
5. Open http://localhost/Practical-8/test.php

Expected result:
- A green success message: Connected successfully to the studenthub database using PHP PDO!
- A table with 3 sample students.

If connection fails:
- Confirm MySQL is running in XAMPP.
- Confirm database studenthub exists.
- The default XAMPP MySQL root password is often blank. If yours is different, edit $password in db.php.
- This is a classroom example. In a real deployment, use a dedicated database user with limited permissions and keep credentials out of public source control.

ER MODEL
students (1) ---- (many) registrations (many) ---- (1) events
Each registration links one student to one event. The unique pair (student_id, event_id) prevents duplicate registrations.
