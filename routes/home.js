const express = require('express');
const router = express.Router();
const Question = require("../models/question");
const Answer = require("../models/answer");


router.get("/request_show",(req,res)=>{
    Question.find({}, function (err, docs) {

        res.render(`request_show.twig`,{questions: docs});

    });
});
router.get("/edit_request",(req,res)=>{
    Question.find({}, function (err, docs) {

        res.render(`edit_request_show.twig`,{questions: docs});

    });
});

router.get("/view_requests",(req,res)=> {

})

router.get("/",(req,res)=>{
    res.render(`home.twig`);
});
router.get("/home",(req,res)=>{
    res.render(`home.twig`);
});



router.get("/modifyquestion/:name",(req,res)=>{
    let name = req.params.name
    if (name === "null") {
        res.render(`snip/edit_question.twig`);
    } else {
        Question.find({name: name},function(err,docs) {
            if (err) {
                throw err;
            }
            if (!docs.length) {
                res.status(404).json({error: "Question Not Found"})
            } else {
                res.render(`snip/edit_question.twig`,{q: docs[0]});
            }
        })
    }


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
    if (newname.length ==0 || newname == "_option_") {
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

router.post("/submit_request",(req,res)=> {
    let question = {}

    //extract question object from req.body

    for (let key in req.body) {
        if (req.body.hasOwnProperty(key)) {
            let value = req.body[key]

            //multiple choice split
            let index = key.indexOf("_option_");
            if (index == -1) {
                if (value !== '') {
                    question[key] = value;
                }
            } else {
                let name = key.substr(0,index);
                let values = question[name] == null ? [] : question[name];
                // other

                if (req.body[name+"_option_other_checkbox"] != null && key == name+"_option_other_checkbox") {
                    values.push(req.body[name+"_option_other_text"])
                    req.body[name+"_option_other_checkbox"] = undefined
                } else {
                    if (key.indexOf(name+"_option_") != -1 && key.indexOf("option_other_text") == -1) {
                        values.push(value)
                    }
                }
                question[name] = values;
            }
        }
    }

    Question.find({}, function (err, docs) {
        if (err) {
            throw err;
        }

        let answerSchemaJson = {}

        //verify if questions are valid
        for (let key in question) {
            if (question.hasOwnProperty(key)) {
                let valid = false;
                for (let validquestion in docs) {
                    if (docs[validquestion].name === key) {
                        valid = true;
                        let type = null;
                        let options = null;
                        switch (docs[validquestion].type) {
                            case "text":
                            case "largetext":
                                type = String
                                break;
                            case "multiplechoice":
                                options=docs[validquestion].options
                                type = [String]
                                break;
                        }
                        //insert data about question into answer document
                        question[key] = {
                            data: question[key],
                            type: docs[validquestion].type,
                            preview: docs[validquestion].preview
                        }
                        question[key]["options"] = docs[validquestion].options
                        answerSchemaJson[key] = {type:type};
                    }
                }
                if (!valid) {
                    console.log("Invalid")
                    res.status(400).json({"error":"Invalid"});
                    return;
                }

            }
        }

        Answer.create({
            data: question
        })





    });

});
router.get("/view_shows",function(req,res) {
    Answer.find({}, function (err, docs) {
        res.render("view_shows.twig", {docs:docs})
        console.log(docs[0].data.wut)
    })
});
router.get("/view_show/:id",function(req,res) {
    Answer.find({_id:req.params.id}, function (err, docs) {
        for (let i in docs[0].data) {
            if (docs[0].data.hasOwnProperty(i)) {
                docs[0].data[i].name = i;
                console.log(i)
            }
        }
        res.render("view_show.twig", {questions:docs[0].data})
        console.log(docs[0])
    })
});
// router.get("/question",())

module.exports = router;
