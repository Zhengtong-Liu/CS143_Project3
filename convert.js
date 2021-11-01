# import fs module to read/write file
const fs = require('fs');

# load JSON data
let file = fs.readFileSync("/home/cs143/data/nobel-laureates.json");
let data = JSON.parse(file)

# get the id, givenName, and familyName of the first laureate
let laureate = data.laureates[0];
let id = laureate.id;
let givenName = laureate.givenName.en;
let familyName = laureate.familyName.en;

# print the extracted information
console.log(id + "\t" + givenName + "\t" + familyName);
