
# Potato Peeling Planning

MVC MongoDB application for managing annual potato peeling schedules.

## 🌟 Features
- **User authentication** (login/register)
- **Annual planning** (52 weeks)
- **Task assignments**
- **Statistics tracking**
- **Admin management**

## 🛠️ Tech Stack
- **PHP 8.0+** (Backend technology)
- **MongoDB** (Database for storing user data and plans)
- **MVC Pattern** (To keep the code clean and structured)
- **HTML/CSS** (Frontend)

## 🚀 Installation

### 1. Clone the repository
```bash
git clone https://github.com/fadymikhael/corvee-planning.git
```

### 2. Install dependencies
Make sure you have **Composer** installed, then run:
```bash
composer install
```

### 3. Configure MongoDB
Set up your MongoDB connection in `config/database.php`. Replace the placeholder values with your credentials:
```php
// config/database.php
$uri = "mongodb+srv://username:password@your-cluster-url/";
```

## 🗂️ MongoDB Structure

### Users Collection
```javascript
users: {
    username: String,
    password: String,
    role: String  // Example: 'admin', 'user'
}
```

### Planning Collection
```javascript
planning: {
    year: Number,   // Example: 2025
    weeks: Array     // Array of tasks for each week of the year
}
```

## 📋 Usage

1. **Login**: Use the credentials to log in to the system.
2. **Select planning year**: Choose the year to view or edit the potato peeling schedule.
3. **Assign/modify tasks**: Admins can assign tasks for each week, while users can modify or complete them.
4. **View statistics**: Track statistics and progress for the tasks.

## 🗂️ Directory Structure
```
project/
├── config/
│   └── database.php          # MongoDB connection configuration
├── controllers/
│   ├── AuthController.php    # Logic for user authentication
│   └── PlanningController.php # Logic for handling potato peeling plans
├── models/
│   ├── User.php              # User data model
│   └── Planning.php          # Planning data model
├── views/
│   ├── login.php             # Login page view
│   └── planning.php          # Planning page view to assign tasks
└── index.php                 # Entry point of the application
```

## 📋 Requirements
- **PHP 8.0+**
- **MongoDB**
- **Composer**
- **Web server** (Apache/Nginx)

## ⚖️ License
MIT License 
