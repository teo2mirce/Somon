var system = require('system');var page = require('webpage').create();page.open(system.args[1], function(status){var ua = page.evaluate(function() {return     document.getElementsByClassName("cst3")[0].innerText     });console.log(ua);phantom.exit();});