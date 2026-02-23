const express = requie(express);
const multer = require("multer");
const path = require("path");


const app = express();
const PORT = 3000;

//Server fronted 
app.use(express.static("public"));
app.use("/uploads", express.staticc("uploads"));

//Storage config
const storage = multer.diskStorage({
    destination: "uploads/",
    filename: (req, file, cb) => {
        cb(null, Date.now() + "_" + file.originalname);
    
    }
});

const upload = multer({ storage });

// Uploaad endpoint
app.post("/upload", upload.single("file"), (red, res) => {
    res.send(`
        <h2>Upload successful!</h2>
        <a href="/">Uploaad another file</a><br><br>
        <a href="/upload/${req.file.filename}" taget="_black">
            View uploaded file
        </a>
    `);
});

app.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}`);
});
