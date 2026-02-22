const express = require('express');
const fs = require('fs');
const path = require('path');

const app = express();
const DATA_FILE = path.join(__dirname, 'reviews.json');

app.use(express.json());
app.use(express.static(__dirname)); // serve static files (index.html, css, etc.)

function readData() {
    try {
        const raw = fs.readFileSync(DATA_FILE, 'utf8');
        return JSON.parse(raw);
    } catch (e) {
        return [];
    }
}

function writeData(data) {
    fs.writeFileSync(DATA_FILE, JSON.stringify(data, null, 2));
}

app.get('/api/reviews', (req, res) => {
    const reviews = readData();
    res.json(reviews);
});

app.post('/api/reviews', (req, res) => {
    const { name, rating, text } = req.body;
    if (!name || !text || typeof rating !== 'number') {
        return res.status(400).json({ error: 'Invalid payload' });
    }
    const reviews = readData();
    const id = reviews.length ? reviews[reviews.length-1].id + 1 : 1;
    const newReview = { id, name, rating, text };
    reviews.push(newReview);
    writeData(reviews);
    res.status(201).json(newReview);
});

app.delete('/api/reviews/:id', (req, res) => {
    const id = parseInt(req.params.id, 10);
    let reviews = readData();
    const before = reviews.length;
    reviews = reviews.filter(r => r.id !== id);
    if (reviews.length === before) {
        return res.status(404).json({ error: 'Not found' });
    }
    writeData(reviews);
    res.json({ success: true });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Server running on http://localhost:${PORT}`);
});
