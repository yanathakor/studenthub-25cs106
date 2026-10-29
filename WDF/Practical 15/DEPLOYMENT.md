# Local Deployment Guide – StudentHub

## Requirements
- Windows 10/11
- XAMPP 8.x (Apache, PHP, MySQL)
- Web browser, VS Code

## Steps
1. **Install XAMPP** and open the XAMPP Control Panel.
2. **Start** the *Apache* and *MySQL* modules.
3. **Copy project** to `C:\xampp\htdocs\studenthub`.
4. **Create database**
   - Go to `http://localhost/phpmyadmin`
   - New → name: `studenthub` → Create
   - Select it → Import → choose `database.sql` → Go
5. **Configure** `config.php` (host `localhost`, user `root`, password empty, db `studenthub`).
6. **Folder permissions:** make sure `assignment_uploads/` exists and is writable.
7. **Run:** open `http://localhost/studenthub/login.php`.

## Troubleshooting
| Problem | Solution |
|---------|----------|
| Port 80 in use | Change Apache port in `httpd.conf` or stop IIS/Skype |
| MySQL won't start | Port 3306 conflict – stop other MySQL service |
| DB connection failed | Check credentials in `config.php` |
| Blank page | Enable `display_errors` in `php.ini` |
| Upload fails | Check `upload_max_filesize` and folder permissions |

## Deployment Checklist
- [ ] Apache + MySQL running
- [ ] Database imported
- [ ] config.php set
- [ ] Login works for admin and student
- [ ] All pages load without errors
