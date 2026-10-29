# Testing Document – StudentHub

**Tester:** Feni Kaila  **Environment:** XAMPP (Apache + MySQL), Chrome  **Date:** ____

## 1. Functional Test Cases
| ID  | Module | Test Case | Steps | Expected Result | Actual Result | Status |
|-----|--------|-----------|-------|-----------------|---------------|--------|
| T01 | Register | Valid registration | Fill all fields, submit | Account created, redirect to login | | Pass/Fail |
| T02 | Register | Duplicate email | Register with existing email | Error "Email already exists" | | |
| T03 | Register | Empty fields | Submit blank form | Validation messages shown | | |
| T04 | Register | Weak password | Enter short password | Rejected with message | | |
| T05 | Login | Valid login | Enter correct credentials | Redirect to dashboard | | |
| T06 | Login | Wrong password | Enter wrong password | Error message, no login | | |
| T07 | Login | SQL injection | Enter `' OR '1'='1` | Login fails | | |
| T08 | Logout | Logout | Click logout | Session destroyed, redirect to login | | |
| T09 | Session | Direct URL without login | Open `index.php` logged out | Redirect to login | | |
| T10 | Session | Timeout | Stay idle past timeout | Auto logout | | |
| T11 | Roles | Student opens admin page | Visit `admin.php` as student | Access denied | | |
| T12 | Roles | Admin opens admin page | Login as admin | Page loads | | |
| T13 | Profile | Update profile | Edit details, save | Changes saved | | |
| T14 | Courses | View courses | Open courses page | List displays from DB | | |
| T15 | Course API | Call `course-api.php` | Open in browser | Valid JSON returned | | |
| T16 | Assignment | Upload file | Choose file, submit | File saved, listed | | |
| T17 | Assignment | Download file | Click download | File downloads | | |
| T18 | Attendance | View attendance | Open attendance | Correct records shown | | |
| T19 | Result | View result | Open result page | Marks displayed | | |
| T20 | Student Mgmt | Add/Edit/Delete student (admin) | Perform each action | DB updated correctly | | |
| T21 | Contact | Submit contact form | Fill and send | Success message | | |

## 2. UI / Compatibility
| Check | Chrome | Edge | Firefox | Mobile view |
|-------|--------|------|---------|-------------|
| Layout correct | | | | |
| Sidebar works | | | | |
| Forms usable | | | | |

## 3. Security Checks
- [ ] Passwords stored hashed (`password_hash`)
- [ ] Prepared statements used for queries
- [ ] Session ID regenerated on login
- [ ] Protected pages check session
- [ ] File upload restricted by type/size

## 4. Bugs Found & Fixed
| Bug ID | Description | Fix | Status |
|--------|-------------|-----|--------|
| B01 | | | |

## 5. Conclusion
All major modules were tested. Remaining issues: ______.
