const mysql = require('mysql');

const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: ''
});

db.connect(err => {
    if (err) throw err;
    console.log('Connected to MySQL');

    db.query('CREATE DATABASE IF NOT EXISTS AttachMeDB', (err, result) => {
        if (err) throw err;
        console.log('Database created or already exists');

        db.changeUser({ database: 'AttachMeDB' }, err => {
            if (err) throw err;

            const createStudentsTable = `
                CREATE TABLE IF NOT EXISTS students (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    firstName VARCHAR(255) NOT NULL,
                    lastName VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    gender ENUM('male', 'female') NOT NULL
                )
            `;
            db.query(createStudentsTable, (err, result) => {
                if (err) throw err;
                console.log('Students table created or already exists');
            });

            const createCompaniesTable = `
                CREATE TABLE IF NOT EXISTS companies (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    companyName VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    phone VARCHAR(20) NOT NULL,
                    address TEXT NOT NULL,
                    password VARCHAR(255) NOT NULL
                )
            `;
            db.query(createCompaniesTable, (err, result) => {
                if (err) throw err;
                console.log('Companies table created or already exists');
            });

            const createAdminsTable = `
                CREATE TABLE IF NOT EXISTS admins (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    fullName VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    password VARCHAR(255) NOT NULL
                )
            `;
            db.query(createAdminsTable, (err, result) => {
                if (err) throw err;
                console.log('Admins table created or already exists');
            });
        });
    });
});
