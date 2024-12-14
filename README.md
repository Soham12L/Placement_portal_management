# Placement Portal Management System

## About
NHIT Placement Portal Management System will help the students, staffs, and administrator for placements. The portal allows:
placed to enable students upload and share their placement details including job information and offer letters.
Employees to check all the OTP details of students, download offer letters and to prepare student lists according to the company requirement and performance metrics.
–To keep track of marks posted by instructors and to look after the general running of the portal as an administrator.

## Features
- Student Management: Placement details and offer letters can be also uploaded by students.
- Monitoring and Reporting: Through the lists, staff can easily filter the students based on their academic performance and the company’s preferences.
- Administrative Control: Teachers may change scores that students got.

## Security Features
The portal incorporates robust security measures to ensure data protection and secure operations:
1. Weak Lockout Mechanism:
   It is featured as the HOD and admin accounts get locked on three invalid login entries.
   They make accounts locked for a given time to avoid abuse through passwords hacking.

2. Secure Cookie Attributes:
   - Cookies are set with the following attributes to enhance security:
     - `HttpOnly`: Restricts clientside scripts from executing cookies.
     - `Secure`: Cookies here should only be transmitted over HTTPS to enhance users’ security.
     - `SameSite=Strict`: It stops cross-site request forgeries because cookies are allowed only from the same site.

3. Unique Constraint:
   Student registration numbers are used to make sure that the student joining a particular class does not duplicate another student on the database.

4. CAPTCHA for Login:
   - Google reCAPTCHA is used to ensure that everyone who logs in is actually a human-being, to avoid bot scams.

## Technologies Used
- Frontend: HTML, CSS
- Backend: PHP
- Database: MySQL
- Security Tools: Google reCAPTCHA

## Setup and Usage
1. Clone the repository:
   ```bash
   git clone [repository link]
