# Campus Compass — Student Space

A simple Campus Compass Dashboard built using HTML5 and CSS3 for a college assignment.

## Project Structure

```
student-dashboard/
│
├── home.html           (Home / Landing Page with Login & Sign Up buttons)
├── login.html          (Student Login with demo fill & theme switcher)
├── signup.html         (Student Registration Page with full validation)
├── index.html          (Dashboard Overview)
├── profile.html        (Profile)
├── attendance.html     (Attendance)
├── courses.html        (Courses)
├── assignments.html    (Assignments)
├── exams.html          (Exams)
├── results.html        (Results)
├── timetable.html      (Timetable)
├── fees.html           (Fees)
├── settings.html       (Settings)
│
├── css/
│   ├── style.css       (Base styles, theme tokens, buttons, form validation, tables)
│   ├── layout.css      (Sidebar, navbar, grid layout)
│   └── pages.css       (Page-specific styles, home page, auth cards)
│
├── js/
│   ├── theme.js             (Dark/Light mode switcher with localStorage persistence & tab sync)
│   ├── signup-validation.js (Form validation for Sign Up)
│   └── components.js        (Dynamic UI components: slider, FAQ, modal, banner, hamburger)
│
├── images/
│   ├── Profile.jpg
│   ├── campus_library.jpg   (Education slider image)
│   ├── college_campus.jpg   (Education slider image)
│   └── college_classroom.jpg(Education slider image)
│
└── README.md
```

## Features

- **Home Page (`home.html`)**: Simple home page with portal overview, theme switcher, and direct **Login** and **Sign Up** buttons.
- **Dynamic UI Components (JavaScript)**:
  - **Image / Content Slider**: Interactive carousel on the home page showcasing educational campus infrastructure with next/prev buttons, indicators, and auto-play.
  - **Collapsible FAQ (Accordion)**: Interactive FAQ section where clicking questions expands/collapses answers.
  - **Modal Popup**: Notice dialog popup with circular details, backdrop click dismissal, and ESC key support.
  - **Notification Banner**: Dismissible announcement banner with close button.
  - **Hamburger Menu**: Mobile responsive navigation toggle for smaller screens.
  - **Light/Dark Theme Switcher**: Toggle theme on any page with persistence via `localStorage`.
- **Login Page (`login.html`)**: Simple student login form with input validation and theme switcher.
- **Sign Up Page (`signup.html`)**: Registration form asking for:
  - First Name
  - Last Name
  - Email ID
  - Gender (Radio options)
  - Department (Dropdown)
  - Mobile Number (10 digits)
  - Institute (Dropdown)
  - Academic Year (Dropdown)
  - Password
- **Form Validation (`js/signup-validation.js`)**: JavaScript validation for all required fields, valid email check, 10-digit mobile check, and password length.
- 10 student dashboard pages with consistent sidebar navigation.

## Technologies Used

- HTML5
- CSS3 (Variables, Flexbox)
- JavaScript (Theme Switcher, Dynamic Components, Form Validation)

## Practical 6 — Campus Event Explorer (Fetch API + JSON)

The home page now includes a campus event explorer powered by `data/events.json` and the Fetch API.

Features:
- Loads event records asynchronously from a local JSON file.
- Searches event title, category, venue, organizer, description, and tags.
- Filters by event category.
- Sorts by earliest date, latest date, or title A–Z.
- Displays six records per page with Previous/Next controls.
- Shows a useful empty state when no records match.
- Includes 15 sample event records in `data/events.json`.

### Run locally
1. Open the `StudentHubPortal` folder in VS Code.
2. Install the **Live Server** extension if it is not already installed.
3. Open `home.html` and select **Open with Live Server**.
4. Use the search, category, sort, and pagination controls in the Campus Events section.

The Fetch API generally needs a local web server; opening `home.html` directly as a `file://` URL may block JSON requests.


## Practical 7 — PHP Registration Processing (Server-Side Validation + JSON Storage)

The existing registration form in `signup.html` submits using `POST` to `register.php`. The PHP handler validates the submitted values again on the server, checks for an existing email address, hashes the password with `password_hash()`, and stores registration records in `data/registrations.json`. The `data/.htaccess` file blocks direct web access to the data directory on Apache.

### How to run Practical 7 on Windows

1. Install and open **XAMPP**. Start **Apache** from the XAMPP Control Panel. MySQL is not required for this practical.
2. Copy the whole `StudentHubPortal` folder into `C:\xampp\htdocs\StudentHubPortal`.
3. In a browser, open `http://localhost/StudentHubPortal/signup.html`. Do not open the PHP file with Live Server or by double-clicking it.
4. Fill in the form and submit it. A valid registration should show a success message; invalid fields or a duplicate email should show an error.
5. To verify saved data, inspect `data/registrations.json` locally. Passwords should appear as hashes, not readable text. Do not upload real student details or the generated registrations file to a public repository.

### What this practical demonstrates

- Reading submitted form values from `$_POST`.
- Server-side validation and input trimming.
- Email format and allowed-option checks.
- Duplicate-email prevention.
- Password hashing with `password_hash()` rather than plain-text storage.
- JSON file writing with a file lock and clear success/error feedback.

### Test cases

| Test | Expected result |
|---|---|
| Valid details and new email | Registration saved and success message displayed |
| Invalid email | Validation error; record not saved |
| Mobile number not exactly 10 digits | Validation error; record not saved |
| Password shorter than 6 characters | Validation error; record not saved |
| Submit the same email again | Duplicate-email error |

`data/registrations.json` is initialized with an empty registrations list. Keep the file writable by PHP; on a normal XAMPP Windows installation this generally works without changing permissions.


## Practical Implementations Included (6–14)

- **Practical 6:** JSON + Fetch API event search, category filter, sorting and pagination in the existing `js/json-data.js` / `data/events.json` flow.
- **Practical 7:** `register.php` processes form data with server-side validation and file-based JSON storage; see `PRACTICAL_7_GUIDE.md`.
- **Practical 8:** `setup.sql`, `db.php`, and `db-test.php` provide the schema and connection check.
- **Practical 9:** `register_db.php` validates registration, checks duplicate email, and hashes passwords.
- **Practical 10:** `login.php`, `logout.php`, and `dashboard.php` demonstrate sessions, roles, and timeout.
- **Practical 11:** `students.php` demonstrates student add/list/search/delete for admins.
- **Practical 12:** `events_admin.php` demonstrates event add/list/delete and poster validation for admins.
- **Practical 13:** `api/events.php` exposes JSON endpoints and `api-events-demo.html` consumes them with Fetch.
- **Practical 14:** `admin.php` shows counts and recent audit activity for admins.

### Local setup for PHP/MySQL practicals
1. Copy this project folder to `C:\xampp\htdocs\StudentHubPortal`.
2. Start Apache and MySQL from XAMPP.
3. Import `setup.sql` using phpMyAdmin.
4. Check the local database credentials in `db.php`.
5. Open `http://localhost/StudentHubPortal/db-test.php`.
6. Create an account at `register_db.php`, then assign it admin role in phpMyAdmin: `UPDATE users SET role='admin' WHERE email='your-email@example.com';`.
7. Sign in at `login.php` and open the dashboard.

**Learning-project note:** not every rubric extension is implemented. Practicals 1–14 are represented in the project, but some items are partial: remember-me tokens, full edit/update forms on the CRUD pages, and CSRF protection are not implemented. Practical 15 is not included in this package. Test each practical and be prepared to explain the code before submitting.
