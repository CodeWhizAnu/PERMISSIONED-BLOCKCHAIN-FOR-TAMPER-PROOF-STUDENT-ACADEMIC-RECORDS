# AcademicChain 🔗

**Permissioned Blockchain for Tamper-Proof Student Academic Records**

AcademicChain is a prototype permissioned ledger designed to secure student academic records against insider threats and silent database tampering. Built for a college project (Sanjivani University), it uses deterministic serialization and cryptographic SHA-256 hash chaining to ensure data immutability.

## 🚀 Features

*   **Secure Authentication:** BCRYPT-hashed administrator login with session management.
*   **Live Dashboard:** Real-time statistics and a visual Blockchain Explorer.
*   **SHA-256 Hash Chaining:** Every academic record is mathematically linked to the previous record, creating an unbreakable chain.
*   **Cryptographic Verification Engine:** A built-in scanner that recalculates the entire ledger in real-time to detect broken links or altered data.
*   **Tamper & Heal Simulations:** Included scripts to demonstrate a malicious database edit and simulate a "51% attack" by rewriting history.

## 🛠️ Tech Stack

*   **Frontend:** HTML5, CSS3, Vanilla JavaScript (Glassmorphism UI)
*   **Backend:** Core PHP (v8.x recommended)
*   **Database:** MySQL (via PDO Prepared Statements)
*   **Environment:** XAMPP

## ⚙️ Installation & Setup

1. **Install XAMPP:** Ensure you have XAMPP installed with Apache and MySQL services running.
2. **Clone the Repository:** Place the `AcademicChain` folder inside your XAMPP `htdocs` directory (e.g., `C:\xampp\htdocs\AcademicChain`).
3. **Database Setup:**
   * Open your browser and go to `http://localhost/phpmyadmin/`.
   * Create a new database named `academic_chain` or simply run the provided SQL script.
   * Import the `schema.sql` file (if provided) or manually run the SQL commands generated in Phase 3 of the project to create the `admins` and `blocks` tables.
4. **Default Administrator Credentials:**
   * **Username:** `admin`
   * **Password:** `admin123`

## 📖 How to Run the Demonstration

1. Navigate to `http://localhost/AcademicChain/login.php` in your browser.
2. Log in using the default admin credentials.
3. **Add Records:** Go to the "Add Record" tab and stage 2-3 fictional student records.
4. **View Ledger:** Return to the Dashboard to see the live blockchain visualizer showing the connected SHA-256 hashes.
5. **Simulate a Hack:** 
   * Navigate to the "Verify Record" page.
   * Click **"Simulate Malicious Database Edit"** (`tamper.php`). This alters a grade directly in the database, bypassing the hashing engine.
   * Watch the verification engine detect the breach and flag the ledger as "Invalid".
6. **Simulate a 51% Attack:** Run `http://localhost/AcademicChain/heal_ledger.php` in your browser to recalculate all hashes and hide the tampering, demonstrating the vulnerability of centralized databases.
7. **Factory Reset:** Run `http://localhost/AcademicChain/reset_ledger.php` to wipe the ledger and generate a fresh, mathematically true Genesis Block.

## ⚠️ Disclaimer

This is a **simulated permissioned ledger** built for educational purposes. It demonstrates the cryptographic logic of a blockchain but relies on a centralized MySQL database. In a production environment, this ledger would be distributed across multiple independent nodes to achieve true decentralized consensus.

---
**Author:** Anushka Anil Deshmukh  
**PRN:** 212UCSF1097  
**Domain:** Blockchain and Distributed Ledger Security