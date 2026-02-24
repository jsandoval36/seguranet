// upload.js
const express = require("express");
const multer = require("multer");
const path = require("path");
const fs = require("fs");

const app = express();

// IMPORTANT for Azure App Service:
const PORT = process.env.PORT || 3000;

// Ensure uploads folder exists
const uploadsDir = path.join(__dirname, "uploads");
if (!fs.existsSync(uploadsDir)) {
  fs.mkdirSync(uploadsDir, { recursive: true });
}

// Serve static frontend + uploaded files
app.use(express.static(path.join(__dirname, "public")));
app.use("/uploads", express.static(uploadsDir));

// Home route (so your Azure domain loads something)
app.get("/", (req, res) => {
  res.sendFile(path.join(__dirname, "public", "upload.html"));
});

// Multer storage config
const storage = multer.diskStorage({
  destination: (req, file, cb) => cb(null, uploadsDir),
  filename: (req, file, cb) => cb(null, Date.now() + "_" + file.originalname),
});

// Upload handler (max 500MB)
const upload = multer({
  storage,
  limits: { fileSize: 500 * 1024 * 1024 }, // 500MB
});

// Upload endpoint (field name must be "file")
app.post("/upload", upload.single("file"), (req, res) => {
  if (!req.file) return res.status(400).send("No file uploaded.");

  res.send(`
    <h2>Upload successful!</h2>
    <p><strong>Saved as:</strong> ${req.file.filename}</p>
    <a href="/">Upload another file</a><br><br>
    <a href="/uploads/${req.file.filename}" target="_blank">View uploaded file</a>
  `);
});

// Error handler (multer file too large, etc.)
app.use((err, req, res, next) => {
  if (err && err.code === "LIMIT_FILE_SIZE") {
    return res.status(413).send("File too large. Max is 500MB.");
  }
  return res.status(500).send(err ? err.message : "Server error");
});

app.listen(PORT, () => {
  console.log(`Server running at http://localhost:${PORT}`);
});
