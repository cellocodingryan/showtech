const express = require('express');
const router = express.Router();
const Question = require("../models/question");

router.get("/request_show",(req,res)=>{
    let questions = null;
    Question.find({}, function (err, docs) {

        res.render(`request_show.twig`,{questions: docs});

    });
});
router.get("/",(req,res)=>{
    res.render(`home.twig`);
});
router.get("/home",(req,res)=>{
    res.render(`home.twig`);
});


router.get("/findcat",(req,res)=>{

    const Kitty = require("../models/kitten");

    Kitty.findOne({name: req.params.name}).then(cat => {
        if (cat) {
            res.json({name: cat.name,color: cat.color})
        } else {
            res.status(404).json({error: "404 cat not found"})
        }
    })


})

router.get("/modifyquestion/:name",(req,res)=>{
    let name = req.params.name
    Question.find({name: name},function(err,docs) {
        if (err) {
            throw err;
        }
        if (!docs.length) {
            res.render(`snip/edit_question.twig`,{q: docs[0]});
        } else {
            res.render(`snip/edit_question.twig`,{q: docs[0]});
        }
    })

})
router.get("/getquestions",(req,res)=>{
    let name = req.params.name
    Question.find({},function(err,docs) {
        res.render(`snip/request_questions.twig`,{questions: docs});
    })

})

router.delete("/question/:id",(req,res)=> {
    Question.findOneAndDelete({_id:req.params.id},function(error,docs) {
        if (error){
            console.log(error)
            res.status(500).json({error: error})
        } else {
            res.json({docs: docs})

        }

    })
})
router.post("/change_question", (req,res)=> {
    let question = req.body;
    let newname = "";
    for (let i = 0;i < question.name.length;++i) {
        if (question.name[i] != "#") {
            newname += question.name[i]
        }
    }
    if (newname.length ==0) {
        newname = "Unnamed"
    }
    question.name  = newname;
    let multichoicequestions = []
    for (let i in question) {
        if (i.substr(0,12) == "option_value") {
            multichoicequestions.push(question[i]);
        }
    }
    multichoicequestions.pop()

    let doc = {
        name: question.name,
        type: question.type,
        options: multichoicequestions
    }
    if (question._id != null) {

        Question.findOneAndUpdate(
            {_id: question._id},
            doc, {upsert: false},
            function(err, result) {
                if (err) {
                    res.send(err);
                } else {
                    res.send(result);
                }
            }
        )

    } else {

        Question.create(doc,function(err,result) {
            if (err) {
                res.send(err)
            } else {
                res.send(result);
            }


        })
    }

});

// router.get("/question",())

module.exports = router;
