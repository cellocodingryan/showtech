const express = require('express');
const mongoose = require('mongoose');
const passport = require('passport');
const flash = require('connect-flash');
const session = require('express-session');
const path = require('path');
const app = express();




// Connect to MongoDB
mongoose
    .connect(
        "mongodb://localhost:27017/test",
        { useNewUrlParser: true , useFindAndModify: false, useUnifiedTopology: true}
    )
    .then(() => console.log('MongoDB Connected'))
    .catch(err => console.log(err));



// Express body parser
app.use(express.urlencoded({ extended: true }));

// Express session
app.use(
    session({
        secret: 'secret',
        resave: true,
        saveUninitialized: true
    })
);

// view engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'twig');

// Passport middleware
app.use(passport.initialize());
app.use(passport.session());
app.use(express.static(__dirname + '/public'));
// Connect flash
app.use(flash());

// Global variables
app.use(function(req, res, next) {
    res.locals.success_msg = req.flash('success_msg');
    res.locals.error_msg = req.flash('error_msg');
    res.locals.error = req.flash('error');
    next();
});

// Routes
app.use('/', require('./routes/home.js'));

const PORT = process.env.PORT || 5000;

app.listen(PORT, console.log(`Server started on port ${PORT}`));
