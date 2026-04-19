The **Expensio** project is built using a classic, lightweight web development stack that is easy to set up and manage locally. Here are the tech stack details:

### **1. Core Technologies**
* **Language:** **PHP** (Server-side scripting). It handles all the logic, database communication, and session management.
* **Database:** **MySQL** (Relational database). Used to store user profiles, budget settings, and transaction history.
* **Frontend:** **HTML5** and **JavaScript**. Standard web structure and interactivity.

### **2. Design & Styling**
* **CSS Framework:** **Tailwind CSS** (Utility-first framework). This is loaded via CDN, allowing for fast, modern, and highly responsive UI design without writing custom CSS files.
* **Typography:** **Google Fonts (Quicksand)**. A rounded, friendly font that contributes to the "fine" and elegant aesthetic.
* **Icons:** **Heroicons / Inline SVG**. Used for the heart icons, lock icons, and navigation elements.

### **3. Libraries & Tools**
* **Visualization:** **Chart.js**. A powerful JavaScript library used to generate the "Spending Categories" doughnut chart on the dashboard.
* **Security:** **BCRYPT Password Hashing**. Built-in PHP functions (`password_hash` and `password_verify`) are used to ensure user passwords are stored securely.
* **Environment:** **XAMPP** (Local Server).
    * **XAMPP Installer:** ~160 MB.
    * This provides the Apache server and MySQL database needed to run the PHP code.

### **4. Key Features Implemented**
* **Responsive UI:** Works on mobile, tablet, and desktop.
* **Session Management:** Keeps users logged in across different pages.
* **CRUD Operations:** Create, Read, and Delete functionality for expenses.
* **Dynamic Calculations:** Real-time balance and budget warning logic.
