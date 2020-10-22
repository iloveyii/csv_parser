# CSV PARSER

This is a small web application built in PHP which is required to upload csv file, parse it and show info to user.



## TASK Division
 This task has the following sub-tasks.
#### Form submission
- When the user submits the form, return html table or show errors to the user.



#### Validate form
- Email : email should be validated
- Name : name should be validated
- File : uploading file should be validated



#### Parse CSV 
- Parse csv file without using builtin php methods


## INSTALLATION
- Clone repo `git clone https://github.com/iloveyii/csv_parser.git`
- Using a PHP based web server, serve public directory as root.
- For convenience you can use php builtin web server inside public directory as :
`php -S localhost:8080 `
- Test with the existing files in directory `test_data`

## ASSUMPTIONS
- We intentionally did not use php short tags `<?= $var ?>` as it is not configured on servers be default.
- We intentionally did not use classes to stick to task description.
- We did not use any backend or frontend frameworks like laravel, react, material ui to keep the task simple, 
and do more manual programming.
