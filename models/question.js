const mongoose = require('mongoose');


const kittySchema = new mongoose.Schema({
    name: {
        type: String,
        required: true
    },
    placeholder: {
        type: String,
        required: false
    },
    type: {
        type: String,
        required: true
    },
    showonpreview: {
        type: String,
        required: false
    },
    options: {
        type: [String]
    }
});

const question = mongoose.model("Question",kittySchema);

module.exports = question;
