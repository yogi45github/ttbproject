var config = {
    "paths" : {
        "popper"        : "https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min",
        "bootstrap"     : "js/bootstrap.min",
        "owlcarousel"   : "js/owlcarousel",
    },
    "shim" : {
        "popper" : {
            "deps": ["jquery"]
        },
        "bootstrap" : {
            "deps": ["jquery"]
        },
        "owlcarousel": {
            "deps": ["jquery"]
        },
    }
};


// define("initBootstrap", ["popper"], function(popper) {
//     // set popper as required by Bootstrap
//     window.Popper = popper;
//     require(["bootstrap"], function(bootstrap) {
//         // do nothing - just let Bootstrap initialise itself
//     });
// });

// var config = {
//     paths: {            
//             'owlcarousel': "js/owlcarousel",
//             'bootstrap':'js/bootstrap.bundle',

//         },   
//     shim: {
//         'owlcarousel': {
//             deps: ['jquery']
//         },
//         'bootstrap': {
//             'deps': ['jquery']
//         }
//     }
// };