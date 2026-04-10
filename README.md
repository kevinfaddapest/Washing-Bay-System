# 🚗 Washing Bay System

## 📌 Project Overview

The **Washing Bay System** is a web-based application designed to manage and streamline operations in a car wash business. It helps track services, manage records, and maintain backups efficiently.

---

## 🚀 Features

* Vehicle wash management
* Service tracking and records
* Image upload functionality
* Backup system for database
* Simple and user-friendly interface
* File and asset management

---

## 🛠️ Technologies Used

* PHP
* MySQL
* HTML, CSS, JavaScript
* Apache Server (.htaccess configuration)

---

## 📂 Project Structure

```id="r8m1qa"
Car-Wash/
│── Backups/              # Database backup files
│── assets/images/        # System images
│── uploads/              # Uploaded files
│── .htaccess             # Server configuration
│── info.php              # PHP info / system file
│── README.md
```

---

## ⚙️ Installation & Setup

1. Clone the repository:

```id="k2w7zm"
git clone https://github.com/kevinfaddapest/Washing-Bay-System.git
```

2. Move the project to your server directory:

* For XAMPP → `htdocs/`
* For WAMP → `www/`

3. Import the database:

* Open **phpMyAdmin**
* Create a database
* Import one of the `.sql` files from the `Backups/` folder

4. Configure database connection:

* Edit your PHP config file (if available) with:

```id="5cz0pf"
host: localhost
user: root
password: ""
database: your_database_name
```

5. Run the project:

* Open your browser and go to:

```id="n4bj8y"
http://localhost/Car-Wash/
```

---

## ▶️ Usage

* Add and manage car wash services
* Upload and view images
* Monitor system activity
* Restore backups when needed

---

## ⚠️ Notes

* Ensure Apache and MySQL are running
* Backup files are stored in the `Backups/` directory
* Large files (images/backups) may increase repository size

---

## 🤝 Contributing

Feel free to fork this project and improve it. Pull requests are welcome.

---

## 📄 License

This project is for educational and demonstration purposes.

---

## 👨‍💻 Author

Developed by **Misairi Mulabbi**
Contact: **0700667769**
