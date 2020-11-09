const mongoose = require('mongoose');

const kittySchema = new mongoose.Schema({
    data: {
        type: Object,
        required: true
    },
});
const question = mongoose.model("Response",kittySchema);

module.exports = question;
