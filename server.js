const express = require('express');
const mysql = require('mysql');
const bodyParser = require('body-parser');
const multer = require('multer');
const upload = multer();

const app = express();
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'AttachMeDB'
});

db.connect(err => {
    if (err) throw err;
    console.log('Connected to database');
});

app.post('/registerCompany', upload.none(), (req, res) => {
    const { companyName, email, phone, address, password } = req.body;
    console.log('Received data for /registerCompany:', req.body); // Debugging statement
    const query = 'INSERT INTO companies (companyName, email, phone, address, password) VALUES (?, ?, ?, ?, ?)';
    db.query(query, [companyName, email, phone, address, password], (err, result) => {
        if (err) {
            console.error('Error inserting into companies:', err); // Debugging statement
            res.json({ success: false, message: err.message });
        } else {
            res.json({ success: true });
        }
    });
});

app.post('/registerStudent', upload.none(), (req, res) => {
    const { registerFirstName, registerLastName, registerEmail, registerPassword, gender } = req.body;
    console.log('Received data for /registerStudent:', req.body); // Debugging statement
    const query = 'INSERT INTO students (firstName, lastName, email, password, gender) VALUES (?, ?, ?, ?, ?)';
    db.query(query, [registerFirstName, registerLastName, registerEmail, registerPassword, gender], (err, result) => {
        if (err) {
            console.error('Error inserting into students:', err); // Debugging statement
            res.json({ success: false, message: err.message });
        } else {
            res.json({ success: true });
        }
    });
});

app.post('/loginStudent', upload.none(), (req, res) => {
    const { loginUsername, loginPassword } = req.body;
    console.log('Received data for /loginStudent:', req.body); // Debugging statement
    const query = 'SELECT * FROM students WHERE email = ? AND password = ?';
    db.query(query, [loginUsername, loginPassword], (err, results) => {
        if (err) {
            console.error('Error querying students:', err); // Debugging statement
            res.json({ success: false, message: err.message });
        } else if (results.length > 0) {
            res.json({ success: true });
        } else {
            res.json({ success: false, message: 'Invalid credentials' });
        }
    });
});

app.post('/resetPassword', upload.none(), (req, res) => {
    const { resetEmail } = req.body;
    console.log('Received data for /resetPassword:', req.body); // Debugging statement
    // Implement password reset logic here
    res.json({ success: true });
});

app.post('/registerAdmin', upload.none(), (req, res) => {
    const { fullName, email, password } = req.body;
    console.log('Received data for /registerAdmin:', req.body); // Debugging statement
    const query = 'INSERT INTO admins (fullName, email, password) VALUES (?, ?, ?)';
    db.query(query, [fullName, email, password], (err, result) => {
        if (err) {
            console.error('Error inserting into admins:', err); // Debugging statement
            res.json({ success: false, message: err.message });
        } else {
            res.json({ success: true });
        }
    });
});

app.listen(3000, () => {
    console.log('Server running on port 3000');
});