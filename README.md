# In-quiz-ition

## Overview

In-quiz-ition is a medieval/crusader-themed quiz web application built with PHP, MySQL, HTML, CSS, and JavaScript. The project was designed as a full-stack web development project featuring user authentication, quiz functionality, profile management, score tracking, and leaderboard rankings.

The application uses a custom visual theme inspired by medieval church aesthetics and parody imagery. The site was developed locally using XAMPP and later deployed online using InfinityFree.

---

# Features

## User Authentication

* User sign up and login system
* Password hashing using PHP `password_hash()`
* Secure login verification using `password_verify()`
* Session-based authentication
* Protected pages using authentication middleware (`auth.php`)
* Logout functionality
* Password recovery and reset system

## Quiz System

* Dynamic quiz generation from a JSON question bank
* Configurable number of quiz questions
* Randomized question selection
* Prevention of immediate repeated question sets
* Automatic score calculation
* Quiz attempt history storage in MySQL
* Post/Redirect/Get pattern used to prevent duplicate quiz submissions

## User Profiles

* Editable user bio
* Profile picture uploads
* Password change functionality
* User quiz history display
* Average score tracking
* Best score tracking
* Total quizzes taken tracking

## Leaderboard

* Displays top quiz players
* Ranking system based on average quiz score percentage
* Logged-in users can see their current rank

## UI / Design

* Medieval-inspired “In-quiz-ition” theme
* Shared PHP includes for consistent headers and footers
* Responsive layouts using CSS Flexbox and Grid
* Custom logo and themed styling
* Profile images displayed in circular avatar containers

---

# Technologies Used

## Front-End

* HTML5
* CSS3
* JavaScript

## Back-End

* PHP
* MySQL

## Development Tools

* XAMPP
* phpMyAdmin
* InfinityFree hosting

---

# File Structure

htdocs/
├── css/
│   └── style.css
├── images/
│   └── inquizition_logo_circle.png
├── includes/
│   ├── auth.php
│   ├── db.php
│   ├── footer.php
│   └── header.php
├── uploads/
├── index.php
├── quiz.php
├── results.php
├── view_results.php
├── login.php
├── logout.php
├── profile.php
├── leaderboard.php
├── forgot_password.php
├── reset_password.php
└── questions.json
---

---

# Database Schema

## users

| Column          | Type                           |
| --------------- | ------------------------------ |
| user_id         | INT PRIMARY KEY AUTO_INCREMENT |
| username        | VARCHAR(50)                    |
| email           | VARCHAR(100)                   |
| password_hash   | VARCHAR(255)                   |
| bio             | TEXT                           |
| profile_picture | VARCHAR(255)                   |
| reset_token     | VARCHAR(255)                   |
| reset_expires   | DATETIME                       |
| created_at      | DATETIME                       |

## quiz_attempts

| Column          | Type                           |
| --------------- | ------------------------------ |
| attempt_id      | INT PRIMARY KEY AUTO_INCREMENT |
| user_id         | INT                            |
| score           | INT                            |
| total_questions | INT                            |
| taken_at        | DATETIME                       |

---

# How to Run Locally with XAMPP

## 1. Install XAMPP

Install XAMPP and start:

* Apache
* MySQL

## 2. Place Project in htdocs

Move the project folder into:

----------------
xampp/htdocs/
----------------

## 3. Create Database

Open:

----------------------------
http://localhost/phpmyadmin
----------------------------

Create a database named:

-----------
quiz_app
-----------

## 4. Import SQL

Import the SQL schema and seed data using phpMyAdmin.

## 5. Configure Database Connection

Update `includes/db.php` with local XAMPP credentials:

---php---
$host = "localhost";
$dbname = "quiz_app";
$username = "root";
$password = "";
---------

## 6. Run the Website

Open:

---text---
http://localhost/quiz-app/
----------

---

# Deployment

The final version of the project was deployed using InfinityFree.

## Live Website

---text---
https://in-quiz-ition.rf.gd
----------

## Deployment Steps

1. Exported the local MySQL database from phpMyAdmin.
2. Created a MySQL database in InfinityFree.
3. Imported the SQL database into InfinityFree phpMyAdmin.
4. Updated `includes/db.php` with InfinityFree database credentials.
5. Uploaded project files into the `htdocs` directory using InfinityFree File Manager.

---

# Optional Features Implemented

* Quiz randomization
* Variable quiz lengths
* Password recovery system
* User profile customization
* Leaderboard ranking system
* Profile picture uploads

---

# Future Improvements

* Email-based password reset system
* Question categories and difficulty levels
* Admin panel for managing questions
* Timed quizzes
* Achievement/badge system
* Mobile navigation improvements
* Better anti-cheat quiz protections

---

# Credits

Developed as a full-stack web development project using PHP, MySQL, HTML, CSS, and JavaScript.

Theme inspiration combines medieval church imagery with parody internet meme styling to create the “In-quiz-ition” branding.
