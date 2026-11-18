# HabitTrack - Simple Habit Tracker

A simple habit tracker built with HTML, CSS, JavaScript, Bootstrap, MySQL and PHP. Add habits, mark them done each day, and watch your streaks grow.

## Features
- Add a habit with a name, category and target days
- Mark a habit done for today, or undo it with one click
- See your current streak and total check-ins for every habit
- Daily progress bar showing how many habits are done today
- Filter habits by category and search by name
- Edit and delete habits

## Tech stack
- HTML and CSS
- JavaScript
- Bootstrap 5
- PHP with PDO
- MySQL

## Setup
1. Create the database and tables using `database.sql`:
```sql
mysql -u root -p < database.sql
```
2. Update the database details in `config.php` if needed.
3. Copy the project folder to your web server, for example `htdocs` for XAMPP.
4. Open `http://localhost/habit-tracker-php-mysql/` in your browser.

## Database
Database name: `habit_tracker`

Table: `habits`
- id: INT, auto increment, primary key
- name: VARCHAR(255)
- category: ENUM health, fitness, study, productivity, personal
- target_days: INT
- created_at: TIMESTAMP, default current timestamp

Table: `habit_checkins`
- id: INT, auto increment, primary key
- habit_id: INT, foreign key to habits, deleted with the habit
- checkin_date: DATE
- created_at: TIMESTAMP, default current timestamp
- unique check-in per habit per day

## Project structure
- `index.php` - main page, progress bar, habit list and streaks
- `config.php` - MySQL connection with PDO
- `add_habit.php` - handles new habits
- `edit_habit.php` - handles editing a habit
- `delete_habit.php` - deletes a habit and its check-ins
- `toggle_habit.php` - marks a habit done or undone for today
- `database.sql` - database and table setup with sample habits
- `assets/css/style.css` - custom styles
- `assets/js/script.js` - delete confirmation and form helpers

## Author
Built by Hammad
